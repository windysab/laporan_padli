<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Faktor_perceraian_detail extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("M_faktor_perceraian_detail");
    }

    public function index()
    {
        $lap_tahun = $this->input->post('lap_tahun', true);
        $param = $this->input->post('wilayah', true);
        $selected_wilayah = ($param) ? $param : 'SEMUA';
        $selected_tahun   = ($lap_tahun) ? $lap_tahun : date('Y');

        $wilayah_map = [
            'HSU'     => 'HULU SUNGAI UTARA',
            'Balangan' => 'BALANGAN',
            'SEMUA'   => 'HSU + BALANGAN',
        ];
        $selected_wilayah_label = $wilayah_map[$selected_wilayah] ?? $selected_wilayah;

        $data['datafilter'] = $this->M_faktor_perceraian_detail->data_faktor_perceraian_detail($selected_tahun, $selected_wilayah);
        $data['selected_tahun']   = $selected_tahun;
        $data['selected_wilayah'] = $selected_wilayah;
        $data['selected_wilayah_label'] = $selected_wilayah_label;

        $this->_render('v_faktor_perceraian_detail', $data);
    }

    public function tabel_727()
    {
        $lap_tahun = $this->input->post('lap_tahun', true);
        $wilayah   = $this->input->post('wilayah', true);
        $selected_tahun   = ($lap_tahun) ? $lap_tahun : date('Y');
        $selected_wilayah = ($wilayah) ? $wilayah : 'SEMUA';

        $wilayah_map = [
            'HSU'     => 'HULU SUNGAI UTARA',
            'Balangan' => 'BALANGAN',
            'SEMUA'   => 'HSU + BALANGAN',
        ];
        $selected_wilayah_label = $wilayah_map[$selected_wilayah] ?? $selected_wilayah;

        $data = $this->M_faktor_perceraian_detail->tabel_727($selected_tahun, $selected_wilayah);
        $data['selected_tahun']   = $selected_tahun;
        $data['selected_wilayah'] = $selected_wilayah;
        $data['selected_wilayah_label'] = $selected_wilayah_label;

        $this->_render('v_faktor_penyebab_727', $data);
    }
}
