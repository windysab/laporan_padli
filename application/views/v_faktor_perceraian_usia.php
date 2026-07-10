<!-- Content Wrapper -->
<div class="content-wrapper">
	<!-- Content Header -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Faktor Perceraian Berdasarkan Usia</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?= site_url('Dashboard') ?>">Home</a></li>
						<li class="breadcrumb-item"><a href="#">Data Perceraian</a></li>
						<li class="breadcrumb-item active">Faktor Perceraian Berdasarkan Usia</li>
					</ol>
				</div>
			</div>
		</div>
	</div>

	<!-- Main content -->
	<section class="content">
		<div class="container-fluid">

			<!-- Filter Form -->
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-filter"></i>
								Filter Laporan
							</h3>
						</div>
						<form method="post" action="<?= site_url('Faktor_perceraian_usia') ?>" id="filterForm">
							<div class="card-body">
								<div class="row">
									<!-- Tahun -->
									<div class="col-md-4">
										<div class="form-group">
											<label>Tahun</label>
											<select name="lap_tahun" class="form-control" id="lap_tahun">
												<?php
												$selected_tahun = $this->input->post('lap_tahun') ?: date('Y');
												for ($i = date('Y'); $i >= 2020; $i--):
												?>
													<option value="<?= $i ?>" <?= ($selected_tahun == $i) ? 'selected' : '' ?>><?= $i ?></option>
												<?php endfor ?>
											</select>
										</div>
									</div>

									<!-- Wilayah -->
									<div class="col-md-4">
										<div class="form-group">
											<label>Wilayah</label>
											<select name="wilayah" class="form-control" id="wilayah">
												<?php $selected_wilayah = $this->input->post('wilayah') ?: 'Amuntai' ?>
												
												<option value="Hulu Sungai Utara" <?= ($selected_wilayah == 'Hulu Sungai Utara') ? 'selected' : '' ?>>Hulu Sungai Utara</option>
												<option value="Balangan" <?= ($selected_wilayah == 'Balangan') ? 'selected' : '' ?>>Balangan</option>
												<option value="Semua Wilayah" <?= ($selected_wilayah == 'Semua Wilayah') ? 'selected' : '' ?>>Semua Wilayah</option>
											</select>
										</div>
									</div>

									<!-- Button -->
									<div class="col-md-4">
										<div class="form-group">
											<label>&nbsp;</label>
											<div class="d-block">
												<button type="submit" class="btn btn-primary">
													<i class="fas fa-search"></i> Tampilkan Data
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
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- Summary Cards -->
			<?php
			$tot_16_19 = 0;
			$tot_20_25 = 0;
			$tot_26_30 = 0;
			$tot_31_35 = 0;
			$tot_36 = 0;
			$tot_all = 0;

			if (!empty($datafilter)):
				foreach ($datafilter as $row):
					if ($row->faktor != 'Jumlah' && $row->faktor != 'TOTAL'):
						$tot_16_19 += $row->usia_16_19;
						$tot_20_25 += $row->usia_20_25;
						$tot_26_30 += $row->usia_26_30;
						$tot_31_35 += $row->usia_31_35;
						$tot_36 += $row->usia_36;
					endif;
				endforeach;
			endif;
			$tot_all = $tot_16_19 + $tot_20_25 + $tot_26_30 + $tot_31_35 + $tot_36 ?>

			<div class="row">
				<div class="col-lg-2 col-6">
					<div class="small-box bg-info">
						<div class="inner">
							<h3><?= number_format($tot_all) ?></h3>
							<p>Total Kasus</p>
						</div>
						<div class="icon"><i class="fas fa-female"></i></div>
					</div>
				</div>
				<div class="col-lg-2 col-6">
					<div class="small-box bg-danger">
						<div class="inner">
							<h3><?= number_format($tot_16_19) ?></h3>
							<p>Usia 16-19</p>
						</div>
						<div class="icon"><i class="fas fa-child"></i></div>
					</div>
				</div>
				<div class="col-lg-2 col-6">
					<div class="small-box bg-warning">
						<div class="inner">
							<h3><?= number_format($tot_20_25) ?></h3>
							<p>Usia 20-25</p>
						</div>
						<div class="icon"><i class="fas fa-user"></i></div>
					</div>
				</div>
				<div class="col-lg-2 col-6">
					<div class="small-box bg-success">
						<div class="inner">
							<h3><?= number_format($tot_26_30) ?></h3>
							<p>Usia 26-30</p>
						</div>
						<div class="icon"><i class="fas fa-user"></i></div>
					</div>
				</div>
				<div class="col-lg-2 col-6">
					<div class="small-box bg-primary">
						<div class="inner">
							<h3><?= number_format($tot_31_35) ?></h3>
							<p>Usia 31-35</p>
						</div>
						<div class="icon"><i class="fas fa-user"></i></div>
					</div>
				</div>
				<div class="col-lg-2 col-6">
					<div class="small-box bg-secondary">
						<div class="inner">
							<h3><?= number_format($tot_36) ?></h3>
							<p>Usia 36+</p>
						</div>
						<div class="icon"><i class="fas fa-user"></i></div>
					</div>
				</div>
			</div>

			<!-- Data Table -->
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-table"></i>
								Faktor Penyebab Terjadinya Perceraian (Perempuan)
								- Tahun <?= $selected_tahun ?> - <?= $selected_wilayah ?>
							</h3>
							<div class="card-tools">
								<small class="text-muted">Sumber data: Pengadilan Agama Amuntai</small>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table id="dataTable" class="table table-bordered table-striped">
									<thead>
										<tr class="bg-primary">
											<th class="text-center" style="width: 50px;">No</th>
											<th>Faktor Penyebab Terjadinya Perceraian</th>
											<th class="text-center">Usia 16-19</th>
											<th class="text-center">Usia 20-25</th>
											<th class="text-center">Usia 26-30</th>
											<th class="text-center">Usia 31-35</th>
											<th class="text-center">Usia 36+</th>
											<th class="text-center">Jumlah</th>
										</tr>
									</thead>
									<tbody>
										<?php if (!empty($datafilter)): ?>
											<?php
											$no = 1;
											foreach ($datafilter as $row):
												if ($row->faktor != 'Jumlah' && $row->faktor != 'TOTAL'):
													$row_total = $row->usia_16_19 + $row->usia_20_25 + $row->usia_26_30 + $row->usia_31_35 + $row->usia_36 ?>
													<tr>
														<td class="text-center"><?= $no++ ?></td>
														<td><?= $row->faktor ?></td>
														<td class="text-center"><?= number_format($row->usia_16_19) ?></td>
														<td class="text-center"><?= number_format($row->usia_20_25) ?></td>
														<td class="text-center"><?= number_format($row->usia_26_30) ?></td>
														<td class="text-center"><?= number_format($row->usia_31_35) ?></td>
														<td class="text-center"><?= number_format($row->usia_36) ?></td>
														<td class="text-center font-weight-bold"><?= number_format($row_total) ?></td>
													</tr>
											<?php endif;
											endforeach ?>
										<?php else: ?>
											<tr>
												<td colspan="8" class="text-center">Tidak ada data</td>
											</tr>
										<?php endif ?>
									</tbody>
									<tfoot>
										<tr class="bg-light font-weight-bold">
											<th colspan="2" class="text-center">Jumlah</th>
											<th class="text-center"><?= number_format($tot_16_19) ?></th>
											<th class="text-center"><?= number_format($tot_20_25) ?></th>
											<th class="text-center"><?= number_format($tot_26_30) ?></th>
											<th class="text-center"><?= number_format($tot_31_35) ?></th>
											<th class="text-center"><?= number_format($tot_36) ?></th>
											<th class="text-center"><?= number_format($tot_all) ?></th>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Charts -->
			<div class="row">
				<div class="col-md-6">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-chart-bar"></i>
								Distribusi Usia Saat Perceraian
							</h3>
						</div>
						<div class="card-body">
							<div class="chart-container">
								<canvas id="ageChart"></canvas>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-chart-pie"></i>
								Komposisi Faktor Perceraian
							</h3>
						</div>
						<div class="card-body">
							<div class="chart-container">
								<canvas id="faktorPieChart"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Stacked Bar Chart -->
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-chart-bar"></i>
								Faktor Perceraian per Rentang Usia
							</h3>
						</div>
						<div class="card-body">
							<div class="chart-container">
								<canvas id="stackedChart"></canvas>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</section>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	document.addEventListener('DOMContentLoaded', function() {

		// ========== DataTable ==========
		if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined' && $('#dataTable').length) {
			$('#dataTable').DataTable({
				"responsive": true,
				"lengthChange": false,
				"autoWidth": false,
				"paging": true,
				"searching": true,
				"ordering": true,
				"info": true,
				"pageLength": 25,
				"language": {
					"search": "Cari:",
					"lengthMenu": "Tampilkan _MENU_ data per halaman",
					"zeroRecords": "Data tidak ditemukan",
					"info": "Menampilkan halaman _PAGE_ dari _PAGES_",
					"infoEmpty": "Tidak ada data tersedia",
					"infoFiltered": "(difilter dari _MAX_ total data)",
					"paginate": {
						"first": "Pertama",
						"last": "Terakhir",
						"next": "Selanjutnya",
						"previous": "Sebelumnya"
					}
				}
			});
		}

		// ========== Chart Data from PHP ==========
		const ageLabels = ['16-19', '20-25', '26-30', '31-35', '36+'];
		const ageData = [<?= $tot_16_19 ?>, <?= $tot_20_25 ?>, <?= $tot_26_30 ?>, <?= $tot_31_35 ?>, <?= $tot_36 ?>];

		<?php
		$faktor_labels = array();
		$faktor_totals = array();
		if (!empty($datafilter)):
			foreach ($datafilter as $row):
				if ($row->faktor != 'Jumlah' && $row->faktor != 'TOTAL'):
					$faktor_labels[] = "'" . addslashes($row->faktor) . "'";
					$row_total = $row->usia_16_19 + $row->usia_20_25 + $row->usia_26_30 + $row->usia_31_35 + $row->usia_36;
					$faktor_totals[] = $row_total;
				endif;
			endforeach;
		endif ?>

		const faktorLabels = [<?= implode(',', $faktor_labels) ?>];
		const faktorTotals = [<?= implode(',', $faktor_totals) ?>];

		// ========== Chart 1: Bar Chart Distribusi Usia ==========
		setTimeout(function() {
			const ctx1 = document.getElementById('ageChart');
			if (ctx1) {
				new Chart(ctx1, {
					type: 'bar',
					data: {
						labels: ageLabels,
						datasets: [{
							label: 'Jumlah Perempuan',
							data: ageData,
							backgroundColor: ['#dc3545', '#ffc107', '#28a745', '#007bff', '#6c757d'],
							borderColor: ['#c82333', '#e0a800', '#1e7e34', '#0056b3', '#5a6268'],
							borderWidth: 1
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: { display: false }
						},
						scales: {
							y: {
								beginAtZero: true,
								title: {
									display: true,
									text: 'Jumlah Kasus'
								}
							}
						}
					}
				});
			}
		}, 100);

		// ========== Chart 2: Pie Chart Komposisi Faktor ==========
		setTimeout(function() {
			const ctx2 = document.getElementById('faktorPieChart');
			if (ctx2 && faktorLabels.length > 0) {
				const colors = [
					'#dc3545', '#ffc107', '#28a745', '#007bff', '#6c757d',
					'#17a2b8', '#e83e8c', '#20c997', '#6610f2', '#fd7e14'
				];
				new Chart(ctx2, {
					type: 'pie',
					data: {
						labels: faktorLabels,
						datasets: [{
							data: faktorTotals,
							backgroundColor: colors.slice(0, faktorLabels.length),
							borderWidth: 1
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						plugins: {
							legend: {
								position: 'bottom',
								labels: { boxWidth: 12 }
							}
						}
					}
				});
			}
		}, 200);

		// ========== Chart 3: Stacked Bar ==========
		setTimeout(function() {
			const ctx3 = document.getElementById('stackedChart');
			if (ctx3 && faktorLabels.length > 0) {

				<?php
				$stack_16_19 = array();
				$stack_20_25 = array();
				$stack_26_30 = array();
				$stack_31_35 = array();
				$stack_36 = array();
				if (!empty($datafilter)):
					foreach ($datafilter as $row):
						if ($row->faktor != 'Jumlah' && $row->faktor != 'TOTAL'):
							$stack_16_19[] = $row->usia_16_19;
							$stack_20_25[] = $row->usia_20_25;
							$stack_26_30[] = $row->usia_26_30;
							$stack_31_35[] = $row->usia_31_35;
							$stack_36[] = $row->usia_36;
						endif;
					endforeach;
				endif ?>

				new Chart(ctx3, {
					type: 'bar',
					data: {
						labels: faktorLabels,
						datasets: [{
							label: 'Usia 16-19',
							data: [<?= implode(',', $stack_16_19) ?>],
							backgroundColor: '#dc3545'
						}, {
							label: 'Usia 20-25',
							data: [<?= implode(',', $stack_20_25) ?>],
							backgroundColor: '#ffc107'
						}, {
							label: 'Usia 26-30',
							data: [<?= implode(',', $stack_26_30) ?>],
							backgroundColor: '#28a745'
						}, {
							label: 'Usia 31-35',
							data: [<?= implode(',', $stack_31_35) ?>],
							backgroundColor: '#007bff'
						}, {
							label: 'Usia 36+',
							data: [<?= implode(',', $stack_36) ?>],
							backgroundColor: '#6c757d'
						}]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						scales: {
							x: {
								stacked: true
							},
							y: {
								stacked: true,
								beginAtZero: true,
								title: {
									display: true,
									text: 'Jumlah Kasus'
								}
							}
						},
						plugins: {
							legend: {
								position: 'bottom'
							},
							tooltip: {
								mode: 'index',
								intersect: false
							}
						}
					}
				});
			}
		}, 300);
	});

	// ========== Utility Functions ==========
	function exportExcel() {
		alert('Fitur export Excel sedang dalam pengembangan');
	}

	function printReport() {
		window.print();
	}
</script>

<style>
	.chart-container {
		position: relative;
		height: 400px;
		width: 100%;
		margin-bottom: 20px;
	}

	#ageChart,
	#faktorPieChart,
	#stackedChart {
		max-width: 100% !important;
		max-height: 400px !important;
		width: 100% !important;
		height: 400px !important;
	}

	@media (max-width: 768px) {
		.chart-container { height: 300px; }
		#ageChart, #faktorPieChart, #stackedChart { height: 300px !important; }
	}

	@media print {
		.sidebar, .main-header, .content-header .breadcrumb,
		.btn, .content-wrapper .content-header, .main-footer {
			display: none !important;
		}
		.content-wrapper { margin-left: 0 !important; }
		.card { border: none !important; box-shadow: none !important; }
		table { font-size: 12px !important; }
		.small-box { display: none !important; }
		canvas { page-break-inside: avoid; max-height: 250px !important; }
	}

	.table th {
		background-color: #007bff !important;
		color: white !important;
	}

	.bg-light th {
		background-color: #f8f9fa !important;
		color: #495057 !important;
	}

	.small-box {
		border-radius: 0.25rem;
		box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
		display: block;
		margin-bottom: 20px;
		position: relative;
	}
	.small-box>.inner { padding: 10px; }
	.small-box .icon { color: rgba(0,0,0,.15); z-index: 0; }
	.small-box .icon>i {
		font-size: 70px;
		position: absolute;
		right: 15px;
		top: 15px;
		transition: transform .3s linear;
	}
	.small-box:hover .icon>i { transform: scale(1.1); }
</style>
