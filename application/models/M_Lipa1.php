<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_lipa1 extends CI_Model
{
	// Common FROM/JOIN clause shared by all methods
	private function _base_from()
	{
		return "FROM perkara
			LEFT JOIN perkara_penetapan ON perkara.perkara_id = perkara_penetapan.perkara_id
			LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
			LEFT JOIN status_putusan ON status_putusan.id = perkara_putusan.status_putusan_id 
			LEFT JOIN perkara_pihak1 ON perkara.perkara_id = perkara_pihak1.perkara_id
			LEFT JOIN perkara_pihak2 ON perkara.perkara_id = perkara_pihak2.perkara_id
			LEFT JOIN pihak ON perkara_pihak1.pihak_id = pihak.id
			LEFT JOIN perkara_efiling_id ON perkara.perkara_id = perkara_efiling_id.perkara_id";
	}

	// Common WHERE clause with parameter binding for date filters
	private function _date_where_clause($has_sisa = false)
	{
		return "(
				YEAR(tanggal_pendaftaran)=? AND MONTH(tanggal_pendaftaran)=?
				OR YEAR(penetapan_majelis_hakim)=? AND MONTH(penetapan_majelis_hakim)=?
				OR YEAR(penetapan_hari_sidang)=? AND MONTH(penetapan_hari_sidang)=?
				OR YEAR(sidang_pertama)=? AND MONTH(sidang_pertama)=?
				OR YEAR(tanggal_putusan)=? AND MONTH(tanggal_putusan)=?
				OR tanggal_pendaftaran IS NULL
			)";
	}

	// Build repeated date params [tahun, bulan, tahun, bulan, ...] x5
	private function _date_params($lap_tahun, $lap_bulan)
	{
		return array(
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan
		);
	}

	// Build WHERE suffix for pekerjaan filter
	private function _where_pekerjaan($pekerjaan_pattern)
	{
		if ($pekerjaan_pattern === null) {
			return "pekerjaan NOT LIKE '%Pensiunan%'";
		}
		return "pekerjaan LIKE ?";
	}

	public function getData($lap_tahun, $lap_bulan, $jenis_perkara)
	{
		$params = $this->_date_params($lap_tahun, $lap_bulan);

		$sql = "SELECT nomor_perkara, jenis_perkara_nama, majelis_hakim_nama, panitera_pengganti_text, 
				tanggal_pendaftaran, penetapan_majelis_hakim, penetapan_hari_sidang, sidang_pertama, 
				tanggal_putusan, status_putusan.`nama` AS amar, pekerjaan, 
				perkara_pihak2.alamat as alamat_pihak2, prodeo, pihak.email as email_pihak1 
			{$this->_base_from()}
			WHERE {$this->_date_where_clause()}
				AND perkara_pihak1.pihak_id != '1'
				AND perkara.nomor_perkara LIKE ?
				AND perkara_pihak1.urutan = '1'
				AND pekerjaan NOT LIKE '%Pensiunan%'
			ORDER BY tanggal_pendaftaran";

		$all_params = array_merge($params, array('%' . $jenis_perkara . '%'));
		return $this->db->query($sql, $all_params)->result();
	}

	public function getJumlah($lap_tahun, $lap_bulan, $jenis_perkara)
	{
		$param_sisa = array($lap_tahun, $lap_tahun, $lap_bulan);

		$params = $this->_date_params($lap_tahun, $lap_bulan);

		$sql = "SELECT COUNT(perkara.perkara_id) AS jumlah 
			{$this->_base_from()}
			WHERE (
				(YEAR(tanggal_pendaftaran) < ? OR (YEAR(tanggal_pendaftaran) = ? AND MONTH(tanggal_pendaftaran) < ?)) AND tanggal_putusan IS NULL
				OR YEAR(penetapan_majelis_hakim)=? AND MONTH(penetapan_majelis_hakim)=?
				OR YEAR(penetapan_hari_sidang)=? AND MONTH(penetapan_hari_sidang)=?
				OR YEAR(sidang_pertama)=? AND MONTH(sidang_pertama)=?
				OR YEAR(tanggal_putusan)=? AND MONTH(tanggal_putusan)=?
				OR tanggal_pendaftaran IS NULL
			)
			AND perkara_pihak1.pihak_id != '1'
			AND perkara.nomor_perkara LIKE ?
			AND perkara_pihak1.urutan = '1'
			AND pekerjaan NOT LIKE '%Pensiunan%'
			ORDER BY tanggal_pendaftaran";

		$all_params = array_merge($param_sisa, $params, array('%' . $jenis_perkara . '%'));
		return $this->db->query($sql, $all_params)->row();
	}

	// Unified pekerjaan-filtered count: pass 'Pensiunan' or 'PNS' as $pekerjaan
	private function _getJumlahByPekerjaan($lap_tahun, $lap_bulan, $jenis_perkara, $pekerjaan)
	{
		$params = $this->_date_params($lap_tahun, $lap_bulan);

		$sql = "SELECT COUNT(perkara.perkara_id) AS jumlah 
			{$this->_base_from()}
			WHERE {$this->_date_where_clause()}
				AND perkara_pihak1.pihak_id != '1'
				AND perkara.nomor_perkara LIKE ?
				AND perkara_pihak1.urutan = '1'
				AND pekerjaan LIKE ?
			ORDER BY tanggal_pendaftaran";

		$all_params = array_merge($params, array('%' . $jenis_perkara . '%', '%' . $pekerjaan . '%'));
		return $this->db->query($sql, $all_params)->row();
	}

	public function getJumlahPensiunan($lap_tahun, $lap_bulan, $jenis_perkara)
	{
		return $this->_getJumlahByPekerjaan($lap_tahun, $lap_bulan, $jenis_perkara, 'Pensiunan');
	}

	public function getJumlahPNS($lap_tahun, $lap_bulan, $jenis_perkara)
	{
		return $this->_getJumlahByPekerjaan($lap_tahun, $lap_bulan, $jenis_perkara, 'PNS');
	}
}
