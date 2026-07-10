<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Uji_chart extends CI_Model
{
    function uji_chart()
    {
        $query = $this->db->query("SELECT MONTH(tanggal_pendaftaran) AS bulan, COUNT(*) AS total
            FROM perkara
            WHERE YEAR(tanggal_pendaftaran) = '2021'
            GROUP BY MONTH(tanggal_pendaftaran)");
        return $query->result();
    }
}
