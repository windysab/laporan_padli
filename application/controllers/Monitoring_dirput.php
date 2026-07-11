<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Monitoring_dirput extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_monitoring');
		$this->load->helper('url');
	}

	public function index()
	{
		$lap_bulan = $this->input->post('lap_bulan') ?: date('m');
		$lap_tahun = $this->input->post('lap_tahun') ?: date('Y');
		$jenis_perkara = $this->input->post('jenis_perkara') ?: 'semua';
		$mode = $this->input->post('mode') ?: $this->input->get('mode') ?: 'belum';

		if ($mode === 'upload_gagal') {
			$data['datafilter'] = $this->M_monitoring->get_upload_gagal($lap_tahun, $lap_bulan, $jenis_perkara);
		} elseif ($mode === 'sudah') {
			$mode = 'sudah';
			$data['datafilter'] = $this->M_monitoring->get_sudah_publish_anonim($lap_tahun, $lap_bulan, $jenis_perkara);
		} else {
			$mode = 'belum';
			$data['datafilter'] = $this->M_monitoring->get_belum_publish_anonim($lap_tahun, $lap_bulan, $jenis_perkara);
		}

		$data += [
			'summary' => $this->M_monitoring->get_summary($lap_tahun, $lap_bulan, $jenis_perkara),
			'jenis_perkara_list' => $this->M_monitoring->get_jenis_perkara_putusan(),
			'selected_bulan' => $lap_bulan,
			'selected_tahun' => $lap_tahun,
			'selected_jenis_perkara' => $jenis_perkara,
			'selected_mode' => $mode,
		];

		view_load('v_monitoring_dirput', $data);
	}

	public function export_excel()
	{
		$lap_bulan = $this->input->post('lap_bulan') ?: date('m');
		$lap_tahun = $this->input->post('lap_tahun') ?: date('Y');
		$jenis_perkara = $this->input->post('jenis_perkara') ?: 'semua';
		$mode = $this->input->post('mode') ?: 'belum';

		if ($mode === 'upload_gagal') {
			$data = $this->M_monitoring->get_upload_gagal($lap_tahun, $lap_bulan, $jenis_perkara);
			$filename = 'Upload_Dirput_Gagal_' . date('Y-m-d_H-i-s') . '.csv';
			$headers = ['No', 'Nomor Perkara', 'Jenis Perkara', 'Pihak 1', 'Pihak 2', 'Tanggal Putusan', 'Tanggal BHT', 'Status Putusan', 'Hari Sejak Putusan', 'Keterangan'];
		} elseif ($mode === 'sudah') {
			$data = $this->M_monitoring->get_sudah_publish_anonim($lap_tahun, $lap_bulan, $jenis_perkara);
			$filename = 'Putusan_Sudah_Ada_Dirput_Anonim_' . date('Y-m-d_H-i-s') . '.csv';
			$headers = ['No', 'Nomor Perkara', 'Jenis Perkara', 'Pihak 1', 'Pihak 2', 'Tanggal Putusan', 'Tanggal Publish', 'Filename', 'Published', 'Link Dirput'];
		} else {
			$data = $this->M_monitoring->get_belum_publish_anonim($lap_tahun, $lap_bulan, $jenis_perkara);
			$filename = 'Putusan_Belum_Ada_Dirput_Anonim_' . date('Y-m-d_H-i-s') . '.csv';
			$headers = ['No', 'Nomor Perkara', 'Jenis Perkara', 'Pihak 1', 'Pihak 2', 'Tanggal Putusan', 'Tanggal BHT', 'Status Putusan', 'Hari Sejak Putusan'];
		}

		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
		fputcsv($output, $headers, ';');

		$no = 1;
		foreach ($data as $item) {
			if ($mode === 'upload_gagal') {
				$row = [$no++, $item->nomor_perkara, $item->jenis_perkara_nama, $item->pihak_1, $item->pihak_2, $item->tanggal_putusan, $item->tanggal_bht, $item->status_putusan_nama, $item->hari_sejak_putusan, $item->keterangan];
			} elseif ($mode === 'sudah') {
				$row = [$no++, $item->nomor_perkara, $item->jenis_perkara_nama, $item->pihak_1, $item->pihak_2, $item->tanggal_putusan, $item->tanggal_publish, $item->filename, $item->published == 1 ? 'Ya' : 'Belum', $item->link_dirput];
			} else {
				$row = [$no++, $item->nomor_perkara, $item->jenis_perkara_nama, $item->pihak_1, $item->pihak_2, $item->tanggal_putusan, $item->tanggal_bht, $item->status_putusan_nama, $item->hari_sejak_putusan];
			}
			fputcsv($output, $row, ';');
		}

		fclose($output);
		exit();
	}
}
