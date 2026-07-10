<body class="hold-transition sidebar-mini">
	<div class="wrapper">
		<div class="content-wrapper">
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1>
								<i class="fas fa-clipboard-check"></i>
								<?php
								if ($selected_mode === 'cek_nomor') {
									echo 'Cek Nomor Akta Cerai';
								} else {
									echo ($selected_mode === 'terlambat') ? 'Perkara Akta Cerai Terlambat' : 'Validasi Data Akta Cerai';
								}
								?>
							</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active">Validasi Akta Cerai</li>
							</ol>
						</div>
					</div>
				</div>
			</section>

			<?php if ($selected_mode !== 'cek_nomor' && isset($summary)): ?>
				<section class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-3 col-6">
								<div class="small-box bg-info">
									<div class="inner">
										<h3><?= isset($summary->total_bht) ? number_format($summary->total_bht) : 0 ?></h3>
										<p>Perkara Cerai BHT</p>
									</div>
									<div class="icon"><i class="fas fa-gavel"></i></div>
								</div>
							</div>
							<div class="col-lg-3 col-6">
								<div class="small-box bg-danger">
									<div class="inner">
										<h3><?= isset($summary->belum_lengkap) ? number_format($summary->belum_lengkap) : 0 ?></h3>
										<p>Data Akta Belum Lengkap</p>
									</div>
									<div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
								</div>
							</div>
							<div class="col-lg-3 col-6">
								<div class="small-box bg-warning">
									<div class="inner">
										<h3><?= isset($summary->terlambat) ? number_format($summary->terlambat) : 0 ?></h3>
										<p>Lewat Batas Hari</p>
									</div>
									<div class="icon"><i class="fas fa-clock"></i></div>
								</div>
							</div>
							<div class="col-lg-3 col-6">
								<div class="small-box bg-success">
									<div class="inner">
										<h3><?= isset($summary->rata_hari) ? number_format($summary->rata_hari, 1) : 0 ?></h3>
										<p>Rata-rata Hari BHT ke Akta</p>
									</div>
									<div class="icon"><i class="fas fa-chart-line"></i></div>
								</div>
							</div>
						</div>
					</div>
				</section>
			<?php endif ?>

			<section class="content">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div class="card filter-card">
								<div class="card-header">
									<h3 class="card-title"><i class="fas fa-filter"></i> Filter Validasi</h3>
								</div>
								<div class="card-body">
									<form action="<?= base_url() ?>index.php/Validasi_akta_cerai?mode=<?= $selected_mode ?>" method="POST" id="filterForm">
										<input type="hidden" name="mode" id="modeInput" value="<?= $selected_mode ?>">
										<div class="row">
											<div class="col-md-3">
												<div class="form-group">
													<label>Jenis Validasi:</label>
													<select name="mode_select" class="form-control" id="modeSelect" onchange="changeMode()">
														<option value="belum_lengkap" <?= ($selected_mode === 'belum_lengkap') ? 'selected' : '' ?>>Data Akta Belum Lengkap</option>
														<option value="terlambat" <?= ($selected_mode === 'terlambat') ? 'selected' : '' ?>>Akta Cerai Terlambat</option>
														<option value="cek_nomor" <?= ($selected_mode === 'cek_nomor') ? 'selected' : '' ?>>Cek Nomor Akta</option>
													</select>
												</div>
											</div>
											<div class="col-md-2 filter-periodik">
												<div class="form-group">
													<label>Tahun BHT:</label>
													<select name="lap_tahun" class="form-control">
														<?php for ($year = 2016; $year <= date('Y') + 1; $year++): ?>
															<option value="<?= $year ?>" <?= (isset($selected_tahun) && $selected_tahun == $year) ? 'selected' : '' ?>>
																<?= $year ?>
															</option>
														<?php endfor ?>
													</select>
												</div>
											</div>
											<div class="col-md-3 filter-periodik">
												<div class="form-group">
													<label>Jenis Perkara:</label>
													<select name="jenis_perkara" class="form-control">
														<option value="semua" <?= ($selected_jenis_perkara === 'semua') ? 'selected' : '' ?>>Semua Jenis</option>
														<?php if (isset($jenis_perkara_list)): ?>
															<?php foreach ($jenis_perkara_list as $item): ?>
																<option value="<?= $item->jenis_perkara_nama ?>" <?= ($selected_jenis_perkara === $item->jenis_perkara_nama) ? 'selected' : '' ?>>
																	<?= $item->jenis_perkara_nama ?>
																</option>
															<?php endforeach ?>
														<?php endif ?>
													</select>
												</div>
											</div>
											<div class="col-md-2" id="batasHariGroup">
												<div class="form-group">
													<label>Batas Hari:</label>
													<input type="number" min="1" name="batas_hari" class="form-control" value="<?= $selected_batas_hari ?>">
												</div>
											</div>
											<div class="col-md-5" id="nomorAktaGroup">
												<div class="form-group">
													<label>Nomor Akta Cerai:</label>
													<input type="text" name="nomor_akta" class="form-control" value="<?= isset($selected_nomor_akta) ? $selected_nomor_akta : '' ?>" placeholder="Masukkan nomor akta cerai">
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label>&nbsp;</label><br>
													<button type="submit" class="btn btn-primary">
														<i class="fas fa-search"></i> Tampilkan
													</button>
													<button type="button" class="btn btn-success" onclick="exportExcel()">
														<i class="fas fa-file-excel"></i> Export
													</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>

							<?php if ($selected_mode === 'cek_nomor'): ?>
								<div class="card">
									<div class="card-header">
										<h3 class="card-title"><i class="fas fa-search"></i> Hasil Validasi Nomor Akta</h3>
									</div>
									<div class="card-body">
										<?php if (!empty($selected_nomor_akta) && isset($hasil_cek) && $hasil_cek): ?>
											<div class="alert alert-success">
												<h5><i class="fas fa-check-circle"></i> Nomor Akta Valid / Asli</h5>
												Nomor akta cerai ini ditemukan pada database SIPP.
											</div>
											<div class="table-responsive">
												<table class="table table-bordered table-striped">
													<tbody>
														<tr>
															<th style="width: 220px;">Nomor Akta Cerai</th>
															<td><strong><?= $hasil_cek->nomor_akta_cerai ?></strong></td>
														</tr>
														<tr>
															<th>No Seri Akta Cerai</th>
															<td><?= $hasil_cek->no_seri_akta_cerai ?: '-' ?></td>
														</tr>
														<tr>
															<th>Tanggal Akta Cerai</th>
															<td><?= $hasil_cek->tgl_akta_cerai ?: '-' ?></td>
														</tr>
														<tr>
															<th>Nomor Perkara</th>
															<td><?= $hasil_cek->nomor_perkara ?></td>
														</tr>
														<tr>
															<th>Nama Pihak</th>
															<td><?= $hasil_cek->penggugat ?> <strong>vs</strong> <?= $hasil_cek->tergugat ?></td>
														</tr>
														<tr>
															<th>Jenis Perkara</th>
															<td><?= $hasil_cek->jenis_perkara_nama ?></td>
														</tr>
														<tr>
															<th>Tanggal Putusan</th>
															<td><?= $hasil_cek->tanggal_putusan ?: '-' ?></td>
														</tr>
														<tr>
															<th>Tanggal BHT</th>
															<td><?= $hasil_cek->tanggal_bht ?: '-' ?></td>
														</tr>
													</tbody>
												</table>
											</div>
										<?php elseif (!empty($selected_nomor_akta)): ?>
											<div class="alert alert-danger">
												<h5><i class="fas fa-times-circle"></i> Nomor Akta Tidak Ditemukan</h5>
												Nomor akta cerai <strong><?= $selected_nomor_akta ?></strong> tidak ditemukan pada database SIPP.
											</div>
										<?php else: ?>
											<div class="alert alert-info mb-0">
												<i class="fas fa-info-circle"></i> Masukkan nomor akta cerai untuk melakukan validasi.
											</div>
										<?php endif ?>
									</div>
								</div>
							<?php else: ?>
							<div class="card">
								<div class="card-header">
									<h3 class="card-title">
										<i class="fas fa-list"></i>
										<?= ($selected_mode === 'terlambat') ? 'Daftar Perkara Terlambat' : 'Daftar Data Akta Belum Lengkap' ?>
									</h3>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-bordered table-striped table-hover" id="example1">
											<thead>
												<tr>
													<th>No</th>
													<th>Nomor Perkara</th>
													<th>Jenis Perkara</th>
													<th>Penggugat</th>
													<th>Tergugat</th>
													<th>Tgl Putusan</th>
													<th>Tgl BHT</th>
													<?php if ($selected_mode === 'terlambat'): ?>
														<th>Tgl Akta</th>
														<th>Selisih Hari</th>
														<th>Status</th>
													<?php else: ?>
														<th>Nomor Akta</th>
														<th>No Seri</th>
														<th>Tgl Akta</th>
														<th>Catatan</th>
													<?php endif ?>
												</tr>
											</thead>
											<tbody>
												<?php if (isset($datafilter) && count($datafilter) > 0): ?>
													<?php $no = 1; foreach ($datafilter as $row): ?>
														<tr>
															<td><?= $no++ ?></td>
															<td><strong><?= $row->nomor_perkara ?></strong></td>
															<td><?= $row->jenis_perkara_nama ?></td>
															<td><?= character_limiter($row->penggugat, 35) ?></td>
															<td><?= character_limiter($row->tergugat, 35) ?></td>
															<td><?= $row->tanggal_putusan ?: '-' ?></td>
															<td><?= $row->tanggal_bht ?: '-' ?></td>
															<?php if ($selected_mode === 'terlambat'): ?>
																<td><?= $row->tgl_akta_cerai ?: '-' ?></td>
																<td><span class="badge badge-danger"><?= $row->selisih_hari ?> hari</span></td>
																<td><?= $row->status_keterlambatan ?></td>
															<?php else: ?>
																<td><?= $row->nomor_akta_cerai ?: '-' ?></td>
																<td><?= $row->no_seri_akta_cerai ?: '-' ?></td>
																<td><?= $row->tgl_akta_cerai ?: '-' ?></td>
																<td><span class="badge badge-warning"><?= $row->catatan_validasi ?: 'Perlu dicek' ?></span></td>
															<?php endif ?>
														</tr>
													<?php endforeach ?>
												<?php else: ?>
													<tr>
														<td colspan="<?= ($selected_mode === 'terlambat') ? 10 : 11 ?>" class="text-center">
															<div class="alert alert-info mb-0">
																<i class="fas fa-info-circle"></i> Tidak ada data untuk filter ini
															</div>
														</td>
													</tr>
												<?php endif ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<?php endif ?>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>

	<script>
		function changeMode() {
			const mode = document.getElementById('modeSelect').value;
			document.getElementById('modeInput').value = mode;
			document.getElementById('filterForm').action = '<?= base_url() ?>index.php/Validasi_akta_cerai?mode=' + mode;
			toggleBatasHari();
		}

		function toggleBatasHari() {
			const mode = document.getElementById('modeSelect').value;
			document.getElementById('batasHariGroup').style.display = mode === 'terlambat' ? 'block' : 'none';
			document.getElementById('nomorAktaGroup').style.display = mode === 'cek_nomor' ? 'block' : 'none';

			$('.filter-periodik').each(function() {
				$(this).toggle(mode !== 'cek_nomor');
			});
		}

		function exportExcel() {
			changeMode();
			const form = document.getElementById('filterForm');
			const originalAction = form.action;
			form.action = '<?= base_url() ?>index.php/Validasi_akta_cerai/export_excel';
			form.submit();
			form.action = originalAction;
		}

		$(document).ready(function() {
			toggleBatasHari();
			if ($("#example1").length) {
				$("#example1").DataTable({
					"responsive": true,
					"lengthChange": true,
					"autoWidth": false,
					"pageLength": 25,
					"language": {
						"url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
					},
					"order": [[0, "asc"]]
				});
			}
		});
	</script>

	<style>
		.form-group label {
			font-weight: 600;
			color: #495057;
		}

		.card-header {
			background: linear-gradient(45deg, #17a2b8, #117a8b);
			color: white;
		}

		.filter-card .card-header {
			background: linear-gradient(45deg, #007bff, #0056b3);
		}

		.table th {
			background-color: #17a2b8 !important;
			color: white !important;
		}

		.badge {
			font-size: 0.8rem;
			white-space: normal;
			text-align: left;
		}
	</style>
</body>

</html>
