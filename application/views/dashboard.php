<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1 class="m-0">Dashboard</h1>
				</div><!-- /.col -->
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active">Dashboard</li>
					</ol>
				</div><!-- /.col -->
			</div><!-- /.row -->
		</div><!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->

	<!-- Main content -->
	<section class="content">
		<div class="container-fluid">
			<!-- Small boxes (Stat box) -->
			<style>
				.rounded-box {
					border-radius: 50%;
					padding: 20px;
					text-align: center;
					height: 200px;
					width: 200px;
					display: flex;
					align-items: center;
					justify-content: center;
					flex-direction: column;
				}
			</style>

			<h4 style="text-align: center;">Perkara Tahun : <?= $currentYear ?></h4>

			<div class="row">
				<div class="col-lg-3 col-6">
					<div class="small-box bg-info rounded-box">
						<div class="inner">
							<h3><?= $perkara_count ?></h3>
							<p>Diterima</p>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-6">
					<div class="small-box bg-success rounded-box">
						<div class="inner">
							<h3><?= $putus_count ?></h3>
							<p>Putus</p>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-6">
					<div class="small-box bg-warning rounded-box">
						<div class="inner">
							<h3><?= $minutasi_count ?></h3>
							<p>Minutasi</p>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-6">
					<div class="small-box bg-danger rounded-box">
						<div class="inner">
							<h3><?= $sisa_count ?></h3>
							<p>Sisa</p>
						</div>
					</div>
				</div>
			</div><!-- /.row -->

			<h5 style="text-align: center;">Perkara Bulan <?= $currentMonthName ?></h5>

			<!-- Include Chart.js -->
			<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
			<canvas id="myChart" width="200" height="100"></canvas>
			<script>
				// Initialize a new Chart.js object
				var ctx = document.getElementById('myChart').getContext('2d');
				var myChart = new Chart(ctx, {
					type: 'bar',
					data: {
						labels: ['Diterima', 'Putus', 'Minutasi', 'Sisa'],
						datasets: [{
							label: 'Perkara Bulan Ini',
							data: <?= json_encode($chart_data) ?>,
							backgroundColor: [
								'rgba(54, 162, 235, 0.2)',
								'rgba(75, 192, 192, 0.2)',
								'rgba(255, 206, 86, 0.2)',
								'rgba(255, 99, 132, 0.2)',
								'rgba(153, 102, 255, 0.2)'
							],
							borderColor: [
								'rgba(54, 162, 235, 1)',
								'rgba(75, 192, 192, 1)',
								'rgba(255, 206, 86, 1)',
								'rgba(255, 99, 132, 1)',
								'rgba(153, 102, 255, 1)'
							],
							borderWidth: 1
						}]
					},
					options: {
						scales: {
							y: {
								beginAtZero: true
							}
						}
					}
				});
			</script>
	</section>
	<!-- /.content -->

</div>
<!-- /.content-wrapper -->
