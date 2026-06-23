<?php
/**
 * Notifikasi Alert Panel
 * Ditampilkan di dashboard jika ada perkara yang mendekati/melewati batas waktu
 * 
 * Variables:
 * - $notifikasi: object dengan lewat_batas, mendekati_batas, bht_belum_akta, total
 * - $perkara_lewat_batas: array perkara > 5 bulan belum putus
 * - $perkara_mendekati_batas: array perkara 4-5 bulan belum putus
 * - $perkara_bht_belum_akta: array perkara BHT belum akta cerai > 7 hari
 */
$total_notif = isset($notifikasi) ? $notifikasi->total : 0;
if ($total_notif == 0) return; // Tidak tampilkan jika tidak ada notifikasi
?>

<!-- Notifikasi Perkara Alert Section -->
<div class="row mb-4">
	<div class="col-12">
		<div class="card card-outline card-warning">
			<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-exclamation-triangle text-warning mr-2"></i>
					<strong>Notifikasi Perkara</strong>
					<span class="badge badge-danger ml-2"><?php echo $total_notif; ?> peringatan</span>
				</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse">
						<i class="fas fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body p-0">

				<!-- Summary Badges -->
				<div class="p-3 border-bottom bg-light">
					<div class="row text-center">
						<div class="col-md-4">
							<div class="p-2">
								<span class="badge badge-danger badge-lg" style="font-size: 1.1rem; padding: 8px 15px;">
									<i class="fas fa-times-circle mr-1"></i>
									<?php echo $notifikasi->lewat_batas; ?>
								</span>
								<p class="text-muted mt-1 mb-0"><small>Lewat Batas 5 Bulan</small></p>
							</div>
						</div>
						<div class="col-md-4">
							<div class="p-2">
								<span class="badge badge-warning badge-lg" style="font-size: 1.1rem; padding: 8px 15px;">
									<i class="fas fa-exclamation-circle mr-1"></i>
									<?php echo $notifikasi->mendekati_batas; ?>
								</span>
								<p class="text-muted mt-1 mb-0"><small>Mendekati Batas (4-5 Bulan)</small></p>
							</div>
						</div>
						<div class="col-md-4">
							<div class="p-2">
								<span class="badge badge-info badge-lg" style="font-size: 1.1rem; padding: 8px 15px;">
									<i class="fas fa-file-alt mr-1"></i>
									<?php echo $notifikasi->bht_belum_akta; ?>
								</span>
								<p class="text-muted mt-1 mb-0"><small>BHT Belum Akta Cerai (&gt;7 hari)</small></p>
							</div>
						</div>
					</div>
				</div>

				<!-- Tabs -->
				<ul class="nav nav-tabs nav-justified" id="notifTab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active text-danger" id="lewat-batas-tab" data-toggle="tab" href="#lewat-batas" role="tab">
							<i class="fas fa-times-circle mr-1"></i> Lewat Batas
							<?php if ($notifikasi->lewat_batas > 0): ?>
								<span class="badge badge-danger"><?php echo $notifikasi->lewat_batas; ?></span>
							<?php endif; ?>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-warning" id="mendekati-batas-tab" data-toggle="tab" href="#mendekati-batas" role="tab">
							<i class="fas fa-exclamation-circle mr-1"></i> Mendekati Batas
							<?php if ($notifikasi->mendekati_batas > 0): ?>
								<span class="badge badge-warning"><?php echo $notifikasi->mendekati_batas; ?></span>
							<?php endif; ?>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-info" id="bht-akta-tab" data-toggle="tab" href="#bht-akta" role="tab">
							<i class="fas fa-file-alt mr-1"></i> BHT Belum Akta
							<?php if ($notifikasi->bht_belum_akta > 0): ?>
								<span class="badge badge-info"><?php echo $notifikasi->bht_belum_akta; ?></span>
							<?php endif; ?>
						</a>
					</li>
				</ul>

				<!-- Tab Content -->
				<div class="tab-content" id="notifTabContent">

					<!-- Tab 1: Perkara Lewat Batas 5 Bulan -->
					<div class="tab-pane fade show active" id="lewat-batas" role="tabpanel">
						<?php if (!empty($perkara_lewat_batas)): ?>
							<div class="table-responsive">
								<table class="table table-sm table-hover mb-0">
									<thead class="thead-light">
										<tr>
											<th>No</th>
											<th>Nomor Perkara</th>
											<th>Jenis</th>
											<th>Tgl Daftar</th>
											<th>Umur</th>
											<th>Penggugat</th>
										</tr>
									</thead>
									<tbody>
										<?php $no = 1; foreach ($perkara_lewat_batas as $row): ?>
											<tr>
												<td><?php echo $no++; ?></td>
												<td><strong><?php echo htmlspecialchars($row->nomor_perkara); ?></strong></td>
												<td><span class="badge badge-secondary"><?php echo htmlspecialchars($row->jenis_perkara_nama); ?></span></td>
												<td><?php echo date('d-m-Y', strtotime($row->tanggal_pendaftaran)); ?></td>
												<td>
													<span class="badge badge-danger">
														<?php echo $row->umur_hari; ?> hari
														(<?php echo $row->umur_bulan; ?> bln)
													</span>
												</td>
												<td><?php echo htmlspecialchars(substr($row->penggugat, 0, 30)); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
							<?php if ($notifikasi->lewat_batas > 10): ?>
								<div class="p-2 text-center border-top">
									<a href="<?php echo site_url('Monitoring_sipp?tab=aging'); ?>" class="text-danger">
										<i class="fas fa-arrow-right mr-1"></i>
										Lihat semua <?php echo $notifikasi->lewat_batas; ?> perkara
									</a>
								</div>
							<?php endif; ?>
						<?php else: ?>
							<div class="p-4 text-center text-success">
								<i class="fas fa-check-circle fa-2x mb-2"></i>
								<p class="mb-0">Tidak ada perkara yang melewati batas 5 bulan.</p>
							</div>
						<?php endif; ?>
					</div>

					<!-- Tab 2: Perkara Mendekati Batas -->
					<div class="tab-pane fade" id="mendekati-batas" role="tabpanel">
						<?php if (!empty($perkara_mendekati_batas)): ?>
							<div class="table-responsive">
								<table class="table table-sm table-hover mb-0">
									<thead class="thead-light">
										<tr>
											<th>No</th>
											<th>Nomor Perkara</th>
											<th>Jenis</th>
											<th>Tgl Daftar</th>
											<th>Umur</th>
											<th>Penggugat</th>
										</tr>
									</thead>
									<tbody>
										<?php $no = 1; foreach ($perkara_mendekati_batas as $row): ?>
											<tr>
												<td><?php echo $no++; ?></td>
												<td><strong><?php echo htmlspecialchars($row->nomor_perkara); ?></strong></td>
												<td><span class="badge badge-secondary"><?php echo htmlspecialchars($row->jenis_perkara_nama); ?></span></td>
												<td><?php echo date('d-m-Y', strtotime($row->tanggal_pendaftaran)); ?></td>
												<td>
													<span class="badge badge-warning">
														<?php echo $row->umur_hari; ?> hari
														(<?php echo $row->umur_bulan; ?> bln)
													</span>
												</td>
												<td><?php echo htmlspecialchars(substr($row->penggugat, 0, 30)); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
							<?php if ($notifikasi->mendekati_batas > 10): ?>
								<div class="p-2 text-center border-top">
									<a href="<?php echo site_url('Monitoring_sipp?tab=aging'); ?>" class="text-warning">
										<i class="fas fa-arrow-right mr-1"></i>
										Lihat semua <?php echo $notifikasi->mendekati_batas; ?> perkara
									</a>
								</div>
							<?php endif; ?>
						<?php else: ?>
							<div class="p-4 text-center text-success">
								<i class="fas fa-check-circle fa-2x mb-2"></i>
								<p class="mb-0">Tidak ada perkara yang mendekati batas 5 bulan.</p>
							</div>
						<?php endif; ?>
					</div>

					<!-- Tab 3: BHT Belum Akta Cerai -->
					<div class="tab-pane fade" id="bht-akta" role="tabpanel">
						<?php if (!empty($perkara_bht_belum_akta)): ?>
							<div class="table-responsive">
								<table class="table table-sm table-hover mb-0">
									<thead class="thead-light">
										<tr>
											<th>No</th>
											<th>Nomor Perkara</th>
											<th>Jenis</th>
											<th>Tgl Putusan</th>
											<th>Tgl BHT</th>
											<th>Hari Sejak BHT</th>
											<th>Penggugat</th>
										</tr>
									</thead>
									<tbody>
										<?php $no = 1; foreach ($perkara_bht_belum_akta as $row): ?>
											<tr>
												<td><?php echo $no++; ?></td>
												<td><strong><?php echo htmlspecialchars($row->nomor_perkara); ?></strong></td>
												<td><span class="badge badge-secondary"><?php echo htmlspecialchars($row->jenis_perkara_nama); ?></span></td>
												<td><?php echo $row->tanggal_putusan; ?></td>
												<td><?php echo $row->tanggal_bht; ?></td>
												<td>
													<span class="badge <?php echo ($row->hari_sejak_bht > 30) ? 'badge-danger' : 'badge-info'; ?>">
														<?php echo $row->hari_sejak_bht; ?> hari
													</span>
												</td>
												<td><?php echo htmlspecialchars(substr($row->penggugat, 0, 30)); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
							<?php if ($notifikasi->bht_belum_akta > 10): ?>
								<div class="p-2 text-center border-top">
									<a href="<?php echo site_url('Monitoring_sipp?tab=minutasi'); ?>" class="text-info">
										<i class="fas fa-arrow-right mr-1"></i>
										Lihat semua <?php echo $notifikasi->bht_belum_akta; ?> perkara
									</a>
								</div>
							<?php endif; ?>
						<?php else: ?>
							<div class="p-4 text-center text-success">
								<i class="fas fa-check-circle fa-2x mb-2"></i>
								<p class="mb-0">Semua perkara BHT sudah terbit akta cerai.</p>
							</div>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
