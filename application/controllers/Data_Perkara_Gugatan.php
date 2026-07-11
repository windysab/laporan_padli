<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data_Perkara_Gugatan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("M_data_perkara");
		$this->load->helper('url');
	}

	public function index()
	{
		$wilayah = validate_wilayah($this->input->post('wilayah'), 'HSU');
		$lap_bulan = validate_bulan($this->input->post('lap_bulan'));
		$lap_tahun = validate_tahun($this->input->post('lap_tahun'));
		$jenis_perkara = validate_jenis_perkara($this->input->post('jenis_perkara'), 'Cerai Gugat');
		$report_type = validate_report_type($this->input->post('report_type'));
		$tanggal_mulai = validate_tanggal($this->input->post('tanggal_mulai'), date('Y-m-01'));
		$tanggal_akhir = validate_tanggal($this->input->post('tanggal_akhir'), date('Y-m-d'));

		$data = [
			'selected_wilayah' => $wilayah,
			'selected_bulan' => $lap_bulan,
			'selected_tahun' => $lap_tahun,
			'selected_jenis' => $jenis_perkara,
			'selected_report' => $report_type,
			'selected_tanggal_mulai' => $tanggal_mulai,
			'selected_tanggal_akhir' => $tanggal_akhir,
		];

		switch ($report_type) {
			case 'summary':
				$data['datafilter'] = $this->M_data_perkara->data_summary_perceraian($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'yearly':
				$data['datafilter'] = $this->M_data_perkara->data_yearly_perceraian($lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'monthly':
				$data['datafilter'] = $this->M_data_perkara->data_monthly_perceraian($lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'comparison':
				$data['datafilter'] = $this->M_data_perkara->data_comparison_gugat_talak($lap_bulan, $lap_tahun, $wilayah);
				break;
			case 'faktor':
				$jenis_kelamin = validate_jenis_kelamin($this->input->post('jenis_kelamin'));
				$data['selected_gender'] = $jenis_kelamin;
				$data['datafilter'] = $this->M_data_perkara->data_faktor_perceraian($lap_tahun, $wilayah, $jenis_kelamin);
				break;
			case 'faktor_detail':
				$data['datafilter'] = $this->M_data_perkara->data_faktor_perceraian_detail($lap_bulan, $lap_tahun, $wilayah);
				break;
			case 'custom_range':
				$data['datafilter'] = $this->M_data_perkara->data_custom_range($tanggal_mulai, $tanggal_akhir, $jenis_perkara, $wilayah);
				break;
			case 'yearly_comparison':
				$data['datafilter'] = $this->M_data_perkara->data_yearly_comparison_gugat_talak($lap_tahun, $wilayah);
				break;
			default:
				$data['datafilter'] = $this->M_data_perkara->data_summary_perceraian($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah);
				break;
		}

		$data['jenis_perkara_list'] = $this->M_data_perkara->get_jenis_perkara_gugatan();
		view_load('v_data_perkara_gugatan', $data);
	}

	public function export_excel()
	{
		$wilayah = validate_wilayah($this->input->post('wilayah'), 'HSU');
		$lap_bulan = validate_bulan($this->input->post('lap_bulan'));
		$lap_tahun = validate_tahun($this->input->post('lap_tahun'));
		$jenis_perkara = validate_jenis_perkara($this->input->post('jenis_perkara'), 'Cerai Gugat');
		$report_type = validate_report_type($this->input->post('report_type'));
		$tanggal_mulai = validate_tanggal($this->input->post('tanggal_mulai'), date('Y-m-01'));
		$tanggal_akhir = validate_tanggal($this->input->post('tanggal_akhir'), date('Y-m-d'));

		switch ($report_type) {
			case 'summary':
				$data = $this->M_data_perkara->data_summary_perceraian($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'yearly':
				$data = $this->M_data_perkara->data_yearly_perceraian($lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'monthly':
				$data = $this->M_data_perkara->data_monthly_perceraian($lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'comparison':
				$data = $this->M_data_perkara->data_comparison_gugat_talak($lap_bulan, $lap_tahun, $wilayah);
				break;
			case 'faktor':
				$jenis_kelamin = $this->input->post('jenis_kelamin') ?: '';
				$data = $this->M_data_perkara->data_faktor_perceraian($lap_tahun, $wilayah, $jenis_kelamin);
				break;
			case 'faktor_detail':
				$data = $this->M_data_perkara->data_faktor_perceraian_detail($lap_bulan, $lap_tahun, $wilayah);
				break;
			case 'custom_range':
				$data = $this->M_data_perkara->data_custom_range($tanggal_mulai, $tanggal_akhir, $jenis_perkara, $wilayah);
				break;
			case 'yearly_comparison':
				$data = $this->M_data_perkara->data_yearly_comparison_gugat_talak($lap_tahun, $wilayah);
				break;
			default:
				$data = $this->M_data_perkara->data_summary_perceraian($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah);
				break;
		}

		$filename = 'Data_Perkara_Gugatan_' . $wilayah . '_' . $report_type . '_' . date('Y-m-d_H-i-s') . '.csv';

		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Pragma: public');

		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

		$headers = $this->_get_csv_headers($report_type);
		fputcsv($output, $headers, ';');
		$this->_add_csv_data($output, $data, $report_type);

		fclose($output);
		exit();
	}

	private function _get_csv_headers($report_type)
	{
		$maps = [
			'summary' => ['Kecamatan', 'Perkara Masuk', 'Perkara Putus', 'Perkara Telah BHT', 'Jumlah Akta Cerai'],
			'yearly' => ['Kecamatan', 'Perkara Masuk', 'Perkara Putus', 'Perkara Telah BHT', 'Jumlah Akta Cerai'],
			'monthly' => ['Kecamatan', 'Perkara Masuk', 'Perkara Putus', 'Perkara Telah BHT', 'Jumlah Akta Cerai'],
			'custom_range' => ['Kecamatan', 'Perkara Masuk', 'Perkara Putus', 'Perkara Telah BHT', 'Jumlah Akta Cerai'],
			'comparison' => ['Kecamatan', 'Cerai Gugat', 'Cerai Talak', 'Total'],
			'faktor' => ['Faktor Perceraian', 'Jumlah Kasus', 'Persentase'],
			'faktor_detail' => ['Faktor Perceraian', 'Jumlah Kasus', 'Persentase'],
			'yearly_comparison' => ['Tahun', 'Cerai Gugat', 'Cerai Talak', 'Total'],
		];
		return $maps[$report_type] ?? $maps['summary'];
	}

	private function _add_csv_data($output, $data, $report_type)
	{
		foreach ($data as $item) {
			switch ($report_type) {
				case 'summary':
				case 'yearly':
				case 'monthly':
				case 'custom_range':
					$row_data = [$item->KECAMATAN, $item->PERKARA_MASUK, $item->PERKARA_PUTUS, $item->PERKARA_TELAH_BHT, $item->JUMLAH_AKTA_CERAI];
					break;
				case 'comparison':
					$row_data = [$item->KECAMATAN, $item->CERAI_GUGAT, $item->CERAI_TALAK, $item->TOTAL];
					break;
				case 'faktor':
				case 'faktor_detail':
					$row_data = [
						$item->faktor_perceraian ?? $item->FAKTOR,
						$item->jumlah ?? $item->JUMLAH,
						(isset($item->persentase) ? $item->persentase : ($item->PERSENTASE ?? '0')) . '%'
					];
					break;
				case 'yearly_comparison':
					$row_data = [$item->TAHUN, $item->CERAI_GUGAT, $item->CERAI_TALAK, $item->TOTAL];
					break;
				default:
					$row_data = [$item->KECAMATAN, $item->PERKARA_MASUK, $item->PERKARA_PUTUS, $item->PERKARA_TELAH_BHT, $item->JUMLAH_AKTA_CERAI];
					break;
			}
			fputcsv($output, $row_data, ';');
		}
	}
}
