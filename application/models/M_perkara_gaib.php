<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_perkara_gaib extends CI_Model
{
	// Get perkara gaib bulanan
	public function get_perkara_gaib_bulanan($lap_tahun, $lap_bulan, $jenis_perkara = 'semua')
	{
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array(
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan
		);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT 
				perkara.perkara_id,
				perkara.nomor_perkara,
				perkara.jenis_perkara_nama,
				perkara_penetapan.majelis_hakim_nama,
				perkara_penetapan.panitera_pengganti_text,
				DATE_FORMAT(perkara.tanggal_pendaftaran, '%d-%m-%Y') AS tanggal_pendaftaran,
				DATE_FORMAT(perkara_penetapan.penetapan_majelis_hakim, '%d-%m-%Y') AS penetapan_majelis_hakim,
				DATE_FORMAT(perkara_penetapan.penetapan_hari_sidang, '%d-%m-%Y') AS penetapan_hari_sidang,
				DATE_FORMAT(perkara_penetapan.sidang_pertama, '%d-%m-%Y') AS sidang_pertama,
				DATE_FORMAT(perkara_putusan.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				perkara_putusan.status_putusan_nama,
				perkara_pihak2.alamat AS alamat_termohon
			FROM perkara
			LEFT JOIN perkara_penetapan ON perkara.perkara_id = perkara_penetapan.perkara_id
			LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
			LEFT JOIN perkara_pihak2 ON perkara.perkara_id = perkara_pihak2.perkara_id
			LEFT JOIN pihak ON perkara_pihak2.pihak_id = pihak.id
			WHERE (
				(YEAR(perkara.tanggal_pendaftaran) = ? AND MONTH(perkara.tanggal_pendaftaran) = ?)
				OR (YEAR(perkara_penetapan.penetapan_majelis_hakim) = ? AND MONTH(perkara_penetapan.penetapan_majelis_hakim) = ?)
				OR (YEAR(perkara_penetapan.penetapan_hari_sidang) = ? AND MONTH(perkara_penetapan.penetapan_hari_sidang) = ?)
				OR (YEAR(perkara_penetapan.sidang_pertama) = ? AND MONTH(perkara_penetapan.sidang_pertama) = ?)
				OR (YEAR(perkara_putusan.tanggal_putusan) = ? AND MONTH(perkara_putusan.tanggal_putusan) = ?)
				OR perkara_putusan.tanggal_putusan IS NULL
			)
			AND perkara_pihak2.alamat LIKE '%tidak diketahui%'
			{$where_jenis['sql']}
			ORDER BY perkara.perkara_id DESC";

		return $this->db->query($sql, $params)->result();
	}

	// Get perkara gaib tahunan
	public function get_perkara_gaib_tahunan($lap_tahun, $jenis_perkara = 'semua')
	{
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($lap_tahun, $lap_tahun, $lap_tahun, $lap_tahun, $lap_tahun);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT 
				perkara.perkara_id,
				perkara.nomor_perkara,
				perkara.jenis_perkara_nama,
				perkara_penetapan.majelis_hakim_nama,
				perkara_penetapan.panitera_pengganti_text,
				DATE_FORMAT(perkara.tanggal_pendaftaran, '%d-%m-%Y') AS tanggal_pendaftaran,
				DATE_FORMAT(perkara_penetapan.penetapan_majelis_hakim, '%d-%m-%Y') AS penetapan_majelis_hakim,
				DATE_FORMAT(perkara_penetapan.penetapan_hari_sidang, '%d-%m-%Y') AS penetapan_hari_sidang,
				DATE_FORMAT(perkara_penetapan.sidang_pertama, '%d-%m-%Y') AS sidang_pertama,
				DATE_FORMAT(perkara_putusan.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				perkara_putusan.status_putusan_nama,
				perkara_pihak2.alamat AS alamat_termohon
			FROM perkara
			LEFT JOIN perkara_penetapan ON perkara.perkara_id = perkara_penetapan.perkara_id
			LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
			LEFT JOIN perkara_pihak2 ON perkara.perkara_id = perkara_pihak2.perkara_id
			LEFT JOIN pihak ON perkara_pihak2.pihak_id = pihak.id
			WHERE (
				YEAR(perkara.tanggal_pendaftaran) = ?
				OR YEAR(perkara_penetapan.penetapan_majelis_hakim) = ?
				OR YEAR(perkara_penetapan.penetapan_hari_sidang) = ?
				OR YEAR(perkara_penetapan.sidang_pertama) = ?
				OR YEAR(perkara_putusan.tanggal_putusan) = ?
				OR perkara_putusan.tanggal_putusan IS NULL
			)
			AND perkara_pihak2.alamat LIKE '%tidak diketahui%'
			{$where_jenis['sql']}
			ORDER BY perkara.perkara_id DESC";

		return $this->db->query($sql, $params)->result();
	}

	// Get perkara gaib custom date range
	public function get_perkara_gaib_custom($tanggal_mulai, $tanggal_akhir, $jenis_perkara = 'semua')
	{
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array(
			$tanggal_mulai, $tanggal_akhir,
			$tanggal_mulai, $tanggal_akhir,
			$tanggal_mulai, $tanggal_akhir,
			$tanggal_mulai, $tanggal_akhir,
			$tanggal_mulai, $tanggal_akhir
		);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT 
				perkara.perkara_id,
				perkara.nomor_perkara,
				perkara.jenis_perkara_nama,
				perkara_penetapan.majelis_hakim_nama,
				perkara_penetapan.panitera_pengganti_text,
				DATE_FORMAT(perkara.tanggal_pendaftaran, '%d-%m-%Y') AS tanggal_pendaftaran,
				DATE_FORMAT(perkara_penetapan.penetapan_majelis_hakim, '%d-%m-%Y') AS penetapan_majelis_hakim,
				DATE_FORMAT(perkara_penetapan.penetapan_hari_sidang, '%d-%m-%Y') AS penetapan_hari_sidang,
				DATE_FORMAT(perkara_penetapan.sidang_pertama, '%d-%m-%Y') AS sidang_pertama,
				DATE_FORMAT(perkara_putusan.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				perkara_putusan.status_putusan_nama,
				perkara_pihak2.alamat AS alamat_termohon
			FROM perkara
			LEFT JOIN perkara_penetapan ON perkara.perkara_id = perkara_penetapan.perkara_id
			LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
			LEFT JOIN perkara_pihak2 ON perkara.perkara_id = perkara_pihak2.perkara_id
			LEFT JOIN pihak ON perkara_pihak2.pihak_id = pihak.id
			WHERE (
				perkara.tanggal_pendaftaran BETWEEN ? AND ?
				OR perkara_penetapan.penetapan_majelis_hakim BETWEEN ? AND ?
				OR perkara_penetapan.penetapan_hari_sidang BETWEEN ? AND ?
				OR perkara_penetapan.sidang_pertama BETWEEN ? AND ?
				OR perkara_putusan.tanggal_putusan BETWEEN ? AND ?
				OR perkara_putusan.tanggal_putusan IS NULL
			)
			AND perkara_pihak2.alamat LIKE '%tidak diketahui%'
			{$where_jenis['sql']}
			ORDER BY perkara.perkara_id DESC";

		return $this->db->query($sql, $params)->result();
	}

	// Get summary perkara gaib
	public function get_summary_gaib_bulanan($lap_tahun, $lap_bulan, $jenis_perkara = 'semua')
	{
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array(
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan,
			$lap_tahun, $lap_bulan
		);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT 
				COUNT(*) as total_gaib,
				SUM(CASE WHEN perkara_putusan.tanggal_putusan IS NOT NULL THEN 1 ELSE 0 END) as sudah_putus,
				SUM(CASE WHEN perkara_putusan.tanggal_putusan IS NULL THEN 1 ELSE 0 END) as belum_putus,
				SUM(CASE WHEN perkara.jenis_perkara_nama LIKE '%Gugat%' THEN 1 ELSE 0 END) as cerai_gugat,
				SUM(CASE WHEN perkara.jenis_perkara_nama LIKE '%Talak%' THEN 1 ELSE 0 END) as cerai_talak
			FROM perkara
			LEFT JOIN perkara_penetapan ON perkara.perkara_id = perkara_penetapan.perkara_id
			LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
			LEFT JOIN perkara_pihak2 ON perkara.perkara_id = perkara_pihak2.perkara_id
			WHERE (
				(YEAR(perkara.tanggal_pendaftaran) = ? AND MONTH(perkara.tanggal_pendaftaran) = ?)
				OR (YEAR(perkara_penetapan.penetapan_majelis_hakim) = ? AND MONTH(perkara_penetapan.penetapan_majelis_hakim) = ?)
				OR (YEAR(perkara_penetapan.penetapan_hari_sidang) = ? AND MONTH(perkara_penetapan.penetapan_hari_sidang) = ?)
				OR (YEAR(perkara_penetapan.sidang_pertama) = ? AND MONTH(perkara_penetapan.sidang_pertama) = ?)
				OR (YEAR(perkara_putusan.tanggal_putusan) = ? AND MONTH(perkara_putusan.tanggal_putusan) = ?)
				OR perkara_putusan.tanggal_putusan IS NULL
			)
			AND perkara_pihak2.alamat LIKE '%tidak diketahui%'
			{$where_jenis['sql']}";

		return $this->db->query($sql, $params)->row();
	}

	public function get_summary_gaib_tahunan($lap_tahun, $jenis_perkara = 'semua')
	{
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($lap_tahun, $lap_tahun, $lap_tahun, $lap_tahun, $lap_tahun);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT 
				COUNT(*) as total_gaib,
				SUM(CASE WHEN perkara_putusan.tanggal_putusan IS NOT NULL THEN 1 ELSE 0 END) as sudah_putus,
				SUM(CASE WHEN perkara_putusan.tanggal_putusan IS NULL THEN 1 ELSE 0 END) as belum_putus,
				SUM(CASE WHEN perkara.jenis_perkara_nama LIKE '%Gugat%' THEN 1 ELSE 0 END) as cerai_gugat,
				SUM(CASE WHEN perkara.jenis_perkara_nama LIKE '%Talak%' THEN 1 ELSE 0 END) as cerai_talak
			FROM perkara
			LEFT JOIN perkara_penetapan ON perkara.perkara_id = perkara_penetapan.perkara_id
			LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
			LEFT JOIN perkara_pihak2 ON perkara.perkara_id = perkara_pihak2.perkara_id
			WHERE (
				YEAR(perkara.tanggal_pendaftaran) = ?
				OR YEAR(perkara_penetapan.penetapan_majelis_hakim) = ?
				OR YEAR(perkara_penetapan.penetapan_hari_sidang) = ?
				OR YEAR(perkara_penetapan.sidang_pertama) = ?
				OR YEAR(perkara_putusan.tanggal_putusan) = ?
				OR perkara_putusan.tanggal_putusan IS NULL
			)
			AND perkara_pihak2.alamat LIKE '%tidak diketahui%'
			{$where_jenis['sql']}";

		return $this->db->query($sql, $params)->row();
	}

	public function get_summary_gaib_custom($tanggal_mulai, $tanggal_akhir, $jenis_perkara = 'semua')
	{
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array(
			$tanggal_mulai, $tanggal_akhir,
			$tanggal_mulai, $tanggal_akhir,
			$tanggal_mulai, $tanggal_akhir,
			$tanggal_mulai, $tanggal_akhir,
			$tanggal_mulai, $tanggal_akhir
		);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT 
				COUNT(*) as total_gaib,
				SUM(CASE WHEN perkara_putusan.tanggal_putusan IS NOT NULL THEN 1 ELSE 0 END) as sudah_putus,
				SUM(CASE WHEN perkara_putusan.tanggal_putusan IS NULL THEN 1 ELSE 0 END) as belum_putus,
				SUM(CASE WHEN perkara.jenis_perkara_nama LIKE '%Gugat%' THEN 1 ELSE 0 END) as cerai_gugat,
				SUM(CASE WHEN perkara.jenis_perkara_nama LIKE '%Talak%' THEN 1 ELSE 0 END) as cerai_talak
			FROM perkara
			LEFT JOIN perkara_penetapan ON perkara.perkara_id = perkara_penetapan.perkara_id
			LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
			LEFT JOIN perkara_pihak2 ON perkara.perkara_id = perkara_pihak2.perkara_id
			WHERE (
				perkara.tanggal_pendaftaran BETWEEN ? AND ?
				OR perkara_penetapan.penetapan_majelis_hakim BETWEEN ? AND ?
				OR perkara_penetapan.penetapan_hari_sidang BETWEEN ? AND ?
				OR perkara_penetapan.sidang_pertama BETWEEN ? AND ?
				OR perkara_putusan.tanggal_putusan BETWEEN ? AND ?
				OR perkara_putusan.tanggal_putusan IS NULL
			)
			AND perkara_pihak2.alamat LIKE '%tidak diketahui%'
			{$where_jenis['sql']}";

		return $this->db->query($sql, $params)->row();
	}

	// Get dokumen relas panggilan gaib
	public function get_dokumen_relas($perkara_id)
	{
		$sql = "SELECT * FROM perkara_document_siap WHERE perkara_id = ? ORDER BY id DESC";
		$query = $this->db->query($sql, array($perkara_id));
		return $query ? $query->result() : array();
	}

	// Get jenis perkara list
	public function get_jenis_perkara_gaib()
	{
		$sql = "SELECT DISTINCT perkara.jenis_perkara_nama 
				FROM perkara
				LEFT JOIN perkara_pihak2 ON perkara.perkara_id = perkara_pihak2.perkara_id
				WHERE perkara_pihak2.alamat LIKE '%tidak diketahui%'
				AND perkara.jenis_perkara_nama IS NOT NULL 
				AND perkara.jenis_perkara_nama != ''
				ORDER BY perkara.jenis_perkara_nama";

		return $this->db->query($sql)->result();
	}

	// Fixed: returns array with 'sql' + 'params' for parameter binding
	private function _get_jenis_perkara_condition($jenis_perkara)
	{
		if ($jenis_perkara === 'semua' || empty($jenis_perkara)) {
			return array('sql' => '', 'params' => array());
		}

		return array(
			'sql' => ' AND perkara.nomor_perkara LIKE ?',
			'params' => array('%' . $jenis_perkara . '%')
		);
	}
}
