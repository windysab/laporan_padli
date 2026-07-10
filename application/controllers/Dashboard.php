<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function index()
    {
        $this->load->model('M_dashboard');

        $currentYear  = date('Y');
        $currentMonth = date('m');
        $currentMonthName = date('F');

        $data['currentYear']      = $currentYear;
        $data['currentMonth']     = $currentMonth;
        $data['currentMonthName'] = $currentMonthName;

        $data['perkara_count']   = $this->M_dashboard->countPerkaraTahun($currentYear);
        $data['putus_count']     = $this->M_dashboard->countPutusTahun($currentYear);
        $data['minutasi_count']  = $this->M_dashboard->countMinutasiTahun($currentYear);
        $data['sisa_count']      = $this->M_dashboard->countSisaTahun($currentYear);

        $data['chart_data'] = [
            $this->M_dashboard->countPerkaraBulan($currentYear, $currentMonth),
            $this->M_dashboard->countPutusBulan($currentYear, $currentMonth),
            $this->M_dashboard->countMinutasiBulan($currentYear, $currentMonth),
            $this->M_dashboard->countSisaBulan($currentYear, $currentMonth),
        ];

        $this->_render('dashboard', $data);
    }
    
}
