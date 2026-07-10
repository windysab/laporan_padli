<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lipa1 extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("M_lipa1");
	}

	public function index()
	{
		['jenis_perkara' => $jenis_perkara, 'lap_bulan' => $lap_bulan, 'lap_tahun' => $lap_tahun] = $this->_getParams();
		$data['datafilter'] = $this->M_lipa1->getData($lap_tahun, $lap_bulan, $jenis_perkara);
		view_load('v_lipa1', $data);
	}

	public function generateExcelDocument()
	{
		ini_set('max_execution_time', '60');
		ini_set('memory_limit', '512M');

		['jenis_perkara' => $jenis_perkara, 'lap_bulan' => $lap_bulan, 'lap_tahun' => $lap_tahun] = $this->_getParams();
		$data = $this->M_lipa1->getData($lap_tahun, $lap_bulan, $jenis_perkara);

		$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(__DIR__ . '/../../template/1. Lipa 1 Keadan Perkara.xlsx');
		$sheet = $spreadsheet->getActiveSheet();

		$row = 13;
		foreach ($data as $item) {
			$sheet->insertNewRowBefore($row, 1);
			$this->_populateExcelRow($sheet, $row, $item);
			$row++;
		}

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		$filename = '1. Lipa 1 Keadan Perkara  ' . $lap_bulan . '-' . $lap_tahun . '.xlsx';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
	}

	private function _getParams(): array
	{
		return [
			'jenis_perkara' => validate_perkara_pattern($this->input->post('jenis_perkara'), 'Pdt.G'),
			'lap_bulan' => validate_bulan($this->input->post('lap_bulan')),
			'lap_tahun' => validate_tahun($this->input->post('lap_tahun')),
		];
	}

	private function _populateExcelRow($sheet, int $row, $item): void
	{
		$sheet->setCellValue('B' . $row, $item->nomor_perkara);
		$sheet->setCellValue('C' . $row, $item->jenis_perkara_nama);
		$sheet->setCellValue('D' . $row, $item->majelis_hakim_nama);
		$sheet->setCellValue('E' . $row, $item->panitera_pengganti_text);
		$sheet->setCellValue('F' . $row, $item->tanggal_pendaftaran);
		$sheet->setCellValue('G' . $row, $item->penetapan_majelis_hakim);
		$sheet->setCellValue('H' . $row, $item->penetapan_hari_sidang);
		$sheet->setCellValue('I' . $row, $item->sidang_pertama);
		$sheet->setCellValue('J' . $row, $item->tanggal_putusan);
		$sheet->setCellValue('K' . $row, $item->amar);
		$sheet->setCellValue('L' . $row, $item->pekerjaan);
		$sheet->setCellValue('M' . $row, $item->alamat_pihak2);
		$sheet->setCellValue('N' . $row, $item->prodeo);
		$sheet->setCellValue('O' . $row, $item->email_pihak1);
	}
}
