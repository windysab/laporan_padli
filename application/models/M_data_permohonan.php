
<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_data_permohonan extends CI_Model
{
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

	// Build a single subquery for the main section
	private function _main_subquery($case_when, $date_type, $from_join, $where)
	{
		return "SELECT 
			{$case_when} AS KECAMATAN,
			'{$date_type}' AS date_type, COUNT(*) AS COUNT
		{$from_join}
		WHERE {$where}
		GROUP BY KECAMATAN";
	}

	// Build a single subquery for the TOTAL section
	private function _total_subquery($date_type, $from_join, $where, $address_filter)
	{
		return "SELECT 
			'TOTAL' AS KECAMATAN,
			'{$date_type}' AS date_type, COUNT(*) AS COUNT
		{$from_join}
		WHERE {$where}
			AND {$address_filter}";
	}

	// FROM/JOIN for the main subquery types
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
