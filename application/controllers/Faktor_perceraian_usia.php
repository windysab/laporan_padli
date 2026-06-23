<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Faktor_perceraian_usia extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_faktor_perceraian_usia');
	}

	public function index()
	{
		// Filter defaults
		$lap_tahun = $this->input->post('lap_tahun');
		$wilayah = $this->input->post('wilayah');

		if (empty($lap_tahun)) {
			$lap_tahun = date('Y');
		}
		if (empty($wilayah)) {
			$wilayah = 'Amuntai';
		}

		// Normalize wilayah
		$wilayah_param = $this->_normalize_wilayah($wilayah);

		$data['datafilter'] = $this->M_faktor_perceraian_usia->get_data($lap_tahun, $wilayah_param);
		$data['selected_tahun'] = $lap_tahun;
		$data['selected_wilayah'] = $wilayah;

		// Hitung grand total
		$grand_total = 0;
		if (!empty($data['datafilter'])) {
			foreach ($data['datafilter'] as $row) {
				if ($row->faktor != 'Jumlah') {
					$grand_total += $row->usia_16_19
						+ $row->usia_20_25
						+ $row->usia_26_30
						+ $row->usia_31_35
						+ $row->usia_36;
				}
			}
		}
		$data['grand_total'] = $grand_total;

		$this->load->view('template/new_header');
		$this->load->view('template/new_sidebar');
		$this->load->view('v_faktor_perceraian_usia', $data);
		$this->load->view('template/new_footer');
	}

	/**
	 * Normalize wilayah name for consistent querying
	 */
	private function _normalize_wilayah($wilayah)
	{
		$wilayah = trim($wilayah);

		// Check for HSU variants
		if (strtoupper($wilayah) == 'HSU' || stripos($wilayah, 'hulu sungai utara') !== false) {
			return 'Hulu Sungai Utara';
		}

		// Check for Amuntai (kota dalam HSU)
		if (strtoupper($wilayah) == 'AMUNTAI' || stripos($wilayah, 'amuntai') !== false) {
			return 'Amuntai';
		}

		return $wilayah;
	}
}
