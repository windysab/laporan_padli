<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_simtepa_profil
 */

defined('_JEXEC') or die;

JHtml::_('stylesheet', 'mod_simtepa_profil/default.css', array('version' => 'auto', 'relative' => true));
?>

<div class="simtepa-profil<?php echo $moduleclass_sfx ? ' ' . $moduleclass_sfx : ''; ?>">
    <?php if (empty($pegawai)) : ?>
        <div class="simtepa-empty">Data profil pelaksana belum tersedia.</div>
    <?php else : ?>
        <div class="simtepa-grid">
            <?php foreach ($pegawai as $item) : ?>
                <?php
                $nama = isset($item['nama_gelar']) ? $item['nama_gelar'] : '';
                $foto = ModSimtepaProfilHelper::photoUrl(isset($item['FotoPegawai']) ? $item['FotoPegawai'] : '');
                $lhkpn1 = ModSimtepaProfilHelper::lhkpnUrl(isset($item['link_lhkpn_tahun1']) ? $item['link_lhkpn_tahun1'] : '');
                $lhkpn2 = ModSimtepaProfilHelper::lhkpnUrl(isset($item['link_lhkpn_tahun2']) ? $item['link_lhkpn_tahun2'] : '');
                ?>
                <article class="simtepa-card">
                    <div class="simtepa-card-main">
                        <?php if ($foto) : ?>
                            <img class="simtepa-photo" src="<?php echo $foto; ?>" alt="<?php echo ModSimtepaProfilHelper::escape($nama); ?>" loading="lazy">
                        <?php endif; ?>

                        <div class="simtepa-info">
                            <h3><?php echo ModSimtepaProfilHelper::escape($nama); ?></h3>
                            <p class="simtepa-position"><?php echo ModSimtepaProfilHelper::escape(isset($item['jabatan']) ? $item['jabatan'] : '-'); ?></p>
                            <dl>
                                <div>
                                    <dt>Satker</dt>
                                    <dd><?php echo ModSimtepaProfilHelper::escape(isset($item['nama_satker']) ? $item['nama_satker'] : '-'); ?></dd>
                                </div>
                                <div>
                                    <dt>Pangkat/Gol</dt>
                                    <dd><?php echo ModSimtepaProfilHelper::escape((isset($item['pangkat_ruang']) ? $item['pangkat_ruang'] : '-') . ' / ' . (isset($item['golongan']) ? $item['golongan'] : '-')); ?></dd>
                                </div>
                                <div>
                                    <dt>Lahir</dt>
                                    <dd><?php echo ModSimtepaProfilHelper::escape(isset($item['tempat_lahir']) ? $item['tempat_lahir'] : '-'); ?>, <?php echo ModSimtepaProfilHelper::formatDate(isset($item['tgl_lahir']) ? $item['tgl_lahir'] : ''); ?></dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <details class="simtepa-detail">
                        <summary>Riwayat pendidikan</summary>
                        <?php echo ModSimtepaProfilHelper::cleanHistory(isset($item['riwayat_pendidikan']) ? $item['riwayat_pendidikan'] : ''); ?>
                    </details>

                    <details class="simtepa-detail">
                        <summary>Riwayat pekerjaan</summary>
                        <?php echo ModSimtepaProfilHelper::cleanHistory(isset($item['riwayat_pekerjaan']) ? $item['riwayat_pekerjaan'] : ''); ?>
                    </details>

                    <?php if ($lhkpn1 || $lhkpn2) : ?>
                        <div class="simtepa-links">
                            <?php if ($lhkpn1) : ?>
                                <a href="<?php echo $lhkpn1; ?>" target="_blank" rel="noopener">LHKPN 1</a>
                            <?php endif; ?>
                            <?php if ($lhkpn2) : ?>
                                <a href="<?php echo $lhkpn2; ?>" target="_blank" rel="noopener">LHKPN 2</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
