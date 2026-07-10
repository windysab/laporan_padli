<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function index()
	{
		$this->load->model('M_dashboard');

		$currentYear  = date('Y');
		$currentMonth = date('m');
		$currentMonthName = date('F');

		$data['currentYear']      = $currentYear;
		$data['currentMonth']     = $currentMonth;
		$data['currentMonthName'] = $currentMonthName;

		// Yearly counts
		$data['perkara_count']   = $this->M_dashboard->countPerkaraTahun($currentYear);
		$data['putus_count']     = $this->M_dashboard->countPutusTahun($currentYear);
		$data['minutasi_count']  = $this->M_dashboard->countMinutasiTahun($currentYear);
		$data['sisa_count']      = $this->M_dashboard->countSisaTahun($currentYear);

		// Monthly chart data: [diterima, putus, minutasi, sisa]
		$data['chart_data'] = [
			$this->M_dashboard->countPerkaraBulan($currentYear, $currentMonth),
			$this->M_dashboard->countPutusBulan($currentYear, $currentMonth),
			$this->M_dashboard->countMinutasiBulan($currentYear, $currentMonth),
			$this->M_dashboard->countSisaBulan($currentYear, $currentMonth),
		];

		$this->load->view('template/new_header');
		$this->load->view('template/new_sidebar');
		$this->load->view('dashboard', $data);
		$this->load->view('template/new_footer');
	}
	
}
