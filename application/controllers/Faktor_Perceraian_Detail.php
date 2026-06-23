<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Faktor_perceraian_detail extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_faktor_perceraian_detail');
	}

	public function index()
	{
		// Set default values jika tidak ada POST data
		$lap_tahun = $this->input->post('lap_tahun');
		$wilayah = $this->input->post('wilayah');

		if (empty($lap_tahun)) {
			$lap_tahun = date('Y');
		}
		if (empty($wilayah)) {
			$wilayah = 'Balangan';
		}

		// Mapping wilayah untuk konsistensi dengan database
		if ($wilayah == 'HSU') {
			$wilayah_param = 'Hulu Sungai Utara';
		} else {
			$wilayah_param = $wilayah;
		}

		// Prepare data for view
		$data = array();
		$data['datafilter'] = $this->M_faktor_perceraian_detail->data_faktor_perceraian_detail($lap_tahun, $wilayah_param);
		$data['selected_tahun'] = $lap_tahun;
		$data['selected_wilayah'] = $wilayah;
		$data['selected_wilayah_label'] = $this->get_wilayah_label($wilayah);

		// Load views
		$this->load->view('template/new_header');
		$this->load->view('template/new_sidebar');
		$this->load->view('v_faktor_perceraian_detail', $data);
		$this->load->view('template/new_footer');
	}

	public function tabel_727()
	{
		$lap_tahun = $this->input->post('lap_tahun') ?: $this->input->get('lap_tahun');
		$wilayah = $this->input->post('wilayah') ?: $this->input->get('wilayah');

		if (empty($lap_tahun)) {
			$lap_tahun = date('Y');
		}
		if (empty($wilayah)) {
			$wilayah = 'HSU';
		}

		$raw = $this->M_faktor_perceraian_detail->data_faktor_perceraian_usia($lap_tahun, $wilayah, 'P');
		$indexed = array();
		foreach ($raw as $row) {
			$indexed[$row->FaktorPerceraian] = $row;
		}

		$ordered_factors = array(
			'Perselisihan Terus Menerus',
			'Kawin Paksa',
			'Murtad',
			'Ekonomi',
			'Lain-Lain',
		);

		$rows = array();
		foreach ($ordered_factors as $i => $factor_name) {
			$src = isset($indexed[$factor_name]) ? $indexed[$factor_name] : null;
			$usia_16_19 = $src ? (int) $src->usia_16_19 : 0;
			$usia_20_25 = $src ? (int) $src->usia_20_25 : 0;
			$usia_26_30 = $src ? (int) $src->usia_26_30 : 0;
			$usia_31_35 = $src ? (int) $src->usia_31_35 : 0;
			$usia_36 = $src ? (int) $src->usia_36 : 0;

			$rows[] = array(
				'no' => $i + 1,
				'faktor' => $factor_name,
				'usia_16_19' => $usia_16_19,
				'usia_20_25' => $usia_20_25,
				'usia_26_30' => $usia_26_30,
				'usia_31_35' => $usia_31_35,
				'usia_36' => $usia_36,
			);
		}

		$totals = array(
			'usia_16_19' => 0,
			'usia_20_25' => 0,
			'usia_26_30' => 0,
			'usia_31_35' => 0,
			'usia_36' => 0,
		);

		foreach ($rows as $row) {
			$totals['usia_16_19'] += $row['usia_16_19'];
			$totals['usia_20_25'] += $row['usia_20_25'];
			$totals['usia_26_30'] += $row['usia_26_30'];
			$totals['usia_31_35'] += $row['usia_31_35'];
			$totals['usia_36'] += $row['usia_36'];
		}

		$data = array(
			'selected_tahun' => $lap_tahun,
			'selected_wilayah' => $wilayah,
			'selected_wilayah_label' => $this->get_wilayah_label($wilayah),
			'rows' => $rows,
			'totals' => $totals,
		);

		$this->load->view('template/new_header');
		$this->load->view('template/new_sidebar');
		$this->load->view('v_faktor_penyebab_727', $data);
		$this->load->view('template/new_footer');
	}

	private function get_wilayah_label($wilayah)
	{
		if (empty($wilayah)) {
			return 'HSU';
		}

		$normalized = strtoupper(trim($wilayah));

		if ($normalized === 'SEMUA' || $normalized === 'SEMUA WILAYAH') {
			return 'HSU dan Balangan';
		}

		if ($normalized === 'HSU' || $normalized === 'HULU SUNGAI UTARA') {
			return 'HSU';
		}

		if ($normalized === 'BALANGAN') {
			return 'Balangan';
		}

		return $wilayah;
	}
}
