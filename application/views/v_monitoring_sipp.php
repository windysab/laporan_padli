<?php
$nama_bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] ?>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
<div class="content-wrapper">

<!-- Header -->
<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1><i class="fas fa-desktop"></i> Monitoring SIPP</h1>
			</div>
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="<?= site_url('Dashboard') ?>">Home</a></li>
					<li class="breadcrumb-item active">Monitoring SIPP</li>
				</ol>
			</div>
		</div>
	</div>
</section>

<!-- Filter Bar -->
<section class="content">
<div class="container-fluid">
	<div class="card card-outline card-primary mb-3">
		<div class="card-body py-2">
			<form method="GET" action="<?= site_url('Monitoring_sipp') ?>" class="form-inline" id="filterForm">
				<input type="hidden" name="tab" value="<?= $active_tab ?>" id="hiddenTab">
				<label class="mr-2">Wilayah:</label>
				<select name="wilayah" class="form-control form-control-sm mr-3">
					<option value="Semua" <?= ($selected_wilayah === 'Semua') ? 'selected' : '' ?>>Semua</option>
					<option value="HSU" <?= ($selected_wilayah === 'HSU') ? 'selected' : '' ?>>HSU</option>
					<option value="Balangan" <?= ($selected_wilayah === 'Balangan') ? 'selected' : '' ?>>Balangan</option>
				</select>
				<label class="mr-2">Tahun:</label>
				<select name="tahun" class="form-control form-control-sm mr-3">
					<?php for ($y = 2016; $y <= date('Y') + 1; $y++): ?>
						<option value="<?= $y ?>" <?= ($selected_tahun == $y) ? 'selected' : '' ?>><?= $y ?></option>
					<?php endfor ?>
				</select>
				<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-sync"></i> Refresh</button>
			</form>
		</div>
	</div>

	<!-- Tabs Navigation -->
	<ul class="nav nav-tabs" id="monitoringTabs">
		<li class="nav-item">
			<a class="nav-link <?= ($active_tab === 'dashboard') ? 'active' : '' ?>" href="#" onclick="switchTab('dashboard')">
				<i class="fas fa-tachometer-alt"></i> Dashboard Harian
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link <?= ($active_tab === 'aging') ? 'active' : '' ?>" href="#" onclick="switchTab('aging')">
				<i class="fas fa-hourglass-half"></i> Aging Report
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link <?= ($active_tab === 'minutasi') ? 'active' : '' ?>" href="#" onclick="switchTab('minutasi')">
				<i class="fas fa-tasks"></i> Monitoring Minutasi
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link <?= ($active_tab === 'kinerja') ? 'active' : '' ?>" href="#" onclick="switchTab('kinerja')">
				<i class="fas fa-chart-line"></i> Kinerja
			</a>
		</li>
	</ul>

	<!-- ============================================================ -->
	<!-- TAB 1: DASHBOARD HARIAN -->
	<!-- ============================================================ -->
	<div class="tab-content mt-3" id="tabContent">
	<div class="tab-pane <?= ($active_tab === 'dashboard') ? 'show active' : '' ?>" id="tab-dashboard">

		<!-- Summary Hari Ini -->
		<div class="row">
			<div class="col-lg-3 col-6">
				<div class="small-box bg-info">
					<div class="inner">
						<h3><?= number_format($dashboard_hari_ini->masuk_hari_ini) ?></h3>
						<p>Perkara Masuk Hari Ini</p>
					</div>
					<div class="icon"><i class="fas fa-inbox"></i></div>
				</div>
			</div>
			<div class="col-lg-3 col-6">
				<div class="small-box bg-success">
					<div class="inner">
						<h3><?= number_format($dashboard_hari_ini->putus_hari_ini) ?></h3>
						<p>Putusan Hari Ini</p>
					</div>
					<div class="icon"><i class="fas fa-gavel"></i></div>
				</div>
			</div>
			<div class="col-lg-3 col-6">
				<div class="small-box bg-warning">
					<div class="inner">
						<h3><?= number_format($dashboard_hari_ini->akta_cerai_hari_ini) ?></h3>
						<p>Akta Cerai Hari Ini</p>
					</div>
					<div class="icon"><i class="fas fa-certificate"></i></div>
				</div>
			</div>
			<div class="col-lg-3 col-6">
				<div class="small-box bg-danger">
					<div class="inner">
						<h3><?= number_format($dashboard_hari_ini->backlog) ?></h3>
						<p>Backlog (Belum Putus)</p>
					</div>
					<div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
				</div>
			</div>
		</div>

		<!-- Summary Bulan Ini -->
		<div class="row">
			<div class="col-lg-3 col-6">
				<div class="info-box">
					<span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Masuk Bulan Ini</span>
						<span class="info-box-number"><?= number_format($dashboard_bulan_ini->masuk_bulan_ini) ?></span>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-6">
				<div class="info-box">
					<span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Putus Bulan Ini</span>
						<span class="info-box-number"><?= number_format($dashboard_bulan_ini->putus_bulan_ini) ?></span>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-6">
				<div class="info-box">
					<span class="info-box-icon bg-warning"><i class="fas fa-file-alt"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Akta Cerai Bulan Ini</span>
						<span class="info-box-number"><?= number_format($dashboard_bulan_ini->akta_cerai_bulan_ini) ?></span>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-6">
				<div class="info-box">
					<span class="info-box-icon bg-secondary"><i class="fas fa-calculator"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Rata-rata Masuk/Hari</span>
						<span class="info-box-number"><?= $dashboard_bulan_ini->rata_rata_masuk_harian ?></span>
					</div>
				</div>
			</div>
		</div>

		<!-- Chart Trend Bulanan -->
		<div class="card">
			<div class="card-header bg-gradient-primary">
				<h3 class="card-title"><i class="fas fa-chart-area"></i> Trend Bulanan <?= date('Y') ?></h3>
			</div>
			<div class="card-body">
				<canvas id="trendChart" height="80"></canvas>
			</div>
		</div>
	</div>

	<!-- ============================================================ -->
	<!-- TAB 2: AGING REPORT -->
	<!-- ============================================================ -->
	<div class="tab-pane <?= ($active_tab === 'aging') ? 'show active' : '' ?>" id="tab-aging">

		<!-- Aging Summary -->
		<div class="row">
			<div class="col-lg-3 col-6">
				<div class="small-box bg-secondary">
					<div class="inner">
						<h3><?= number_format($aging_summary->total_belum_putus) ?></h3>
						<p>Total Belum Putus</p>
					</div>
					<div class="icon"><i class="fas fa-folder-open"></i></div>
				</div>
			</div>
			<div class="col-lg-3 col-6">
				<div class="small-box" style="background-color: #28a745; color: white;">
					<div class="inner">
						<h3><?= number_format($aging_summary->hijau) ?></h3>
						<p><i class="fas fa-check-circle"></i> Normal (&le; 3 Bulan)</p>
					</div>
					<div class="icon"><i class="fas fa-smile"></i></div>
				</div>
			</div>
			<div class="col-lg-3 col-6">
				<div class="small-box" style="background-color: #ffc107; color: #333;">
					<div class="inner">
						<h3><?= number_format($aging_summary->kuning) ?></h3>
						<p><i class="fas fa-exclamation-circle"></i> Warning (3-5 Bulan)</p>
					</div>
					<div class="icon"><i class="fas fa-meh"></i></div>
				</div>
			</div>
			<div class="col-lg-3 col-6">
				<div class="small-box bg-danger">
					<div class="inner">
						<h3><?= number_format($aging_summary->merah) ?></h3>
						<p><i class="fas fa-times-circle"></i> Melebihi SEMA (&gt; 5 Bulan)</p>
					</div>
					<div class="icon"><i class="fas fa-frown"></i></div>
				</div>
			</div>
		</div>

		<div class="row mb-3">
			<div class="col-md-4">
				<div class="info-box bg-gradient-info">
					<span class="info-box-icon"><i class="fas fa-clock"></i></span>
					<div class="info-box-content">
						<span class="info-box-text">Rata-rata Umur Perkara</span>
						<span class="info-box-number"><?= round($aging_summary->rata_rata_umur) ?> Hari</span>
					</div>
				</div>
			</div>
		</div>

		<!-- Aging Table -->
		<div class="card">
			<div class="card-header bg-gradient-danger">
				<h3 class="card-title"><i class="fas fa-hourglass-half"></i> Daftar Perkara Belum Putus</h3>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover table-sm" id="agingTable">
						<thead>
							<tr>
								<th>No</th>
								<th>Nomor Perkara</th>
								<th>Jenis Perkara</th>
								<th>Pihak 1</th>
								<th>Pihak 2</th>
								<th>Tgl Pendaftaran</th>
								<th>Umur (Hari)</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
						<?php if (!empty($aging_data)): ?>
							<?php $no = 1; foreach ($aging_data as $row): ?>
							<tr>
								<td><?= $no++ ?></td>
								<td><strong><?= $row->nomor_perkara ?></strong></td>
								<td><?= $row->jenis_perkara_nama ?></td>
								<td><?= $row->nama_pihak_1 ?></td>
								<td><?= $row->nama_pihak_2 ?></td>
								<td><?= date('d-m-Y', strtotime($row->tanggal_pendaftaran)) ?></td>
								<td class="text-center"><strong><?= $row->umur_perkara_hari ?></strong></td>
								<td class="text-center">
									<?php if ($row->status_warna === 'hijau'): ?>
										<span class="badge badge-success"><i class="fas fa-check"></i> Normal</span>
									<?php elseif ($row->status_warna === 'kuning'): ?>
										<span class="badge badge-warning"><i class="fas fa-exclamation"></i> Warning</span>
									<?php else: ?>
										<span class="badge badge-danger"><i class="fas fa-times"></i> Melebihi SEMA</span>
									<?php endif ?>
								</td>
							</tr>
							<?php endforeach ?>
						<?php else: ?>
							<tr><td colspan="8" class="text-center"><div class="alert alert-success mb-0"><i class="fas fa-check-circle"></i> Tidak ada perkara yang belum putus</div></td></tr>
						<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- ============================================================ -->
	<!-- TAB 3: MONITORING MINUTASI -->
	<!-- ============================================================ -->
	<div class="tab-pane <?= ($active_tab === 'minutasi') ? 'show active' : '' ?>" id="tab-minutasi">

		<!-- Minutasi Summary -->
		<div class="row">
			<div class="col-lg-4 col-6">
				<div class="small-box bg-warning">
					<div class="inner">
						<h3><?= number_format($minutasi_summary->belum_bht) ?></h3>
						<p>Sudah Putus, Belum BHT</p>
					</div>
					<div class="icon"><i class="fas fa-hourglass-start"></i></div>
				</div>
			</div>
			<div class="col-lg-4 col-6">
				<div class="small-box bg-danger">
					<div class="inner">
						<h3><?= number_format($minutasi_summary->belum_akta) ?></h3>
						<p>Sudah BHT, Belum Akta Cerai</p>
					</div>
					<div class="icon"><i class="fas fa-hourglass-end"></i></div>
				</div>
			</div>
			<div class="col-lg-4 col-6">
				<div class="small-box bg-info">
					<div class="inner">
						<h3><?= number_format($minutasi_summary->belum_bht + $minutasi_summary->belum_akta) ?></h3>
						<p>Total Perlu Tindakan</p>
					</div>
					<div class="icon"><i class="fas fa-clipboard-check"></i></div>
				</div>
			</div>
		</div>

		<!-- Table: Belum BHT -->
		<div class="card">
			<div class="card-header bg-gradient-warning">
				<h3 class="card-title"><i class="fas fa-hourglass-start"></i> Perkara Sudah Putus - Belum BHT (<?= count($minutasi_belum_bht) ?>)</h3>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover table-sm" id="belumBhtTable">
						<thead>
							<tr>
								<th>No</th>
								<th>Nomor Perkara</th>
								<th>Jenis Perkara</th>
								<th>Pihak 1</th>
								<th>Pihak 2</th>
								<th>Tgl Putusan</th>
								<th>Hari Sejak Putusan</th>
							</tr>
						</thead>
						<tbody>
						<?php if (!empty($minutasi_belum_bht)): ?>
							<?php $no = 1; foreach ($minutasi_belum_bht as $row): ?>
							<tr class="<?= ($row->hari_sejak_putusan > 30) ? 'table-danger' : (($row->hari_sejak_putusan > 14) ? 'table-warning' : '') ?>">
								<td><?= $no++ ?></td>
								<td><strong><?= $row->nomor_perkara ?></strong></td>
								<td><?= $row->jenis_perkara_nama ?></td>
								<td><?= $row->nama_pihak_1 ?></td>
								<td><?= $row->nama_pihak_2 ?></td>
								<td><?= $row->tanggal_putusan ?></td>
								<td class="text-center"><strong><?= $row->hari_sejak_putusan ?></strong></td>
							</tr>
							<?php endforeach ?>
						<?php else: ?>
							<tr><td colspan="7" class="text-center"><div class="alert alert-success mb-0"><i class="fas fa-check-circle"></i> Semua perkara sudah BHT</div></td></tr>
						<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- Table: Belum Akta Cerai -->
		<div class="card">
			<div class="card-header bg-gradient-danger">
				<h3 class="card-title"><i class="fas fa-hourglass-end"></i> Perkara Sudah BHT - Belum Akta Cerai (<?= count($minutasi_belum_akta) ?>)</h3>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover table-sm" id="belumAktaTable">
						<thead>
							<tr>
								<th>No</th>
								<th>Nomor Perkara</th>
								<th>Jenis Perkara</th>
								<th>Pihak 1</th>
								<th>Pihak 2</th>
								<th>Tgl Putusan</th>
								<th>Tgl BHT</th>
								<th>Hari Sejak BHT</th>
							</tr>
						</thead>
						<tbody>
						<?php if (!empty($minutasi_belum_akta)): ?>
							<?php $no = 1; foreach ($minutasi_belum_akta as $row): ?>
							<tr class="<?= ($row->hari_sejak_bht > 30) ? 'table-danger' : (($row->hari_sejak_bht > 14) ? 'table-warning' : '') ?>">
								<td><?= $no++ ?></td>
								<td><strong><?= $row->nomor_perkara ?></strong></td>
								<td><?= $row->jenis_perkara_nama ?></td>
								<td><?= $row->nama_pihak_1 ?></td>
								<td><?= $row->nama_pihak_2 ?></td>
								<td><?= $row->tanggal_putusan ?></td>
								<td><?= $row->tanggal_bht ?></td>
								<td class="text-center"><strong><?= $row->hari_sejak_bht ?></strong></td>
							</tr>
							<?php endforeach ?>
						<?php else: ?>
							<tr><td colspan="8" class="text-center"><div class="alert alert-success mb-0"><i class="fas fa-check-circle"></i> Semua perkara cerai sudah terbit akta</div></td></tr>
						<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- ============================================================ -->
	<!-- TAB 4: MONITORING KINERJA -->
	<!-- ============================================================ -->
	<div class="tab-pane <?= ($active_tab === 'kinerja') ? 'show active' : '' ?>" id="tab-kinerja">

		<!-- Kinerja Summary Cards -->
		<div class="row">
			<div class="col-lg-2 col-6">
				<div class="small-box bg-info">
					<div class="inner">
						<h3><?= number_format($kinerja->perkara_masuk) ?></h3>
						<p>Perkara Masuk</p>
					</div>
					<div class="icon"><i class="fas fa-inbox"></i></div>
				</div>
			</div>
			<div class="col-lg-2 col-6">
				<div class="small-box bg-success">
					<div class="inner">
						<h3><?= number_format($kinerja->perkara_putus) ?></h3>
						<p>Perkara Putus</p>
					</div>
					<div class="icon"><i class="fas fa-gavel"></i></div>
				</div>
			</div>
			<div class="col-lg-2 col-6">
				<div class="small-box <?= ($kinerja->clearance_rate >= 100) ? 'bg-success' : (($kinerja->clearance_rate >= 80) ? 'bg-warning' : 'bg-danger') ?>">
					<div class="inner">
						<h3><?= $kinerja->clearance_rate ?>%</h3>
						<p>Clearance Rate</p>
					</div>
					<div class="icon"><i class="fas fa-percentage"></i></div>
				</div>
			</div>
			<div class="col-lg-2 col-6">
				<div class="small-box bg-secondary">
					<div class="inner">
						<h3><?= number_format($kinerja->disposition_time) ?></h3>
						<p>Avg Hari Putus</p>
					</div>
					<div class="icon"><i class="fas fa-clock"></i></div>
				</div>
			</div>
			<div class="col-lg-2 col-6">
				<div class="small-box bg-danger">
					<div class="inner">
						<h3><?= number_format($kinerja->backlog) ?></h3>
						<p>Backlog</p>
					</div>
					<div class="icon"><i class="fas fa-layer-group"></i></div>
				</div>
			</div>
			<div class="col-lg-2 col-6">
				<div class="small-box <?= ($kinerja->persen_tepat_waktu >= 90) ? 'bg-success' : (($kinerja->persen_tepat_waktu >= 70) ? 'bg-warning' : 'bg-danger') ?>">
					<div class="inner">
						<h3><?= $kinerja->persen_tepat_waktu ?>%</h3>
						<p>Tepat Waktu</p>
					</div>
					<div class="icon"><i class="fas fa-stopwatch"></i></div>
				</div>
			</div>
		</div>

		<!-- Tepat Waktu vs Terlambat -->
		<div class="row">
			<div class="col-md-6">
				<div class="card">
					<div class="card-header bg-gradient-success">
						<h3 class="card-title"><i class="fas fa-chart-pie"></i> Ketepatan Waktu Tahun <?= $selected_tahun ?></h3>
					</div>
					<div class="card-body">
						<canvas id="tepatWaktuChart" height="200"></canvas>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card">
					<div class="card-header bg-gradient-info">
						<h3 class="card-title"><i class="fas fa-chart-bar"></i> Clearance Rate per Bulan <?= $selected_tahun ?></h3>
					</div>
					<div class="card-body">
						<canvas id="clearanceChart" height="200"></canvas>
					</div>
				</div>
			</div>
		</div>

		<!-- Table Kinerja per Bulan -->
		<div class="card">
			<div class="card-header bg-gradient-primary">
				<h3 class="card-title"><i class="fas fa-table"></i> Detail Kinerja per Bulan <?= $selected_tahun ?></h3>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover table-sm">
						<thead>
							<tr>
								<th>Bulan</th>
								<th class="text-center">Masuk</th>
								<th class="text-center">Putus</th>
								<th class="text-center">Clearance Rate</th>
								<th class="text-center">Status</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$nama_bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
						$total_masuk = 0; $total_putus = 0;
						foreach ($kinerja_bulanan as $row): 
							$total_masuk += $row->perkara_masuk;
							$total_putus += $row->perkara_putus ?>
							<tr>
								<td><?= $nama_bulan[$row->bulan] ?></td>
								<td class="text-center"><?= number_format($row->perkara_masuk) ?></td>
								<td class="text-center"><?= number_format($row->perkara_putus) ?></td>
								<td class="text-center"><?= $row->clearance_rate ?>%</td>
								<td class="text-center">
									<?php if ($row->clearance_rate >= 100): ?>
										<span class="badge badge-success">Excellent</span>
									<?php elseif ($row->clearance_rate >= 80): ?>
										<span class="badge badge-warning">Good</span>
									<?php elseif ($row->perkara_masuk == 0 && $row->perkara_putus == 0): ?>
										<span class="badge badge-secondary">-</span>
									<?php else: ?>
										<span class="badge badge-danger">Needs Improvement</span>
									<?php endif ?>
								</td>
							</tr>
						<?php endforeach ?>
						</tbody>
						<tfoot>
							<tr class="table-primary font-weight-bold">
								<td>TOTAL</td>
								<td class="text-center"><?= number_format($total_masuk) ?></td>
								<td class="text-center"><?= number_format($total_putus) ?></td>
								<td class="text-center"><?= ($total_masuk > 0) ? round(($total_putus / $total_masuk) * 100, 1) : 0 ?>%</td>
								<td></td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	</div><!-- /tab-content -->
