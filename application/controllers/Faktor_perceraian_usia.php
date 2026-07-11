<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Faktor_perceraian_usia extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_faktor_perceraian');
	}

	public function index()
	{
		$lap_tahun = $this->input->post('lap_tahun') ?: date('Y');
		$wilayah = $this->input->post('wilayah') ?: 'Amuntai';
		$wilayah_param = wilayah_map($wilayah);

		$datafilter = $this->M_faktor_perceraian->get_data($lap_tahun, $wilayah_param);
		$grand_total = 0;
		if (!empty($datafilter)) {
			foreach ($datafilter as $row) {
				if ($row->faktor != 'Jumlah') {
					$grand_total += $row->usia_16_19 + $row->usia_20_25 + $row->usia_26_30 + $row->usia_31_35 + $row->usia_36;
				}
			}
		}

		$data = [
			'datafilter' => $datafilter,
			'selected_tahun' => $lap_tahun,
			'selected_wilayah' => $wilayah,
			'grand_total' => $grand_total,
		];

		view_load('v_faktor_perceraian_usia', $data);
	}
}
