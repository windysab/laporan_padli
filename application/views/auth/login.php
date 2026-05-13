<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login - SI LAPER PA Amuntai</title>

	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/dist/css/adminlte.min.css">

	<link rel="icon" type="image/png" href="<?php echo base_url() ?>assets/dist/img/Logo PA Amuntai - Trans.png" />

	<style>
		.login-page {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
		}
		.login-box {
			width: 400px;
		}
		.login-logo img {
			width: 80px;
			height: 80px;
			border-radius: 50%;
			margin-bottom: 10px;
		}
		.card {
			border-radius: 10px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
		}
		.btn-primary {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border: none;
			border-radius: 5px;
			padding: 10px;
			font-weight: 600;
		}
		.btn-primary:hover {
			background: linear-gradient(135deg, #5a6fd6 0%, #6a4299 100%);
		}
	</style>
</head>

<body class="hold-transition login-page">
	<div class="login-box">
		<!-- Logo -->
		<div class="login-logo">
			<img src="<?php echo base_url() ?>assets/dist/img/Logo PA Amuntai - Trans.png" alt="Logo PA Amuntai">
			<br>
			<a href="#"><b>SI LAPER</b></a>
			<br>
			<small style="color: #fff;">Sistem Laporan Perkara</small>
		</div>

		<!-- Login Card -->
		<div class="card">
			<div class="card-body login-card-body">
				<p class="login-box-msg">Silakan login untuk melanjutkan</p>

				<?php if (!empty($error)): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<i class="fas fa-exclamation-triangle mr-1"></i>
						<?php echo htmlspecialchars($error); ?>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
				<?php endif; ?>

				<form action="<?php echo site_url('auth/process'); ?>" method="post" autocomplete="off">
					<div class="input-group mb-3">
						<input type="text" name="username" class="form-control" placeholder="Username" required autofocus
							maxlength="50" aria-label="Username">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-user"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input type="password" name="password" class="form-control" placeholder="Password" required
							maxlength="100" aria-label="Password">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-12">
							<button type="submit" class="btn btn-primary btn-block">
								<i class="fas fa-sign-in-alt mr-1"></i> Masuk
							</button>
						</div>
					</div>
				</form>

				<hr>
				<p class="text-center text-muted mb-0">
					<small>Pengadilan Agama Amuntai &copy; <?php echo date('Y'); ?></small>
				</p>
			</div>
		</div>
	</div>

	<!-- jQuery -->
	<script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- AdminLTE App -->
	<script src="<?php echo base_url() ?>assets/dist/js/adminlte.min.js"></script>
</body>

</html>
