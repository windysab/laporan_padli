<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_akta_cerai extends CI_Model
{

	// ==================== PENERBITAN AKTA CERAI ====================

	public function get_penerbitan_akta_cerai($lap_tahun, $lap_bulan)
	{
		$sql = "SELECT 
				pac.nomor_akta_cerai,
				pac.tgl_akta_cerai,
				pac.no_seri_akta_cerai,
				pac.jenis_cerai,
				p.nomor_perkara,
				p.tanggal_pendaftaran,
				p.jenis_perkara_nama,
				pp.tanggal_putusan,
				pp.tanggal_bht,
				pp.status_putusan_nama,
				pit.tgl_ikrar_talak,
				COALESCE(p.pihak1_text, 'Tidak Ada Data') as penggugat,
				COALESCE(p.pihak2_text, 'Tidak Ada Data') as tergugat,
				fc.nama as faktor_perceraian
			FROM perkara_akta_cerai pac
			INNER JOIN perkara p ON pac.perkara_id = p.perkara_id
			LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			LEFT JOIN perkara_ikrar_talak pit ON p.perkara_id = pit.perkara_id
			LEFT JOIN faktor_perceraian fc ON pac.faktor_perceraian_id = fc.id
			WHERE YEAR(pac.tgl_akta_cerai) = ? 
			  AND MONTH(pac.tgl_akta_cerai) = ?
			  AND pac.nomor_akta_cerai IS NOT NULL
			ORDER BY pac.tgl_akta_cerai DESC, p.nomor_perkara";
			
		$query = $this->db->query($sql, array($lap_tahun, $lap_bulan));
		return $query->result();
	}
	
	public function get_penerbitan_akta_cerai_tahunan($lap_tahun)
	{
		$sql = "SELECT 
				pac.nomor_akta_cerai,
				pac.tgl_akta_cerai,
				pac.no_seri_akta_cerai,
				pac.jenis_cerai,
				p.nomor_perkara,
				p.tanggal_pendaftaran,
				p.jenis_perkara_nama,
				pp.tanggal_putusan,
				pp.tanggal_bht,
				pp.status_putusan_nama,
				pit.tgl_ikrar_talak,
				COALESCE(p.pihak1_text, 'Tidak Ada Data') as penggugat,
				COALESCE(p.pihak2_text, 'Tidak Ada Data') as tergugat,
				fc.nama as faktor_perceraian
			FROM perkara_akta_cerai pac
			INNER JOIN perkara p ON pac.perkara_id = p.perkara_id
			LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			LEFT JOIN perkara_ikrar_talak pit ON p.perkara_id = pit.perkara_id
			LEFT JOIN faktor_perceraian fc ON pac.faktor_perceraian_id = fc.id
			WHERE YEAR(pac.tgl_akta_cerai) = ?
			  AND pac.nomor_akta_cerai IS NOT NULL
			ORDER BY pac.tgl_akta_cerai DESC, p.nomor_perkara";
			
		$query = $this->db->query($sql, array($lap_tahun));
		return $query->result();
	}
	
	public function get_penerbitan_akta_cerai_custom($tanggal_mulai, $tanggal_akhir)
	{
		$sql = "SELECT 
				pac.nomor_akta_cerai,
				pac.tgl_akta_cerai,
				pac.no_seri_akta_cerai,
				pac.jenis_cerai,
				p.nomor_perkara,
				p.tanggal_pendaftaran,
				p.jenis_perkara_nama,
				pp.tanggal_putusan,
				pp.tanggal_bht,
				pp.status_putusan_nama,
				pit.tgl_ikrar_talak,
				COALESCE(p.pihak1_text, 'Tidak Ada Data') as penggugat,
				COALESCE(p.pihak2_text, 'Tidak Ada Data') as tergugat,
				fc.nama as faktor_perceraian
			FROM perkara_akta_cerai pac
			INNER JOIN perkara p ON pac.perkara_id = p.perkara_id
			LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			LEFT JOIN perkara_ikrar_talak pit ON p.perkara_id = pit.perkara_id
			LEFT JOIN faktor_perceraian fc ON pac.faktor_perceraian_id = fc.id
			WHERE pac.tgl_akta_cerai BETWEEN ? AND ?
			  AND pac.nomor_akta_cerai IS NOT NULL
			ORDER BY pac.tgl_akta_cerai DESC, p.nomor_perkara";
			
		$query = $this->db->query($sql, array($tanggal_mulai, $tanggal_akhir));
		return $query->result();
	}
	
	public function get_summary_statistics($lap_tahun, $lap_bulan, $jenis_laporan = 'bulanan')
	{
		$where_clause = "";
		$params = array();
		
		switch ($jenis_laporan) {
			case 'tahunan':
				$where_clause = "YEAR(pac.tgl_akta_cerai) = ?";
				$params = array($lap_tahun);
				break;
			default: // bulanan
				$where_clause = "YEAR(pac.tgl_akta_cerai) = ? AND MONTH(pac.tgl_akta_cerai) = ?";
				$params = array($lap_tahun, $lap_bulan);
				break;
		}
		
		$sql = "SELECT 
				COUNT(*) as total_akta_cerai,
				SUM(CASE WHEN pac.jenis_cerai = 'Cerai Talak' THEN 1 ELSE 0 END) as cerai_talak,
				SUM(CASE WHEN pac.jenis_cerai = 'Cerai Gugat' THEN 1 ELSE 0 END) as cerai_gugat,
				SUM(CASE WHEN pac.tgl_penyerahan_akta_cerai IS NOT NULL THEN 1 ELSE 0 END) as sudah_diserahkan,
				SUM(CASE WHEN pac.tgl_penyerahan_akta_cerai IS NULL THEN 1 ELSE 0 END) as belum_diserahkan
			FROM perkara_akta_cerai pac
			INNER JOIN perkara p ON pac.perkara_id = p.perkara_id
			WHERE {$where_clause}
			  AND pac.nomor_akta_cerai IS NOT NULL";
			
		$query = $this->db->query($sql, $params);
		return $query->row();
	}
	
	public function get_monthly_summary($tahun)
	{
		$sql = "SELECT 
				MONTH(pac.tgl_akta_cerai) as bulan,
				MONTHNAME(pac.tgl_akta_cerai) as nama_bulan,
				COUNT(*) as jumlah
			FROM perkara_akta_cerai pac
			INNER JOIN perkara p ON pac.perkara_id = p.perkara_id
			WHERE YEAR(pac.tgl_akta_cerai) = ?
			  AND pac.nomor_akta_cerai IS NOT NULL
			GROUP BY MONTH(pac.tgl_akta_cerai)
			ORDER BY MONTH(pac.tgl_akta_cerai)";
			
		$query = $this->db->query($sql, array($tahun));
		return $query->result();
	}

	// ==================== PENYERAHAN AKTA CERAI ====================

	private function _get_wilayah_condition($wilayah)
	{
		if ($wilayah === 'Semua') return '';

		if ($wilayah === 'HSU') {
			return " AND (pp1.alamat LIKE '%Hulu Sungai Utara%' OR pp1.alamat LIKE '%HSU%' 
					   OR pp1.alamat LIKE '%Amuntai%' OR pp1.alamat LIKE '%Haur Gading%' 
					   OR pp1.alamat LIKE '%Banjang%' OR pp1.alamat LIKE '%Paminggir%' 
					   OR pp1.alamat LIKE '%Babirik%' OR pp1.alamat LIKE '%Sungai Pandan%' 
					   OR pp1.alamat LIKE '%Danau Panggang%' OR pp1.alamat LIKE '%Sungai Tabukan%')";
		}
		if ($wilayah === 'Balangan') {
			return " AND (pp1.alamat LIKE '%Balangan%' OR pp1.alamat LIKE '%Paringin%' 
					   OR pp1.alamat LIKE '%Awayan%' OR pp1.alamat LIKE '%Tebing Tinggi%' 
					   OR pp1.alamat LIKE '%Juai%' OR pp1.alamat LIKE '%Lampihong%' 
					   OR pp1.alamat LIKE '%Halong%' OR pp1.alamat LIKE '%Batumandi%')";
		}

		return '';
	}

	private function _build_base_select()
	{
		return "SELECT 
				p.nomor_perkara,
				p.jenis_perkara_nama,
				pac.nomor_akta_cerai,
				pac.tgl_akta_cerai,
				pp.tanggal_putusan,
				pit.tgl_ikrar_talak,
				pp.tanggal_bht,
				pac.tgl_penyerahan_akta_cerai,
				pac.tgl_penyerahan_akta_cerai_pihak2,
				COALESCE(p.pihak1_text, ph1.nama, 'Tidak Ada Data') as nama_penggugat,
				COALESCE(p.pihak2_text, ph2.nama, 'Tidak Ada Data') as nama_tergugat
			FROM perkara_akta_cerai pac
			INNER JOIN perkara p ON pac.perkara_id = p.perkara_id
			LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			LEFT JOIN perkara_ikrar_talak pit ON p.perkara_id = pit.perkara_id
			LEFT JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
			LEFT JOIN pihak ph1 ON pp1.pihak_id = ph1.id
			LEFT JOIN perkara_pihak2 pp2 ON p.perkara_id = pp2.perkara_id
			LEFT JOIN pihak ph2 ON pp2.pihak_id = ph2.id";
	}

	private function _order_by()
	{
		return "ORDER BY 
				COALESCE(pac.tgl_penyerahan_akta_cerai, pac.tgl_penyerahan_akta_cerai_pihak2) DESC,
				p.nomor_perkara";
	}

	public function get_penyerahan_akta_cerai($lap_tahun, $lap_bulan, $wilayah = 'Semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$base = $this->_build_base_select();

		$sql = "{$base}
			WHERE (
				(YEAR(pac.tgl_penyerahan_akta_cerai) = ? AND MONTH(pac.tgl_penyerahan_akta_cerai) = ?) OR
				(YEAR(pac.tgl_penyerahan_akta_cerai_pihak2) = ? AND MONTH(pac.tgl_penyerahan_akta_cerai_pihak2) = ?)
			) AND pac.nomor_akta_cerai IS NOT NULL {$where_wilayah}
			{$this->_order_by()}";

		return $this->db->query($sql, array($lap_tahun, $lap_bulan, $lap_tahun, $lap_bulan))->result();
	}

	public function get_penyerahan_akta_cerai_tahunan($lap_tahun, $wilayah = 'Semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$base = $this->_build_base_select();

		$sql = "{$base}
			WHERE (
				(YEAR(pac.tgl_penyerahan_akta_cerai) = ?) OR
				(YEAR(pac.tgl_penyerahan_akta_cerai_pihak2) = ?)
			) AND pac.nomor_akta_cerai IS NOT NULL {$where_wilayah}
			{$this->_order_by()}";

		return $this->db->query($sql, array($lap_tahun, $lap_tahun))->result();
	}

	public function get_penyerahan_akta_cerai_custom($tanggal_mulai, $tanggal_akhir, $wilayah = 'Semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$base = $this->_build_base_select();

		$sql = "{$base}
			WHERE (
				(pac.tgl_penyerahan_akta_cerai BETWEEN ? AND ?) OR
				(pac.tgl_penyerahan_akta_cerai_pihak2 BETWEEN ? AND ?)
			) AND pac.nomor_akta_cerai IS NOT NULL {$where_wilayah}
			{$this->_order_by()}";

		return $this->db->query($sql, array($tanggal_mulai, $tanggal_akhir, $tanggal_mulai, $tanggal_akhir))->result();
	}

	private function _build_summary_select()
	{
		return "SELECT 
				COUNT(*) as total_akta,
				SUM(CASE WHEN pac.tgl_penyerahan_akta_cerai IS NOT NULL THEN 1 ELSE 0 END) as diserahkan_pihak1,
				SUM(CASE WHEN pac.tgl_penyerahan_akta_cerai_pihak2 IS NOT NULL THEN 1 ELSE 0 END) as diserahkan_pihak2,
				SUM(CASE WHEN p.jenis_perkara_nama LIKE '%Cerai Talak%' THEN 1 ELSE 0 END) as cerai_talak,
				SUM(CASE WHEN p.jenis_perkara_nama LIKE '%Cerai Gugat%' THEN 1 ELSE 0 END) as cerai_gugat,
				SUM(CASE WHEN pac.tgl_penyerahan_akta_cerai IS NOT NULL AND pac.tgl_penyerahan_akta_cerai_pihak2 IS NOT NULL THEN 1 ELSE 0 END) as kedua_pihak_selesai
			FROM perkara_akta_cerai pac
			INNER JOIN perkara p ON pac.perkara_id = p.perkara_id
			LEFT JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id";
	}

	public function get_summary_penyerahan($lap_tahun, $lap_bulan, $wilayah = 'Semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$sql = "{$this->_build_summary_select()}
			WHERE (
				(YEAR(pac.tgl_penyerahan_akta_cerai) = ? AND MONTH(pac.tgl_penyerahan_akta_cerai) = ?) OR
				(YEAR(pac.tgl_penyerahan_akta_cerai_pihak2) = ? AND MONTH(pac.tgl_penyerahan_akta_cerai_pihak2) = ?)
			) AND pac.nomor_akta_cerai IS NOT NULL {$where_wilayah}";

		return $this->db->query($sql, array($lap_tahun, $lap_bulan, $lap_tahun, $lap_bulan))->row();
	}

	public function get_summary_penyerahan_tahunan($lap_tahun, $wilayah = 'Semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$sql = "{$this->_build_summary_select()}
			WHERE (
				(YEAR(pac.tgl_penyerahan_akta_cerai) = ?) OR
				(YEAR(pac.tgl_penyerahan_akta_cerai_pihak2) = ?)
			) AND pac.nomor_akta_cerai IS NOT NULL {$where_wilayah}";

		return $this->db->query($sql, array($lap_tahun, $lap_tahun))->row();
	}

	public function get_summary_penyerahan_custom($tanggal_mulai, $tanggal_akhir, $wilayah = 'Semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$sql = "{$this->_build_summary_select()}
			WHERE (
				(pac.tgl_penyerahan_akta_cerai BETWEEN ? AND ?) OR
				(pac.tgl_penyerahan_akta_cerai_pihak2 BETWEEN ? AND ?)
			) AND pac.nomor_akta_cerai IS NOT NULL {$where_wilayah}";

		return $this->db->query($sql, array($tanggal_mulai, $tanggal_akhir, $tanggal_mulai, $tanggal_akhir))->row();
	}

	// ==================== VALIDASI AKTA CERAI ====================

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