</div>
</section>

</div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
var chartInstances = {};

function initTrendChart() {
	if (chartInstances.trend) return;
	var trendCtx = document.getElementById('trendChart');
	if (!trendCtx) return;
	<?php if (isset($trend_bulanan) && !empty($trend_bulanan)): ?>
	chartInstances.trend = new Chart(trendCtx.getContext('2d'), {
		type: 'line',
		data: {
			labels: [<?php foreach ($trend_bulanan as $t) { echo "'" . $nama_bulan[$t->bulan] . "',"; } ?>],
			datasets: [
				{
					label: 'Perkara Masuk',
					data: [<?php foreach ($trend_bulanan as $t) { echo $t->perkara_masuk . ','; } ?>],
					borderColor: '#17a2b8', backgroundColor: 'rgba(23,162,184,0.1)',
					fill: true, tension: 0.3
				},
				{
					label: 'Perkara Putus',
					data: [<?php foreach ($trend_bulanan as $t) { echo $t->perkara_putus . ','; } ?>],
					borderColor: '#28a745', backgroundColor: 'rgba(40,167,69,0.1)',
					fill: true, tension: 0.3
				},
				{
					label: 'Akta Cerai',
					data: [<?php foreach ($trend_bulanan as $t) { echo $t->akta_cerai . ','; } ?>],
					borderColor: '#ffc107', backgroundColor: 'rgba(255,193,7,0.1)',
					fill: true, tension: 0.3
				}
			]
		},
		options: {
			responsive: true,
			maintainAspectRatio: true,
			plugins: { legend: { position: 'top' } },
			scales: { y: { beginAtZero: true } }
		}
	});
	<?php endif ?>
}

