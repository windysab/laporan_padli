<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delegasi_k extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("M_delegasi");
		$this->load->helper('url');
	}

	public function index()
	{
		$lap_bulan = $this->input->post('lap_bulan');
		$lap_tahun = $this->input->post('lap_tahun');
		$data['datafilter'] = $this->M_delegasi->delegasi_k($lap_bulan, $lap_tahun);
		view_load('v_delegasi_k', $data);
	}
}
