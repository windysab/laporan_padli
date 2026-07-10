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
        $jenis_perkara = $this->input->post('jenis_perkara');
        $lap_bulan = $this->input->post('lap_bulan');
        $lap_tahun = $this->input->post('lap_tahun');
        $data['datafilter'] = $this->M_lipa1->getData($lap_tahun, $lap_bulan, $jenis_perkara);
        $this->load->view('template/new_header');
        $this->load->view('template/new_sidebar');
        $this->load->view('v_lipa1', $data);
        $this->load->view('template/new_footer');
    }

    public function generateExcelDocument()
    {
        $jenis_perkara = $this->input->post('jenis_perkara');
        $lap_bulan = $this->input->post('lap_bulan');
        $lap_tahun = $this->input->post('lap_tahun');
        $data = $this->M_lipa1->getData($lap_tahun, $lap_bulan, $jenis_perkara);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan');
        $sheet->setCellValue('A1', "Laporan Lipa1 $lap_bulan-$lap_tahun");

        $headers = ['No','NOMOR PERKARA','JENIS PERKARA','MAJELIS HAKIM','PANITERA PENGANTI',
            'TANGGAL PENDAFTARAN','PENETAPAN MAJELIS HAKIM','PENETAPAN HARI SIDANG',
            'SIDANG PERTAMA','TANGGAL PUTUSAN','STATUS PUTUSAN','PEKERJAAN',
            'ALAMAT PIHAK 2','PRODEO','EMAIL PIHAK 1'];
        foreach ($headers as $i => $h) {
            $col = chr(66 + $i); // B=66
            $sheet->setCellValue("{$col}2", $h);
        }

        $row = 3;
        $cols = ['nomor_perkara','jenis_perkara_nama','majelis_hakim_nama','panitera_pengganti_text',
            'tanggal_pendaftaran','penetapan_majelis_hakim','penetapan_hari_sidang',
            'sidang_pertama','tanggal_putusan','status_putusan_nama','pekerjaan',
            'alamat_pihak2','prodeo','email_pihak1'];
        foreach ($data as $i => $item) {
            $sheet->setCellValue('A' . $row, $i + 1);
            foreach ($cols as $j => $field) {
                $sheet->setCellValue(chr(66 + $j) . $row, $item->$field ?? '');
            }
            $row++;
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = "Laporan Lipa1 $lap_bulan-$lap_tahun.xlsx";
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}
