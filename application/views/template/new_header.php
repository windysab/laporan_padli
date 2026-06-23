<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>LAPORAN PERKARA PA AMUNTAI</title>

	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
	<!-- DataTables -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="<?php echo base_url() ?>assets/dist/css/adminlte.min.css">

	<link rel="icon" type="image/png" href="<?php echo base_url() ?>assets/dist/img/Logo PA Amuntai - Trans.png?>" />



</head>

<body class="hold-transition sidebar-mini">
	<div class="wrapper">
		<!-- Navbar -->
		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<!-- Left navbar links -->
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
				</li>
				<!-- <li class="nav-item d-none d-sm-inline-block">
					<a href="<?php echo base_url() ?>dashboard" class="nav-link">Home</a>
				</li>
				<li class="nav-item d-none d-sm-inline-block">
					<a href="<?php echo base_url() ?>dashboard" class="nav-link">Dashboard</a>
				</li> -->
			</ul>

			<!-- Right navbar links -->
			<ul class="navbar-nav ml-auto">
				<?php if ($this->session->userdata('logged_in')): ?>
				<!-- Notification Bell -->
				<?php
					$CI =& get_instance();
					$CI->load->model('M_notifikasi_perkara');
					$notif_count = $CI->M_notifikasi_perkara->get_notifikasi_summary();
				?>
				<?php if ($notif_count->total > 0): ?>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo site_url('dashboard'); ?>#notifTab" title="Notifikasi Perkara">
						<i class="fas fa-bell"></i>
						<span class="badge badge-danger navbar-badge"><?php echo $notif_count->total; ?></span>
					</a>
				</li>
				<?php endif; ?>
				<!-- User Info -->
				<li class="nav-item d-none d-sm-inline-block">
					<span class="nav-link text-muted">
						<i class="fas fa-user-circle mr-1"></i>
						<?php echo htmlspecialchars($this->session->userdata('username')); ?>
					</span>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo site_url('auth/logout'); ?>" title="Logout">
						<i class="fas fa-sign-out-alt"></i> Logout
					</a>
				</li>
				<?php endif; ?>
			</ul>
		</nav>


		<!-- /.navbar -->
