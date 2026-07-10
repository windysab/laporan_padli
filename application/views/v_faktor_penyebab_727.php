<?php
$selected_tahun = isset($selected_tahun) ? $selected_tahun : date('Y');
$selected_wilayah_label = isset($selected_wilayah_label) ? $selected_wilayah_label : 'HSU';
$rows = isset($rows) && is_array($rows) ? $rows : array();
$totals = isset($totals) && is_array($totals) ? $totals : array(
	'usia_16_19' => 0,
	'usia_20_25' => 0,
	'usia_26_30' => 0,
	'usia_31_35' => 0,
	'usia_36' => 0,
) ?>

<div class="content-wrapper">
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Tabel 7.27</h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?= site_url('Dashboard') ?>">Home</a></li>
						<li class="breadcrumb-item"><a href="<?= site_url('Faktor_perceraian_detail') ?>">Faktor Perceraian Detail</a></li>
						<li class="breadcrumb-item active">Tabel 7.27</li>
					</ol>
				</div>
			</div>
		</div>
	</div>

	<section class="content">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header d-flex justify-content-between align-items-center">
					<div>
						<h3 class="card-title mb-0">Faktor Penyebab Terjadinya Perceraian Dari Jumlah Umur Dalam Perceraian Tahun <?= (int) $selected_tahun ?></h3>
						<div class="mt-1"><span class="badge badge-primary">Wilayah: <?= $selected_wilayah_label ?></span></div>
					</div>
					<div class="card-tools">
						<a href="<?= site_url('Faktor_perceraian_detail') ?>" class="btn btn-sm btn-secondary">
							<i class="fas fa-arrow-left"></i> Kembali
						</a>
						<button type="button" class="btn btn-sm btn-info" onclick="window.print();">
							<i class="fas fa-print"></i> Print
						</button>
					</div>
				</div>
				<div class="card-body">
					<div class="text-center mb-3">
						<div class="report-subtitle">7.27 Faktor Penyebab Terjadinya Perceraian Pada PA Amuntai</div>
						<div class="report-title">Tabel 7.27</div>
						<div class="report-subtitle">Faktor Penyebab Terjadinya Perceraian Pada PA Amuntai</div>
						<div class="report-subtitle">Dari Jumlah Umur Dalam Perceraian Tahun <?= (int) $selected_tahun ?></div>
						<div class="report-subtitle">Wilayah Data: <?= $selected_wilayah_label ?></div>
					</div>

					<div class="table-responsive">
						<table class="table table-bordered report-table">
							<thead>
								<tr>
									<th rowspan="2" class="text-center align-middle" style="width: 60px;">NO</th>
									<th rowspan="2" class="text-center align-middle" style="min-width: 260px;">Faktor Penyebab Terjadinya Perceraian</th>
									<th class="text-center">Usia 16-19</th>
									<th class="text-center">Usia 20-25</th>
									<th class="text-center">Usia 26-30</th>
									<th class="text-center">Usia 31-35</th>
									<th class="text-center">Usia 36</th>
								</tr>
								<tr>
									<th class="text-center">Perempuan</th>
									<th class="text-center">Perempuan</th>
									<th class="text-center">Perempuan</th>
									<th class="text-center">Perempuan</th>
									<th class="text-center">Perempuan</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($rows as $row): ?>
									<tr>
										<td class="text-center"><?= $row['no'] ?></td>
										<td><?= $row['faktor'] ?></td>
										<td class="text-center"><?= $row['usia_16_19'] > 0 ? number_format($row['usia_16_19']) : '' ?></td>
										<td class="text-center"><?= $row['usia_20_25'] > 0 ? number_format($row['usia_20_25']) : '' ?></td>
										<td class="text-center"><?= $row['usia_26_30'] > 0 ? number_format($row['usia_26_30']) : '' ?></td>
										<td class="text-center"><?= $row['usia_31_35'] > 0 ? number_format($row['usia_31_35']) : '' ?></td>
										<td class="text-center"><?= $row['usia_36'] > 0 ? number_format($row['usia_36']) : '' ?></td>
									</tr>
								<?php endforeach ?>
								<tr class="font-weight-bold">
									<td colspan="2" class="text-center">Jumlah</td>
									<td class="text-center"><?= $totals['usia_16_19'] > 0 ? number_format($totals['usia_16_19']) : '' ?></td>
									<td class="text-center"><?= $totals['usia_20_25'] > 0 ? number_format($totals['usia_20_25']) : '' ?></td>
									<td class="text-center"><?= $totals['usia_26_30'] > 0 ? number_format($totals['usia_26_30']) : '' ?></td>
									<td class="text-center"><?= $totals['usia_31_35'] > 0 ? number_format($totals['usia_31_35']) : '' ?></td>
									<td class="text-center"><?= $totals['usia_36'] > 0 ? number_format($totals['usia_36']) : '' ?></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="report-footer">Sumber data : Pengadilan Agama Amuntai</div>
				</div>
			</div>
		</div>
	</section>
</div>

<style>
	.report-title {
		font-size: 22px;
		font-weight: 700;
	}

	.report-subtitle {
		font-size: 18px;
		font-weight: 600;
	}

	.report-table th,
	.report-table td {
		border: 1px solid #222 !important;
	}

	.report-footer {
		font-size: 24px;
		font-weight: 700;
		margin-top: 12px;
	}

	@media print {
		.main-header,
		.main-sidebar,
		.main-footer,
		.card-tools,
		.content-header {
			display: none !important;
		}

		.content-wrapper {
			margin-left: 0 !important;
		}

		.card {
			border: none !important;
			box-shadow: none !important;
		}
	}
</style>
