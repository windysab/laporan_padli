<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan_ecourt extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_ecourt');
		$this->load->helper('url');
	}

	public function index()
	{
		$lap_tahun = validate_tahun($this->input->post('lap_tahun'));
		$lap_bulan = $this->input->post('lap_bulan') ?: 'semua';
		if ($lap_bulan !== 'semua') {
			$lap_bulan = validate_bulan($lap_bulan);
		}

		// Summary data
		if ($lap_bulan === 'semua') {
			$data['summary'] = $this->M_ecourt->get_data_tahunan($lap_tahun);
		} else {
			$data['summary'] = $this->M_ecourt->get_data_bulanan($lap_tahun, $lap_bulan);
		}

		// Breakdown per bulan (chart)
		$data['breakdown_bulanan'] = $this->M_ecourt->get_breakdown_per_bulan($lap_tahun);

		// Breakdown per jenis perkara
		$data['breakdown_jenis'] = $this->M_ecourt->get_breakdown_per_jenis($lap_tahun, $lap_bulan);

		// Tren tahunan
		$data['tren_tahunan'] = $this->M_ecourt->get_tren_tahunan();

		// Pass selected values
		$data['selected_tahun'] = $lap_tahun;
		$data['selected_bulan'] = $lap_bulan;

		$this->load->view('template/new_header');
		$this->load->view('template/new_sidebar');
		$this->load->view('v_laporan_ecourt', $data);
		$this->load->view('template/new_footer');
	}

	/**
	 * Export CSV
	 */
	public function export_csv()
	{
		$lap_tahun = validate_tahun($this->input->post('lap_tahun'));
		$lap_bulan = $this->input->post('lap_bulan') ?: 'semua';
		if ($lap_bulan !== 'semua') {
			$lap_bulan = validate_bulan($lap_bulan);
		}

		$breakdown = $this->M_ecourt->get_breakdown_per_bulan($lap_tahun);
		$breakdown_jenis = $this->M_ecourt->get_breakdown_per_jenis($lap_tahun, $lap_bulan);

		$filename = 'Laporan_ECourt_' . $lap_tahun . '_' . date('Y-m-d') . '.csv';

		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

		// Header section
		fputcsv($output, array('LAPORAN E-COURT VS NON E-COURT'), ';');
		fputcsv($output, array('Pengadilan Agama Amuntai - Tahun ' . $lap_tahun), ';');
		fputcsv($output, array(''), ';');

		// Per bulan
		fputcsv($output, array('BREAKDOWN PER BULAN'), ';');
		fputcsv($output, array('Bulan', 'Total Perkara', 'E-Court', 'Non E-Court', '% E-Court'), ';');

		$nama_bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
		foreach ($breakdown as $row) {
			fputcsv($output, array(
				$nama_bulan[$row->bulan],
				$row->total_perkara,
				$row->total_ecourt,
				$row->total_non_ecourt,
				$row->persen_ecourt . '%'
			), ';');
		}

		fputcsv($output, array(''), ';');

		// Per jenis perkara
		fputcsv($output, array('BREAKDOWN PER JENIS PERKARA'), ';');
		fputcsv($output, array('Jenis Perkara', 'Total Perkara', 'E-Court', 'Non E-Court', '% E-Court'), ';');
		foreach ($breakdown_jenis as $row) {
			fputcsv($output, array(
				$row->jenis_perkara_nama,
				$row->total_perkara,
				$row->total_ecourt,
				$row->total_non_ecourt,
				$row->persen_ecourt . '%'
			), ';');
		}

		fclose($output);
		exit();
	}
}
