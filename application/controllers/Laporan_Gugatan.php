<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan_Gugatan extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("M_laporan");
		$this->load->helper('url');
		$this->load->helper('text');
	}

	public function index()
	{
		$lap_bulan = $this->input->post('lap_bulan') ?: date('m');
		$lap_tahun = $this->input->post('lap_tahun') ?: date('Y');
		$jenis_laporan = $this->input->post('jenis_laporan') ?: 'bulanan';
		$format_laporan = $this->input->post('format_laporan') ?: 'lengkap';

		$data = [
			'selected_bulan' => $lap_bulan,
			'selected_tahun' => $lap_tahun,
			'selected_jenis' => $jenis_laporan,
			'selected_format' => $format_laporan,
		];

		switch ($jenis_laporan) {
			case 'tahunan':
				$data['datafilter'] = $this->M_laporan->get_laporan_tahunan($lap_tahun, $format_laporan);
				$data['summary_data'] = $this->M_laporan->get_summary_tahunan($lap_tahun);
				break;
			case 'semester':
				$semester = $this->input->post('semester') ?: '1';
				$data['datafilter'] = $this->M_laporan->get_laporan_semester($semester, $lap_tahun, $format_laporan);
				$data['summary_data'] = $this->M_laporan->get_summary_semester($semester, $lap_tahun);
				$data['selected_semester'] = $semester;
				break;
			case 'triwulan':
				$triwulan = $this->input->post('triwulan') ?: '1';
				$data['datafilter'] = $this->M_laporan->get_laporan_triwulan($triwulan, $lap_tahun, $format_laporan);
				$data['summary_data'] = $this->M_laporan->get_summary_triwulan($triwulan, $lap_tahun);
				$data['selected_triwulan'] = $triwulan;
				break;
			case 'custom':
				$tanggal_mulai = $this->input->post('tanggal_mulai') ?: date('Y-m-01');
				$tanggal_akhir = $this->input->post('tanggal_akhir') ?: date('Y-m-t');
				$data['datafilter'] = $this->M_laporan->get_laporan_custom($tanggal_mulai, $tanggal_akhir, $format_laporan);
				$data['summary_data'] = $this->M_laporan->get_summary_custom($tanggal_mulai, $tanggal_akhir);
				$data['tanggal_mulai'] = $tanggal_mulai;
				$data['tanggal_akhir'] = $tanggal_akhir;
				break;
			default: // bulanan
				$data['datafilter'] = $this->M_laporan->get_laporan_bulanan($lap_bulan, $lap_tahun, $format_laporan);
				$data['summary_data'] = $this->M_laporan->get_summary_bulanan($lap_bulan, $lap_tahun);
				break;
		}

		$data += [
			'total_perkara' => $this->M_laporan->get_total_perkara($lap_bulan, $lap_tahun, $jenis_laporan),
			'total_dikabulkan' => $this->M_laporan->get_total_dikabulkan($lap_bulan, $lap_tahun, $jenis_laporan),
			'total_ditolak' => $this->M_laporan->get_total_ditolak($lap_bulan, $lap_tahun, $jenis_laporan),
			'total_dicabut' => $this->M_laporan->get_total_dicabut($lap_bulan, $lap_tahun, $jenis_laporan),
		];

		view_load('v_laporan_gugatan', $data);
	}

	public function export_pdf()
	{
		$lap_bulan = $this->input->post('lap_bulan') ?: date('m');
		$lap_tahun = $this->input->post('lap_tahun') ?: date('Y');
		$jenis_laporan = $this->input->post('jenis_laporan') ?: 'bulanan';
		$format_laporan = $this->input->post('format_laporan') ?: 'lengkap';

		require_once APPPATH . 'libraries/tcpdf/tcpdf.php';

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('PA Amuntai');
		$pdf->SetTitle('Laporan Gugatan');
		$pdf->SetSubject('Laporan Perkara Gugatan');
		$pdf->SetHeaderData('', 0, 'LAPORAN PERKARA GUGATAN', 'Pengadilan Agama Amuntai');
		$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
		$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		$pdf->AddPage();

		$datafilter = $this->M_laporan->get_laporan_pdf($jenis_laporan, $lap_bulan, $lap_tahun);
		$html = $this->generate_pdf_content($datafilter, $jenis_laporan, $lap_bulan, $lap_tahun);
		$pdf->writeHTML($html, true, false, true, false, '');

		$filename = 'Laporan_Gugatan_' . $jenis_laporan . '_' . $lap_bulan . '_' . $lap_tahun . '.pdf';
		$pdf->Output($filename, 'D');
	}

	public function export_excel()
	{
		$lap_bulan = $this->input->post('lap_bulan') ?: date('m');
		$lap_tahun = $this->input->post('lap_tahun') ?: date('Y');
		$jenis_laporan = $this->input->post('jenis_laporan') ?: 'bulanan';
		$format_laporan = $this->input->post('format_laporan') ?: 'lengkap';

		require_once APPPATH . 'PHPExcel-1.8/Classes/PHPExcel.php';

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->setTitle('Laporan Gugatan');
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN PERKARA GUGATAN');
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'PENGADILAN AGAMA AMUNTAI');
		$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Periode: ' . $this->get_periode_text($jenis_laporan, $lap_bulan, $lap_tahun));

		$export_data = $this->M_laporan->get_laporan_export($jenis_laporan, $lap_bulan, $lap_tahun);

		$row = 5;
		$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, 'No');
		$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, 'Nomor Perkara');
		$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, 'Tanggal Daftar');
		$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, 'Penggugat');
		$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, 'Tergugat');
		$objPHPExcel->getActiveSheet()->setCellValue('F' . $row, 'Jenis Perkara');
		$objPHPExcel->getActiveSheet()->setCellValue('G' . $row, 'Status Putusan');
		$objPHPExcel->getActiveSheet()->setCellValue('H' . $row, 'Tanggal Putusan');

		$row++;
		$no = 1;
		if (!empty($export_data)) {
			foreach ($export_data as $item) {
				$objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $no++);
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $item->nomor_perkara);
				$objPHPExcel->getActiveSheet()->setCellValue('C' . $row, date('d/m/Y', strtotime($item->tanggal_pendaftaran)));
				$objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $item->penggugat);
				$objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $item->tergugat);
				$objPHPExcel->getActiveSheet()->setCellValue('F' . $row, $item->jenis_perkara_nama);
				$objPHPExcel->getActiveSheet()->setCellValue('G' . $row, $item->status_putusan);
				$objPHPExcel->getActiveSheet()->setCellValue('H' . $row, $item->tanggal_putusan ? date('d/m/Y', strtotime($item->tanggal_putusan)) : '-');
				$row++;
			}
		}

		foreach (range('A', 'H') as $col) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		}

		$filename = 'Laporan_Gugatan_' . $jenis_laporan . '_' . $lap_tahun . '.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	public function print_laporan()
	{
		$lap_bulan = $this->input->post('lap_bulan') ?: date('m');
		$lap_tahun = $this->input->post('lap_tahun') ?: date('Y');
		$jenis_laporan = $this->input->post('jenis_laporan') ?: 'bulanan';
		$format_laporan = $this->input->post('format_laporan') ?: 'lengkap';

		$data = [
			'selected_bulan' => $lap_bulan,
			'selected_tahun' => $lap_tahun,
			'selected_jenis' => $jenis_laporan,
			'selected_format' => $format_laporan,
			'datafilter' => $this->M_laporan->get_laporan_print($jenis_laporan, $lap_bulan, $lap_tahun),
			'summary_data' => $this->M_laporan->get_summary_bulanan($lap_bulan, $lap_tahun),
		];

		$this->load->view('v_laporan_gugatan_print', $data);
	}

	private function get_periode_text($jenis_laporan, $bulan, $tahun)
	{
		$bulan_names = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

		switch ($jenis_laporan) {
			case 'bulanan': return $bulan_names[$bulan] . ' ' . $tahun;
			case 'tahunan': return 'Tahun ' . $tahun;
			case 'semester': return 'Semester ' . ($bulan <= 6 ? '1' : '2') . ' Tahun ' . $tahun;
			case 'triwulan': return 'Triwulan ' . ceil($bulan / 3) . ' Tahun ' . $tahun;
			default: return $bulan_names[$bulan] . ' ' . $tahun;
		}
	}

	private function generate_pdf_content($datafilter, $jenis_laporan, $bulan, $tahun)
	{
		$html = '<h2 style="text-align: center;">LAPORAN PERKARA GUGATAN</h2>';
		$html .= '<h3 style="text-align: center;">Periode: ' . $this->get_periode_text($jenis_laporan, $bulan, $tahun) . '</h3><br/>';
		$html .= '<table border="1" cellspacing="0" cellpadding="5"><thead><tr style="background-color: #f5f5f5;">';
		$html .= '<th width="5%">No</th><th width="15%">Nomor Perkara</th><th width="12%">Tgl Daftar</th><th width="20%">Penggugat</th><th width="20%">Tergugat</th><th width="15%">Jenis Perkara</th><th width="13%">Status</th></tr></thead><tbody>';

		$no = 1;
		if (!empty($datafilter)) {
			foreach ($datafilter as $row) {
				$html .= '<tr><td>' . $no++ . '</td><td>' . $row->nomor_perkara . '</td><td>' . date('d/m/Y', strtotime($row->tanggal_pendaftaran)) . '</td><td>' . $row->penggugat . '</td><td>' . $row->tergugat . '</td><td>' . $row->jenis_perkara_nama . '</td><td>' . ($row->status_putusan ?: '-') . '</td></tr>';
			}
		} else {
			$html .= '<tr><td colspan="7" style="text-align: center;">Tidak ada data</td></tr>';
		}

		$html .= '</tbody></table>';
		return $html;
	}
}
