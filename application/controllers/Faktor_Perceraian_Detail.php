<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Faktor_perceraian_detail extends CI_Controller
{
    private $wilayah_map = ['HSU' => 'Hulu Sungai Utara', 'Balangan' => 'Balangan'];
    private $wilayah_label = ['HSU' => 'HSU', 'Balangan' => 'Balangan', 'SEMUA' => 'HSU dan Balangan'];

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_faktor_perceraian_detail');
    }

    public function index()
    {
        $tahun = $this->input->post('lap_tahun') ?: date('Y');
        $wilayah = $this->input->post('wilayah') ?: 'Balangan';
        $param = $this->wilayah_map[$wilayah] ?? $wilayah;
        view_load('v_faktor_perceraian_detail', [
            'datafilter' => $this->M_faktor_perceraian_detail->data_faktor_perceraian_detail($tahun, $param),
            'selected_tahun' => $tahun,
            'selected_wilayah' => $wilayah,
            'selected_wilayah_label' => $this->wilayah_label[strtoupper($wilayah)] ?? $wilayah,
        ]);
    }

    public function tabel_727()
    {
        $tahun = ($this->input->post('lap_tahun') ?: $this->input->get('lap_tahun')) ?: date('Y');
        $wilayah = ($this->input->post('wilayah') ?: $this->input->get('wilayah')) ?: 'HSU';
        $raw = $this->M_faktor_perceraian_detail->data_faktor_perceraian_usia($tahun, $wilayah, 'P');

        $indexed = [];
        foreach ($raw as $row) {
            $indexed[$row->FaktorPerceraian] = $row;
        }

        $factors = ['Perselisihan Terus Menerus', 'Kawin Paksa', 'Murtad', 'Ekonomi', 'Lain-Lain'];
        $keys = ['usia_16_19', 'usia_20_25', 'usia_26_30', 'usia_31_35', 'usia_36'];
        $rows = [];
        $totals = array_fill_keys($keys, 0);
        foreach ($factors as $i => $name) {
            $src = $indexed[$name] ?? null;
            $row = ['no' => $i + 1, 'faktor' => $name];
            foreach ($keys as $k) {
                $row[$k] = $src ? (int) $src->$k : 0;
                $totals[$k] += $row[$k];
            }
            $rows[] = $row;
        }
        view_load('v_faktor_penyebab_727', [
            'selected_tahun' => $tahun,
            'selected_wilayah' => $wilayah,
            'selected_wilayah_label' => $this->wilayah_label[strtoupper($wilayah)] ?? $wilayah,
            'rows' => $rows,
            'totals' => $totals,
        ]);
    }
}
