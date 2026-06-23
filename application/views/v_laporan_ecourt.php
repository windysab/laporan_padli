<?php
$nama_bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
	<!-- Content Header -->
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1><i class="fas fa-laptop mr-2"></i>Laporan E-Court vs Non E-Court</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?php echo site_url('dashboard'); ?>">Dashboard</a></li>
						<li class="breadcrumb-item active">Laporan E-Court</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="container-fluid">

			<!-- Filter Form -->
			<div class="card card-outline card-primary">
				<div class="card-header">
					<h3 class="card-title"><i class="fas fa-filter mr-1"></i> Filter Laporan</h3>
				</div>
				<div class="card-body">
					<form method="post" action="<?php echo site_url('Laporan_ecourt'); ?>" class="form-inline">
						<div class="form-group mr-3 mb-2">
							<label class="mr-2">Tahun:</label>
							<select name="lap_tahun" class="form-control">
								<?php for ($y = date('Y'); $y >= 2018; $y--): ?>
									<option value="<?php echo $y; ?>" <?php echo ($selected_tahun == $y) ? 'selected' : ''; ?>>
										<?php echo $y; ?>
									</option>
								<?php endfor; ?>
							</select>
						</div>
						<div class="form-group mr-3 mb-2">
							<label class="mr-2">Bulan:</label>
							<select name="lap_bulan" class="form-control">
								<option value="semua" <?php echo ($selected_bulan == 'semua') ? 'selected' : ''; ?>>Semua Bulan</option>
								<?php for ($m = 1; $m <= 12; $m++): ?>
									<option value="<?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>" <?php echo ($selected_bulan == str_pad($m, 2, '0', STR_PAD_LEFT)) ? 'selected' : ''; ?>>
										<?php echo $nama_bulan[$m]; ?>
									</option>
								<?php endfor; ?>
							</select>
						</div>
						<button type="submit" class="btn btn-primary mb-2 mr-2">
							<i class="fas fa-search mr-1"></i> Tampilkan
						</button>
						<button type="submit" formaction="<?php echo site_url('Laporan_ecourt/export_csv'); ?>" class="btn btn-success mb-2">
							<i class="fas fa-file-csv mr-1"></i> Export CSV
						</button>
					</form>
				</div>
			</div>

			<!-- Summary Cards -->
			<div class="row">
				<div class="col-lg-3 col-md-6">
					<div class="small-box bg-info">
						<div class="inner">
							<h3><?php echo isset($summary->total_perkara) ? $summary->total_perkara : 0; ?></h3>
							<p>Total Perkara Masuk</p>
						</div>
						<div class="icon"><i class="fas fa-gavel"></i></div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="small-box bg-success">
						<div class="inner">
							<h3><?php echo isset($summary->total_ecourt) ? $summary->total_ecourt : 0; ?></h3>
							<p>Via E-Court</p>
						</div>
						<div class="icon"><i class="fas fa-laptop"></i></div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="small-box bg-warning">
						<div class="inner">
							<h3><?php echo isset($summary->total_non_ecourt) ? $summary->total_non_ecourt : 0; ?></h3>
							<p>Non E-Court (Manual)</p>
						</div>
						<div class="icon"><i class="fas fa-file-alt"></i></div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="small-box bg-primary">
						<div class="inner">
							<h3><?php echo isset($summary->persen_ecourt) ? $summary->persen_ecourt : 0; ?>%</h3>
							<p>Persentase E-Court</p>
						</div>
						<div class="icon"><i class="fas fa-chart-pie"></i></div>
					</div>
				</div>
			</div>

			<!-- Charts Row -->
			<div class="row">
				<!-- Chart Bulanan -->
				<div class="col-lg-8">
					<div class="card card-outline card-info">
						<div class="card-header">
							<h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Perbandingan Per Bulan - <?php echo $selected_tahun; ?></h3>
						</div>
						<div class="card-body">
							<canvas id="chartBulanan" height="300"></canvas>
						</div>
					</div>
				</div>

				<!-- Chart Pie -->
				<div class="col-lg-4">
					<div class="card card-outline card-success">
						<div class="card-header">
							<h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> Komposisi <?php echo ($selected_bulan === 'semua') ? 'Tahun ' . $selected_tahun : $nama_bulan[(int)$selected_bulan] . ' ' . $selected_tahun; ?></h3>
						</div>
						<div class="card-body">
							<canvas id="chartPie" height="300"></canvas>
						</div>
					</div>
				</div>
			</div>

			<!-- Tren Tahunan -->
			<div class="row">
				<div class="col-12">
					<div class="card card-outline card-purple">
						<div class="card-header">
							<h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> Tren Adopsi E-Court Per Tahun</h3>
						</div>
						<div class="card-body">
							<canvas id="chartTren" height="120"></canvas>
						</div>
					</div>
				</div>
			</div>

			<!-- Tabel Breakdown Per Bulan -->
			<div class="row">
				<div class="col-lg-7">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title"><i class="fas fa-table mr-1"></i> Data Per Bulan - <?php echo $selected_tahun; ?></h3>
						</div>
						<div class="card-body table-responsive p-0">
							<table class="table table-hover table-striped table-sm">
								<thead class="thead-dark">
									<tr>
										<th>Bulan</th>
										<th class="text-center">Total</th>
										<th class="text-center">E-Court</th>
										<th class="text-center">Non E-Court</th>
										<th class="text-center">% E-Court</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$grand_total = 0;
									$grand_ecourt = 0;
									$grand_non = 0;
									if (!empty($breakdown_bulanan)):
										foreach ($breakdown_bulanan as $row):
											$grand_total += $row->total_perkara;
											$grand_ecourt += $row->total_ecourt;
											$grand_non += $row->total_non_ecourt;
									?>
										<tr>
											<td><?php echo $nama_bulan[$row->bulan]; ?></td>
											<td class="text-center"><strong><?php echo $row->total_perkara; ?></strong></td>
											<td class="text-center"><span class="badge badge-success"><?php echo $row->total_ecourt; ?></span></td>
											<td class="text-center"><span class="badge badge-warning"><?php echo $row->total_non_ecourt; ?></span></td>
											<td class="text-center">
												<div class="progress progress-sm" style="height: 18px;">
													<div class="progress-bar bg-success" style="width: <?php echo $row->persen_ecourt; ?>%">
														<?php echo $row->persen_ecourt; ?>%
													</div>
												</div>
											</td>
										</tr>
									<?php endforeach; endif; ?>
								</tbody>
								<tfoot class="bg-light">
									<tr>
										<th>TOTAL</th>
										<th class="text-center"><?php echo $grand_total; ?></th>
										<th class="text-center"><?php echo $grand_ecourt; ?></th>
										<th class="text-center"><?php echo $grand_non; ?></th>
										<th class="text-center">
											<?php echo ($grand_total > 0) ? round($grand_ecourt * 100 / $grand_total, 2) : 0; ?>%
										</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>

				<!-- Tabel Per Jenis Perkara -->
				<div class="col-lg-5">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title"><i class="fas fa-list mr-1"></i> Per Jenis Perkara</h3>
						</div>
						<div class="card-body table-responsive p-0">
							<table class="table table-hover table-striped table-sm">
								<thead class="thead-dark">
									<tr>
										<th>Jenis Perkara</th>
										<th class="text-center">Total</th>
										<th class="text-center">E-Court</th>
										<th class="text-center">% E-Court</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($breakdown_jenis)):
										foreach ($breakdown_jenis as $row): ?>
										<tr>
											<td><?php echo htmlspecialchars($row->jenis_perkara_nama); ?></td>
											<td class="text-center"><?php echo $row->total_perkara; ?></td>
											<td class="text-center"><span class="badge badge-success"><?php echo $row->total_ecourt; ?></span></td>
											<td class="text-center"><?php echo $row->persen_ecourt; ?>%</td>
										</tr>
									<?php endforeach; endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

		</div>
	</section>
