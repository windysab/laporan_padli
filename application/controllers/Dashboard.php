<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

	public function index()
	{
		$this->load->model('Dashboard_model');
		$this->load->model('M_notifikasi_perkara');
		$currentYear = date('Y');
		$currentMonth = date('m');

		// Get enhanced dashboard data
		$data['statistics'] = $this->Dashboard_model->get_statistics($currentYear);
		$data['daily_statistics'] = $this->Dashboard_model->get_daily_statistics();
		$data['monthly_statistics'] = $this->Dashboard_model->get_monthly_statistics();
		$data['yearly_statistics'] = $this->Dashboard_model->get_yearly_statistics();
		$data['yearly_growth'] = $this->Dashboard_model->get_yearly_growth();
		$data['case_types'] = $this->Dashboard_model->get_case_types();
		$data['monthly_classification'] = $this->Dashboard_model->get_monthly_case_classification();
		$data['kinerja_pn'] = $this->Dashboard_model->get_kinerja_pn();
		$data['daily_trend'] = $this->Dashboard_model->get_daily_trend();

		// Notifikasi perkara
		$data['notifikasi'] = $this->M_notifikasi_perkara->get_notifikasi_summary();
		$data['perkara_lewat_batas'] = $this->M_notifikasi_perkara->get_perkara_lewat_batas(10);
		$data['perkara_mendekati_batas'] = $this->M_notifikasi_perkara->get_perkara_mendekati_batas(10);
		$data['perkara_bht_belum_akta'] = $this->M_notifikasi_perkara->get_perkara_bht_belum_akta(10);

		$data['currentYear'] = $currentYear;
		$data['currentMonth'] = $currentMonth;
		$data['currentMonthName'] = date('F');

		$this->load->view('template/new_header');
		$this->load->view('template/new_sidebar');
		$this->load->view('dashboard', $data);
		$this->load->view('template/new_footer');
	}

	public function get_monthly_data()
	{
		$this->load->model('Dashboard_model');
		$year = validate_tahun($this->input->post('year'));

		$data = [
			'monthly_classification' => $this->Dashboard_model->get_monthly_case_classification(),
			'yearly_growth' => $this->Dashboard_model->get_yearly_growth(),
			'case_types' => $this->Dashboard_model->get_case_types()
		];

		header('Content-Type: application/json');
		echo json_encode($data);
	}

	// AJAX endpoint untuk update daily statistics
	public function get_daily_statistics()
	{
		$this->load->model('Dashboard_model');
		$data = $this->Dashboard_model->get_daily_statistics();

		header('Content-Type: application/json');
		echo json_encode($data);
	}

	// Simple AdminLTE Dashboard
	public function simple()
	{
		$this->load->model('Dashboard_model');
		$currentYear = date('Y');
		$currentMonth = date('m');

		// Get enhanced dashboard data
		$data['statistics'] = $this->Dashboard_model->get_statistics($currentYear);
		$data['daily_statistics'] = $this->Dashboard_model->get_daily_statistics();
		$data['monthly_statistics'] = $this->Dashboard_model->get_monthly_statistics();
		$data['yearly_statistics'] = $this->Dashboard_model->get_yearly_statistics();
		$data['yearly_growth'] = $this->Dashboard_model->get_yearly_growth();
		$data['case_types'] = $this->Dashboard_model->get_case_types();
		$data['monthly_classification'] = $this->Dashboard_model->get_monthly_case_classification();
		$data['kinerja_pn'] = $this->Dashboard_model->get_kinerja_pn();
		$data['daily_trend'] = $this->Dashboard_model->get_daily_trend();

		$data['currentYear'] = $currentYear;
		$data['currentMonth'] = $currentMonth;
		$data['currentMonthName'] = date('F');

		$this->load->view('template/new_header');
		$this->load->view('template/new_sidebar');
		$this->load->view('dashboard_simple', $data);
		$this->load->view('template/new_footer');
	}

	// Debug method untuk test minutasi query
	public function debug_minutasi()
	{
		$this->load->model('Dashboard_model');
		$this->Dashboard_model->debug_minutasi_query();
	}
}
