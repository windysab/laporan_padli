<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_Perceraian_hsu extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("M_data_perceraian_hsu");
		$this->load->helper('url');
	}

	public function index()
	{
		$lap_bulan = validate_bulan($this->input->post('lap_bulan'));
		$lap_tahun = validate_tahun($this->input->post('lap_tahun'));
		$data['datafilter'] = $this->M_data_perceraian_hsu->data_perceraian_hsu($lap_bulan, $lap_tahun);
		$data['wilayah'] = 'hsu';
		view_load('v_perceraian', $data);
	}
}
