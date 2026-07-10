<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Perkara_Banding extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_P_Banding');
    }

    public function index()
    {
        $lap_tahun = $this->input->post('lap_tahun');
        $lap_bulan = $this->input->post('lap_bulan');
        $data['results'] = $this->M_P_Banding->getData($lap_tahun, $lap_bulan);

        $this->load->view('template/new_header');
        $this->load->view('template/new_sidebar');
        $this->load->view('v_p_banding', $data);
        $this->load->view('template/new_footer');
    }

    public function generateExcelDocument()
    {
        ini_set('max_execution_time', '60');
        ini_set('memory_limit', '512M');

        $lap_tahun = $this->input->post('tahun');
        $lap_bulan = $this->input->post('bulan');
        $data = $this->M_P_Banding->getData($lap_tahun, $lap_bulan);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan');
        $sheet->setCellValue('A1', "Laporan Perkara Banding $lap_bulan-$lap_tahun");

        $headers = ['No', 'NOMOR PERKARA', 'PUTUSAN PN', 'PERMOHONAN BANDING',
            'PEMBERITAHUAN INZAGE', 'PENGIRIMAN BERKAS BANDING',
            'PUTUSAN BANDING', 'PEMBERITAHUAN PUTUSAN BANDING'];
        foreach ($headers as $i => $h) {
            $col = $i === 0 ? 'A' : chr(65 + $i);
            $sheet->setCellValue("{$col}2", $h);
        }

        $colRange = 'A1:H2';
        $sheet->getStyle($colRange)->getFont()->setBold(true);
        $sheet->getStyle($colRange)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFA07A');
        $sheet->getStyle($colRange)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $widths = [5, 20, 20, 20, 20, 20, 20, 20];
        foreach ($widths as $i => $w) {
            $col = $i === 0 ? 'A' : chr(65 + $i);
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $fields = ['nomor_perkara_pn', 'putusan_pn', 'permohonan_banding',
            'pemberitahuan_inzage', 'pengiriman_berkas_banding',
            'putusan_banding', 'pemberitahuan_putusan_banding'];
        foreach ($data as $key => $value) {
            $row = $key + 3;
            $sheet->setCellValue("A{$row}", $key + 1);
            foreach ($fields as $j => $f) {
                $sheet->setCellValue(chr(66 + $j) . $row, $value->$f);
            }
        }

        $filename = "Laporan Perkara Banding $lap_bulan-$lap_tahun.xlsx";
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        $writer->save('php://output');
    }
}
