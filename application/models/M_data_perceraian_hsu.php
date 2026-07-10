
<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_data_perceraian_hsu extends CI_Model
{
	private function _case_kecamatan($alias = 'perkara_pihak1')
	{
		return "CASE 
			WHEN {$alias}.`alamat` LIKE '%Danau Panggang%' THEN 'Danau Panggang'
			WHEN {$alias}.`alamat` LIKE '%Babirik%' THEN 'Babirik'
			WHEN {$alias}.`alamat` LIKE '%Sungai Pandan%' THEN 'Sungai Pandan'
			WHEN {$alias}.`alamat` LIKE '%Amuntai Selatan%' THEN 'Amuntai Selatan'
			WHEN {$alias}.`alamat` LIKE '%Amuntai Tengah%' THEN 'Amuntai Tengah'
			WHEN {$alias}.`alamat` LIKE '%Amuntai Utara%' THEN 'Amuntai Utara'
			WHEN {$alias}.`alamat` LIKE '%Banjang%' THEN 'Banjang'
			WHEN {$alias}.`alamat` LIKE '%Haur Gading%' THEN 'Haur Gading'
			WHEN {$alias}.`alamat` LIKE '%Paminggir%' THEN 'Paminggir'
			WHEN {$alias}.`alamat` LIKE '%Sungai Tabukan%' THEN 'Sungai Tabukan'
			ELSE 'BALANGAN'
		END";
	}

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

	private function _kecamatan_list()
	{
		return "'Danau Panggang', 'Babirik', 'Sungai Pandan', 'Amuntai Selatan', 'Amuntai Tengah', 'Amuntai Utara', 'Banjang', 'Haur Gading', 'Paminggir', 'Sungai Tabukan'";
	}

	function data_perceraian_hsu($lap_bulan, $lap_tahun)
	{
		$subqueries = $this->_build_subqueries_set();

		$sql = "SELECT 
			KECAMATAN,
			SUM(CASE WHEN date_type = 'tanggal_pendaftaran' THEN COUNT ELSE 0 END) AS PERKARA_MASUK,
			SUM(CASE WHEN date_type = 'tanggal_putusan' THEN COUNT ELSE 0 END) AS PERKARA_PUTUS,
			SUM(CASE WHEN date_type = 'tanggal_bht' THEN COUNT ELSE 0 END) AS PERKARA_TELAH_BHT,
			SUM(CASE WHEN date_type = 'tgl_akta_cerai' THEN COUNT ELSE 0 END) AS JUMLAH_AKTA_CERAI
		FROM (
			{$subqueries}
		) AS subquery
		WHERE KECAMATAN IN ({$this->_kecamatan_list()})
		GROUP BY KECAMATAN

		UNION ALL

		SELECT 
			'TOTAL',
			SUM(CASE WHEN date_type = 'tanggal_pendaftaran' THEN COUNT ELSE 0 END),
			SUM(CASE WHEN date_type = 'tanggal_putusan' THEN COUNT ELSE 0 END),
			SUM(CASE WHEN date_type = 'tanggal_bht' THEN COUNT ELSE 0 END),
			SUM(CASE WHEN date_type = 'tgl_akta_cerai' THEN COUNT ELSE 0 END)
		FROM (
			{$subqueries}
		) AS subquery
		WHERE KECAMATAN IN ({$this->_kecamatan_list()})
		ORDER BY KECAMATAN";

		// 4 sub-subqueries × 2 params = 8 params, used twice (once per UNION block) = 16 params
		$params = [];
		for ($i = 0; $i < 8; $i++) {
			$params[] = $lap_tahun;
			$params[] = $lap_bulan;
		}

		$query = $this->db->query($sql, $params);
		return $query->result();
	}
}
