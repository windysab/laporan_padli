<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data_Perceraian_hsu extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("M_data_perceraian_hsu");
    }

    public function index()
    {
        $lap_bulan = $this->input->post('lap_bulan');
        $lap_tahun = $this->input->post('lap_tahun');
        $data['datafilter'] = $this->M_data_perceraian_hsu->data_perceraian_hsu($lap_bulan, $lap_tahun);
        $this->_render('v_perceraian_hsu', $data);
    }
}
