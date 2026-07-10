<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Delegasi_k extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model("M_delegasi_k");
    }

    public function index()
    {
        $lap_bulan = $this->input->post('lap_bulan');
        $lap_tahun = $this->input->post('lap_tahun');
        $data['datafilter'] = $this->M_delegasi_k->delegasi_k($lap_bulan, $lap_tahun);
        $this->_render('v_delegasi_k', $data);
    }
}
