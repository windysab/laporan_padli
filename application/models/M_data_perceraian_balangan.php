
<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_data_perceraian_balangan extends CI_Model
{
	private function _case_kecamatan($alias = 'perkara_pihak1')
	{
		return "CASE 
			WHEN {$alias}.`alamat` LIKE '%Paringin Selatan%' THEN 'Paringin Selatan'
			WHEN {$alias}.`alamat` LIKE '%Paringin%' THEN 'Paringin'
			WHEN {$alias}.`alamat` LIKE '%Lampihong%' THEN 'Lampihong'
			WHEN {$alias}.`alamat` LIKE '%Batumandi%' THEN 'Batumandi'
			WHEN {$alias}.`alamat` LIKE '%Awayan%' THEN 'Awayan'
			WHEN {$alias}.`alamat` LIKE '%Halong%' THEN 'Halong'
			WHEN {$alias}.`alamat` LIKE '%Tebing Tinggi%' THEN 'Tebing Tinggi'
			WHEN {$alias}.`alamat` LIKE '%Juai%' THEN 'Juai'
			ELSE 'HULU SUNGAI UTARA'
		END";
	}

	private function _all_kecamatan()
	{
		return "SELECT 'Paringin' AS KECAMATAN
			UNION ALL SELECT 'Paringin Selatan'
			UNION ALL SELECT 'Lampihong'
			UNION ALL SELECT 'Batumandi'
			UNION ALL SELECT 'Awayan'
			UNION ALL SELECT 'Halong'
			UNION ALL SELECT 'Tebing Tinggi'
			UNION ALL SELECT 'Juai'";
	}

	// Build a single subquery: always FROM perkara, LEFT JOIN additional tables
	// $date_field: column name to filter & count by
	// $extra_join: additional LEFT JOIN clause to bring in the date table
	private function _build_subquery($date_field, $extra_join)
	{
		return "SELECT 
			{$this->_case_kecamatan()} AS KECAMATAN,
			'{$date_field}' AS date_type, COUNT(*) AS COUNT
		FROM perkara
		LEFT JOIN perkara_pihak1 ON perkara.`perkara_id`=perkara_pihak1.`perkara_id`
		{$extra_join}
		WHERE YEAR({$date_field})=? AND MONTH({$date_field})=?
			AND perkara_pihak1.`urutan`='1'
		GROUP BY KECAMATAN";
	}

	private function _build_subqueries_set()
	{
		// [date_field, extra_join]
		$defs = [
			['tgl_akta_cerai',       'LEFT JOIN perkara_akta_cerai ON perkara.`perkara_id`=perkara_akta_cerai.`perkara_id`'],
			['tanggal_pendaftaran',  ''],
			['tanggal_putusan',      'LEFT JOIN perkara_putusan ON perkara.`perkara_id`=perkara_putusan.`perkara_id`'],
			['tanggal_bht',          'LEFT JOIN perkara_putusan ON perkara.perkara_id=perkara_putusan.perkara_id LEFT JOIN perkara_akta_cerai ON perkara.perkara_id=perkara_akta_cerai.perkara_id'],
		];
		$parts = [];
		foreach ($defs as $d) {
			$parts[] = $this->_build_subquery($d[0], $d[1]);
		}
		return implode("\n\t\t\tUNION ALL\n\t\t\t", $parts);
	}

	private function _agg_select()
	{
		return "COALESCE(SUM(CASE WHEN date_type = 'tanggal_pendaftaran' THEN COUNT ELSE 0 END), 0) AS PERKARA_MASUK,
			COALESCE(SUM(CASE WHEN date_type = 'tanggal_putusan' THEN COUNT ELSE 0 END), 0) AS PERKARA_PUTUS,
			COALESCE(SUM(CASE WHEN date_type = 'tanggal_bht' THEN COUNT ELSE 0 END), 0) AS PERKARA_TELAH_BHT,
			COALESCE(SUM(CASE WHEN date_type = 'tgl_akta_cerai' THEN COUNT ELSE 0 END), 0) AS JUMLAH_AKTA_CERAI";
	}

	function data_perceraian_balangan($lap_bulan, $lap_tahun)
	{
		$subqueries = $this->_build_subqueries_set();

		$sql = "SELECT 
			all_kecamatan.KECAMATAN,
			{$this->_agg_select()}
		FROM ({$this->_all_kecamatan()}) AS all_kecamatan
		LEFT JOIN (
			SELECT 
				KECAMATAN,
				{$this->_agg_select()}
			FROM (
				{$subqueries}
			) AS subquery
			GROUP BY KECAMATAN
		) AS subquery ON all_kecamatan.KECAMATAN = subquery.KECAMATAN

		UNION ALL

		SELECT 
			'TOTAL' AS KECAMATAN,
			SUM(PERKARA_MASUK) AS PERKARA_MASUK,
			SUM(PERKARA_PUTUS) AS PERKARA_PUTUS,
			SUM(PERKARA_TELAH_BHT) AS PERKARA_TELAH_BHT,
			SUM(JUMLAH_AKTA_CERAI) AS JUMLAH_AKTA_CERAI
		FROM (
			SELECT 
				KECAMATAN,
				{$this->_agg_select()}
			FROM (
				{$subqueries}
			) AS subquery
			WHERE KECAMATAN IN ('Paringin', 'Paringin Selatan', 'Lampihong', 'Batumandi', 'Awayan', 'Halong', 'Tebing Tinggi', 'Juai')
			GROUP BY KECAMATAN
		) AS subquery";

		// 4 sub-subqueries in main × 2 params + 4 sub-subqueries in total × 2 params = 16 params
		$params = [];
		for ($i = 0; $i < 8; $i++) {
			$params[] = $lap_tahun;
			$params[] = $lap_bulan;
		}

		$query = $this->db->query($sql, $params);
		return $query->result();
	}
}
