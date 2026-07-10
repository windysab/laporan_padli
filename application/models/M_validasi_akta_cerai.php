<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_validasi_akta_cerai extends CI_Model
{
	public function get_belum_lengkap($tahun, $jenis_perkara = 'semua')
	{
		$where_jenis = $this->_get_jenis_condition($jenis_perkara);

		$params = array($tahun);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				P.nomor_perkara,
				P.jenis_perkara_nama,
				P.pihak1_text AS penggugat,
				P.pihak2_text AS tergugat,
				DATE_FORMAT(PP.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATE_FORMAT(PP.tanggal_bht, '%d-%m-%Y') AS tanggal_bht,
				PP.status_putusan_nama,
				PAC.nomor_akta_cerai,
				PAC.no_seri_akta_cerai,
				DATE_FORMAT(PAC.tgl_akta_cerai, '%d-%m-%Y') AS tgl_akta_cerai,
				TRIM(BOTH ', ' FROM CONCAT(
					CASE WHEN PAC.perkara_id IS NULL THEN 'Data akta belum ada, ' ELSE '' END,
					CASE WHEN PAC.nomor_akta_cerai IS NULL OR PAC.nomor_akta_cerai = '' THEN 'Nomor akta kosong, ' ELSE '' END,
					CASE WHEN PAC.no_seri_akta_cerai IS NULL OR PAC.no_seri_akta_cerai = '' THEN 'Nomor seri kosong, ' ELSE '' END,
					CASE WHEN PAC.tgl_akta_cerai IS NULL THEN 'Tanggal akta kosong, ' ELSE '' END
				)) AS catatan_validasi
			FROM perkara P
			INNER JOIN perkara_putusan PP ON PP.perkara_id = P.perkara_id
			LEFT JOIN perkara_akta_cerai PAC ON PAC.perkara_id = P.perkara_id
			WHERE PP.tanggal_putusan IS NOT NULL
				AND PP.tanggal_bht IS NOT NULL
				AND YEAR(PP.tanggal_bht) = ?
				AND (
					P.jenis_perkara_nama LIKE '%Cerai Gugat%'
					OR P.jenis_perkara_nama LIKE '%Cerai Talak%'
				)
				{$where_jenis['sql']}
				AND (
					PAC.perkara_id IS NULL
					OR PAC.nomor_akta_cerai IS NULL
					OR PAC.nomor_akta_cerai = ''
					OR PAC.no_seri_akta_cerai IS NULL
					OR PAC.no_seri_akta_cerai = ''
					OR PAC.tgl_akta_cerai IS NULL
				)
			ORDER BY PP.tanggal_bht DESC, P.nomor_perkara";

		return $this->db->query($sql, $params)->result();
	}

	public function get_terlambat($tahun, $batas_hari = 7, $jenis_perkara = 'semua')
	{
		$where_jenis = $this->_get_jenis_condition($jenis_perkara);

		$params = array($tahun, (int) $batas_hari);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				P.nomor_perkara,
				P.jenis_perkara_nama,
				P.pihak1_text AS penggugat,
				P.pihak2_text AS tergugat,
				DATE_FORMAT(PP.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATE_FORMAT(PP.tanggal_bht, '%d-%m-%Y') AS tanggal_bht,
				PP.status_putusan_nama,
				PAC.nomor_akta_cerai,
				PAC.no_seri_akta_cerai,
				DATE_FORMAT(PAC.tgl_akta_cerai, '%d-%m-%Y') AS tgl_akta_cerai,
				DATEDIFF(PAC.tgl_akta_cerai, PP.tanggal_bht) AS selisih_hari,
				CASE
					WHEN DATEDIFF(PAC.tgl_akta_cerai, PP.tanggal_bht) > 30 THEN 'Sangat terlambat'
					WHEN DATEDIFF(PAC.tgl_akta_cerai, PP.tanggal_bht) > 14 THEN 'Terlambat'
					ELSE 'Perlu perhatian'
				END AS status_keterlambatan
			FROM perkara P
			INNER JOIN perkara_putusan PP ON PP.perkara_id = P.perkara_id
			INNER JOIN perkara_akta_cerai PAC ON PAC.perkara_id = P.perkara_id
			WHERE PP.tanggal_bht IS NOT NULL
				AND PAC.tgl_akta_cerai IS NOT NULL
				AND YEAR(PP.tanggal_bht) = ?
				AND DATEDIFF(PAC.tgl_akta_cerai, PP.tanggal_bht) > ?
				AND (
					P.jenis_perkara_nama LIKE '%Cerai Gugat%'
					OR P.jenis_perkara_nama LIKE '%Cerai Talak%'
				)
				{$where_jenis['sql']}
			ORDER BY selisih_hari DESC, PP.tanggal_bht DESC";

		return $this->db->query($sql, $params)->result();
	}

	public function get_summary($tahun, $batas_hari = 7, $jenis_perkara = 'semua')
	{
		$where_jenis = $this->_get_jenis_condition($jenis_perkara);

		$params = array((int) $batas_hari, $tahun);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				COUNT(*) AS total_bht,
				SUM(CASE WHEN PAC.perkara_id IS NULL OR PAC.nomor_akta_cerai IS NULL OR PAC.nomor_akta_cerai = '' OR PAC.no_seri_akta_cerai IS NULL OR PAC.no_seri_akta_cerai = '' OR PAC.tgl_akta_cerai IS NULL THEN 1 ELSE 0 END) AS belum_lengkap,
				SUM(CASE WHEN PAC.tgl_akta_cerai IS NOT NULL AND DATEDIFF(PAC.tgl_akta_cerai, PP.tanggal_bht) > ? THEN 1 ELSE 0 END) AS terlambat,
				AVG(CASE WHEN PAC.tgl_akta_cerai IS NOT NULL THEN DATEDIFF(PAC.tgl_akta_cerai, PP.tanggal_bht) ELSE NULL END) AS rata_hari
			FROM perkara P
			INNER JOIN perkara_putusan PP ON PP.perkara_id = P.perkara_id
			LEFT JOIN perkara_akta_cerai PAC ON PAC.perkara_id = P.perkara_id
			WHERE PP.tanggal_putusan IS NOT NULL
				AND PP.tanggal_bht IS NOT NULL
				AND YEAR(PP.tanggal_bht) = ?
				AND (
					P.jenis_perkara_nama LIKE '%Cerai Gugat%'
					OR P.jenis_perkara_nama LIKE '%Cerai Talak%'
				)
				{$where_jenis['sql']}";

		return $this->db->query($sql, $params)->row();
	}

	public function get_by_nomor_akta($nomor_akta)
	{
		$sql = "SELECT
				P.nomor_perkara,
				P.jenis_perkara_nama,
				P.pihak1_text AS penggugat,
				P.pihak2_text AS tergugat,
				DATE_FORMAT(PP.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATE_FORMAT(PP.tanggal_bht, '%d-%m-%Y') AS tanggal_bht,
				PP.status_putusan_nama,
				PAC.nomor_akta_cerai,
				PAC.no_seri_akta_cerai,
				DATE_FORMAT(PAC.tgl_akta_cerai, '%d-%m-%Y') AS tgl_akta_cerai,
				PAC.jenis_cerai
			FROM perkara_akta_cerai PAC
			INNER JOIN perkara P ON P.perkara_id = PAC.perkara_id
			LEFT JOIN perkara_putusan PP ON PP.perkara_id = P.perkara_id
			WHERE PAC.nomor_akta_cerai = ?
			LIMIT 1";

		return $this->db->query($sql, array($nomor_akta))->row();
	}

	public function get_jenis_perkara_perceraian()
	{
		$sql = "SELECT DISTINCT jenis_perkara_nama
			FROM perkara
			WHERE jenis_perkara_nama IS NOT NULL
				AND jenis_perkara_nama != ''
				AND (
					jenis_perkara_nama LIKE '%Cerai Gugat%'
					OR jenis_perkara_nama LIKE '%Cerai Talak%'
				)
			ORDER BY jenis_perkara_nama";

		return $this->db->query($sql)->result();
	}

	// Fixed: returns array with 'sql' + 'params' for parameter binding
	private function _get_jenis_condition($jenis_perkara)
	{
		if ($jenis_perkara === 'semua' || empty($jenis_perkara)) {
			return array('sql' => '', 'params' => array());
		}

		return array(
			'sql' => ' AND P.jenis_perkara_nama = ?',
			'params' => array($jenis_perkara)
		);
	}
}
