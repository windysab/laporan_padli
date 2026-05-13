<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

	/**
	 * Daftar user yang diizinkan login.
	 * Format: 'username' => 'password_hash'
	 * 
	 * Untuk generate hash baru, gunakan: password_hash('password_baru', PASSWORD_DEFAULT)
	 */
	private $users = array(
		'admin' => '$2y$10$YJ8kZ5Q9X5Z5Z5Z5Z5Z5ZuKxKxKxKxKxKxKxKxKxKxKxKxKxKxKxK', // placeholder
		'padli' => '$2y$10$YJ8kZ5Q9X5Z5Z5Z5Z5Z5ZuKxKxKxKxKxKxKxKxKxKxKxKxKxKxKxK', // placeholder
	);

	public function __construct()
	{
		parent::__construct();
		// Generate proper hashes on first load (will be used for default passwords)
		$this->users = array(
			'admin'  => password_hash('admin123', PASSWORD_DEFAULT),
			'padli'  => password_hash('laper2024', PASSWORD_DEFAULT),
			'panitera' => password_hash('panitera123', PASSWORD_DEFAULT),
		);
	}

	/**
	 * Halaman login
	 */
	public function login()
	{
		// Jika sudah login, redirect ke dashboard
		if ($this->session->userdata('logged_in')) {
			redirect('dashboard');
		}

		$data = array(
			'error' => $this->session->flashdata('login_error')
		);

		$this->load->view('auth/login', $data);
	}

	/**
	 * Proses login
	 */
	public function process()
	{
		$username = trim($this->input->post('username'));
		$password = $this->input->post('password');

		// Validasi input kosong
		if (empty($username) || empty($password)) {
			$this->session->set_flashdata('login_error', 'Username dan password harus diisi.');
			redirect('auth/login');
			return;
		}

		// Cek apakah username ada
		if (!array_key_exists($username, $this->users)) {
			// Delay untuk mencegah brute force
			sleep(1);
			$this->session->set_flashdata('login_error', 'Username atau password salah.');
			redirect('auth/login');
			return;
		}

		// Verifikasi password
		if (password_verify($password, $this->users[$username])) {
			// Login berhasil
			$session_data = array(
				'username'  => $username,
				'logged_in' => TRUE,
				'login_time' => time()
			);
			$this->session->set_userdata($session_data);

			// Regenerate session ID untuk keamanan
			$this->session->sess_regenerate(TRUE);

			redirect('dashboard');
		} else {
			// Login gagal
			sleep(1);
			$this->session->set_flashdata('login_error', 'Username atau password salah.');
			redirect('auth/login');
		}
	}

	/**
	 * Logout
	 */
	public function logout()
	{
		$this->session->unset_userdata(array('username', 'logged_in', 'login_time'));
		$this->session->sess_destroy();
		redirect('auth/login');
	}
}
