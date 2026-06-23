<?php
/**
 * Tampilan profil pelaksana dari JSON SIMTEPA.
 *
 * Cara pakai di Joomla:
 * - Simpan file ini di folder template/module yang mengizinkan PHP.
 * - Include file ini dari custom module/template override:
 *   require JPATH_ROOT . '/joomla_profil_pelaksana.php';
 */

$jsonUrl = 'https://simtepa.mahkamahagung.go.id/share/profil_pelaksana/json/804d379b5aac4946eadbdcc6bc7149ef';
$lhkpnBaseUrl = 'https://simtepa.mahkamahagung.go.id/dokumen/file_edoc?folder=folderBuktiKirim&filename=';
$cacheFile = __DIR__ . DIRECTORY_SEPARATOR . 'profil_pelaksana_cache.json';
$cacheTtl = 3600;

function pp_escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function pp_fetch_url($url)
{
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 Profil Pelaksana Joomla',
        ));

        $response = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response !== false && $status >= 200 && $status < 300) {
            return $response;
        }
    }

    $context = stream_context_create(array(
        'http' => array(
            'timeout' => 15,
            'header' => "User-Agent: Mozilla/5.0 Profil Pelaksana Joomla\r\n",
        ),
    ));

    $response = @file_get_contents($url, false, $context);
    return $response === false ? null : $response;
}

function pp_get_json($jsonUrl, $cacheFile, $cacheTtl)
{
    $hasFreshCache = is_file($cacheFile) && (time() - filemtime($cacheFile) < $cacheTtl);

    if ($hasFreshCache) {
        return file_get_contents($cacheFile);
    }

    $json = pp_fetch_url($jsonUrl);

    if ($json && is_dir(dirname($cacheFile)) && is_writable(dirname($cacheFile))) {
        @file_put_contents($cacheFile, $json, LOCK_EX);
    }

    if (!$json && is_file($cacheFile)) {
        return file_get_contents($cacheFile);
    }

    return $json;
}

function pp_format_date($date)
{
    if (!$date) {
        return '-';
    }

    $timestamp = strtotime($date);
    if (!$timestamp) {
        return pp_escape($date);
    }

    $months = array(
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    );

    return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
}

function pp_clean_history($html)
{
    $html = strip_tags((string) $html, '<ol><ul><li><br><b><strong><i><em>');
    $html = preg_replace('/\s+style=("|\')[^"\']*("|\')/i', '', $html);
    $html = preg_replace('/\s+on[a-z]+=("|\')[^"\']*("|\')/i', '', $html);
    return $html ?: '<span class="pp-muted">Belum ada data.</span>';
}

function pp_initials($name)
{
    $parts = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z\s]/', ' ', (string) $name)));
    $initials = '';

    foreach ($parts as $part) {
        if ($part !== '') {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        if (strlen($initials) >= 2) {
            break;
        }
    }

    return $initials ?: 'PA';
}

function pp_photo_candidates($filename)
{
    if (!$filename) {
        return array();
    }

    $file = rawurlencode($filename);

    return array(
        'https://simtepa.mahkamahagung.go.id/foto/pegawai/' . $file,
        'https://simtepa.mahkamahagung.go.id/foto/foto_pegawai/' . $file,
        'https://simtepa.mahkamahagung.go.id/upload/foto/' . $file,
        'https://simtepa.mahkamahagung.go.id/dokumen/file_foto?folder=fotoPegawai&filename=' . $file,
        'https://simtepa.mahkamahagung.go.id/dokumen/file_foto?folder=pegawai&filename=' . $file,
    );
}

$json = pp_get_json($jsonUrl, $cacheFile, $cacheTtl);
$pegawai = $json ? json_decode($json, true) : array();
$pegawai = is_array($pegawai) ? $pegawai : array();
$jabatanList = array();
$genderCount = array('Pria' => 0, 'Wanita' => 0);

foreach ($pegawai as $item) {
    $jabatan = isset($item['jabatan']) ? trim($item['jabatan']) : '';
    $gender = isset($item['jenis_kelamin']) ? trim($item['jenis_kelamin']) : '';

    if ($jabatan !== '') {
        $jabatanList[$jabatan] = true;
    }

    if (isset($genderCount[$gender])) {
        $genderCount[$gender]++;
    }
}

ksort($jabatanList);
?>

