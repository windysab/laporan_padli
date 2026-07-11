<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_laporan extends CI_Model
{
	// Mappings for status putusan (from M_laporan_putusan)
	private $_status_map = array(
		'dikabulkan' => ' AND pp.status_putusan_id = 1',
		'ditolak' => ' AND pp.status_putusan_id = 2',
		'tidak_dapat_diterima' => ' AND pp.status_putusan_id IN (3, 4)',
		'dicabut' => ' AND pp.status_putusan_id = 7',
		'digugurkan' => ' AND pp.status_putusan_id IN (5, 6)',
	);

	// From M_laporan_gugatan
	public function __construct()
	{
		parent::__construct();
	}

	// ========== Methods from M_laporan_perceraian ==========

	// Get laporan perceraian bulanan
	public function get_laporan_perceraian_bulanan($lap_tahun, $lap_bulan, $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$where_wilayah = $this->_get_wilayah_condition_perceraian($wilayah);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($lap_tahun, $lap_bulan);
		if (!empty($where_jenis['sql'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				P.nomor_perkara,
				P.jenis_perkara_nama,
				P.pihak1_text AS nama_pihak_1,
				PHK1.nomor_indentitas AS nik_pihak_1,
				PHK1.pekerjaan AS pekerjaan_pihak_1,
				P.pihak2_text AS nama_pihak_2,
				PHK2.nomor_indentitas AS nik_pihak_2,
				PHK2.pekerjaan AS pekerjaan_pihak_2,
				DATE_FORMAT(A.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATE_FORMAT(A.tanggal_bht, '%d-%m-%Y') AS tanggal_bht,
				SP.nama AS status_putusan,
				C.nomor_akta_cerai,
				C.no_seri_akta_cerai,
				DATE_FORMAT(C.tgl_akta_cerai, '%d-%m-%Y') AS tgl_akta_cerai
			FROM perkara AS P
			INNER JOIN perkara_putusan AS A ON A.perkara_id = P.perkara_id
			INNER JOIN perkara_akta_cerai AS C ON C.perkara_id = P.perkara_id
			LEFT JOIN status_putusan AS SP ON SP.id = A.status_putusan_id
			LEFT JOIN perkara_pihak1 AS PP1 ON PP1.perkara_id = P.perkara_id
			LEFT JOIN pihak AS PHK1 ON PHK1.id = PP1.pihak_id
			LEFT JOIN perkara_pihak2 AS PP2 ON PP2.perkara_id = P.perkara_id
			LEFT JOIN pihak AS PHK2 ON PHK2.id = PP2.pihak_id
			WHERE C.tgl_akta_cerai IS NOT NULL
				AND YEAR(C.tgl_akta_cerai) = ?
				AND MONTH(C.tgl_akta_cerai) = ?
				{$where_wilayah}
				{$where_jenis['sql']}
			ORDER BY C.tgl_akta_cerai DESC, A.tanggal_putusan DESC";

		$query = $this->db->query($sql, $params);
		return $query->result();
	}

	// Get laporan perceraian tahunan
	public function get_laporan_perceraian_tahunan($lap_tahun, $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$where_wilayah = $this->_get_wilayah_condition_perceraian($wilayah);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($lap_tahun);
		if (!empty($where_jenis['sql'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				P.nomor_perkara,
				P.jenis_perkara_nama,
				P.pihak1_text AS nama_pihak_1,
				PHK1.nomor_indentitas AS nik_pihak_1,
				PHK1.pekerjaan AS pekerjaan_pihak_1,
				P.pihak2_text AS nama_pihak_2,
				PHK2.nomor_indentitas AS nik_pihak_2,
				PHK2.pekerjaan AS pekerjaan_pihak_2,
				DATE_FORMAT(A.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATE_FORMAT(A.tanggal_bht, '%d-%m-%Y') AS tanggal_bht,
				SP.nama AS status_putusan,
				C.nomor_akta_cerai,
				C.no_seri_akta_cerai,
				DATE_FORMAT(C.tgl_akta_cerai, '%d-%m-%Y') AS tgl_akta_cerai
			FROM perkara AS P
			INNER JOIN perkara_putusan AS A ON A.perkara_id = P.perkara_id
			INNER JOIN perkara_akta_cerai AS C ON C.perkara_id = P.perkara_id
			LEFT JOIN status_putusan AS SP ON SP.id = A.status_putusan_id
			LEFT JOIN perkara_pihak1 AS PP1 ON PP1.perkara_id = P.perkara_id
			LEFT JOIN pihak AS PHK1 ON PHK1.id = PP1.pihak_id
			LEFT JOIN perkara_pihak2 AS PP2 ON PP2.perkara_id = P.perkara_id
			LEFT JOIN pihak AS PHK2 ON PHK2.id = PP2.pihak_id
			WHERE C.tgl_akta_cerai IS NOT NULL
				AND YEAR(C.tgl_akta_cerai) = ?
				{$where_wilayah}
				{$where_jenis['sql']}
			ORDER BY C.tgl_akta_cerai DESC, A.tanggal_putusan DESC";

		$query = $this->db->query($sql, $params);
		return $query->result();
	}

	// Get laporan perceraian custom date range
	public function get_laporan_perceraian_custom($tanggal_mulai, $tanggal_akhir, $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$where_wilayah = $this->_get_wilayah_condition_perceraian($wilayah);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($tanggal_mulai, $tanggal_akhir);
		if (!empty($where_jenis['sql'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				P.nomor_perkara,
				P.jenis_perkara_nama,
				P.pihak1_text AS nama_pihak_1,
				PHK1.nomor_indentitas AS nik_pihak_1,
				PHK1.pekerjaan AS pekerjaan_pihak_1,
				P.pihak2_text AS nama_pihak_2,
				PHK2.nomor_indentitas AS nik_pihak_2,
				PHK2.pekerjaan AS pekerjaan_pihak_2,
				DATE_FORMAT(A.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATE_FORMAT(A.tanggal_bht, '%d-%m-%Y') AS tanggal_bht,
				SP.nama AS status_putusan,
				C.nomor_akta_cerai,
				C.no_seri_akta_cerai,
				DATE_FORMAT(C.tgl_akta_cerai, '%d-%m-%Y') AS tgl_akta_cerai
			FROM perkara AS P
			INNER JOIN perkara_putusan AS A ON A.perkara_id = P.perkara_id
			INNER JOIN perkara_akta_cerai AS C ON C.perkara_id = P.perkara_id
			LEFT JOIN status_putusan AS SP ON SP.id = A.status_putusan_id
			LEFT JOIN perkara_pihak1 AS PP1 ON PP1.perkara_id = P.perkara_id
			LEFT JOIN pihak AS PHK1 ON PHK1.id = PP1.pihak_id
			LEFT JOIN perkara_pihak2 AS PP2 ON PP2.perkara_id = P.perkara_id
			LEFT JOIN pihak AS PHK2 ON PHK2.id = PP2.pihak_id
			WHERE C.tgl_akta_cerai IS NOT NULL
				AND C.tgl_akta_cerai BETWEEN ? AND ?
				{$where_wilayah}
				{$where_jenis['sql']}
			ORDER BY C.tgl_akta_cerai DESC, A.tanggal_putusan DESC";

		$query = $this->db->query($sql, $params);
		return $query->result();
	}

	// Get summary perceraian bulanan
	public function get_summary_perceraian_bulanan($lap_tahun, $lap_bulan, $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$where_wilayah = $this->_get_wilayah_condition_perceraian($wilayah);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($lap_tahun, $lap_bulan);
		if (!empty($where_jenis['sql'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				COUNT(*) as total_perceraian,
				SUM(CASE WHEN P.jenis_perkara_nama LIKE '%Cerai Gugat%' THEN 1 ELSE 0 END) as cerai_gugat,
				SUM(CASE WHEN P.jenis_perkara_nama LIKE '%Cerai Talak%' THEN 1 ELSE 0 END) as cerai_talak
			FROM perkara AS P
			INNER JOIN perkara_putusan AS A ON A.perkara_id = P.perkara_id
			INNER JOIN perkara_akta_cerai AS C ON C.perkara_id = P.perkara_id
			LEFT JOIN perkara_pihak1 AS PP1 ON PP1.perkara_id = P.perkara_id
			WHERE C.tgl_akta_cerai IS NOT NULL
				AND YEAR(C.tgl_akta_cerai) = ?
				AND MONTH(C.tgl_akta_cerai) = ?
				{$where_wilayah}
				{$where_jenis['sql']}";

		$query = $this->db->query($sql, $params);
		return $query->row();
	}

	// Get summary perceraian tahunan
	public function get_summary_perceraian_tahunan($lap_tahun, $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$where_wilayah = $this->_get_wilayah_condition_perceraian($wilayah);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($lap_tahun);
		if (!empty($where_jenis['sql'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				COUNT(*) as total_perceraian,
				SUM(CASE WHEN P.jenis_perkara_nama LIKE '%Cerai Gugat%' THEN 1 ELSE 0 END) as cerai_gugat,
				SUM(CASE WHEN P.jenis_perkara_nama LIKE '%Cerai Talak%' THEN 1 ELSE 0 END) as cerai_talak
			FROM perkara AS P
			INNER JOIN perkara_putusan AS A ON A.perkara_id = P.perkara_id
			INNER JOIN perkara_akta_cerai AS C ON C.perkara_id = P.perkara_id
			LEFT JOIN perkara_pihak1 AS PP1 ON PP1.perkara_id = P.perkara_id
			WHERE C.tgl_akta_cerai IS NOT NULL
				AND YEAR(C.tgl_akta_cerai) = ?
				{$where_wilayah}
				{$where_jenis['sql']}";

		$query = $this->db->query($sql, $params);
		return $query->row();
	}

	// Get summary perceraian custom
	public function get_summary_perceraian_custom($tanggal_mulai, $tanggal_akhir, $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$where_wilayah = $this->_get_wilayah_condition_perceraian($wilayah);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($tanggal_mulai, $tanggal_akhir);
		if (!empty($where_jenis['sql'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				COUNT(*) as total_perceraian,
				SUM(CASE WHEN P.jenis_perkara_nama LIKE '%Cerai Gugat%' THEN 1 ELSE 0 END) as cerai_gugat,
				SUM(CASE WHEN P.jenis_perkara_nama LIKE '%Cerai Talak%' THEN 1 ELSE 0 END) as cerai_talak
			FROM perkara AS P
			INNER JOIN perkara_putusan AS A ON A.perkara_id = P.perkara_id
			INNER JOIN perkara_akta_cerai AS C ON C.perkara_id = P.perkara_id
			LEFT JOIN perkara_pihak1 AS PP1 ON PP1.perkara_id = P.perkara_id
			WHERE C.tgl_akta_cerai IS NOT NULL
				AND C.tgl_akta_cerai BETWEEN ? AND ?
				{$where_wilayah}
				{$where_jenis['sql']}";

		$query = $this->db->query($sql, $params);
		return $query->row();
	}

	// Get jenis perkara perceraian
	public function get_jenis_perkara_perceraian()
	{
		$sql = "SELECT DISTINCT P.jenis_perkara_nama
				FROM perkara P
				INNER JOIN perkara_akta_cerai C ON C.perkara_id = P.perkara_id
				WHERE P.jenis_perkara_nama IS NOT NULL
				  AND P.jenis_perkara_nama != ''
				  AND C.tgl_akta_cerai IS NOT NULL
				ORDER BY P.jenis_perkara_nama";

		return $this->db->query($sql)->result();
	}

	// ========== Methods from M_laporan_putusan ==========

	// Get laporan putusan bulanan
	public function get_laporan_putusan_bulanan($lap_tahun, $lap_bulan, $status_putusan = 'semua', $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$base_sql = $this->_build_base_select();
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$where_status = $this->_get_status_condition($status_putusan);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($lap_tahun, $lap_bulan);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "{$base_sql}
			WHERE p.jenis_perkara_nama IS NOT NULL
				AND pp.tanggal_putusan IS NOT NULL
				AND YEAR(pp.tanggal_putusan) = ?
				AND MONTH(pp.tanggal_putusan) = ?
				{$where_status}
				{$where_wilayah}
				{$where_jenis['sql']}
			ORDER BY pp.tanggal_putusan DESC";

		return $this->db->query($sql, $params)->result();
	}

	// Get laporan putusan tahunan
	public function get_laporan_putusan_tahunan($lap_tahun, $status_putusan = 'semua', $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$base_sql = $this->_build_base_select();
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$where_status = $this->_get_status_condition($status_putusan);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($lap_tahun);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "{$base_sql}
			WHERE p.jenis_perkara_nama IS NOT NULL
				AND pp.tanggal_putusan IS NOT NULL
				AND YEAR(pp.tanggal_putusan) = ?
				{$where_status}
				{$where_wilayah}
				{$where_jenis['sql']}
			ORDER BY pp.tanggal_putusan DESC";

		return $this->db->query($sql, $params)->result();
	}

	// Get laporan putusan custom date range
	public function get_laporan_putusan_custom($tanggal_mulai, $tanggal_akhir, $status_putusan = 'semua', $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$base_sql = $this->_build_base_select();
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$where_status = $this->_get_status_condition($status_putusan);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($tanggal_mulai, $tanggal_akhir);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "{$base_sql}
			WHERE p.jenis_perkara_nama IS NOT NULL
				AND pp.tanggal_putusan IS NOT NULL
				AND pp.tanggal_putusan BETWEEN ? AND ?
				{$where_status}
				{$where_wilayah}
				{$where_jenis['sql']}
			ORDER BY pp.tanggal_putusan DESC";

		return $this->db->query($sql, $params)->result();
	}

	// Get summary putusan bulanan
	public function get_summary_putusan_bulanan($lap_tahun, $lap_bulan, $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($lap_tahun, $lap_bulan);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				COUNT(*) as total_putusan,
				SUM(CASE WHEN pp.status_putusan_id = 1 THEN 1 ELSE 0 END) as dikabulkan,
				SUM(CASE WHEN pp.status_putusan_id = 2 THEN 1 ELSE 0 END) as ditolak,
				SUM(CASE WHEN pp.status_putusan_id IN (3, 4) THEN 1 ELSE 0 END) as tidak_dapat_diterima,
				SUM(CASE WHEN pp.status_putusan_id = 7 THEN 1 ELSE 0 END) as dicabut,
				SUM(CASE WHEN pp.status_putusan_id IN (5, 6) THEN 1 ELSE 0 END) as digugurkan
			FROM perkara p
			JOIN perkara_putusan pp ON pp.perkara_id = p.perkara_id
			LEFT JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
			WHERE p.jenis_perkara_nama IS NOT NULL
				AND YEAR(pp.tanggal_putusan) = ?
				AND MONTH(pp.tanggal_putusan) = ?
				{$where_wilayah}
				{$where_jenis['sql']}";

		return $this->db->query($sql, $params)->row();
	}

	// Get summary putusan tahunan
	public function get_summary_putusan_tahunan($lap_tahun, $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($lap_tahun);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				COUNT(*) as total_putusan,
				SUM(CASE WHEN pp.status_putusan_id = 1 THEN 1 ELSE 0 END) as dikabulkan,
				SUM(CASE WHEN pp.status_putusan_id = 2 THEN 1 ELSE 0 END) as ditolak,
				SUM(CASE WHEN pp.status_putusan_id IN (3, 4) THEN 1 ELSE 0 END) as tidak_dapat_diterima,
				SUM(CASE WHEN pp.status_putusan_id = 7 THEN 1 ELSE 0 END) as dicabut,
				SUM(CASE WHEN pp.status_putusan_id IN (5, 6) THEN 1 ELSE 0 END) as digugurkan
			FROM perkara p
			JOIN perkara_putusan pp ON pp.perkara_id = p.perkara_id
			LEFT JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
			WHERE p.jenis_perkara_nama IS NOT NULL
				AND YEAR(pp.tanggal_putusan) = ?
				{$where_wilayah}
				{$where_jenis['sql']}";

		return $this->db->query($sql, $params)->row();
	}

	// Get summary putusan custom
	public function get_summary_putusan_custom($tanggal_mulai, $tanggal_akhir, $wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$params = array($tanggal_mulai, $tanggal_akhir);
		if (!empty($where_jenis['params'])) {
			$params = array_merge($params, $where_jenis['params']);
		}

		$sql = "SELECT
				COUNT(*) as total_putusan,
				SUM(CASE WHEN pp.status_putusan_id = 1 THEN 1 ELSE 0 END) as dikabulkan,
				SUM(CASE WHEN pp.status_putusan_id = 2 THEN 1 ELSE 0 END) as ditolak,
				SUM(CASE WHEN pp.status_putusan_id IN (3, 4) THEN 1 ELSE 0 END) as tidak_dapat_diterima,
				SUM(CASE WHEN pp.status_putusan_id = 7 THEN 1 ELSE 0 END) as dicabut,
				SUM(CASE WHEN pp.status_putusan_id IN (5, 6) THEN 1 ELSE 0 END) as digugurkan
			FROM perkara p
			JOIN perkara_putusan pp ON pp.perkara_id = p.perkara_id
			LEFT JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
			WHERE p.jenis_perkara_nama IS NOT NULL
				AND pp.tanggal_putusan BETWEEN ? AND ?
				{$where_wilayah}
				{$where_jenis['sql']}";

		return $this->db->query($sql, $params)->row();
	}

	// Get daftar jenis perkara yang tersedia
	public function get_jenis_perkara_list()
	{
		$sql = "SELECT DISTINCT p.jenis_perkara_nama
				FROM perkara p
				JOIN perkara_putusan pp ON pp.perkara_id = p.perkara_id
				WHERE p.jenis_perkara_nama IS NOT NULL
				  AND p.jenis_perkara_nama != ''
				ORDER BY p.jenis_perkara_nama";

		return $this->db->query($sql)->result();
	}

	// Get daftar jenis perkara gugatan saja
	public function get_jenis_perkara_gugatan()
	{
		$sql = "SELECT DISTINCT p.jenis_perkara_nama
				FROM perkara p
				JOIN perkara_putusan pp ON pp.perkara_id = p.perkara_id
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

	// Get daftar status putusan yang tersedia
	public function get_status_putusan_list()
	{
		$sql = "SELECT DISTINCT sp.id, sp.nama as status_putusan_nama
				FROM status_putusan sp
				JOIN perkara_putusan pp ON sp.id = pp.status_putusan_id
				WHERE sp.nama IS NOT NULL
				  AND sp.nama != ''
				ORDER BY sp.nama";

		return $this->db->query($sql)->result();
	}

	// ========== Methods from M_laporan_gugatan ==========

	// Get monthly report data
	public function get_laporan_bulanan($bulan, $tahun, $format = 'lengkap')
	{
		$sql = "SELECT
					p.perkara_id,
					p.nomor_perkara,
					p.tanggal_pendaftaran,
					p.jenis_perkara_nama,
					p.pihak1_text as penggugat,
					p.pihak2_text as tergugat,
					pp.tanggal_putusan,
					sp.nama as status_putusan,
					pp.status_putusan_nama,
					p.tahapan_terakhir_text,
					p.proses_terakhir_text
				FROM perkara p
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				LEFT JOIN status_putusan sp ON pp.status_putusan_id = sp.id
				WHERE MONTH(p.tanggal_pendaftaran) = ?
				AND YEAR(p.tanggal_pendaftaran) = ?
				AND p.jenis_perkara_nama LIKE '%Cerai Gugat%'";

		if ($format == 'ringkas') {
			$sql .= " AND pp.status_putusan_id IS NOT NULL";
		}

		$sql .= " ORDER BY p.tanggal_pendaftaran DESC, p.nomor_perkara";

		$result = $this->db->query($sql, array($bulan, $tahun));
		return $result->result();
	}

	// Get yearly report data
	public function get_laporan_tahunan($tahun, $format = 'lengkap')
	{
		$sql = "SELECT
					p.perkara_id,
					p.nomor_perkara,
					p.tanggal_pendaftaran,
					p.jenis_perkara_nama,
					p.pihak1_text as penggugat,
					p.pihak2_text as tergugat,
					pp.tanggal_putusan,
					sp.nama as status_putusan,
					pp.status_putusan_nama,
					p.tahapan_terakhir_text,
					p.proses_terakhir_text,
					MONTH(p.tanggal_pendaftaran) as bulan
				FROM perkara p
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				LEFT JOIN status_putusan sp ON pp.status_putusan_id = sp.id
				WHERE YEAR(p.tanggal_pendaftaran) = ?
				AND p.jenis_perkara_nama LIKE '%Cerai Gugat%'";

		if ($format == 'ringkas') {
			$sql .= " AND pp.status_putusan_id IS NOT NULL";
		}

		$sql .= " ORDER BY p.tanggal_pendaftaran DESC, p.nomor_perkara";

		$result = $this->db->query($sql, array($tahun));
		return $result->result();
	}

	// Get semester report data
	public function get_laporan_semester($semester, $tahun, $format = 'lengkap')
	{
		$start_month = ($semester == '1') ? 1 : 7;
		$end_month = ($semester == '1') ? 6 : 12;

		$sql = "SELECT
					p.perkara_id,
					p.nomor_perkara,
					p.tanggal_pendaftaran,
					p.jenis_perkara_nama,
					p.pihak1_text as penggugat,
					p.pihak2_text as tergugat,
					pp.tanggal_putusan,
					sp.nama as status_putusan,
					pp.status_putusan_nama,
					p.tahapan_terakhir_text,
					p.proses_terakhir_text,
					MONTH(p.tanggal_pendaftaran) as bulan
				FROM perkara p
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				LEFT JOIN status_putusan sp ON pp.status_putusan_id = sp.id
				WHERE YEAR(p.tanggal_pendaftaran) = ?
				AND MONTH(p.tanggal_pendaftaran) BETWEEN ? AND ?
				AND p.jenis_perkara_nama LIKE '%Cerai Gugat%'";

		if ($format == 'ringkas') {
			$sql .= " AND pp.status_putusan_id IS NOT NULL";
		}

		$sql .= " ORDER BY p.tanggal_pendaftaran DESC, p.nomor_perkara";

		$result = $this->db->query($sql, array($tahun, $start_month, $end_month));
		return $result->result();
	}

	// Get quarterly report data
	public function get_laporan_triwulan($triwulan, $tahun, $format = 'lengkap')
	{
		$start_month = (($triwulan - 1) * 3) + 1;
		$end_month = $triwulan * 3;

		$sql = "SELECT
					p.perkara_id,
					p.nomor_perkara,
					p.tanggal_pendaftaran,
					p.jenis_perkara_nama,
					p.pihak1_text as penggugat,
					p.pihak2_text as tergugat,
					pp.tanggal_putusan,
					sp.nama as status_putusan,
					pp.status_putusan_nama,
					p.tahapan_terakhir_text,
					p.proses_terakhir_text,
					MONTH(p.tanggal_pendaftaran) as bulan
				FROM perkara p
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				LEFT JOIN status_putusan sp ON pp.status_putusan_id = sp.id
				WHERE YEAR(p.tanggal_pendaftaran) = ?
				AND MONTH(p.tanggal_pendaftaran) BETWEEN ? AND ?
				AND p.jenis_perkara_nama LIKE '%Cerai Gugat%'";

		if ($format == 'ringkas') {
			$sql .= " AND pp.status_putusan_id IS NOT NULL";
		}

		$sql .= " ORDER BY p.tanggal_pendaftaran DESC, p.nomor_perkara";

		$result = $this->db->query($sql, array($tahun, $start_month, $end_month));
		return $result->result();
	}

	// Get custom date range report data
	public function get_laporan_custom($tanggal_mulai, $tanggal_akhir, $format = 'lengkap')
	{
		$sql = "SELECT
					p.perkara_id,
					p.nomor_perkara,
					p.tanggal_pendaftaran,
					p.jenis_perkara_nama,
					p.pihak1_text as penggugat,
					p.pihak2_text as tergugat,
					pp.tanggal_putusan,
					sp.nama as status_putusan,
					pp.status_putusan_nama,
					p.tahapan_terakhir_text,
					p.proses_terakhir_text
				FROM perkara p
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				LEFT JOIN status_putusan sp ON pp.status_putusan_id = sp.id
				WHERE p.tanggal_pendaftaran BETWEEN ? AND ?
				AND p.jenis_perkara_nama LIKE '%Cerai Gugat%'";

		if ($format == 'ringkas') {
			$sql .= " AND pp.status_putusan_id IS NOT NULL";
		}

		$sql .= " ORDER BY p.tanggal_pendaftaran DESC, p.nomor_perkara";

		$result = $this->db->query($sql, array($tanggal_mulai, $tanggal_akhir));
		return $result->result();
	}

	// Get summary data for monthly report
	public function get_summary_bulanan($bulan, $tahun)
	{
		$sql = "SELECT
					COUNT(*) as total_perkara,
					SUM(CASE WHEN pp.status_putusan_id = 1 THEN 1 ELSE 0 END) as dikabulkan,
					SUM(CASE WHEN pp.status_putusan_id = 2 THEN 1 ELSE 0 END) as ditolak,
					SUM(CASE WHEN pp.status_putusan_id = 7 THEN 1 ELSE 0 END) as dicabut,
					COUNT(CASE WHEN pp.status_putusan_id IS NULL THEN 1 END) as belum_putusan,
					ROUND(AVG(DATEDIFF(pp.tanggal_putusan, p.tanggal_pendaftaran)), 0) as rata_hari
				FROM perkara p
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				WHERE MONTH(p.tanggal_pendaftaran) = ?
				AND YEAR(p.tanggal_pendaftaran) = ?
				AND p.jenis_perkara_nama LIKE '%Cerai Gugat%'";

		$result = $this->db->query($sql, array($bulan, $tahun));
		return $result->row();
	}

	// Get summary data for yearly report
	public function get_summary_tahunan($tahun)
	{
		$sql = "SELECT
					COUNT(*) as total_perkara,
					SUM(CASE WHEN pp.status_putusan_id = 1 THEN 1 ELSE 0 END) as dikabulkan,
					SUM(CASE WHEN pp.status_putusan_id = 2 THEN 1 ELSE 0 END) as ditolak,
					SUM(CASE WHEN pp.status_putusan_id = 7 THEN 1 ELSE 0 END) as dicabut,
					COUNT(CASE WHEN pp.status_putusan_id IS NULL THEN 1 END) as belum_putusan,
					ROUND(AVG(DATEDIFF(pp.tanggal_putusan, p.tanggal_pendaftaran)), 0) as rata_hari
				FROM perkara p
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				WHERE YEAR(p.tanggal_pendaftaran) = ?
				AND p.jenis_perkara_nama LIKE '%Cerai Gugat%'";

		$result = $this->db->query($sql, array($tahun));
		return $result->row();
	}

	// Get summary data for semester report
	public function get_summary_semester($semester, $tahun)
	{
		$start_month = ($semester == '1') ? 1 : 7;
		$end_month = ($semester == '1') ? 6 : 12;

		$sql = "SELECT
					COUNT(*) as total_perkara,
					SUM(CASE WHEN pp.status_putusan_id = 1 THEN 1 ELSE 0 END) as dikabulkan,
					SUM(CASE WHEN pp.status_putusan_id = 2 THEN 1 ELSE 0 END) as ditolak,
					SUM(CASE WHEN pp.status_putusan_id = 7 THEN 1 ELSE 0 END) as dicabut,
					COUNT(CASE WHEN pp.status_putusan_id IS NULL THEN 1 END) as belum_putusan,
					ROUND(AVG(DATEDIFF(pp.tanggal_putusan, p.tanggal_pendaftaran)), 0) as rata_hari
				FROM perkara p
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				WHERE YEAR(p.tanggal_pendaftaran) = ?
				AND MONTH(p.tanggal_pendaftaran) BETWEEN ? AND ?
				AND p.jenis_perkara_nama LIKE '%Cerai Gugat%'";

		$result = $this->db->query($sql, array($tahun, $start_month, $end_month));
		return $result->row();
	}

	// Get summary data for quarterly report
	public function get_summary_triwulan($triwulan, $tahun)
	{
		$start_month = (($triwulan - 1) * 3) + 1;
		$end_month = $triwulan * 3;

		$sql = "SELECT
					COUNT(*) as total_perkara,
					SUM(CASE WHEN pp.status_putusan_id = 1 THEN 1 ELSE 0 END) as dikabulkan,
					SUM(CASE WHEN pp.status_putusan_id = 2 THEN 1 ELSE 0 END) as ditolak,
					SUM(CASE WHEN pp.status_putusan_id = 7 THEN 1 ELSE 0 END) as dicabut,
					COUNT(CASE WHEN pp.status_putusan_id IS NULL THEN 1 END) as belum_putusan,
					ROUND(AVG(DATEDIFF(pp.tanggal_putusan, p.tanggal_pendaftaran)), 0) as rata_hari
				FROM perkara p
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				WHERE YEAR(p.tanggal_pendaftaran) = ?
				AND MONTH(p.tanggal_pendaftaran) BETWEEN ? AND ?
				AND p.jenis_perkara_nama LIKE '%Cerai Gugat%'";

		$result = $this->db->query($sql, array($tahun, $start_month, $end_month));
		return $result->row();
	}

	// Get summary data for custom date range
	public function get_summary_custom($tanggal_mulai, $tanggal_akhir)
	{
		$sql = "SELECT
					COUNT(*) as total_perkara,
					SUM(CASE WHEN pp.status_putusan_id = 1 THEN 1 ELSE 0 END) as dikabulkan,
					SUM(CASE WHEN pp.status_putusan_id = 2 THEN 1 ELSE 0 END) as ditolak,
					SUM(CASE WHEN pp.status_putusan_id = 7 THEN 1 ELSE 0 END) as dicabut,
					COUNT(CASE WHEN pp.status_putusan_id IS NULL THEN 1 END) as belum_putusan,
					ROUND(AVG(DATEDIFF(pp.tanggal_putusan, p.tanggal_pendaftaran)), 0) as rata_hari
				FROM perkara p
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				WHERE p.tanggal_pendaftaran BETWEEN ? AND ?
				AND p.jenis_perkara_nama LIKE '%Cerai Gugat%'";

		$result = $this->db->query($sql, array($tanggal_mulai, $tanggal_akhir));
		return $result->row();
	}

	// Helper methods for summary metrics
	public function get_total_perkara($bulan, $tahun, $jenis_laporan)
	{
		switch ($jenis_laporan) {
			case 'tahunan':
				$summary = $this->get_summary_tahunan($tahun);
				break;
			default:
				$summary = $this->get_summary_bulanan($bulan, $tahun);
				break;
		}
		return $summary ? $summary->total_perkara : 0;
	}

	public function get_total_dikabulkan($bulan, $tahun, $jenis_laporan)
	{
		switch ($jenis_laporan) {
			case 'tahunan':
				$summary = $this->get_summary_tahunan($tahun);
				break;
			default:
				$summary = $this->get_summary_bulanan($bulan, $tahun);
				break;
		}
		return $summary ? $summary->dikabulkan : 0;
	}

	public function get_total_ditolak($bulan, $tahun, $jenis_laporan)
	{
		switch ($jenis_laporan) {
			case 'tahunan':
				$summary = $this->get_summary_tahunan($tahun);
				break;
			default:
				$summary = $this->get_summary_bulanan($bulan, $tahun);
				break;
		}
		return $summary ? $summary->ditolak : 0;
	}

	public function get_total_dicabut($bulan, $tahun, $jenis_laporan)
	{
		switch ($jenis_laporan) {
			case 'tahunan':
				$summary = $this->get_summary_tahunan($tahun);
				break;
			default:
				$summary = $this->get_summary_bulanan($bulan, $tahun);
				break;
		}
		return $summary ? $summary->dicabut : 0;
	}

	// Export methods
	public function get_laporan_export($jenis_laporan, $bulan, $tahun)
	{
		switch ($jenis_laporan) {
			case 'tahunan':
				return $this->get_laporan_tahunan($tahun, 'lengkap');
			case 'semester':
				$semester = ($bulan <= 6) ? '1' : '2';
				return $this->get_laporan_semester($semester, $tahun, 'lengkap');
			case 'triwulan':
				$triwulan = ceil($bulan / 3);
				return $this->get_laporan_triwulan($triwulan, $tahun, 'lengkap');
			default:
				return $this->get_laporan_bulanan($bulan, $tahun, 'lengkap');
		}
	}

	public function get_laporan_pdf($jenis_laporan, $bulan, $tahun)
	{
		return $this->get_laporan_export($jenis_laporan, $bulan, $tahun);
	}

	public function get_laporan_print($jenis_laporan, $bulan, $tahun)
	{
		return $this->get_laporan_export($jenis_laporan, $bulan, $tahun);
	}

	// ========== Private Helpers ==========

	// Private helper from M_laporan_perceraian (renamed to avoid conflict with putusan version)
	private function _get_wilayah_condition_perceraian($wilayah)
	{
		if ($wilayah === 'Semua') return '';

		$hsu_condition = "(PP1.alamat LIKE '%Hulu Sungai Utara%' OR PP1.alamat LIKE '%HSU%'
					   OR PP1.alamat LIKE '%Amuntai%' OR PP1.alamat LIKE '%Haur Gading%'
					   OR PP1.alamat LIKE '%Banjang%' OR PP1.alamat LIKE '%Paminggir%'
					   OR PP1.alamat LIKE '%Babirik%' OR PP1.alamat LIKE '%Sungai Pandan%'
					   OR PP1.alamat LIKE '%Danau Panggang%' OR PP1.alamat LIKE '%Sungai Tabukan%')";

		$balangan_condition = "(PP1.alamat LIKE '%Balangan%' OR PP1.alamat LIKE '%Paringin%'
					   OR PP1.alamat LIKE '%Awayan%' OR PP1.alamat LIKE '%Tebing Tinggi%'
					   OR PP1.alamat LIKE '%Juai%' OR PP1.alamat LIKE '%Lampihong%'
					   OR PP1.alamat LIKE '%Halong%' OR PP1.alamat LIKE '%Batumandi%')";

		if ($wilayah === 'HSU') {
			return " AND {$hsu_condition}";
		} else if ($wilayah === 'Balangan') {
			return " AND {$balangan_condition} AND NOT {$hsu_condition}";
		}

		return '';
	}

	// Private helper from M_laporan_putusan
	private function _get_wilayah_condition($wilayah)
	{
		if ($wilayah === 'Semua') return '';

		if ($wilayah === 'HSU') {
			return " AND (pp1.alamat LIKE '%Hulu Sungai Utara%' OR pp1.alamat LIKE '%HSU%'
					   OR pp1.alamat LIKE '%Amuntai%' OR pp1.alamat LIKE '%Haur Gading%'
					   OR pp1.alamat LIKE '%Banjang%' OR pp1.alamat LIKE '%Paminggir%'
					   OR pp1.alamat LIKE '%Babirik%' OR pp1.alamat LIKE '%Sungai Pandan%'
					   OR pp1.alamat LIKE '%Danau Panggang%' OR pp1.alamat LIKE '%Sungai Tabukan%')";
		} else if ($wilayah === 'Balangan') {
			return " AND (pp1.alamat LIKE '%Balangan%' OR pp1.alamat LIKE '%Paringin%'
					   OR pp1.alamat LIKE '%Awayan%' OR pp1.alamat LIKE '%Tebing Tinggi%'
					   OR pp1.alamat LIKE '%Juai%' OR pp1.alamat LIKE '%Lampihong%'
					   OR pp1.alamat LIKE '%Halong%' OR pp1.alamat LIKE '%Batumandi%')";
		}

		return '';
	}

	// Fixed: returns array with 'sql' and 'params' for parameter binding
	// (identical implementation in both M_laporan_perceraian and M_laporan_putusan)
	private function _get_jenis_perkara_condition($jenis_perkara)
	{
		if ($jenis_perkara === 'semua' || empty($jenis_perkara)) {
			return array('sql' => '', 'params' => array());
		}

		return array(
			'sql' => ' AND p.jenis_perkara_nama = ?',
			'params' => array($jenis_perkara)
		);
	}

	// Private helper from M_laporan_putusan
	private function _build_base_select()
	{
		return "SELECT
				p.nomor_perkara,
				p.jenis_perkara_nama,
				p.pihak1_text AS pihak1,
				p.pihak2_text AS pihak2,
				DATE_FORMAT(pp.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				pp.status_putusan_id,
				COALESCE(sp.nama, pp.status_putusan_nama) AS status_putusan_nama,
				LEFT(pp.amar_putusan, 400) AS ringkasan_amar,
				DATEDIFF(CURDATE(), pp.tanggal_putusan) AS hari_sejak_putusan
			FROM perkara p
			INNER JOIN perkara_putusan pp ON pp.perkara_id = p.perkara_id
			LEFT JOIN status_putusan sp ON pp.status_putusan_id = sp.id
			LEFT JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id";
	}

	// Fixed: returns SQL string (hardcoded status IDs are safe)
	private function _get_status_condition($status_putusan)
	{
		if ($status_putusan === 'semua' || empty($status_putusan)) return '';

		// Array mapping instead of switch
		if (isset($this->_status_map[$status_putusan])) {
			return $this->_status_map[$status_putusan];
		}

		// Jika berupa ID numerik
		if (is_numeric($status_putusan)) {
			return ' AND pp.status_putusan_id = ' . (int)$status_putusan;
		}

		return '';
	}
}
