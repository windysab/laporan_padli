<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data_Permohonan extends CI_Controller
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
		$lap_bulan = validate_bulan($this->input->post('lap_bulan'));
		$lap_tahun = validate_tahun($this->input->post('lap_tahun'));
		$jenis_perkara = validate_jenis_perkara($this->input->post('jenis_perkara'), 'Dispensasi Kawin');
		$wilayah = validate_wilayah($this->input->post('wilayah'), 'Semua');
		$jenis_laporan = validate_jenis_laporan($this->input->post('jenis_laporan'));

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

		$data += [
			'jenis_perkara_list' => $this->M_data_permohonan->get_jenis_perkara_permohonan(),
			'summary' => $this->M_data_permohonan->get_summary_statistics($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah, $jenis_laporan),
			'selected_bulan' => $lap_bulan,
			'selected_tahun' => $lap_tahun,
			'selected_jenis_perkara' => $jenis_perkara,
			'selected_wilayah' => $wilayah,
			'selected_jenis_laporan' => $jenis_laporan,
		];

		view_load('v_permohonan', $data);
	}

	public function export_excel()
	{
		$lap_bulan = validate_bulan($this->input->post('lap_bulan'));
		$lap_tahun = validate_tahun($this->input->post('lap_tahun'));
		$jenis_perkara = validate_jenis_perkara($this->input->post('jenis_perkara'), 'Dispensasi Kawin');
		$wilayah = validate_wilayah($this->input->post('wilayah'), 'Semua');
		$jenis_laporan = validate_jenis_laporan($this->input->post('jenis_laporan'));

		$headers = ['No', 'Kecamatan'];
		if ($jenis_laporan === 'bulanan') {
			$headers[] = 'Sisa Bulan Lalu';
		} elseif ($jenis_laporan === 'tahunan') {
			$headers[] = 'Sisa Tahun Lalu';
		} else {
			$headers[] = 'Sisa Sebelumnya';
		}
		$headers = array_merge($headers, ['Perkara Masuk', 'Perkara Putus', 'Sisa Perkara', 'Persentase Penyelesaian']);

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

		$filename = 'Laporan_Data_Permohonan_' . date('Y-m-d_H-i-s') . '.csv';

		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
		fputcsv($output, $headers, ';');

		$no = 1;
		foreach ($data as $item) {
			$sisa_base = $jenis_laporan === 'tahunan' ? ($item->SISA_TAHUN_LALU ?? 0) : ($jenis_laporan === 'custom' ? ($item->SISA_SEBELUMNYA ?? 0) : ($item->SISA_BULAN_LALU ?? 0));
			$sisa_perkara = $sisa_base + $item->PERKARA_MASUK - $item->PERKARA_PUTUS;
			$total_perkara = $sisa_base + $item->PERKARA_MASUK;
			$persentase = $total_perkara > 0 ? round(($item->PERKARA_PUTUS / $total_perkara) * 100, 2) : 0;

			fputcsv($output, [$no++, $item->KECAMATAN, $sisa_base, $item->PERKARA_MASUK, $item->PERKARA_PUTUS, $sisa_perkara, $persentase . '%'], ';');
		}

		fclose($output);
		exit();
	}
}
