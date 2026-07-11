<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_data_perkara extends CI_Model
{
	// ============================================================
	// Shared helpers (from M_data_perkara_gugatan)
	// ============================================================

	private function _get_kecamatan_list($wilayah)
	{
		switch ($wilayah) {
			case 'HSU':
				return "SELECT 'Danau Panggang' AS KECAMATAN
					UNION ALL SELECT 'Babirik'
					UNION ALL SELECT 'Sungai Pandan'
					UNION ALL SELECT 'Amuntai Selatan'
					UNION ALL SELECT 'Amuntai Tengah'
					UNION ALL SELECT 'Amuntai Utara'
					UNION ALL SELECT 'Banjang'
					UNION ALL SELECT 'Haur Gading'
					UNION ALL SELECT 'Paminggir'
					UNION ALL SELECT 'Sungai Tabukan'";

			case 'Balangan':
				return "SELECT 'Awayan' AS KECAMATAN
					UNION ALL SELECT 'Batu Mandi'
					UNION ALL SELECT 'Halong'
					UNION ALL SELECT 'Juai'
					UNION ALL SELECT 'Lampihong'
					UNION ALL SELECT 'Paringin'
					UNION ALL SELECT 'Paringin Selatan'
					UNION ALL SELECT 'Tebing Tinggi'";

			default: // Semua
				return "SELECT 'Danau Panggang' AS KECAMATAN
					UNION ALL SELECT 'Babirik'
					UNION ALL SELECT 'Sungai Pandan'
					UNION ALL SELECT 'Amuntai Selatan'
					UNION ALL SELECT 'Amuntai Tengah'
					UNION ALL SELECT 'Amuntai Utara'
					UNION ALL SELECT 'Banjang'
					UNION ALL SELECT 'Haur Gading'
					UNION ALL SELECT 'Paminggir'
					UNION ALL SELECT 'Sungai Tabukan'
					UNION ALL SELECT 'Awayan'
					UNION ALL SELECT 'Batu Mandi'
					UNION ALL SELECT 'Halong'
					UNION ALL SELECT 'Juai'
					UNION ALL SELECT 'Lampihong'
					UNION ALL SELECT 'Paringin'
					UNION ALL SELECT 'Paringin Selatan'
					UNION ALL SELECT 'Tebing Tinggi'";
		}
	}

	private function _get_case_statement($wilayah, $alias = 'perkara_pihak1')
	{
		switch ($wilayah) {
			case 'HSU':
				return "CASE 
					WHEN {$alias}.alamat LIKE '%Danau Panggang%' THEN 'Danau Panggang'
					WHEN {$alias}.alamat LIKE '%Babirik%' THEN 'Babirik'
					WHEN {$alias}.alamat LIKE '%Sungai Pandan%' THEN 'Sungai Pandan'
					WHEN {$alias}.alamat LIKE '%Amuntai Selatan%' THEN 'Amuntai Selatan'
					WHEN {$alias}.alamat LIKE '%Amuntai Tengah%' THEN 'Amuntai Tengah'
					WHEN {$alias}.alamat LIKE '%Amuntai Utara%' THEN 'Amuntai Utara'
					WHEN {$alias}.alamat LIKE '%Banjang%' THEN 'Banjang'
					WHEN {$alias}.alamat LIKE '%Haur Gading%' THEN 'Haur Gading'
					WHEN {$alias}.alamat LIKE '%Paminggir%' THEN 'Paminggir'
					WHEN {$alias}.alamat LIKE '%Sungai Tabukan%' THEN 'Sungai Tabukan'
					ELSE 'Lainnya'
				END";

			case 'Balangan':
				return "CASE 
					WHEN {$alias}.alamat LIKE '%Awayan%' THEN 'Awayan'
					WHEN {$alias}.alamat LIKE '%Batu Mandi%' THEN 'Batu Mandi'
					WHEN {$alias}.alamat LIKE '%Halong%' THEN 'Halong'
					WHEN {$alias}.alamat LIKE '%Juai%' THEN 'Juai'
					WHEN {$alias}.alamat LIKE '%Lampihong%' THEN 'Lampihong'
					WHEN {$alias}.alamat LIKE '%Paringin%' THEN 'Paringin'
					WHEN {$alias}.alamat LIKE '%Paringin Selatan%' THEN 'Paringin Selatan'
					WHEN {$alias}.alamat LIKE '%Tebing Tinggi%' THEN 'Tebing Tinggi'
					ELSE 'Lainnya'
				END";

			default:
				return "CASE 
					WHEN {$alias}.alamat LIKE '%Danau Panggang%' THEN 'Danau Panggang'
					WHEN {$alias}.alamat LIKE '%Babirik%' THEN 'Babirik'
					WHEN {$alias}.alamat LIKE '%Sungai Pandan%' THEN 'Sungai Pandan'
					WHEN {$alias}.alamat LIKE '%Amuntai Selatan%' THEN 'Amuntai Selatan'
					WHEN {$alias}.alamat LIKE '%Amuntai Tengah%' THEN 'Amuntai Tengah'
					WHEN {$alias}.alamat LIKE '%Amuntai Utara%' THEN 'Amuntai Utara'
					WHEN {$alias}.alamat LIKE '%Banjang%' THEN 'Banjang'
					WHEN {$alias}.alamat LIKE '%Haur Gading%' THEN 'Haur Gading'
					WHEN {$alias}.alamat LIKE '%Paminggir%' THEN 'Paminggir'
					WHEN {$alias}.alamat LIKE '%Sungai Tabukan%' THEN 'Sungai Tabukan'
					WHEN {$alias}.alamat LIKE '%Awayan%' THEN 'Awayan'
					WHEN {$alias}.alamat LIKE '%Batu Mandi%' THEN 'Batu Mandi'
					WHEN {$alias}.alamat LIKE '%Halong%' THEN 'Halong'
					WHEN {$alias}.alamat LIKE '%Juai%' THEN 'Juai'
					WHEN {$alias}.alamat LIKE '%Lampihong%' THEN 'Lampihong'
					WHEN {$alias}.alamat LIKE '%Paringin%' THEN 'Paringin'
					WHEN {$alias}.alamat LIKE '%Paringin Selatan%' THEN 'Paringin Selatan'
					WHEN {$alias}.alamat LIKE '%Tebing Tinggi%' THEN 'Tebing Tinggi'
					ELSE 'Lainnya'
				END";
		}
	}

	private function _get_alamat_condition($wilayah, $alias = 'perkara_pihak1')
	{
		switch ($wilayah) {
			case 'HSU':
				return " AND ({$alias}.alamat LIKE '%Danau Panggang%' OR {$alias}.alamat LIKE '%Babirik%' OR {$alias}.alamat LIKE '%Sungai Pandan%' OR {$alias}.alamat LIKE '%Amuntai%' OR {$alias}.alamat LIKE '%Banjang%' OR {$alias}.alamat LIKE '%Haur Gading%' OR {$alias}.alamat LIKE '%Paminggir%' OR {$alias}.alamat LIKE '%Sungai Tabukan%')";
			case 'Balangan':
				return " AND ({$alias}.alamat LIKE '%Awayan%' OR {$alias}.alamat LIKE '%Batu Mandi%' OR {$alias}.alamat LIKE '%Halong%' OR {$alias}.alamat LIKE '%Juai%' OR {$alias}.alamat LIKE '%Lampihong%' OR {$alias}.alamat LIKE '%Paringin%' OR {$alias}.alamat LIKE '%Tebing Tinggi%')";
			default:
				return "";
		}
	}

	private function _validate_wilayah($wilayah)
	{
		$allowed = array('HSU', 'Balangan', 'Semua');
		return in_array($wilayah, $allowed) ? $wilayah : 'HSU';
	}

	private function _build_join_clause($table)
	{
		if ($table === 'perkara') return '';
		if ($table === 'perkara_putusan') return "LEFT JOIN perkara ON perkara_putusan.perkara_id = perkara.perkara_id ";
		if ($table === 'perkara_akta_cerai') return "LEFT JOIN perkara ON perkara_akta_cerai.perkara_id = perkara.perkara_id ";
		return "LEFT JOIN perkara ON {$table}.perkara_id = perkara.perkara_id ";
	}

	private function _build_subquery($date_field, $table, $where, $wilayah)
	{
		$case_stmt = $this->_get_case_statement($wilayah);
		$join_clause = $this->_build_join_clause($table);
		$where = str_replace('__DATE_FIELD__', $date_field, $where);

		return "SELECT {$case_stmt} AS KECAMATAN,
			'{$date_field}' AS date_type, COUNT(*) AS COUNT
		FROM {$table}
		{$join_clause}
		LEFT JOIN perkara_pihak1 ON perkara.perkara_id = perkara_pihak1.perkara_id
		WHERE {$where}
			AND perkara_pihak1.urutan = '1'
		GROUP BY KECAMATAN";
	}

	private function _subquery_defs()
	{
		return [
			['tgl_akta_cerai', 'perkara_akta_cerai'],
			['tanggal_pendaftaran', 'perkara'],
			['tanggal_putusan', 'perkara_putusan'],
			['tanggal_bht', 'perkara_putusan'],
		];
	}

	private function _build_subqueries_set($where_template, $wilayah)
	{
		$parts = [];
		foreach ($this->_subquery_defs() as $d) {
			list($field, $table) = $d;
			$parts[] = $this->_build_subquery($field, $table, $where_template, $wilayah);
		}
		return implode(' UNION ALL ', $parts);
	}

	private function _agg_select($use_least = false)
	{
		$bht = $use_least
			? "LEAST(
				COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_bht' THEN subquery.COUNT ELSE 0 END), 0),
				COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_putusan' THEN subquery.COUNT ELSE 0 END), 0)
			)"
			: "COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_bht' THEN subquery.COUNT ELSE 0 END), 0)";

		return "COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_pendaftaran' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_MASUK,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_putusan' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_PUTUS,
			{$bht} AS PERKARA_TELAH_BHT,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'tgl_akta_cerai' THEN subquery.COUNT ELSE 0 END), 0) AS JUMLAH_AKTA_CERAI";
	}

	// ============================================================
	// Helpers (from M_data_permohonan)
	// ============================================================

	private function get_kecamatan_list($wilayah)
	{
		if ($wilayah == 'HSU') {
			return [
				'Danau Panggang', 'Babirik', 'Sungai Pandan',
				'Amuntai Selatan', 'Amuntai Tengah', 'Amuntai Utara',
				'Banjang', 'Haur Gading', 'Paminggir', 'Sungai Tabukan'
			];
		} elseif ($wilayah == 'Balangan') {
			return [
				'Paringin', 'Paringin Selatan', 'Lampihong', 'Batumandi',
				'Awayan', 'Halong', 'Tebing Tinggi', 'Juai'
			];
		} else { // Semua wilayah
			return [
				'Danau Panggang', 'Babirik', 'Sungai Pandan',
				'Amuntai Selatan', 'Amuntai Tengah', 'Amuntai Utara',
				'Banjang', 'Haur Gading', 'Paminggir', 'Sungai Tabukan',
				'Paringin', 'Paringin Selatan', 'Lampihong', 'Batumandi',
				'Awayan', 'Halong', 'Tebing Tinggi', 'Juai'
			];
		}
	}

	private function build_address_filter($wilayah, $alias = 'pp1')
	{
		$kecamatan_list = $this->get_kecamatan_list($wilayah);
		$conditions = [];
		foreach ($kecamatan_list as $kec) {
			$conditions[] = "{$alias}.alamat LIKE '%{$kec}%'";
		}
		return '(' . implode(' OR ', $conditions) . ')';
	}

	private function build_case_when($wilayah, $fallback)
	{
		$kecamatan_list = $this->get_kecamatan_list($wilayah);
		$case_when = "CASE ";
		foreach ($kecamatan_list as $kecamatan) {
			$case_when .= "WHEN pp1.alamat LIKE '%{$kecamatan}%' THEN '{$kecamatan}' ";
		}
		$case_when .= "ELSE '{$fallback}' END";
		return $case_when;
	}

	private function _get_fallback($wilayah)
	{
		if ($wilayah == 'HSU') return 'HULU SUNGAI UTARA';
		if ($wilayah == 'Balangan') return 'BALANGAN';
		return 'LAINNYA';
	}

	private function _build_locations_union($kecamatan_list)
	{
		$parts = [];
		foreach ($kecamatan_list as $i => $kec) {
			$parts[] = ($i > 0 ? "\t\t\tUNION ALL " : "\t\t\t") . "SELECT '{$kec}' AS KECAMATAN";
		}
		return implode("\n", $parts);
	}

	private function _kecamatan_csv($kecamatan_list)
	{
		return "'" . implode("', '", $kecamatan_list) . "'";
	}

	private function _main_subquery($case_when, $date_type, $from_join, $where)
	{
		return "SELECT 
			{$case_when} AS KECAMATAN,
			'{$date_type}' AS date_type, COUNT(*) AS COUNT
		{$from_join}
		WHERE {$where}
		GROUP BY KECAMATAN";
	}

	private function _total_subquery($date_type, $from_join, $where, $address_filter)
	{
		return "SELECT 
			'TOTAL' AS KECAMATAN,
			'{$date_type}' AS date_type, COUNT(*) AS COUNT
		{$from_join}
		WHERE {$where}
			AND {$address_filter}";
	}

	private function _from_join_masuk()
	{
		return "FROM perkara p
			INNER JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id AND pp1.urutan = 1";
	}

	private function _from_join_putus()
	{
		return "FROM perkara p
			INNER JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id AND pp1.urutan = 1
			INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id";
	}

	private function _from_join_sisa()
	{
		return "FROM perkara p
			INNER JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id AND pp1.urutan = 1
			LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id";
	}

	// ============================================================
	// Public methods from M_data_perkara_gugatan
	// ============================================================

	function data_summary_perceraian($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah)
	{
		if (empty($lap_bulan)) $lap_bulan = date('m');
		if (empty($lap_tahun)) $lap_tahun = date('Y');
		if (empty($jenis_perkara)) $jenis_perkara = 'Cerai Gugat';
		if (empty($wilayah)) $wilayah = 'HSU';

		$kecamatan_list = $this->_get_kecamatan_list($wilayah);
		$where = 'YEAR(__DATE_FIELD__) = ? AND MONTH(__DATE_FIELD__) = ? AND jenis_perkara_nama = ?';
		$subqueries = $this->_build_subqueries_set($where, $wilayah);

		$sql = "SELECT locations.KECAMATAN, {$this->_agg_select(true)}
			FROM ({$kecamatan_list}) AS locations
			LEFT JOIN ({$subqueries}) AS subquery ON locations.KECAMATAN = subquery.KECAMATAN
			GROUP BY locations.KECAMATAN
			ORDER BY locations.KECAMATAN";

		$params = [];
		for ($i = 0; $i < 4; $i++) {
			$params[] = $lap_tahun;
			$params[] = $lap_bulan;
			$params[] = $jenis_perkara;
		}

		return $this->db->query($sql, $params)->result();
	}

	function data_yearly_perceraian($lap_tahun, $jenis_perkara, $wilayah)
	{
		if (empty($lap_tahun)) $lap_tahun = date('Y');
		if (empty($jenis_perkara)) $jenis_perkara = 'Cerai Gugat';
		if (empty($wilayah)) $wilayah = 'HSU';

		$kecamatan_list = $this->_get_kecamatan_list($wilayah);
		$where = 'YEAR(__DATE_FIELD__) = ? AND jenis_perkara_nama = ?';
		$subqueries = $this->_build_subqueries_set($where, $wilayah);

		$sql = "SELECT locations.KECAMATAN, {$this->_agg_select(false)}
			FROM ({$kecamatan_list}) AS locations
			LEFT JOIN ({$subqueries}) AS subquery ON locations.KECAMATAN = subquery.KECAMATAN
			GROUP BY locations.KECAMATAN
			ORDER BY locations.KECAMATAN";

		$params = [];
		for ($i = 0; $i < 4; $i++) {
			$params[] = $lap_tahun;
			$params[] = $jenis_perkara;
		}

		return $this->db->query($sql, $params)->result();
	}

	function data_monthly_perceraian($lap_tahun, $jenis_perkara, $wilayah)
	{
		if (empty($lap_tahun)) $lap_tahun = date('Y');
		if (empty($jenis_perkara)) $jenis_perkara = 'Cerai Gugat';
		if (empty($wilayah)) $wilayah = 'HSU';

		$params = array($lap_tahun, '%' . $jenis_perkara . '%');
		$sql = "SELECT 
			MONTH(tanggal_pendaftaran) as BULAN,
			MONTHNAME(tanggal_pendaftaran) as NAMA_BULAN,
			COUNT(*) as JUMLAH
		FROM perkara
		LEFT JOIN perkara_pihak1 ON perkara.perkara_id = perkara_pihak1.perkara_id
		WHERE YEAR(tanggal_pendaftaran) = ? 
			AND jenis_perkara_nama LIKE ?
			AND perkara_pihak1.urutan = '1'";

		if ($wilayah !== 'Semua') {
			$sql .= $this->_get_alamat_condition($wilayah);
		}

		$sql .= " GROUP BY MONTH(tanggal_pendaftaran), MONTHNAME(tanggal_pendaftaran)
		ORDER BY BULAN";

		return $this->db->query($sql, $params)->result();
	}

	function data_comparison_gugat_talak($lap_bulan, $lap_tahun, $wilayah)
	{
		if (empty($lap_bulan)) $lap_bulan = date('m');
		if (empty($lap_tahun)) $lap_tahun = date('Y');
		if (empty($wilayah)) $wilayah = 'HSU';

		$kecamatan_list = $this->_get_kecamatan_list($wilayah);

		$sql = "SELECT 
			locations.KECAMATAN,
			COALESCE(gugat.JUMLAH, 0) AS CERAI_GUGAT,
			COALESCE(talak.JUMLAH, 0) AS CERAI_TALAK,
			(COALESCE(gugat.JUMLAH, 0) + COALESCE(talak.JUMLAH, 0)) AS TOTAL
		FROM ({$kecamatan_list}) AS locations
		LEFT JOIN (
			SELECT {$this->_get_case_statement($wilayah)} AS KECAMATAN, COUNT(*) AS JUMLAH
			FROM perkara
			LEFT JOIN perkara_pihak1 ON perkara.perkara_id = perkara_pihak1.perkara_id
			WHERE YEAR(tanggal_pendaftaran) = ? AND MONTH(tanggal_pendaftaran) = ?
				AND jenis_perkara_nama = 'Cerai Gugat'
				AND perkara_pihak1.urutan = '1'
			GROUP BY KECAMATAN
		) AS gugat ON locations.KECAMATAN = gugat.KECAMATAN
		LEFT JOIN (
			SELECT {$this->_get_case_statement($wilayah)} AS KECAMATAN, COUNT(*) AS JUMLAH
			FROM perkara
			LEFT JOIN perkara_pihak1 ON perkara.perkara_id = perkara_pihak1.perkara_id
			WHERE YEAR(tanggal_pendaftaran) = ? AND MONTH(tanggal_pendaftaran) = ?
				AND jenis_perkara_nama = 'Cerai Talak'
				AND perkara_pihak1.urutan = '1'
			GROUP BY KECAMATAN
		) AS talak ON locations.KECAMATAN = talak.KECAMATAN
		ORDER BY locations.KECAMATAN";

		return $this->db->query($sql, array($lap_tahun, $lap_bulan, $lap_tahun, $lap_bulan))->result();
	}

	function data_faktor_perceraian($lap_tahun, $wilayah, $jenis_kelamin = 'L')
	{
		if (empty($lap_tahun)) $lap_tahun = date('Y');
		if (empty($wilayah)) $wilayah = 'HSU';
		$jenis_kelamin = in_array($jenis_kelamin, array('L', 'P')) ? $jenis_kelamin : 'L';

		$params = array($lap_tahun);
		$where_clause = "YEAR(pac.tgl_akta_cerai) = ?";
		if ($wilayah !== 'Semua') {
			$where_clause .= $this->_get_alamat_condition($wilayah, 'pp1');
		}

		$gender_suffix = "($jenis_kelamin)";

		$sql = "SELECT
			faktor.FaktorPerceraian,
			COALESCE(agg.`Usia 16-19 {$gender_suffix}`, 0) AS `Usia 16-19 {$gender_suffix}`,
			COALESCE(agg.`Usia 20-25 {$gender_suffix}`, 0) AS `Usia 20-25 {$gender_suffix}`,
			COALESCE(agg.`Usia 26-30 {$gender_suffix}`, 0) AS `Usia 26-30 {$gender_suffix}`,
			COALESCE(agg.`Usia 31-35 {$gender_suffix}`, 0) AS `Usia 31-35 {$gender_suffix}`,
			COALESCE(agg.`Usia 36+ {$gender_suffix}`, 0) AS `Usia 36+ {$gender_suffix}`,
			COALESCE(agg.`Total {$gender_suffix}`, 0) AS `Total {$gender_suffix}`
		FROM (
			SELECT '9' AS id, 'Perselisihan Terus Menerus' AS FaktorPerceraian
			UNION ALL SELECT '10', 'Kawin Paksa'
			UNION ALL SELECT '11', 'Murtad'
			UNION ALL SELECT '12', 'Ekonomi'
			UNION ALL SELECT '14', 'Lain-Lain'
		) AS faktor
		LEFT JOIN (
			SELECT
				pac.faktor_perceraian_id, 
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, p.tanggal_pendaftaran) BETWEEN 16 AND 19 AND pd.jenis_kelamin = ? THEN 1 ELSE 0 END) AS `Usia 16-19 {$gender_suffix}`,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, p.tanggal_pendaftaran) BETWEEN 20 AND 25 AND pd.jenis_kelamin = ? THEN 1 ELSE 0 END) AS `Usia 20-25 {$gender_suffix}`,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, p.tanggal_pendaftaran) BETWEEN 26 AND 30 AND pd.jenis_kelamin = ? THEN 1 ELSE 0 END) AS `Usia 26-30 {$gender_suffix}`,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, p.tanggal_pendaftaran) BETWEEN 31 AND 35 AND pd.jenis_kelamin = ? THEN 1 ELSE 0 END) AS `Usia 31-35 {$gender_suffix}`,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, p.tanggal_pendaftaran) >= 36 AND pd.jenis_kelamin = ? THEN 1 ELSE 0 END) AS `Usia 36+ {$gender_suffix}`,
				SUM(CASE WHEN pd.jenis_kelamin = ? THEN 1 ELSE 0 END) AS `Total {$gender_suffix}`
			FROM perkara_akta_cerai pac
				JOIN perkara p ON pac.perkara_id = p.perkara_id
				JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
				JOIN pihak pd ON pp1.pihak_id = pd.id
			WHERE {$where_clause}
			GROUP BY pac.faktor_perceraian_id
		) AS agg ON faktor.id = agg.faktor_perceraian_id";

		$bind_params = $params;
		for ($i = 0; $i < 6; $i++) {
			$bind_params[] = $jenis_kelamin;
		}

		return $this->db->query($sql, $bind_params)->result();
	}

	function data_faktor_perceraian_detail($lap_bulan, $lap_tahun, $wilayah)
	{
		if (empty($lap_bulan)) $lap_bulan = date('m');
		if (empty($lap_tahun)) $lap_tahun = date('Y');
		if (empty($wilayah)) $wilayah = 'HSU';

		$params = array($lap_tahun, $lap_bulan, $lap_tahun, $lap_bulan);
		$where_clause = "YEAR(pac.tgl_akta_cerai) = ? AND MONTH(pac.tgl_akta_cerai) = ?";
		$wilayah_sql = '';
		if ($wilayah !== 'Semua') {
			$wilayah_sql = $this->_get_alamat_condition($wilayah, 'pp1');
		}

		$sql = "SELECT 
			CASE 
				WHEN pac.faktor_perceraian_id = '9' THEN 'Perselisihan Terus Menerus'
				WHEN pac.faktor_perceraian_id = '10' THEN 'Kawin Paksa'
				WHEN pac.faktor_perceraian_id = '11' THEN 'Murtad'
				WHEN pac.faktor_perceraian_id = '12' THEN 'Ekonomi'
				WHEN pac.faktor_perceraian_id = '14' THEN 'Lain-Lain'
				ELSE 'Tidak Diketahui'
			END AS FAKTOR,
			COUNT(*) AS JUMLAH,
			ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM perkara_akta_cerai pac2 
				JOIN perkara p2 ON pac2.perkara_id = p2.perkara_id 
				JOIN perkara_pihak1 pp12 ON p2.perkara_id = pp12.perkara_id 
				WHERE YEAR(pac2.tgl_akta_cerai) = ? AND MONTH(pac2.tgl_akta_cerai) = ?
				{$wilayah_sql})), 2) AS PERSENTASE
		FROM perkara_akta_cerai pac
		JOIN perkara p ON pac.perkara_id = p.perkara_id
		JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
		WHERE {$where_clause}
		{$wilayah_sql}
		GROUP BY pac.faktor_perceraian_id
		ORDER BY JUMLAH DESC";

		return $this->db->query($sql, $params)->result();
	}

	function data_custom_range($tanggal_mulai, $tanggal_akhir, $jenis_perkara, $wilayah)
	{
		if (empty($tanggal_mulai)) $tanggal_mulai = date('Y-m-01');
		if (empty($tanggal_akhir)) $tanggal_akhir = date('Y-m-d');
		if (empty($jenis_perkara)) $jenis_perkara = 'Cerai Gugat';
		if (empty($wilayah)) $wilayah = 'HSU';

		$wilayah = $this->_validate_wilayah($wilayah);
		$kecamatan_list = $this->_get_kecamatan_list($wilayah);
		$where = 'DATE(__DATE_FIELD__) >= ? AND DATE(__DATE_FIELD__) <= ? AND jenis_perkara_nama = ?';
		$subqueries = $this->_build_subqueries_set($where, $wilayah);

		$sql = "SELECT locations.KECAMATAN, {$this->_agg_select(true)}
			FROM ({$kecamatan_list}) AS locations
			LEFT JOIN ({$subqueries}) AS subquery ON locations.KECAMATAN = subquery.KECAMATAN
			GROUP BY locations.KECAMATAN
			ORDER BY locations.KECAMATAN";

		$params = [];
		for ($i = 0; $i < 4; $i++) {
			$params[] = $tanggal_mulai;
			$params[] = $tanggal_akhir;
			$params[] = $jenis_perkara;
		}

		return $this->db->query($sql, $params)->result();
	}

	function data_yearly_comparison_gugat_talak($lap_tahun, $wilayah)
	{
		if (empty($lap_tahun)) $lap_tahun = date('Y');
		if (empty($wilayah)) $wilayah = 'HSU';

		$params = array($lap_tahun, $lap_tahun - 4);
		$sql = "SELECT 
			YEAR(tanggal_pendaftaran) as TAHUN,
			SUM(CASE WHEN jenis_perkara_nama = 'Cerai Gugat' THEN 1 ELSE 0 END) AS CERAI_GUGAT,
			SUM(CASE WHEN jenis_perkara_nama = 'Cerai Talak' THEN 1 ELSE 0 END) AS CERAI_TALAK,
			COUNT(*) AS TOTAL
		FROM perkara
		LEFT JOIN perkara_pihak1 ON perkara.perkara_id = perkara_pihak1.perkara_id
		WHERE YEAR(tanggal_pendaftaran) <= ? 
			AND YEAR(tanggal_pendaftaran) >= ?
			AND jenis_perkara_nama IN ('Cerai Gugat', 'Cerai Talak')
			AND perkara_pihak1.urutan = '1'";

		if ($wilayah !== 'Semua') {
			$sql .= $this->_get_alamat_condition($wilayah);
		}

		$sql .= " GROUP BY YEAR(tanggal_pendaftaran)
		ORDER BY TAHUN DESC";

		return $this->db->query($sql, $params)->result();
	}

	public function get_jenis_perkara_gugatan()
	{
		$sql = "SELECT DISTINCT p.jenis_perkara_nama 
			FROM perkara p 
			WHERE p.jenis_perkara_nama IS NOT NULL 
			  AND p.jenis_perkara_nama != ''
			  AND (p.nomor_perkara LIKE '%Pdt.Gt%' 
				   OR p.nomor_perkara LIKE '%Pdt.G/%' 
				   OR p.nomor_perkara LIKE '%PDT.G%'
				   OR p.jenis_perkara_nama LIKE '%Cerai Gugat%'
				   OR p.jenis_perkara_nama = 'Cerai Gugat')
			ORDER BY p.jenis_perkara_nama";

		return $this->db->query($sql)->result();
	}

	// ============================================================
	// Public methods from M_data_permohonan
	// ============================================================

	public function data_permohonan($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah = 'Semua')
	{
		$kecamatan_list = $this->get_kecamatan_list($wilayah);
		$locations_union = $this->_build_locations_union($kecamatan_list);
		$fallback = $this->_get_fallback($wilayah);
		$case_when = $this->build_case_when($wilayah, $fallback);
		$address_filter = $this->build_address_filter($wilayah);
		$like_pattern = '%' . $jenis_perkara . '%';

		$masuk_where = "YEAR(p.tanggal_pendaftaran) = ? AND MONTH(p.tanggal_pendaftaran) = ? AND p.jenis_perkara_nama LIKE ?";
		$putus_where = "YEAR(pp.tanggal_putusan) = ? AND MONTH(pp.tanggal_putusan) = ? AND p.jenis_perkara_nama LIKE ?";
		$sisa_bulan_where = "((YEAR(p.tanggal_pendaftaran) = ? AND MONTH(p.tanggal_pendaftaran) < ?) OR (YEAR(p.tanggal_pendaftaran) < ?)) 
			AND (pp.tanggal_putusan IS NULL OR (YEAR(pp.tanggal_putusan) = ? AND MONTH(pp.tanggal_putusan) >= ?) OR (YEAR(pp.tanggal_putusan) > ?))
			AND p.jenis_perkara_nama LIKE ?";
		$sisa_tahun_where = "YEAR(p.tanggal_pendaftaran) < ? 
			AND (pp.tanggal_putusan IS NULL OR YEAR(pp.tanggal_putusan) >= ?)
			AND p.jenis_perkara_nama LIKE ?";

		$sql = "SELECT 
			locations.KECAMATAN,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_pendaftaran' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_MASUK,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_putusan' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_PUTUS,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'sisa_bulan_lalu' THEN subquery.COUNT ELSE 0 END), 0) AS SISA_BULAN_LALU,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'sisa_tahun_lalu' THEN subquery.COUNT ELSE 0 END), 0) AS SISA_TAHUN_LALU
		FROM ({$locations_union}) AS locations
		LEFT JOIN (
			{$this->_main_subquery($case_when, 'tanggal_pendaftaran', $this->_from_join_masuk(), $masuk_where)}
			UNION ALL
			{$this->_main_subquery($case_when, 'tanggal_putusan', $this->_from_join_putus(), $putus_where)}
			UNION ALL
			{$this->_main_subquery($case_when, 'sisa_bulan_lalu', $this->_from_join_sisa(), $sisa_bulan_where)}
			UNION ALL
			{$this->_main_subquery($case_when, 'sisa_tahun_lalu', $this->_from_join_sisa(), $sisa_tahun_where)}
		) AS subquery ON locations.KECAMATAN = subquery.KECAMATAN
		GROUP BY locations.KECAMATAN

		UNION ALL

		SELECT 
			'TOTAL' AS KECAMATAN,
			SUM(CASE WHEN date_type = 'tanggal_pendaftaran' THEN COUNT ELSE 0 END) AS PERKARA_MASUK,
			SUM(CASE WHEN date_type = 'tanggal_putusan' THEN COUNT ELSE 0 END) AS PERKARA_PUTUS,
			SUM(CASE WHEN date_type = 'sisa_bulan_lalu' THEN COUNT ELSE 0 END) AS SISA_BULAN_LALU,
			SUM(CASE WHEN date_type = 'sisa_tahun_lalu' THEN COUNT ELSE 0 END) AS SISA_TAHUN_LALU
		FROM (
			{$this->_total_subquery('tanggal_pendaftaran', $this->_from_join_masuk(), $masuk_where, $address_filter)}
			UNION ALL
			{$this->_total_subquery('tanggal_putusan', $this->_from_join_putus(), $putus_where, $address_filter)}
			UNION ALL
			{$this->_total_subquery('sisa_bulan_lalu', $this->_from_join_sisa(), $sisa_bulan_where, $address_filter)}
			UNION ALL
			{$this->_total_subquery('sisa_tahun_lalu', $this->_from_join_sisa(), $sisa_tahun_where, $address_filter)}
		) AS subquery
		ORDER BY CASE WHEN KECAMATAN = 'TOTAL' THEN 1 ELSE 0 END, KECAMATAN";

		$params = [
			// main: masuk
			$lap_tahun, $lap_bulan, $like_pattern,
			// main: putus
			$lap_tahun, $lap_bulan, $like_pattern,
			// main: sisa_bulan_lalu
			$lap_tahun, $lap_bulan, $lap_tahun,
			$lap_tahun, $lap_bulan, $lap_tahun,
			$like_pattern,
			// main: sisa_tahun_lalu
			$lap_tahun, $lap_tahun, $like_pattern,
			// total: masuk
			$lap_tahun, $lap_bulan, $like_pattern,
			// total: putus
			$lap_tahun, $lap_bulan, $like_pattern,
			// total: sisa_bulan_lalu
			$lap_tahun, $lap_bulan, $lap_tahun,
			$lap_tahun, $lap_bulan, $lap_tahun,
			$like_pattern,
			// total: sisa_tahun_lalu
			$lap_tahun, $lap_tahun, $like_pattern
		];

		$query = $this->db->query($sql, $params);
		return $query->result();
	}

	public function data_permohonan_tahunan($lap_tahun, $jenis_perkara, $wilayah = 'Semua')
	{
		$kecamatan_list = $this->get_kecamatan_list($wilayah);
		$locations_union = $this->_build_locations_union($kecamatan_list);
		$fallback = $this->_get_fallback($wilayah);
		$case_when = $this->build_case_when($wilayah, $fallback);
		$address_filter = $this->build_address_filter($wilayah);
		$like_pattern = '%' . $jenis_perkara . '%';

		$masuk_where = "YEAR(p.tanggal_pendaftaran) = ? AND p.jenis_perkara_nama LIKE ?";
		$putus_where = "YEAR(pp.tanggal_putusan) = ? AND p.jenis_perkara_nama LIKE ?";
		$sisa_tahun_where = "YEAR(p.tanggal_pendaftaran) < ? 
			AND (pp.tanggal_putusan IS NULL OR YEAR(pp.tanggal_putusan) >= ?)
			AND p.jenis_perkara_nama LIKE ?";

		$sisa_col = "(COALESCE(SUM(CASE WHEN subquery.date_type = 'sisa_tahun_lalu' THEN subquery.COUNT ELSE 0 END), 0) + 
		 COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_pendaftaran' THEN subquery.COUNT ELSE 0 END), 0) - 
		 COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_putusan' THEN subquery.COUNT ELSE 0 END), 0))";

		$sql = "SELECT 
			locations.KECAMATAN,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_pendaftaran' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_MASUK,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_putusan' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_PUTUS,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'sisa_tahun_lalu' THEN subquery.COUNT ELSE 0 END), 0) AS SISA_TAHUN_LALU,
			{$sisa_col} AS SISA_PERKARA
		FROM ({$locations_union}) AS locations
		LEFT JOIN (
			{$this->_main_subquery($case_when, 'tanggal_pendaftaran', $this->_from_join_masuk(), $masuk_where)}
			UNION ALL
			{$this->_main_subquery($case_when, 'tanggal_putusan', $this->_from_join_putus(), $putus_where)}
			UNION ALL
			{$this->_main_subquery($case_when, 'sisa_tahun_lalu', $this->_from_join_sisa(), $sisa_tahun_where)}
		) AS subquery ON locations.KECAMATAN = subquery.KECAMATAN
		GROUP BY locations.KECAMATAN

		UNION ALL

		SELECT 
			'TOTAL' AS KECAMATAN,
			SUM(CASE WHEN date_type = 'tanggal_pendaftaran' THEN COUNT ELSE 0 END) AS PERKARA_MASUK,
			SUM(CASE WHEN date_type = 'tanggal_putusan' THEN COUNT ELSE 0 END) AS PERKARA_PUTUS,
			SUM(CASE WHEN date_type = 'sisa_tahun_lalu' THEN COUNT ELSE 0 END) AS SISA_TAHUN_LALU,
			(SUM(CASE WHEN date_type = 'sisa_tahun_lalu' THEN COUNT ELSE 0 END) + 
			 SUM(CASE WHEN date_type = 'tanggal_pendaftaran' THEN COUNT ELSE 0 END) - 
			 SUM(CASE WHEN date_type = 'tanggal_putusan' THEN COUNT ELSE 0 END)) AS SISA_PERKARA
		FROM (
			{$this->_total_subquery('tanggal_pendaftaran', $this->_from_join_masuk(), $masuk_where, $address_filter)}
			UNION ALL
			{$this->_total_subquery('tanggal_putusan', $this->_from_join_putus(), $putus_where, $address_filter)}
			UNION ALL
			{$this->_total_subquery('sisa_tahun_lalu', $this->_from_join_sisa(), $sisa_tahun_where, $address_filter)}
		) AS subquery
		ORDER BY CASE WHEN KECAMATAN = 'TOTAL' THEN 1 ELSE 0 END, KECAMATAN";

		$params = [
			// main: masuk
			$lap_tahun, $like_pattern,
			// main: putus
			$lap_tahun, $like_pattern,
			// main: sisa_tahun_lalu
			$lap_tahun, $lap_tahun, $like_pattern,
			// total: masuk
			$lap_tahun, $like_pattern,
			// total: putus
			$lap_tahun, $like_pattern,
			// total: sisa_tahun_lalu
			$lap_tahun, $lap_tahun, $like_pattern
		];

		$query = $this->db->query($sql, $params);
		return $query->result();
	}

	public function data_permohonan_custom($tanggal_mulai, $tanggal_akhir, $jenis_perkara, $wilayah = 'Semua')
	{
		$kecamatan_list = $this->get_kecamatan_list($wilayah);
		$locations_union = $this->_build_locations_union($kecamatan_list);
		$fallback = $this->_get_fallback($wilayah);
		$case_when = $this->build_case_when($wilayah, $fallback);
		$address_filter = $this->build_address_filter($wilayah);
		$like_pattern = '%' . $jenis_perkara . '%';

		$masuk_where = "p.tanggal_pendaftaran BETWEEN ? AND ? AND p.jenis_perkara_nama LIKE ?";
		$putus_where = "pp.tanggal_putusan BETWEEN ? AND ? AND p.jenis_perkara_nama LIKE ?";
		$sisa_where = "p.tanggal_pendaftaran < ? 
			AND (pp.tanggal_putusan IS NULL OR pp.tanggal_putusan > ?)
			AND p.jenis_perkara_nama LIKE ?";

		$sisa_col = "(COALESCE(SUM(CASE WHEN subquery.date_type = 'sisa_sebelumnya' THEN subquery.COUNT ELSE 0 END), 0) + 
		 COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_pendaftaran' THEN subquery.COUNT ELSE 0 END), 0) - 
		 COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_putusan' THEN subquery.COUNT ELSE 0 END), 0))";

		$sql = "SELECT 
			locations.KECAMATAN,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_pendaftaran' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_MASUK,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_putusan' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_PUTUS,
			COALESCE(SUM(CASE WHEN subquery.date_type = 'sisa_sebelumnya' THEN subquery.COUNT ELSE 0 END), 0) AS SISA_SEBELUMNYA,
			{$sisa_col} AS SISA_PERKARA
		FROM ({$locations_union}) AS locations
		LEFT JOIN (
			{$this->_main_subquery($case_when, 'tanggal_pendaftaran', $this->_from_join_masuk(), $masuk_where)}
			UNION ALL
			{$this->_main_subquery($case_when, 'tanggal_putusan', $this->_from_join_putus(), $putus_where)}
			UNION ALL
			{$this->_main_subquery($case_when, 'sisa_sebelumnya', $this->_from_join_sisa(), $sisa_where)}
		) AS subquery ON locations.KECAMATAN = subquery.KECAMATAN
		GROUP BY locations.KECAMATAN

		UNION ALL

		SELECT 
			'TOTAL' AS KECAMATAN,
			SUM(CASE WHEN date_type = 'tanggal_pendaftaran' THEN COUNT ELSE 0 END) AS PERKARA_MASUK,
			SUM(CASE WHEN date_type = 'tanggal_putusan' THEN COUNT ELSE 0 END) AS PERKARA_PUTUS,
			SUM(CASE WHEN date_type = 'sisa_sebelumnya' THEN COUNT ELSE 0 END) AS SISA_SEBELUMNYA,
			(SUM(CASE WHEN date_type = 'sisa_sebelumnya' THEN COUNT ELSE 0 END) + 
			 SUM(CASE WHEN date_type = 'tanggal_pendaftaran' THEN COUNT ELSE 0 END) - 
			 SUM(CASE WHEN date_type = 'tanggal_putusan' THEN COUNT ELSE 0 END)) AS SISA_PERKARA
		FROM (
			{$this->_total_subquery('tanggal_pendaftaran', $this->_from_join_masuk(), $masuk_where, $address_filter)}
			UNION ALL
			{$this->_total_subquery('tanggal_putusan', $this->_from_join_putus(), $putus_where, $address_filter)}
			UNION ALL
			{$this->_total_subquery('sisa_sebelumnya', $this->_from_join_sisa(), $sisa_where, $address_filter)}
		) AS subquery
		ORDER BY CASE WHEN KECAMATAN = 'TOTAL' THEN 1 ELSE 0 END, KECAMATAN";

		$params = [
			// main: masuk
			$tanggal_mulai, $tanggal_akhir, $like_pattern,
			// main: putus
			$tanggal_mulai, $tanggal_akhir, $like_pattern,
			// main: sisa
			$tanggal_mulai, $tanggal_akhir, $like_pattern,
			// total: masuk
			$tanggal_mulai, $tanggal_akhir, $like_pattern,
			// total: putus
			$tanggal_mulai, $tanggal_akhir, $like_pattern,
			// total: sisa
			$tanggal_mulai, $tanggal_akhir, $like_pattern
		];

		$query = $this->db->query($sql, $params);
		return $query->result();
	}

	public function get_summary_statistics($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah, $jenis_laporan = 'bulanan')
	{
		$params = [];
		$like_pattern = '%' . $jenis_perkara . '%';
		$address_filter = $this->build_address_filter($wilayah);

		switch ($jenis_laporan) {
			case 'tahunan':
				$where_clause = "YEAR(p.tanggal_pendaftaran) = ?";
				$where_clause_putusan = "YEAR(pp.tanggal_putusan) = ?";
				$params = [$lap_tahun, $like_pattern, $lap_tahun, $like_pattern];
				break;
			case 'custom':
				$tanggal_mulai = $this->input->post('tanggal_mulai') ?: date('Y-m-01');
				$tanggal_akhir = $this->input->post('tanggal_akhir') ?: date('Y-m-t');
				$where_clause = "p.tanggal_pendaftaran BETWEEN ? AND ?";
				$where_clause_putusan = "pp.tanggal_putusan BETWEEN ? AND ?";
				$params = [$tanggal_mulai, $tanggal_akhir, $like_pattern, $tanggal_mulai, $tanggal_akhir, $like_pattern];
				break;
			default: // bulanan
				$where_clause = "YEAR(p.tanggal_pendaftaran) = ? AND MONTH(p.tanggal_pendaftaran) = ?";
				$where_clause_putusan = "YEAR(pp.tanggal_putusan) = ? AND MONTH(pp.tanggal_putusan) = ?";
				$params = [$lap_tahun, $lap_bulan, $like_pattern, $lap_tahun, $lap_bulan, $like_pattern];
				break;
		}

		$sql = "SELECT 
			(
				SELECT COUNT(*) 
				FROM perkara p
				INNER JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id AND pp1.urutan = 1
				WHERE {$where_clause} AND p.jenis_perkara_nama LIKE ?
				AND {$address_filter}
			) as total_masuk,
			(
				SELECT COUNT(*) 
				FROM perkara p
				INNER JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id AND pp1.urutan = 1
				INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				WHERE {$where_clause_putusan} AND p.jenis_perkara_nama LIKE ?
				AND {$address_filter}
			) as total_putus";

		$query = $this->db->query($sql, $params);
		return $query->row();
	}

	public function get_jenis_perkara_list()
	{
		$sql = "SELECT DISTINCT jenis_perkara_nama 
				FROM perkara 
				WHERE jenis_perkara_nama LIKE '%Dispensasi%' 
					OR jenis_perkara_nama LIKE '%Istbat%' 
					OR jenis_perkara_nama LIKE '%P3HP%'
					OR jenis_perkara_nama LIKE '%Ahli Waris%'
				ORDER BY jenis_perkara_nama";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_jenis_perkara_permohonan()
	{
		$sql = "SELECT DISTINCT jenis_perkara_nama 
				FROM perkara 
				WHERE (
					jenis_perkara_nama LIKE '%Dispensasi%' 
					OR jenis_perkara_nama LIKE '%Istbat%' 
					OR jenis_perkara_nama LIKE '%P3HP%'
					OR jenis_perkara_nama LIKE '%Ahli Waris%'
					OR jenis_perkara_nama LIKE '%Penetapan%'
					OR jenis_perkara_nama LIKE '%Permohonan%'
					OR jenis_perkara_nama LIKE '%Pengesahan%'
					OR jenis_perkara_nama LIKE '%Pengangkatan%'
					OR jenis_perkara_nama LIKE '%Perwalian%'
					OR jenis_perkara_nama LIKE '%Wali%'
					OR jenis_perkara_nama LIKE '%Itsbat%'
					OR jenis_perkara_nama LIKE '%Penunjukan%'
					OR jenis_perkara_nama LIKE '%Hibah%'
					OR jenis_perkara_nama LIKE '%Wakaf%'
					OR jenis_perkara_nama LIKE '%Wasiat%'
					OR jenis_perkara_nama LIKE '%Pembatalan%'
					OR jenis_perkara_nama LIKE '%Pencabutan%'
				)
				AND jenis_perkara_nama NOT LIKE '%Gugat%'
				AND jenis_perkara_nama NOT LIKE '%Cerai Talak%'
				AND jenis_perkara_nama NOT LIKE '%Cerai Gugat%'
				AND jenis_perkara_nama NOT LIKE '%Harta Bersama%'
				AND jenis_perkara_nama NOT LIKE '%Mut\'ah%'
				AND jenis_perkara_nama NOT LIKE '%Nafkah%'
				AND jenis_perkara_nama NOT LIKE '%Hadhanah%'
				ORDER BY jenis_perkara_nama";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_sisa_perkara_data($lap_bulan, $lap_tahun, $jenis_perkara, $wilayah = 'Semua', $jenis_laporan = 'bulanan')
	{
		$kecamatan_list = $this->get_kecamatan_list($wilayah);
		$locations_union = $this->_build_locations_union($kecamatan_list);
		$fallback = $this->_get_fallback($wilayah);
		$case_when = $this->build_case_when($wilayah, $fallback);
		$like_pattern = '%' . $jenis_perkara . '%';

		switch ($jenis_laporan) {
			case 'tahunan':
				$masuk_where = "YEAR(p.tanggal_pendaftaran) = ?";
				$putus_where = "YEAR(pp.tanggal_putusan) = ?";
				$sisa_tahun_where = "YEAR(p.tanggal_pendaftaran) < ?";
				$sisa_tahun_putus = "(pp.tanggal_putusan IS NULL OR YEAR(pp.tanggal_putusan) >= ?)";
				break;
			case 'custom':
				$tanggal_mulai = $this->input->post('tanggal_mulai') ?: date('Y-m-01');
				$tanggal_akhir = $this->input->post('tanggal_akhir') ?: date('Y-m-t');
				$masuk_where = "p.tanggal_pendaftaran BETWEEN ? AND ?";
				$putus_where = "pp.tanggal_putusan BETWEEN ? AND ?";
				$sisa_tahun_where = "p.tanggal_pendaftaran < ?";
				$sisa_tahun_putus = "(pp.tanggal_putusan IS NULL OR pp.tanggal_putusan > ?)";
				break;
			default: // bulanan
				$masuk_where = "YEAR(p.tanggal_pendaftaran) = ? AND MONTH(p.tanggal_pendaftaran) = ?";
				$putus_where = "YEAR(pp.tanggal_putusan) = ? AND MONTH(pp.tanggal_putusan) = ?";
				$sisa_bulan_where = "((YEAR(p.tanggal_pendaftaran) = ? AND MONTH(p.tanggal_pendaftaran) < ?) OR YEAR(p.tanggal_pendaftaran) < ?)";
				$sisa_bulan_putus = "(pp.tanggal_putusan IS NULL OR (YEAR(pp.tanggal_putusan) = ? AND MONTH(pp.tanggal_putusan) >= ?) OR YEAR(pp.tanggal_putusan) > ?)";
				$sisa_tahun_where = "YEAR(p.tanggal_pendaftaran) < ?";
				$sisa_tahun_putus = "(pp.tanggal_putusan IS NULL OR YEAR(pp.tanggal_putusan) >= ?)";
				break;
		}

		if ($jenis_laporan === 'bulanan') {
			$sql = "SELECT 
				locations.KECAMATAN,
				COALESCE(SUM(CASE WHEN subquery.date_type = 'perkara_masuk' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_MASUK,
				COALESCE(SUM(CASE WHEN subquery.date_type = 'perkara_putus' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_PUTUS,
				COALESCE(SUM(CASE WHEN subquery.date_type = 'sisa_bulan_lalu' THEN subquery.COUNT ELSE 0 END), 0) AS SISA_BULAN_LALU,
				COALESCE(SUM(CASE WHEN subquery.date_type = 'sisa_tahun_lalu' THEN subquery.COUNT ELSE 0 END), 0) AS SISA_TAHUN_LALU,
				(COALESCE(SUM(CASE WHEN subquery.date_type = 'sisa_bulan_lalu' THEN subquery.COUNT ELSE 0 END), 0) + 
				 COALESCE(SUM(CASE WHEN subquery.date_type = 'perkara_masuk' THEN subquery.COUNT ELSE 0 END), 0) - 
				 COALESCE(SUM(CASE WHEN subquery.date_type = 'perkara_putus' THEN subquery.COUNT ELSE 0 END), 0)) AS SISA_PERKARA
			FROM ({$locations_union}) AS locations
			LEFT JOIN (
				{$this->_main_subquery($case_when, 'perkara_masuk', $this->_from_join_masuk(), $masuk_where . ' AND p.jenis_perkara_nama LIKE ?')}
				UNION ALL
				{$this->_main_subquery($case_when, 'perkara_putus', $this->_from_join_putus(), $putus_where . ' AND p.jenis_perkara_nama LIKE ?')}
				UNION ALL
				{$this->_main_subquery($case_when, 'sisa_bulan_lalu', $this->_from_join_sisa(), $sisa_bulan_where . ' AND ' . $sisa_bulan_putus . ' AND p.jenis_perkara_nama LIKE ?')}
				UNION ALL
				{$this->_main_subquery($case_when, 'sisa_tahun_lalu', $this->_from_join_sisa(), $sisa_tahun_where . ' AND ' . $sisa_tahun_putus . ' AND p.jenis_perkara_nama LIKE ?')}
			) AS subquery ON locations.KECAMATAN = subquery.KECAMATAN
			GROUP BY locations.KECAMATAN
			ORDER BY locations.KECAMATAN";

			$params = [
				$lap_tahun, $lap_bulan, $like_pattern,      // perkara_masuk
				$lap_tahun, $lap_bulan, $like_pattern,       // perkara_putus
				$lap_tahun, $lap_bulan, $lap_tahun,          // sisa_bulan_lalu
				$lap_tahun, $lap_bulan, $lap_tahun,
				$like_pattern,
				$lap_tahun, $lap_tahun, $like_pattern        // sisa_tahun_lalu
			];

			$query = $this->db->query($sql, $params);
			return $query->result();
		}

		return array();
	}
}
