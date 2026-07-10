<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monitoring_sipp extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!ini_get('date.timezone')) {
			date_default_timezone_set('Asia/Jakarta');
		}
		$this->load->model('M_monitoring_sipp');
		$this->load->helper('url');
	}

	public function index()
	{
		$allowed_tabs = ['dashboard', 'aging', 'minutasi', 'kinerja'];
		$tab = $this->input->get('tab');
		$tab = in_array($tab, $allowed_tabs) ? $tab : 'dashboard';

		$wilayah = validate_wilayah(
			$this->input->post('wilayah') ?: $this->input->get('wilayah'),
			'Semua'
		);
		$jenis_perkara = validate_jenis_perkara(
			$this->input->post('jenis_perkara') ?: $this->input->get('jenis_perkara'),
			'semua'
		);
		$tahun = validate_tahun(
			$this->input->post('tahun') ?: $this->input->get('tahun')
		);

		$data = [
			'active_tab' => $tab,
			'selected_wilayah' => $wilayah,
			'selected_jenis_perkara' => $jenis_perkara,
			'selected_tahun' => $tahun,
			'dashboard_hari_ini' => $this->M_monitoring_sipp->get_dashboard_hari_ini(),
			'dashboard_bulan_ini' => $this->M_monitoring_sipp->get_dashboard_bulan_ini(),
			'trend_bulanan' => $this->M_monitoring_sipp->get_trend_bulanan_tahun_ini(),
			'aging_data' => $this->M_monitoring_sipp->get_perkara_belum_putus($wilayah, $jenis_perkara),
			'aging_summary' => $this->M_monitoring_sipp->get_aging_summary($wilayah, $jenis_perkara),
			'minutasi_belum_bht' => $this->M_monitoring_sipp->get_perkara_sudah_putus_belum_bht($wilayah),
			'minutasi_belum_akta' => $this->M_monitoring_sipp->get_perkara_sudah_bht_belum_akta($wilayah),
			'minutasi_summary' => $this->M_monitoring_sipp->get_minutasi_summary($wilayah),
			'kinerja' => $this->M_monitoring_sipp->get_kinerja($tahun, $wilayah),
			'kinerja_bulanan' => $this->M_monitoring_sipp->get_kinerja_per_bulan($tahun, $wilayah),
		];

		view_load('v_monitoring_sipp', $data);
	}
}
