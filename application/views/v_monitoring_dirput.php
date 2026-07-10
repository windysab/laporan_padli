<body class="hold-transition sidebar-mini">
	<div class="wrapper">
		<div class="content-wrapper">
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1><i class="fas fa-file-upload"></i> Monitoring Dirput Anonimisasi</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active">Monitoring Dirput</li>
							</ol>
						</div>
					</div>
				</div>
			</section>

			<?php if (isset($summary)): ?>
				<section class="content">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-3 col-6">
								<div class="small-box bg-info">
									<div class="inner">
										<h3><?= number_format($summary->total_putusan) ?></h3>
										<p>Total Putusan</p>
									</div>
									<div class="icon"><i class="fas fa-gavel"></i></div>
								</div>
							</div>
							<div class="col-lg-3 col-6">
								<div class="small-box bg-success">
									<div class="inner">
										<h3><?= number_format($summary->sudah_ada_anonim) ?></h3>
										<p>Sudah Ada File Anonim</p>
									</div>
									<div class="icon"><i class="fas fa-check-circle"></i></div>
								</div>
							</div>
							<div class="col-lg-3 col-6">
								<div class="small-box bg-danger">
									<div class="inner">
										<h3><?= number_format($summary->belum_ada_anonim) ?></h3>
										<p>Belum Ada File Anonim</p>
									</div>
									<div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
								</div>
							</div>
							<div class="col-lg-3 col-6">
								<div class="small-box bg-primary">
									<div class="inner">
										<h3><?= isset($summary->upload_gagal) ? number_format($summary->upload_gagal) : 0 ?></h3>
										<p>Upload Gagal</p>
									</div>
									<div class="icon"><i class="fas fa-upload"></i></div>
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
									<h3 class="card-title"><i class="fas fa-filter"></i> Filter Monitoring</h3>
								</div>
								<div class="card-body">
									<form action="<?= base_url() ?>index.php/Monitoring_dirput" method="POST" id="filterForm">
										<div class="row">
											<div class="col-md-2">
												<div class="form-group">
													<label>Status:</label>
													<select name="mode" class="form-control">
														<option value="belum" <?= ($selected_mode === 'belum') ? 'selected' : '' ?>>Belum Ada Anonim</option>
														<option value="sudah" <?= ($selected_mode === 'sudah') ? 'selected' : '' ?>>Sudah Ada Anonim</option>
														<option value="upload_gagal" <?= ($selected_mode === 'upload_gagal') ? 'selected' : '' ?>>Upload Gagal</option>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label>Bulan Putusan:</label>
													<select name="lap_bulan" class="form-control">
														<option value="01" <?= ($selected_bulan === '01') ? 'selected' : '' ?>>Januari</option>
														<option value="02" <?= ($selected_bulan === '02') ? 'selected' : '' ?>>Februari</option>
														<option value="03" <?= ($selected_bulan === '03') ? 'selected' : '' ?>>Maret</option>
														<option value="04" <?= ($selected_bulan === '04') ? 'selected' : '' ?>>April</option>
														<option value="05" <?= ($selected_bulan === '05') ? 'selected' : '' ?>>Mei</option>
														<option value="06" <?= ($selected_bulan === '06') ? 'selected' : '' ?>>Juni</option>
														<option value="07" <?= ($selected_bulan === '07') ? 'selected' : '' ?>>Juli</option>
														<option value="08" <?= ($selected_bulan === '08') ? 'selected' : '' ?>>Agustus</option>
														<option value="09" <?= ($selected_bulan === '09') ? 'selected' : '' ?>>September</option>
														<option value="10" <?= ($selected_bulan === '10') ? 'selected' : '' ?>>Oktober</option>
														<option value="11" <?= ($selected_bulan === '11') ? 'selected' : '' ?>>November</option>
														<option value="12" <?= ($selected_bulan === '12') ? 'selected' : '' ?>>Desember</option>
													</select>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label>Tahun Putusan:</label>
													<select name="lap_tahun" class="form-control">
														<?php for ($year = 2016; $year <= date('Y') + 1; $year++): ?>
															<option value="<?= $year ?>" <?= ($selected_tahun == $year) ? 'selected' : '' ?>>
																<?= $year ?>
															</option>
														<?php endfor ?>
													</select>
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label>Jenis Perkara:</label>
													<select name="jenis_perkara" class="form-control">
														<option value="semua" <?= ($selected_jenis_perkara === 'semua') ? 'selected' : '' ?>>Semua Jenis</option>
														<?php foreach ($jenis_perkara_list as $item): ?>
															<option value="<?= $item->jenis_perkara_nama ?>" <?= ($selected_jenis_perkara === $item->jenis_perkara_nama) ? 'selected' : '' ?>>
																<?= $item->jenis_perkara_nama ?>
															</option>
														<?php endforeach ?>
													</select>
												</div>
											</div>
											<div class="col-md-3">
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

							<div class="card">
								<div class="card-header">
									<h3 class="card-title">
										<i class="fas fa-list"></i>
										<?php
										if ($selected_mode === 'upload_gagal') {
											echo 'Putusan Belum Upload Berkas Dirput';
										} else {
											echo ($selected_mode === 'sudah') ? 'Putusan Sudah Ada Dokumen Anonimisasi' : 'Putusan Belum Ada Dokumen Anonimisasi';
										}
										?>
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
													<th>Pihak 1</th>
													<th>Pihak 2</th>
													<th>Tgl Putusan</th>
													<?php if ($selected_mode === 'upload_gagal'): ?>
														<th>Tgl BHT</th>
														<th>Status Putusan</th>
														<th>Hari Sejak Putusan</th>
														<th>Keterangan</th>
													<?php elseif ($selected_mode === 'sudah'): ?>
														<th>Tgl Publish</th>
														<th>Filename</th>
														<th>Published</th>
														<th>Link</th>
													<?php else: ?>
														<th>Tgl BHT</th>
														<th>Status Putusan</th>
														<th>Hari Sejak Putusan</th>
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
															<td><?= character_limiter($row->pihak_1, 35) ?></td>
															<td><?= character_limiter($row->pihak_2, 35) ?></td>
															<td><?= $row->tanggal_putusan ?></td>
															<?php if ($selected_mode === 'upload_gagal'): ?>
																<td><?= $row->tanggal_bht ?: '-' ?></td>
																<td><?= $row->status_putusan_nama ?: '-' ?></td>
																<td><span class="badge badge-danger"><?= $row->hari_sejak_putusan ?> hari</span></td>
																<td><span class="badge badge-warning"><?= $row->keterangan ?></span></td>
															<?php elseif ($selected_mode === 'sudah'): ?>
																<td><?= $row->tanggal_publish ?: '-' ?></td>
																<td><?= character_limiter($row->filename, 45) ?></td>
																<td>
																	<span class="badge <?= ($row->published == 1) ? 'badge-success' : 'badge-warning' ?>">
																		<?= ($row->published == 1) ? 'Ya' : 'Belum' ?>
																	</span>
																</td>
																<td>
																	<?php if (!empty($row->link_dirput)): ?>
																		<a href="<?= $row->link_dirput ?>" target="_blank" class="btn btn-sm btn-info">Buka</a>
																	<?php else: ?>
																		-
																	<?php endif ?>
																</td>
															<?php else: ?>
																<td><?= $row->tanggal_bht ?: '-' ?></td>
																<td><?= $row->status_putusan_nama ?: '-' ?></td>
																<td><span class="badge badge-danger"><?= $row->hari_sejak_putusan ?> hari</span></td>
															<?php endif ?>
														</tr>
													<?php endforeach ?>
												<?php else: ?>
													<tr>
														<td colspan="<?= ($selected_mode === 'sudah' || $selected_mode === 'upload_gagal') ? 10 : 9 ?>" class="text-center">
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
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>

	<script>
		function exportExcel() {
			const form = document.getElementById('filterForm');
			const originalAction = form.action;
			form.action = '<?= base_url() ?>index.php/Monitoring_dirput/export_excel';
			form.submit();
			form.action = originalAction;
		}

		$(document).ready(function() {
			$("#example1").DataTable({
				"responsive": true,
				"lengthChange": true,
				"autoWidth": false,
				"pageLength": 25,
				"language": {
					"url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
				},
				"order": [[5, "asc"]]
			});
		});
	</script>

	<style>
		.form-group label {
			font-weight: 600;
			color: #495057;
		}

		.card-header {
			background: linear-gradient(45deg, #6f42c1, #563d7c);
			color: white;
		}

		.filter-card .card-header {
			background: linear-gradient(45deg, #007bff, #0056b3);
		}

		.table th {
			background-color: #6f42c1 !important;
			color: white !important;
		}
	</style>
</body>

</html>