function initKinerjaCharts() {
	// Ketepatan Waktu Pie Chart
	if (!chartInstances.tepatWaktu) {
		var tepatCtx = document.getElementById('tepatWaktuChart');
		if (tepatCtx) {
			chartInstances.tepatWaktu = new Chart(tepatCtx.getContext('2d'), {
				type: 'doughnut',
				data: {
					labels: ['Tepat Waktu (<=5 bln)', 'Terlambat (>5 bln)'],
					datasets: [{
						data: [<?= $kinerja->tepat_waktu ?>, <?= $kinerja->terlambat ?>],
						backgroundColor: ['#28a745', '#dc3545']
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: true,
					plugins: {
						legend: { position: 'bottom' },
						title: { display: true, text: 'Tepat Waktu: <?= $kinerja->persen_tepat_waktu ?>%' }
					}
				}
			});
		}
	}

	// Clearance Rate Bar Chart
	if (!chartInstances.clearance) {
		var clearCtx = document.getElementById('clearanceChart');
		if (clearCtx) {
			chartInstances.clearance = new Chart(clearCtx.getContext('2d'), {
				type: 'bar',
				data: {
					labels: [<?php foreach ($kinerja_bulanan as $k) { echo "'" . $nama_bulan[$k->bulan] . "',"; } ?>],
					datasets: [
						{
							label: 'Masuk',
							data: [<?php foreach ($kinerja_bulanan as $k) { echo $k->perkara_masuk . ','; } ?>],
							backgroundColor: 'rgba(23,162,184,0.7)'
						},
						{
							label: 'Putus',
							data: [<?php foreach ($kinerja_bulanan as $k) { echo $k->perkara_putus . ','; } ?>],
							backgroundColor: 'rgba(40,167,69,0.7)'
						}
					]
				},
				options: {
					responsive: true,
					maintainAspectRatio: true,
					plugins: { legend: { position: 'top' } },
					scales: { y: { beginAtZero: true } }
				}
			});
		}
	}
}

function switchTab(tab) {
	event.preventDefault();
	document.getElementById('hiddenTab').value = tab;

	// Show/hide tabs
	document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('show', 'active'));
	document.querySelectorAll('#monitoringTabs .nav-link').forEach(el => el.classList.remove('active'));

	document.getElementById('tab-' + tab).classList.add('show', 'active');
	event.target.closest('.nav-link').classList.add('active');

	// Update URL without reload
	var url = new URL(window.location);
	url.searchParams.set('tab', tab);
	window.history.pushState({}, '', url);

	// Initialize charts after tab is visible
	setTimeout(function() {
		if (tab === 'dashboard') initTrendChart();
		if (tab === 'kinerja') initKinerjaCharts();
	}, 100);
}

$(document).ready(function() {
	// DataTables
	$('#agingTable, #belumBhtTable, #belumAktaTable').DataTable({
		responsive: true,
		pageLength: 25,
		lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
		language: { url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json" }
	});

	// Initialize charts for the active tab after DOM is ready
	var activeTab = '<?= $active_tab ?>';
	setTimeout(function() {
		if (activeTab === 'dashboard') initTrendChart();
		if (activeTab === 'kinerja') initKinerjaCharts();
	}, 300);
});
</script>

<!-- Custom Styles -->
<style>
.tab-content > .tab-pane { display: none; }
.tab-content > .tab-pane.active { display: block; }
.small-box { border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s; }
.small-box:hover { transform: translateY(-2px); }
.small-box .inner h3 { font-weight: bold; font-size: 2rem; }
.nav-tabs .nav-link { font-weight: 600; color: #555; }
.nav-tabs .nav-link.active { color: #007bff; border-bottom: 3px solid #007bff; }
.nav-tabs .nav-link i { margin-right: 5px; }
.info-box { border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.table th { background-color: #007bff !important; color: white !important; font-size: 0.85rem; }
.table-danger td { background-color: #f8d7da !important; }
.table-warning td { background-color: #fff3cd !important; }
@media print {
	.card-outline, .nav-tabs, .btn { display: none !important; }
}
</style>

</body>
</html>
