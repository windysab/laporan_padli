<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Validasi_akta_cerai extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_validasi_akta_cerai');
		$this->load->helper('url');
		$this->load->helper('text');
	}

	public function index()
	{
		$mode = $this->input->get('mode') ?: $this->input->post('mode') ?: 'belum_lengkap';
		$lap_tahun = $this->input->post('lap_tahun') ?: date('Y');
		$batas_hari = $this->input->post('batas_hari') ?: 7;
		$jenis_perkara = $this->input->post('jenis_perkara') ?: 'semua';
		$nomor_akta = trim($this->input->post('nomor_akta') ?: '');

		if ($mode === 'cek_nomor') {
			$data['hasil_cek'] = $nomor_akta !== '' ? $this->M_validasi_akta_cerai->get_by_nomor_akta($nomor_akta) : null;
			$data['datafilter'] = array();
		} else if ($mode === 'terlambat') {
			$data['datafilter'] = $this->M_validasi_akta_cerai->get_terlambat($lap_tahun, $batas_hari, $jenis_perkara);
		} else {
			$mode = 'belum_lengkap';
			$data['datafilter'] = $this->M_validasi_akta_cerai->get_belum_lengkap($lap_tahun, $jenis_perkara);
		}

		$data['summary'] = $this->M_validasi_akta_cerai->get_summary($lap_tahun, $batas_hari, $jenis_perkara);
		$data['jenis_perkara_list'] = $this->M_validasi_akta_cerai->get_jenis_perkara_perceraian();
		$data['selected_mode'] = $mode;
		$data['selected_tahun'] = $lap_tahun;
		$data['selected_batas_hari'] = $batas_hari;
		$data['selected_jenis_perkara'] = $jenis_perkara;
		$data['selected_nomor_akta'] = $nomor_akta;

		$this->load->view('template/new_header');
		$this->load->view('template/new_sidebar');
		$this->load->view('v_validasi_akta_cerai', $data);
		$this->load->view('template/new_footer');
	}

	public function export_excel()
	{
		$mode = $this->input->post('mode') ?: 'belum_lengkap';
		$lap_tahun = $this->input->post('lap_tahun') ?: date('Y');
		$batas_hari = $this->input->post('batas_hari') ?: 7;
		$jenis_perkara = $this->input->post('jenis_perkara') ?: 'semua';
		$nomor_akta = trim($this->input->post('nomor_akta') ?: '');

		if ($mode === 'cek_nomor') {
			$hasil = $nomor_akta !== '' ? $this->M_validasi_akta_cerai->get_by_nomor_akta($nomor_akta) : null;
			$data = $hasil ? array($hasil) : array();
			$filename = 'Cek_Nomor_Akta_Cerai_' . date('Y-m-d_H-i-s') . '.csv';
			$headers = array('Status Validasi', 'Nomor Akta', 'No Seri', 'Tanggal Akta', 'Nomor Perkara', 'Jenis Perkara', 'Penggugat', 'Tergugat', 'Tanggal Putusan', 'Tanggal BHT');
		} else if ($mode === 'terlambat') {
			$data = $this->M_validasi_akta_cerai->get_terlambat($lap_tahun, $batas_hari, $jenis_perkara);
			$filename = 'Perkara_Akta_Cerai_Terlambat_' . date('Y-m-d_H-i-s') . '.csv';
			$headers = array('No', 'Nomor Perkara', 'Jenis Perkara', 'Penggugat', 'Tergugat', 'Tanggal Putusan', 'Tanggal BHT', 'Tanggal Akta Cerai', 'Selisih Hari', 'Status');
		} else {
			$data = $this->M_validasi_akta_cerai->get_belum_lengkap($lap_tahun, $jenis_perkara);
			$filename = 'Validasi_Data_Akta_Cerai_' . date('Y-m-d_H-i-s') . '.csv';
			$headers = array('No', 'Nomor Perkara', 'Jenis Perkara', 'Penggugat', 'Tergugat', 'Tanggal Putusan', 'Tanggal BHT', 'Nomor Akta', 'No Seri', 'Tanggal Akta', 'Catatan Validasi');
		}

		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		header('Pragma: public');

		$output = fopen('php://output', 'w');
		fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
		fputcsv($output, $headers, ';');

		$no = 1;
		foreach ($data as $item) {
			if ($mode === 'cek_nomor') {
				$row = array(
					'VALID / ASLI',
					$item->nomor_akta_cerai,
					$item->no_seri_akta_cerai,
					$item->tgl_akta_cerai,
					$item->nomor_perkara,
					$item->jenis_perkara_nama,
					$item->penggugat,
					$item->tergugat,
					$item->tanggal_putusan,
					$item->tanggal_bht
				);
			} else if ($mode === 'terlambat') {
				$row = array(
					$no++,
					$item->nomor_perkara,
					$item->jenis_perkara_nama,
					$item->penggugat,
					$item->tergugat,
					$item->tanggal_putusan,
					$item->tanggal_bht,
					$item->tgl_akta_cerai,
					$item->selisih_hari,
					$item->status_keterlambatan
				);
			} else {
				$row = array(
					$no++,
					$item->nomor_perkara,
					$item->jenis_perkara_nama,
					$item->penggugat,
					$item->tergugat,
					$item->tanggal_putusan,
					$item->tanggal_bht,
					$item->nomor_akta_cerai,
					$item->no_seri_akta_cerai,
					$item->tgl_akta_cerai,
					$item->catatan_validasi
				);
			}

			fputcsv($output, $row, ';');
		}

		fclose($output);
		exit();
	}
}