</div>


<!-- Chart data (rendered after footer loads jQuery + Chart.js) -->
<script>
window.addEventListener('load', function() {
	// Data untuk chart bulanan
	var dataBulanan = <?php echo json_encode($breakdown_bulanan); ?>;
	var labels = [];
	var ecourtData = [];
	var nonEcourtData = [];
	var namaBulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

	if (dataBulanan && dataBulanan.length > 0) {
		for (var i = 0; i < dataBulanan.length; i++) {
			labels.push(namaBulan[dataBulanan[i].bulan]);
			ecourtData.push(parseInt(dataBulanan[i].total_ecourt));
			nonEcourtData.push(parseInt(dataBulanan[i].total_non_ecourt));
		}
	}

	// Chart Bar Bulanan
	var ctxBulanan = document.getElementById('chartBulanan');
	if (ctxBulanan && typeof Chart !== 'undefined') {
		new Chart(ctxBulanan.getContext('2d'), {
			type: 'bar',
			data: {
				labels: labels,
				datasets: [{
					label: 'E-Court',
					data: ecourtData,
					backgroundColor: 'rgba(40, 167, 69, 0.8)',
					borderColor: 'rgba(40, 167, 69, 1)',
					borderWidth: 1
				}, {
					label: 'Non E-Court',
					data: nonEcourtData,
					backgroundColor: 'rgba(255, 193, 7, 0.8)',
					borderColor: 'rgba(255, 193, 7, 1)',
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				scales: {
					yAxes: [{
						ticks: { beginAtZero: true }
					}]
				},
				tooltips: {
					mode: 'index',
					intersect: false
				}
			}
		});
	}

	// Chart Pie
	var totalEcourt = <?php echo isset($summary->total_ecourt) ? (int)$summary->total_ecourt : 0; ?>;
	var totalNon = <?php echo isset($summary->total_non_ecourt) ? (int)$summary->total_non_ecourt : 0; ?>;

	var ctxPie = document.getElementById('chartPie');
	if (ctxPie && typeof Chart !== 'undefined') {
		new Chart(ctxPie.getContext('2d'), {
			type: 'doughnut',
			data: {
				labels: ['E-Court', 'Non E-Court'],
				datasets: [{
					data: [totalEcourt, totalNon],
					backgroundColor: ['rgba(40, 167, 69, 0.8)', 'rgba(255, 193, 7, 0.8)'],
					borderColor: ['rgba(40, 167, 69, 1)', 'rgba(255, 193, 7, 1)'],
					borderWidth: 2
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				legend: { position: 'bottom' }
			}
		});
	}

	// Chart Tren Tahunan
	var dataTren = <?php echo json_encode($tren_tahunan); ?>;
	var trenLabels = [];
	var trenEcourt = [];
	var trenNon = [];
	var trenPersen = [];

	if (dataTren && dataTren.length > 0) {
		for (var i = 0; i < dataTren.length; i++) {
			trenLabels.push(dataTren[i].tahun);
			trenEcourt.push(parseInt(dataTren[i].total_ecourt));
			trenNon.push(parseInt(dataTren[i].total_non_ecourt));
			trenPersen.push(parseFloat(dataTren[i].persen_ecourt));
		}
	}

	var ctxTren = document.getElementById('chartTren');
	if (ctxTren && typeof Chart !== 'undefined') {
		new Chart(ctxTren.getContext('2d'), {
			type: 'bar',
			data: {
				labels: trenLabels,
				datasets: [{
					label: 'E-Court',
					data: trenEcourt,
					backgroundColor: 'rgba(40, 167, 69, 0.7)',
					borderColor: 'rgba(40, 167, 69, 1)',
					borderWidth: 1,
					yAxisID: 'y-axis-1'
				}, {
					label: 'Non E-Court',
					data: trenNon,
					backgroundColor: 'rgba(255, 193, 7, 0.7)',
					borderColor: 'rgba(255, 193, 7, 1)',
					borderWidth: 1,
					yAxisID: 'y-axis-1'
				}, {
					label: '% E-Court',
					data: trenPersen,
					type: 'line',
					borderColor: 'rgba(0, 123, 255, 1)',
					backgroundColor: 'rgba(0, 123, 255, 0.1)',
					borderWidth: 3,
					pointRadius: 5,
					fill: false,
					yAxisID: 'y-axis-2'
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				scales: {
					yAxes: [{
						id: 'y-axis-1',
						type: 'linear',
						position: 'left',
						ticks: { beginAtZero: true }
					}, {
						id: 'y-axis-2',
						type: 'linear',
						position: 'right',
						ticks: { beginAtZero: true, max: 100, callback: function(v) { return v + '%'; } },
						gridLines: { drawOnChartArea: false }
					}]
				}
			}
		});
	}
});
</script>
