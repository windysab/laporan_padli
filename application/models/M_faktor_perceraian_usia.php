<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_faktor_perceraian_usia extends CI_Model
{
	/**
	 * Build address filter condition (same pattern as M_faktor_perceraian_detail)
	 */
	private function _get_wilayah_condition($wilayah, $alias = 'pp1')
	{
		if ($wilayah == 'Semua Wilayah' || $wilayah == 'Semua') {
			return array('sql' => '', 'params' => array());
		}

		// HSU / Amuntai
		if (stripos($wilayah, 'hulu sungai utara') !== false || $wilayah == 'Amuntai' || $wilayah == 'HSU') {
			$sql = "AND ({$alias}.alamat LIKE '%Hulu Sungai Utara%'
				OR {$alias}.alamat LIKE '%HSU%'
				OR {$alias}.alamat LIKE '%Amuntai%'
				OR {$alias}.alamat LIKE '%Haur Gading%'
				OR {$alias}.alamat LIKE '%Banjang%'
				OR {$alias}.alamat LIKE '%Paminggir%'
				OR {$alias}.alamat LIKE '%Babirik%'
				OR {$alias}.alamat LIKE '%Sungai Pandan%'
				OR {$alias}.alamat LIKE '%Danau Panggang%'
				OR {$alias}.alamat LIKE '%Sungai Tabukan%')";
			return array('sql' => $sql, 'params' => array());
		}

		// Balangan
		if ($wilayah == 'Balangan') {
			$sql = "AND ({$alias}.alamat LIKE '%Balangan%'
				OR {$alias}.alamat LIKE '%Paringin%'
				OR {$alias}.alamat LIKE '%Awayan%'
				OR {$alias}.alamat LIKE '%Tebing Tinggi%'
				OR {$alias}.alamat LIKE '%Juai%'
				OR {$alias}.alamat LIKE '%Lampihong%'
				OR {$alias}.alamat LIKE '%Halong%'
				OR {$alias}.alamat LIKE '%Batumandi%')";
			return array('sql' => $sql, 'params' => array());
		}

		// Generic — use parameter binding
		return array(
			'sql' => "AND {$alias}.alamat LIKE ?",
			'params' => array('%' . $wilayah . '%')
		);
	}

	/**
	 * Get divorce factors grouped by age range (Perempuan only)
	 */
	public function get_data($tahun = null, $wilayah = null)
	{
		if (empty($tahun)) $tahun = date('Y');
		if (empty($wilayah)) $wilayah = 'Amuntai';

		$wilayah_data = $this->_get_wilayah_condition($wilayah, 'pp1');

		$sql = "
			SELECT
				faktor.nama AS faktor,
				COALESCE(SUM(CASE
					WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 16 AND 19
					THEN 1 ELSE 0
				END), 0) AS usia_16_19,
				COALESCE(SUM(CASE
					WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 20 AND 25
					THEN 1 ELSE 0
				END), 0) AS usia_20_25,
				COALESCE(SUM(CASE
					WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 26 AND 30
					THEN 1 ELSE 0
				END), 0) AS usia_26_30,
				COALESCE(SUM(CASE
					WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 31 AND 35
					THEN 1 ELSE 0
				END), 0) AS usia_31_35,
				COALESCE(SUM(CASE
					WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) >= 36
					THEN 1 ELSE 0
				END), 0) AS usia_36
			FROM
				faktor_perceraian faktor
				JOIN perkara_akta_cerai pac ON faktor.id = pac.faktor_perceraian_id
				JOIN perkara p ON pac.perkara_id = p.perkara_id
				JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
				JOIN pihak pd ON pp1.pihak_id = pd.id
			WHERE
				YEAR(pac.tgl_akta_cerai) = ?
				AND pd.jenis_kelamin = 'P'
				AND faktor.aktif = 'Y'
				AND pd.tanggal_lahir IS NOT NULL
				{$wilayah_data['sql']}
			GROUP BY
				faktor.id, faktor.nama
			ORDER BY
				CAST(faktor.id AS UNSIGNED) ASC, faktor.id ASC";

		$params = array_merge(array($tahun), $wilayah_data['params']);
		$query = $this->db->query($sql, $params);
		return $query->result();
	}

	/**
	 * Get aggregated totals for summary cards
	 */
	public function get_summary($tahun = null, $wilayah = null)
	{
		if (empty($tahun)) $tahun = date('Y');
		if (empty($wilayah)) $wilayah = 'Amuntai';

		$wilayah_data = $this->_get_wilayah_condition($wilayah, 'pp1');

		$sql = "
			SELECT
				COUNT(*) AS total_kasus,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 16 AND 19 THEN 1 ELSE 0 END) AS total_16_19,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 20 AND 25 THEN 1 ELSE 0 END) AS total_20_25,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 26 AND 30 THEN 1 ELSE 0 END) AS total_26_30,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 31 AND 35 THEN 1 ELSE 0 END) AS total_31_35,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) >= 36 THEN 1 ELSE 0 END) AS total_36,
				SUM(CASE WHEN pd.jenis_kelamin = 'L' THEN 1 ELSE 0 END) AS total_laki,
				SUM(CASE WHEN pd.jenis_kelamin = 'P' THEN 1 ELSE 0 END) AS total_perempuan
			FROM
				perkara_akta_cerai pac
				JOIN perkara p ON pac.perkara_id = p.perkara_id
				JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
				JOIN pihak pd ON pp1.pihak_id = pd.id
			WHERE
				YEAR(pac.tgl_akta_cerai) = ?
				AND pd.tanggal_lahir IS NOT NULL
				{$wilayah_data['sql']}";

		$params = array_merge(array($tahun), $wilayah_data['params']);
		$query = $this->db->query($sql, $params);
		return $query->row();
	}
}
