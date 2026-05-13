<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Data_Permohonan extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model("M_data_permohonan");
		$this->load->helper('url');
		$this->load->helper('text');
		$this->load->helper('date');
	}

	public function index()
	{
		// Get available jenis perkara for dropdown
		$data['jenis_perkara_list'] = $this->M_data_permohonan->get_jenis_perkara_permohonan();

		// Validate input parameters
		$lap_bulan = validate_bulan($this->input->post('lap_bulan'));
		$lap_tahun = validate_tahun($this->input->post('lap_tahun'));
		$jenis_perkara = validate_jenis_perkara($this->input->post('jenis_perkara'), 'Dispensasi Kawin');
		$wilayah = validate_wilayah($this->input->post('wilayah'), 'Semua');
		$jenis_laporan = validate_jenis_laporan($this->input->post('jenis_laporan'));

		// Get data based on report type
		switch ($jenis_laporan) {
			case 'tahunan':
				$data['datafilter'] = $this->M_data_permohonan->data_permohonan_tahunan($lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'custom':
				$tanggal_mulai = validate_tanggal($this->input->post('tanggal_mulai'), date('Y-m-01'));
				$tanggal_akhir = validate_tanggal($this->input->post('tanggal_akhir'), date('Y-m-t'));
				$data['datafilter'] = $this->M_data_permohonan->data_permohonan_custom($tanggal_mulai, $tanggal_akhir, $jenis_perkara, $wilayah);
				break;
			default: // bulanan
				$data['datafilter'] = $this->M_data_permohonan->data_permohonan($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah);
				break;
		}

		// Get summary statistics
		$data['summary'] = $this->M_data_permohonan->get_summary_statistics($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah, $jenis_laporan);

		// Pass selected values to view
		$data['selected_bulan'] = $lap_bulan;
		$data['selected_tahun'] = $lap_tahun;
		$data['selected_jenis_perkara'] = $jenis_perkara;
		$data['selected_wilayah'] = $wilayah;
		$data['selected_jenis_laporan'] = $jenis_laporan;

		$this->load->view('template/new_header');
		$this->load->view('template/new_sidebar');
		$this->load->view('v_permohonan', $data);
		$this->load->view('template/new_footer');
	}

	public function export_excel()
	{
		$lap_bulan = validate_bulan($this->input->post('lap_bulan'));
		$lap_tahun = validate_tahun($this->input->post('lap_tahun'));
		$jenis_perkara = validate_jenis_perkara($this->input->post('jenis_perkara'), 'Dispensasi Kawin');
		$wilayah = validate_wilayah($this->input->post('wilayah'), 'Semua');
		$jenis_laporan = validate_jenis_laporan($this->input->post('jenis_laporan'));

		// Set headers based on report type
		$headers = array('No', 'Kecamatan');
		if ($jenis_laporan === 'bulanan') {
			$headers[] = 'Sisa Bulan Lalu';
		} elseif ($jenis_laporan === 'tahunan') {
			$headers[] = 'Sisa Tahun Lalu';
		} elseif ($jenis_laporan === 'custom') {
			$headers[] = 'Sisa Sebelumnya';
		}
		$headers = array_merge($headers, array('Perkara Masuk', 'Perkara Putus', 'Sisa Perkara', 'Persentase Penyelesaian'));

		// Get data
		switch ($jenis_laporan) {
			case 'tahunan':
				$data = $this->M_data_permohonan->data_permohonan_tahunan($lap_tahun, $jenis_perkara, $wilayah);
				break;
			case 'custom':
				$tanggal_mulai = $this->input->post('tanggal_mulai') ?: date('Y-m-01');
				$tanggal_akhir = $this->input->post('tanggal_akhir') ?: date('Y-m-t');
				$data = $this->M_data_permohonan->data_permohonan_custom($tanggal_mulai, $tanggal_akhir, $jenis_perkara, $wilayah);
				break;
			default:
				$data = $this->M_data_permohonan->data_permohonan($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah);
				break;
		}

		// Generate filename with timestamp
		$filename = 'Laporan_Data_Permohonan_' . date('Y-m-d_H-i-s') . '.csv';

		// Set CSV headers
		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Pragma: public');

		// Open output stream
		$output = fopen('php://output', 'w');

		// Add BOM for proper UTF-8 encoding in Excel
		fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

		// Write headers
		fputcsv($output, $headers, ';'); // Using semicolon as delimiter for better Excel compatibility

		// Write data rows
		$no = 1;
		foreach ($data as $item) {
			// Determine sisa base based on report type
			$sisa_base = 0;
			if ($jenis_laporan === 'tahunan') {
				$sisa_base = isset($item->SISA_TAHUN_LALU) ? $item->SISA_TAHUN_LALU : 0;
			} elseif ($jenis_laporan === 'custom') {
				$sisa_base = isset($item->SISA_SEBELUMNYA) ? $item->SISA_SEBELUMNYA : 0;
			} else { // bulanan
				$sisa_base = isset($item->SISA_BULAN_LALU) ? $item->SISA_BULAN_LALU : 0;
			}

			$sisa_perkara = $sisa_base + $item->PERKARA_MASUK - $item->PERKARA_PUTUS;
			$total_perkara = $sisa_base + $item->PERKARA_MASUK;

			// Calculate percentage
			$persentase = ($total_perkara > 0) ? round(($item->PERKARA_PUTUS / $total_perkara) * 100, 2) : 0;

			// Prepare row data
			$row_data = array(
				$no++,
				$item->KECAMATAN,
				$sisa_base,
				$item->PERKARA_MASUK,
				$item->PERKARA_PUTUS,
				$sisa_perkara,
				$persentase . '%'
			);

			// Write row
			fputcsv($output, $row_data, ';');
		}

		// Close output stream
		fclose($output);
		exit();
	}
}
