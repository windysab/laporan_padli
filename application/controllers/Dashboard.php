<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Dashboard_model');
	}

	public function index()
	{
		$currentYear = date('Y');
		$currentMonth = date('m');
		view_load('dashboard', $this->_dashboardData($currentYear, $currentMonth));
	}

	public function simple()
	{
		$currentYear = date('Y');
		$currentMonth = date('m');
		view_load('dashboard_simple', $this->_dashboardData($currentYear, $currentMonth));
	}

	public function get_monthly_data()
	{
		json_output([
			'monthly_classification' => $this->Dashboard_model->get_monthly_case_classification(),
			'yearly_growth' => $this->Dashboard_model->get_yearly_growth(),
			'case_types' => $this->Dashboard_model->get_case_types()
		]);
	}

	public function get_daily_statistics()
	{
		json_output($this->Dashboard_model->get_daily_statistics());
	}

	public function debug_minutasi()
	{
		$this->Dashboard_model->debug_minutasi_query();
	}

	private function _dashboardData(string $currentYear, string $currentMonth): array
	{
		return [
			'statistics' => $this->Dashboard_model->get_statistics($currentYear),
			'daily_statistics' => $this->Dashboard_model->get_daily_statistics(),
			'monthly_statistics' => $this->Dashboard_model->get_monthly_statistics(),
			'yearly_statistics' => $this->Dashboard_model->get_yearly_statistics(),
			'yearly_growth' => $this->Dashboard_model->get_yearly_growth(),
			'case_types' => $this->Dashboard_model->get_case_types(),
			'monthly_classification' => $this->Dashboard_model->get_monthly_case_classification(),
			'kinerja_pn' => $this->Dashboard_model->get_kinerja_pn(),
			'daily_trend' => $this->Dashboard_model->get_daily_trend(),
			'currentYear' => $currentYear,
			'currentMonth' => $currentMonth,
			'currentMonthName' => date('F'),
		];
	}
}
