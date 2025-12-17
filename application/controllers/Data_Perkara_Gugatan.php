<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Data_Perkara_Gugatan extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("M_data_perkara_gugatan");
		$this->load->helper('url');
	}

	public function index()
	{
		// Get filter parameters
		$wilayah = $this->input->post('wilayah') ?: 'HSU'; // Default HSU
		$lap_bulan = $this->input->post('lap_bulan') ?: date('m');
		$lap_tahun = $this->input->post('lap_tahun') ?: date('Y');
		$jenis_perkara = $this->input->post('jenis_perkara') ?: 'Cerai Gugat';
		$report_type = $this->input->post('report_type') ?: 'summary';
		$tanggal_mulai = $this->input->post('tanggal_mulai');
		$tanggal_akhir = $this->input->post('tanggal_akhir');

		$data = array();
		$data['selected_wilayah'] = $wilayah;
		$data['selected_bulan'] = $lap_bulan;
		$data['selected_tahun'] = $lap_tahun;
		$data['selected_jenis'] = $jenis_perkara;
		$data['selected_report'] = $report_type;
		$data['selected_tanggal_mulai'] = $tanggal_mulai;
		$data['selected_tanggal_akhir'] = $tanggal_akhir;

		// Get data based on report type and region
		switch ($report_type) {
			case 'summary':
				$data['datafilter'] = $this->M_data_perkara_gugatan->data_summary_perceraian($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'yearly':
				$data['datafilter'] = $this->M_data_perkara_gugatan->data_yearly_perceraian($lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'monthly':
				$data['datafilter'] = $this->M_data_perkara_gugatan->data_monthly_perceraian($lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'comparison':
				$data['datafilter'] = $this->M_data_perkara_gugatan->data_comparison_gugat_talak($lap_bulan, $lap_tahun, $wilayah);
				break;
			case 'faktor':
				$jenis_kelamin = $this->input->post('jenis_kelamin') ?: '';
				$data['selected_gender'] = $jenis_kelamin;
				$data['datafilter'] = $this->M_data_perkara_gugatan->data_faktor_perceraian($lap_tahun, $wilayah, $jenis_kelamin);
				break;
			case 'faktor_detail':
				$data['datafilter'] = $this->M_data_perkara_gugatan->data_faktor_perceraian_detail($lap_bulan, $lap_tahun, $wilayah);
				break;
			case 'custom_range':
				$data['datafilter'] = $this->M_data_perkara_gugatan->data_custom_range($tanggal_mulai, $tanggal_akhir, $jenis_perkara, $wilayah);
				break;
			case 'yearly_comparison':
				$data['datafilter'] = $this->M_data_perkara_gugatan->data_yearly_comparison_gugat_talak($lap_tahun, $wilayah);
				break;
			default:
				$data['datafilter'] = $this->M_data_perkara_gugatan->data_summary_perceraian($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah);
		}

		// Get dropdown data for view
		$data['jenis_perkara_list'] = $this->M_data_perkara_gugatan->get_jenis_perkara_gugatan();

		// Load views
		$this->load->view('template/new_header');
		$this->load->view('template/new_sidebar');
		$this->load->view('v_data_perkara_gugatan', $data);
		$this->load->view('template/new_footer');
	}

	// Export functions
	public function export_excel()
	{
		$wilayah = $this->input->post('wilayah') ?: 'HSU';
		$lap_bulan = $this->input->post('lap_bulan') ?: date('m');
		$lap_tahun = $this->input->post('lap_tahun') ?: date('Y');
		$jenis_perkara = $this->input->post('jenis_perkara') ?: 'Cerai Gugat';
		$report_type = $this->input->post('report_type') ?: 'summary';
		$tanggal_mulai = $this->input->post('tanggal_mulai');
		$tanggal_akhir = $this->input->post('tanggal_akhir');

		// Get data for export
		switch ($report_type) {
			case 'summary':
				$data = $this->M_data_perkara_gugatan->data_summary_perceraian($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'yearly':
				$data = $this->M_data_perkara_gugatan->data_yearly_perceraian($lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'monthly':
				$data = $this->M_data_perkara_gugatan->data_monthly_perceraian($lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'comparison':
				$data = $this->M_data_perkara_gugatan->data_comparison_gugat_talak($lap_bulan, $lap_tahun, $wilayah);
				break;
			case 'faktor':
				$jenis_kelamin = $this->input->post('jenis_kelamin') ?: '';
				$data = $this->M_data_perkara_gugatan->data_faktor_perceraian($lap_tahun, $wilayah, $jenis_kelamin);
				break;
			case 'faktor_detail':
				$data = $this->M_data_perkara_gugatan->data_faktor_perceraian_detail($lap_bulan, $lap_tahun, $wilayah);
				break;
			case 'custom_range':
				$data = $this->M_data_perkara_gugatan->data_custom_range($tanggal_mulai, $tanggal_akhir, $jenis_perkara, $wilayah);
				break;
			case 'yearly_comparison':
				$data = $this->M_data_perkara_gugatan->data_yearly_comparison_gugat_talak($lap_tahun, $wilayah);
				break;
			default:
				$data = $this->M_data_perkara_gugatan->data_summary_perceraian($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah);
		}

		// Generate filename
		$filename = 'Data_Perkara_Gugatan_' . $wilayah . '_' . $report_type . '_' . date('Y-m-d_H-i-s') . '.csv';

		// Set CSV headers
		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Pragma: public');

		// Open output stream
		$output = fopen('php://output', 'w');

		// Add BOM for proper UTF-8 encoding in Excel
		fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

		// Set headers based on report type
		$headers = $this->_get_csv_headers($report_type);
		fputcsv($output, $headers, ';');

		// Add data
		$this->_add_csv_data($output, $data, $report_type);

		// Close output stream
		fclose($output);
		exit();
	}

	private function _get_csv_headers($report_type)
	{
		switch ($report_type) {
			case 'summary':
			case 'yearly':
			case 'monthly':
			case 'custom_range':
				return array('Kecamatan', 'Perkara Masuk', 'Perkara Putus', 'Perkara Telah BHT', 'Jumlah Akta Cerai');
			case 'comparison':
				return array('Kecamatan', 'Cerai Gugat', 'Cerai Talak', 'Total');
			case 'faktor':
			case 'faktor_detail':
				return array('Faktor Perceraian', 'Jumlah Kasus', 'Persentase');
			case 'yearly_comparison':
				return array('Tahun', 'Cerai Gugat', 'Cerai Talak', 'Total');
			default:
				return array('Kecamatan', 'Perkara Masuk', 'Perkara Putus', 'Perkara Telah BHT', 'Jumlah Akta Cerai');
		}
	}

	private function _add_csv_data($output, $data, $report_type)
	{
		foreach ($data as $item) {
			switch ($report_type) {
				case 'summary':
				case 'yearly':
				case 'monthly':
				case 'custom_range':
					$row_data = array(
						$item->KECAMATAN,
						$item->PERKARA_MASUK,
						$item->PERKARA_PUTUS,
						$item->PERKARA_TELAH_BHT,
						$item->JUMLAH_AKTA_CERAI
					);
					break;
				case 'comparison':
					$row_data = array(
						$item->KECAMATAN,
						$item->CERAI_GUGAT,
						$item->CERAI_TALAK,
						$item->TOTAL
					);
					break;
				case 'faktor':
				case 'faktor_detail':
					$row_data = array(
						isset($item->faktor_perceraian) ? $item->faktor_perceraian : $item->FAKTOR,
						isset($item->jumlah) ? $item->jumlah : $item->JUMLAH,
						(isset($item->persentase) ? $item->persentase : (isset($item->PERSENTASE) ? $item->PERSENTASE : '0')) . '%'
					);
					break;
				case 'yearly_comparison':
					$row_data = array(
						$item->TAHUN,
						$item->CERAI_GUGAT,
						$item->CERAI_TALAK,
						$item->TOTAL
					);
					break;
				default:
					$row_data = array(
						$item->KECAMATAN,
						$item->PERKARA_MASUK,
						$item->PERKARA_PUTUS,
						$item->PERKARA_TELAH_BHT,
						$item->JUMLAH_AKTA_CERAI
					);
			}
			fputcsv($output, $row_data, ';');
		}
	}
}