<section class="profil-pelaksana" id="profilPelaksana">
    <style>
        .profil-pelaksana {
            --pp-ink: #172033;
            --pp-muted: #697386;
            --pp-line: #dde5ee;
            --pp-soft: #f5f8fb;
            --pp-accent: #0b7f76;
            --pp-gold: #b88922;
            --pp-red: #9f2b2f;
            color: var(--pp-ink);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.55;
            margin: 0 auto;
            max-width: 1180px;
            padding: 18px;
        }

        .pp-hero {
            background: linear-gradient(135deg, #0f5f68, #16466e 58%, #6a2f31);
            border-radius: 8px;
            color: #fff;
            overflow: hidden;
            padding: 26px;
            position: relative;
        }

        .pp-hero:after {
            background: rgba(255, 255, 255, .12);
            content: "";
            height: 220px;
            position: absolute;
            right: -80px;
            top: -90px;
            transform: rotate(24deg);
            width: 220px;
        }

        .pp-kicker {
            color: #f5d48a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .pp-title {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.15;
            margin: 0;
        }

        .pp-subtitle {
            color: rgba(255, 255, 255, .82);
            max-width: 680px;
            margin: 10px 0 0;
        }

        .pp-stats {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin: 16px 0 18px;
        }

        .pp-stat {
            background: #fff;
            border: 1px solid var(--pp-line);
            border-left: 4px solid var(--pp-accent);
            border-radius: 8px;
            padding: 14px;
        }

        .pp-stat strong {
            display: block;
            font-size: 25px;
            line-height: 1;
        }

        .pp-stat span {
            color: var(--pp-muted);
            font-size: 13px;
        }

        .pp-toolbar {
            align-items: center;
            background: var(--pp-soft);
            border: 1px solid var(--pp-line);
            border-radius: 8px;
            display: grid;
            gap: 10px;
            grid-template-columns: 1.3fr .8fr .7fr;
            margin-bottom: 18px;
            padding: 12px;
        }

        .pp-input,
        .pp-select {
            background: #fff;
            border: 1px solid #cbd5df;
            border-radius: 6px;
            color: var(--pp-ink);
            min-height: 42px;
            padding: 8px 12px;
            width: 100%;
        }

        .pp-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .pp-card {
            background: #fff;
            border: 1px solid var(--pp-line);
            border-radius: 8px;
            box-shadow: 0 10px 22px rgba(23, 32, 51, .07);
            display: flex;
            flex-direction: column;
            min-height: 100%;
            overflow: hidden;
        }

        .pp-card-top {
            align-items: center;
            background: linear-gradient(180deg, #f8fbfd, #fff);
            display: flex;
            gap: 14px;
            padding: 16px;
        }

        .pp-photo,
        .pp-avatar {
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 5px 14px rgba(23, 32, 51, .15);
            flex: 0 0 78px;
            height: 78px;
            object-fit: cover;
            width: 78px;
        }

        .pp-avatar {
            align-items: center;
            background: var(--pp-accent);
            color: #fff;
            display: none;
            font-size: 22px;
            font-weight: 800;
            justify-content: center;
        }

        .pp-name {
            font-size: 17px;
            font-weight: 800;
            margin: 0;
        }

        .pp-role {
            color: var(--pp-accent);
            font-size: 13px;
            font-weight: 700;
            margin-top: 4px;
        }

        .pp-body {
            display: grid;
            gap: 10px;
            padding: 0 16px 16px;
        }

        .pp-meta {
            border-top: 1px solid var(--pp-line);
            display: grid;
            gap: 8px;
            padding-top: 12px;
        }

        .pp-meta-row {
            display: flex;
            gap: 8px;
            justify-content: space-between;
        }

        .pp-label {
            color: var(--pp-muted);
            font-size: 12px;
        }

        .pp-value {
            font-size: 13px;
            font-weight: 700;
            text-align: right;
        }

        .pp-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: auto;
        }

        .pp-button {
            align-items: center;
            background: var(--pp-accent);
            border: 0;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            display: inline-flex;
            font-size: 13px;
            font-weight: 700;
            justify-content: center;
            min-height: 36px;
            padding: 8px 12px;
            text-decoration: none;
        }

        .pp-button:hover {
            background: #075d58;
            color: #fff;
            text-decoration: none;
        }

        .pp-button-secondary {
            background: #eef3f7;
            color: var(--pp-ink);
        }

        .pp-button-secondary:hover {
            background: #dfe8ef;
            color: var(--pp-ink);
        }

        .pp-empty,
        .pp-alert {
            background: #fff8e8;
            border: 1px solid #efd28c;
            border-radius: 8px;
            color: #65480c;
            padding: 14px;
        }

        .pp-modal {
            background: rgba(15, 23, 42, .55);
            display: none;
            inset: 0;
            overflow: auto;
            padding: 24px;
            position: fixed;
            z-index: 9999;
        }

        .pp-modal.is-open {
            display: block;
        }

        .pp-modal-panel {
            background: #fff;
            border-radius: 8px;
            margin: 0 auto;
            max-width: 820px;
            overflow: hidden;
        }

        .pp-modal-head {
            align-items: flex-start;
            background: #f8fbfd;
            border-bottom: 1px solid var(--pp-line);
            display: flex;
            justify-content: space-between;
            gap: 14px;
            padding: 18px;
        }

        .pp-close {
            background: #fff;
            border: 1px solid var(--pp-line);
            border-radius: 6px;
            cursor: pointer;
            font-size: 22px;
            height: 38px;
            line-height: 1;
            width: 38px;
        }

        .pp-modal-body {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            padding: 18px;
        }

        .pp-history {
            border: 1px solid var(--pp-line);
            border-radius: 8px;
            padding: 14px;
        }

        .pp-history h4 {
            color: var(--pp-red);
            font-size: 15px;
            margin: 0 0 10px;
        }

        .pp-history ol,
        .pp-history ul {
            margin: 0;
            padding-left: 20px;
        }

        .pp-muted {
            color: var(--pp-muted);
        }

        @media (max-width: 900px) {
            .pp-grid,
            .pp-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .pp-toolbar,
            .pp-modal-body {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .profil-pelaksana {
                padding: 10px;
            }

            .pp-hero {
                padding: 20px;
            }

            .pp-title {
                font-size: 24px;
            }

            .pp-grid,
            .pp-stats {
                grid-template-columns: 1fr;
            }

            .pp-card-top {
                align-items: flex-start;
            }
        }
    </style>

    <div class="pp-hero">
        <div class="pp-kicker">Profil Pelaksana</div>
        <h2 class="pp-title">Pengadilan Agama Amuntai</h2>
        <p class="pp-subtitle">Data pegawai ditarik dari SIMTEPA Mahkamah Agung dan ditampilkan dalam kartu profil yang mudah dicari, difilter, dan dibaca.</p>
    </div>

    <?php if (!$pegawai): ?>
        <div class="pp-alert" style="margin-top:16px;">
            Data profil belum bisa dimuat. Periksa koneksi server Joomla ke SIMTEPA atau pastikan ekstensi cURL/file_get_contents aktif.
        </div>
    <?php else: ?>
        <div class="pp-stats">
            <div class="pp-stat">
                <strong><?php echo count($pegawai); ?></strong>
                <span>Total pegawai</span>
            </div>
            <div class="pp-stat">
                <strong><?php echo (int) $genderCount['Pria']; ?></strong>
                <span>Pegawai pria</span>
            </div>
            <div class="pp-stat">
                <strong><?php echo (int) $genderCount['Wanita']; ?></strong>
                <span>Pegawai wanita</span>
            </div>
        </div>

        <div class="pp-toolbar">
            <input class="pp-input" id="ppSearch" type="search" placeholder="Cari nama, jabatan, pangkat, atau tempat lahir">
            <select class="pp-select" id="ppJabatan">
                <option value="">Semua jabatan</option>
                <?php foreach (array_keys($jabatanList) as $jabatan): ?>
                    <option value="<?php echo pp_escape(strtolower($jabatan)); ?>"><?php echo pp_escape($jabatan); ?></option>
                <?php endforeach; ?>
            </select>
            <select class="pp-select" id="ppGender">
                <option value="">Semua jenis kelamin</option>
                <option value="pria">Pria</option>
                <option value="wanita">Wanita</option>
            </select>
        </div>

        <div class="pp-grid" id="ppGrid">
            <?php foreach ($pegawai as $index => $item): ?>
                <?php
                $name = isset($item['nama_gelar']) ? $item['nama_gelar'] : '-';
                $role = isset($item['jabatan']) ? $item['jabatan'] : '-';
                $gender = isset($item['jenis_kelamin']) ? $item['jenis_kelamin'] : '-';
                $birthPlace = isset($item['tempat_lahir']) ? $item['tempat_lahir'] : '-';
                $birthDate = isset($item['tgl_lahir']) ? $item['tgl_lahir'] : '';
                $satker = isset($item['nama_satker']) ? $item['nama_satker'] : '-';
                $golongan = isset($item['golongan']) ? $item['golongan'] : '-';
                $pangkat = isset($item['pangkat_ruang']) ? $item['pangkat_ruang'] : '-';
                $tmt = isset($item['tmt_jabatan_terakhir']) ? $item['tmt_jabatan_terakhir'] : '';
                $education = isset($item['riwayat_pendidikan']) ? pp_clean_history($item['riwayat_pendidikan']) : '';
                $career = isset($item['riwayat_pekerjaan']) ? pp_clean_history($item['riwayat_pekerjaan']) : '';
                $photoOptions = pp_photo_candidates(isset($item['FotoFormal']) ? $item['FotoFormal'] : '');
                $searchText = strtolower($name . ' ' . $role . ' ' . $gender . ' ' . $birthPlace . ' ' . $satker . ' ' . $golongan . ' ' . $pangkat);
                $detailId = 'ppDetail' . $index;
                ?>
                <article class="pp-card" data-name="<?php echo pp_escape($searchText); ?>" data-role="<?php echo pp_escape(strtolower($role)); ?>" data-gender="<?php echo pp_escape(strtolower($gender)); ?>">
                    <div class="pp-card-top">
                        <?php if ($photoOptions): ?>
                            <img class="pp-photo" src="<?php echo pp_escape($photoOptions[0]); ?>" data-photo-options="<?php echo pp_escape(json_encode($photoOptions)); ?>" alt="Foto <?php echo pp_escape($name); ?>">
                        <?php endif; ?>
                        <div class="pp-avatar"><?php echo pp_escape(pp_initials($name)); ?></div>
                        <div>
                            <h3 class="pp-name"><?php echo pp_escape($name); ?></h3>
                            <div class="pp-role"><?php echo pp_escape($role); ?></div>
                        </div>
                    </div>
                    <div class="pp-body">
                        <div class="pp-meta">
                            <div class="pp-meta-row">
                                <span class="pp-label">Satker</span>
                                <span class="pp-value"><?php echo pp_escape($satker); ?></span>
                            </div>
                            <div class="pp-meta-row">
                                <span class="pp-label">Pangkat/Gol.</span>
                                <span class="pp-value"><?php echo pp_escape($pangkat . ' / ' . $golongan); ?></span>
                            </div>
                            <div class="pp-meta-row">
                                <span class="pp-label">TTL</span>
                                <span class="pp-value"><?php echo pp_escape($birthPlace . ', ' . pp_format_date($birthDate)); ?></span>
                            </div>
                            <div class="pp-meta-row">
                                <span class="pp-label">TMT Jabatan</span>
                                <span class="pp-value"><?php echo pp_escape(pp_format_date($tmt)); ?></span>
                            </div>
                        </div>

                        <div class="pp-actions">
                            <button class="pp-button" type="button" data-open-detail="<?php echo pp_escape($detailId); ?>">Lihat Detail</button>
                            <?php if (!empty($item['link_lhkpn_tahun1'])): ?>
                                <a class="pp-button pp-button-secondary" href="<?php echo pp_escape($lhkpnBaseUrl . rawurlencode($item['link_lhkpn_tahun1'])); ?>" target="_blank" rel="noopener">LHKPN Terbaru</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div id="<?php echo pp_escape($detailId); ?>" hidden>
                        <div data-detail-title><?php echo pp_escape($name); ?></div>
                        <div data-detail-role><?php echo pp_escape($role); ?></div>
                        <div data-detail-meta>
                            <?php echo pp_escape($gender . ' | ' . $satker . ' | ' . $pangkat . ' / ' . $golongan); ?>
                        </div>
                        <div data-detail-education><?php echo $education; ?></div>
                        <div data-detail-career><?php echo $career; ?></div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="pp-empty" id="ppEmpty" style="display:none;margin-top:16px;">Data tidak ditemukan untuk pencarian/filter tersebut.</div>
    <?php endif; ?>

    <div class="pp-modal" id="ppModal" aria-hidden="true">
        <div class="pp-modal-panel" role="dialog" aria-modal="true" aria-labelledby="ppModalTitle">
            <div class="pp-modal-head">
                <div>
                    <div class="pp-kicker" style="color:#b88922;">Detail Profil</div>
                    <h3 class="pp-name" id="ppModalTitle"></h3>
                    <div class="pp-role" id="ppModalRole"></div>
                    <div class="pp-muted" id="ppModalMeta"></div>
                </div>
                <button class="pp-close" type="button" id="ppModalClose" aria-label="Tutup">&times;</button>
            </div>
            <div class="pp-modal-body">
                <div class="pp-history">
                    <h4>Riwayat Pendidikan</h4>
                    <div id="ppModalEducation"></div>
                </div>
                <div class="pp-history">
                    <h4>Riwayat Pekerjaan</h4>
                    <div id="ppModalCareer"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var root = document.getElementById('profilPelaksana');
            if (!root) {
                return;
            }

            var search = root.querySelector('#ppSearch');
            var jabatan = root.querySelector('#ppJabatan');
            var gender = root.querySelector('#ppGender');
            var cards = Array.prototype.slice.call(root.querySelectorAll('.pp-card'));
            var empty = root.querySelector('#ppEmpty');
            var modal = root.querySelector('#ppModal');
            var modalTitle = root.querySelector('#ppModalTitle');
            var modalRole = root.querySelector('#ppModalRole');
            var modalMeta = root.querySelector('#ppModalMeta');
            var modalEducation = root.querySelector('#ppModalEducation');
            var modalCareer = root.querySelector('#ppModalCareer');
            var modalClose = root.querySelector('#ppModalClose');

            function normalize(value) {
                return (value || '').toString().toLowerCase();
            }

            function filterCards() {
                var term = normalize(search && search.value);
                var role = normalize(jabatan && jabatan.value);
                var selectedGender = normalize(gender && gender.value);
                var visible = 0;

                cards.forEach(function(card) {
                    var matchTerm = !term || normalize(card.getAttribute('data-name')).indexOf(term) !== -1;
                    var matchRole = !role || normalize(card.getAttribute('data-role')) === role;
                    var matchGender = !selectedGender || normalize(card.getAttribute('data-gender')) === selectedGender;
                    var show = matchTerm && matchRole && matchGender;

                    card.style.display = show ? '' : 'none';
                    if (show) {
                        visible += 1;
                    }
                });

                if (empty) {
                    empty.style.display = visible ? 'none' : 'block';
                }
            }

            function tryNextPhoto(img) {
                var options = [];
                var index = parseInt(img.getAttribute('data-photo-index') || '0', 10);

                try {
                    options = JSON.parse(img.getAttribute('data-photo-options') || '[]');
                } catch (error) {
                    options = [];
                }

                index += 1;

                if (options[index]) {
                    img.setAttribute('data-photo-index', index);
                    img.src = options[index];
                    return;
                }

                img.style.display = 'none';
                var avatar = img.parentNode ? img.parentNode.querySelector('.pp-avatar') : null;
                if (avatar) {
                    avatar.style.display = 'flex';
                }
            }

            root.querySelectorAll('.pp-photo').forEach(function(img) {
                img.addEventListener('error', function() {
                    tryNextPhoto(img);
                });
            });

            root.querySelectorAll('[data-open-detail]').forEach(function(button) {
                button.addEventListener('click', function() {
                    var detail = document.getElementById(button.getAttribute('data-open-detail'));
                    if (!detail || !modal) {
                        return;
                    }

                    modalTitle.textContent = detail.querySelector('[data-detail-title]').textContent;
                    modalRole.textContent = detail.querySelector('[data-detail-role]').textContent;
                    modalMeta.textContent = detail.querySelector('[data-detail-meta]').textContent;
                    modalEducation.innerHTML = detail.querySelector('[data-detail-education]').innerHTML;
                    modalCareer.innerHTML = detail.querySelector('[data-detail-career]').innerHTML;
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                });
            });

            function closeModal() {
                if (!modal) {
                    return;
                }

                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            }

            if (search) {
                search.addEventListener('input', filterCards);
            }

            if (jabatan) {
                jabatan.addEventListener('change', filterCards);
            }

            if (gender) {
                gender.addEventListener('change', filterCards);
            }

            if (modalClose) {
                modalClose.addEventListener('click', closeModal);
            }

            if (modal) {
                modal.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        closeModal();
                    }
                });
            }

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });
        })();
    </script>
</section>
