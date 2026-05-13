<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MY_Controller
 * 
 * Base controller yang mengecek autentikasi.
 * Semua controller yang membutuhkan login harus extend class ini.
 */
class MY_Controller extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		// Cek apakah user sudah login
		if (!$this->session->userdata('logged_in')) {
			// Simpan URL yang diminta untuk redirect setelah login
			$this->session->set_flashdata('redirect_url', current_url());
			redirect('auth/login');
		}

		// Cek session timeout (2 jam)
		$login_time = $this->session->userdata('login_time');
		if ($login_time && (time() - $login_time) > 7200) {
			$this->session->set_flashdata('login_error', 'Sesi Anda telah berakhir. Silakan login kembali.');
			$this->session->sess_destroy();
			redirect('auth/login');
		}
	}
}
