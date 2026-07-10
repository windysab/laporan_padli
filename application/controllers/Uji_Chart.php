<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uji_Chart extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('M_Uji_chart');
	}

	function index()
	{
		$data['hasil'] = $this->M_Uji_chart->uji_Chart();
		view_load('v_uji_chart', $data);
	}
}
