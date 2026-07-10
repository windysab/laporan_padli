<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penerbitan_akta_cerai extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_penerbitan_akta_cerai');
    }

    public function index()
    {
        $lap_bulan = $this->input->post('lap_bulan');
        $lap_tahun = $this->input->post('lap_tahun');
        $data['datafilter'] = $this->M_penerbitan_akta_cerai->get_penertiban_akta_cerai($lap_tahun, $lap_bulan);
        $this->_render('v_penertiban_akta_cerai', $data);
    }
}
