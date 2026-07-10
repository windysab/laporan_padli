<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1><i class="fas fa-heart-broken"></i> Laporan Perceraian
                                <?php if (isset($selected_wilayah) && $selected_wilayah !== 'Semua'): ?>
                                    <span class="badge badge-info">
                                        <?= ($selected_wilayah === 'HSU') ? 'Hulu Sungai Utara' : $selected_wilayah ?>
                                    </span>
                                <?php endif ?>
                                <?php if (isset($selected_jenis_perkara) && $selected_jenis_perkara !== 'semua'): ?>
                                    <span class="badge badge-primary">
                                        <?= $selected_jenis_perkara ?>
                                    </span>
                                <?php endif ?>
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Laporan Perceraian</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Summary Statistics -->
            <?php if (isset($summary)): ?>
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4 col-6">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3><?= isset($summary->total_perceraian) ? number_format($summary->total_perceraian) : 0 ?></h3>
                                        <p>Total Perceraian</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-heart-broken"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3><?= isset($summary->cerai_gugat) ? number_format($summary->cerai_gugat) : 0 ?></h3>
                                        <p>Cerai Gugat</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-female"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3><?= isset($summary->cerai_talak) ? number_format($summary->cerai_talak) : 0 ?></h3>
                                        <p>Cerai Talak</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-male"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif ?>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card filter-card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-filter"></i> Filter Laporan</h3>
                                </div>
                                <div class="card-body">
                                    <form action="<?= base_url() ?>index.php/Laporan_perceraian" method="POST" id="filterForm">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Wilayah:</label>
                                                    <select name="wilayah" class="form-control">
                                                        <option value="Semua" <?= (isset($selected_wilayah) && $selected_wilayah === 'Semua') ? 'selected' : '' ?>>Semua Wilayah</option>
                                                        <option value="HSU" <?= (isset($selected_wilayah) && $selected_wilayah === 'HSU') ? 'selected' : '' ?>>Hulu Sungai Utara</option>
                                                        <option value="Balangan" <?= (isset($selected_wilayah) && $selected_wilayah === 'Balangan') ? 'selected' : '' ?>>Balangan</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Jenis Perkara:</label>
                                                    <select name="jenis_perkara" class="form-control">
                                                        <option value="semua" <?= (isset($selected_jenis_perkara) && $selected_jenis_perkara === 'semua') ? 'selected' : '' ?>>Semua Jenis</option>
                                                        <?php if (isset($jenis_perkara_list) && count($jenis_perkara_list) > 0): ?>
                                                            <?php foreach ($jenis_perkara_list as $item): ?>
                                                                <option value="<?= $item->jenis_perkara_nama ?>" <?= (isset($selected_jenis_perkara) && $selected_jenis_perkara === $item->jenis_perkara_nama) ? 'selected' : '' ?>>
                                                                    <?= $item->jenis_perkara_nama ?>
                                                                </option>
                                                            <?php endforeach ?>
                                                        <?php endif ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Jenis Laporan:</label>
                                                    <select name="jenis_laporan" class="form-control" id="jenisLaporan" onchange="toggleFilter()">
                                                        <option value="bulanan" <?= (isset($selected_jenis) && $selected_jenis === 'bulanan') ? 'selected' : '' ?>>Bulanan</option>
                                                        <option value="tahunan" <?= (isset($selected_jenis) && $selected_jenis === 'tahunan') ? 'selected' : '' ?>>Tahunan</option>
                                                        <option value="custom" <?= (isset($selected_jenis) && $selected_jenis === 'custom') ? 'selected' : '' ?>>Custom Range</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="filterBulan">
                                                <div class="form-group">
                                                    <label>Bulan:</label>
                                                    <select name="lap_bulan" class="form-control">
                                                        <option value="01" <?= (isset($selected_bulan) && $selected_bulan === '01') ? 'selected' : '' ?>>Januari</option>
                                                        <option value="02" <?= (isset($selected_bulan) && $selected_bulan === '02') ? 'selected' : '' ?>>Februari</option>
                                                        <option value="03" <?= (isset($selected_bulan) && $selected_bulan === '03') ? 'selected' : '' ?>>Maret</option>
                                                        <option value="04" <?= (isset($selected_bulan) && $selected_bulan === '04') ? 'selected' : '' ?>>April</option>
                                                        <option value="05" <?= (isset($selected_bulan) && $selected_bulan === '05') ? 'selected' : '' ?>>Mei</option>
                                                        <option value="06" <?= (isset($selected_bulan) && $selected_bulan === '06') ? 'selected' : '' ?>>Juni</option>
                                                        <option value="07" <?= (isset($selected_bulan) && $selected_bulan === '07') ? 'selected' : '' ?>>Juli</option>
                                                        <option value="08" <?= (isset($selected_bulan) && $selected_bulan === '08') ? 'selected' : '' ?>>Agustus</option>
                                                        <option value="09" <?= (isset($selected_bulan) && $selected_bulan === '09') ? 'selected' : '' ?>>September</option>
                                                        <option value="10" <?= (isset($selected_bulan) && $selected_bulan === '10') ? 'selected' : '' ?>>Oktober</option>
                                                        <option value="11" <?= (isset($selected_bulan) && $selected_bulan === '11') ? 'selected' : '' ?>>November</option>
                                                        <option value="12" <?= (isset($selected_bulan) && $selected_bulan === '12') ? 'selected' : '' ?>>Desember</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="filterTahun">
                                                <div class="form-group">
                                                    <label>Tahun:</label>
                                                    <select name="lap_tahun" class="form-control">
                                                        <?php for ($year = 2016; $year <= date('Y') + 1; $year++): ?>
                                                            <option value="<?= $year ?>" <?= (isset($selected_tahun) && $selected_tahun == $year) ? 'selected' : '' ?>>
                                                                <?= $year ?>
                                                            </option>
                                                        <?php endfor ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="filterTanggalMulai" style="display:none;">
                                                <div class="form-group">
                                                    <label>Tanggal Mulai:</label>
                                                    <input type="date" name="tanggal_mulai" class="form-control" value="<?= $this->input->post('tanggal_mulai') ?: date('Y-m-01') ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="filterTanggalAkhir" style="display:none;">
                                                <div class="form-group">
                                                    <label>Tanggal Akhir:</label>
                                                    <input type="date" name="tanggal_akhir" class="form-control" value="<?= $this->input->post('tanggal_akhir') ?: date('Y-m-t') ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>&nbsp;</label><br>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-search"></i> Tampilkan
                                                    </button>
                                                    <button type="button" class="btn btn-success" onclick="exportExcel()">
                                                        <i class="fas fa-file-excel"></i> Export Excel
                                                    </button>
                                                    <button type="button" class="btn btn-info" onclick="printReport()">
                                                        <i class="fas fa-print"></i> Print
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-list"></i> Data Perceraian</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover" id="example1">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nomor Perkara</th>
                                                    <th>Jenis Perkara</th>
                                                    <th>Nama Pihak 1</th>
                                                    <th>NIK Pihak 1</th>
                                                    <th>Pekerjaan Pihak 1</th>
                                                    <th>Nama Pihak 2</th>
                                                    <th>NIK Pihak 2</th>
                                                    <th>Pekerjaan Pihak 2</th>
                                                    <th>Tgl Putusan</th>
                                                    <th>Tgl BHT</th>
                                                    <th>Status Putusan</th>
                                                    <th>No. Akta Cerai</th>
                                                    <th>No. Seri Akta Cerai</th>
                                                    <th>Tgl Akta Cerai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (isset($datafilter) && count($datafilter) > 0): ?>
                                                    <?php $no = 1;
                                                    foreach ($datafilter as $row) : ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td><strong><?= $row->nomor_perkara ?></strong></td>
                                                            <td>
                                                                <?php if (strpos($row->jenis_perkara_nama, 'Cerai Gugat') !== false): ?>
                                                                    <span class="badge badge-danger"><?= $row->jenis_perkara_nama ?></span>
                                                                <?php elseif (strpos($row->jenis_perkara_nama, 'Cerai Talak') !== false): ?>
                                                                    <span class="badge badge-warning"><?= $row->jenis_perkara_nama ?></span>
                                                                <?php else: ?>
                                                                    <span class="badge badge-primary"><?= $row->jenis_perkara_nama ?></span>
                                                                <?php endif ?>
                                                            </td>
                                                            <td><?= $row->nama_pihak_1 ?></td>
                                                            <td><?= $row->nik_pihak_1 ?></td>
                                                            <td><?= $row->pekerjaan_pihak_1 ?></td>
                                                            <td><?= $row->nama_pihak_2 ?></td>
                                                            <td><?= $row->nik_pihak_2 ?></td>
                                                            <td><?= $row->pekerjaan_pihak_2 ?></td>
                                                            <td><?= $row->tanggal_putusan ?></td>
                                                            <td><?= $row->tanggal_bht ?></td>
                                                            <td>
                                                                <span class="badge badge-success"><?= $row->status_putusan ?></span>
                                                            </td>
                                                            <td><strong><?= $row->nomor_akta_cerai ?></strong></td>
                                                            <td><?= $row->no_seri_akta_cerai ?></td>
                                                            <td><?= $row->tgl_akta_cerai ?></td>
                                                        </tr>
                                                    <?php endforeach ?>
                                                <?php else: ?>
                                                    <tr>
                                                        <td colspan="15" class="text-center">
                                                            <div class="alert alert-info">
                                                                <i class="fas fa-info-circle"></i> Tidak ada data perceraian untuk periode ini
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endif ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
    </div>
    <!-- ./wrapper -->

    <script>
        function toggleFilter() {
            const jenisLaporan = document.getElementById('jenisLaporan').value;
            const filterBulan = document.getElementById('filterBulan');
            const filterTahun = document.getElementById('filterTahun');
            const filterTanggalMulai = document.getElementById('filterTanggalMulai');
            const filterTanggalAkhir = document.getElementById('filterTanggalAkhir');

            if (jenisLaporan === 'bulanan') {
                filterBulan.style.display = 'block';
                filterTahun.style.display = 'block';
                filterTanggalMulai.style.display = 'none';
                filterTanggalAkhir.style.display = 'none';
            } else if (jenisLaporan === 'tahunan') {
                filterBulan.style.display = 'none';
                filterTahun.style.display = 'block';
                filterTanggalMulai.style.display = 'none';
                filterTanggalAkhir.style.display = 'none';
            } else if (jenisLaporan === 'custom') {
                filterBulan.style.display = 'none';
                filterTahun.style.display = 'none';
                filterTanggalMulai.style.display = 'block';
                filterTanggalAkhir.style.display = 'block';
            }
        }

        function exportExcel() {
            const form = document.getElementById('filterForm');
            const originalAction = form.action;
            form.action = '<?= base_url() ?>index.php/Laporan_perceraian/export_excel';
            form.submit();
            form.action = originalAction;
        }

        function printReport() {
            window.print();
        }

        $(document).ready(function() {
            toggleFilter();

            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "pageLength": 25,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "order": [
                    [14, "desc"]
                ],
                "dom": 'Bfrtip',
                "buttons": [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-default'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv"></i> CSV',
                        className: 'btn btn-default'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-default'
                    }
                ]
            });
        });
    </script>

    <!-- Custom Styles -->
    <style>
        .form-group label {
            font-weight: 600;
            color: #495057;
        }

        .small-box {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .small-box:hover {
            transform: translateY(-2px);
        }

        .small-box .inner h3 {
            font-weight: bold;
        }

        .badge {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
        }

        .table th {
            background-color: #007bff !important;
            color: white !important;
        }

        .card-header {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
        }

        .btn {
            border-radius: 5px;
        }

        .filter-card {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .filter-card .card-header {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        @media print {

            .filter-card,
            .btn,
            .breadcrumb,
            .card-header .card-tools {
                display: none !important;
            }
        }
    </style>

</body>

</html>
