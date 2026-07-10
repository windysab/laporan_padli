<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    /**
     * Load template + view in one call.
     * All controllers follow the same 4-line pattern, now extracted here.
     */
    protected function _render($view, $data = [])
    {
        $this->load->view('template/new_header');
        $this->load->view('template/new_sidebar');
        $this->load->view($view, $data);
        $this->load->view('template/new_footer');
    }
}
