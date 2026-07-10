<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class M_Uji_chart extends CI_Model
{
	function uji_chart()
	{
		// Removed dead commented-out code and hardcoded year
		$query = $this->db->query("SELECT MONTH(tanggal_pendaftaran) as bulan, COUNT(*) as total
			FROM perkara
			GROUP BY MONTH(tanggal_pendaftaran)
			ORDER BY bulan");
		return $query->result();
	}
}
