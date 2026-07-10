<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_data_perkara_gugatan extends CI_Model
{
	// --- Shared helpers ---

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

	// --- Consolidated subquery builder ---

	// Build JOIN clause based on which table carries the date field
	private function _build_join_clause($table)
	{
		if ($table === 'perkara') return '';
		if ($table === 'perkara_putusan') return "LEFT JOIN perkara ON perkara_putusan.perkara_id = perkara.perkara_id ";
		if ($table === 'perkara_akta_cerai') return "LEFT JOIN perkara ON perkara_akta_cerai.perkara_id = perkara.perkara_id ";
		return "LEFT JOIN perkara ON {$table}.perkara_id = perkara.perkara_id ";
	}

	// Build a single subquery SQL
	// $where: SQL WHERE clause with __DATE_FIELD__ placeholder
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

	// Build all 4 subqueries UNIONed
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

	// --- Main data methods ---

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

	// --- Dropdown helpers ---

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
}
