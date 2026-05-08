<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_monitoring_sipp extends CI_Model
{

	// ================================================================
	// 1. AGING REPORT - Perkara Belum Putus
	// ================================================================
	public function get_perkara_belum_putus($wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$sql = "SELECT 
				p.perkara_id,
				p.nomor_perkara,
				p.jenis_perkara_nama,
				p.tanggal_pendaftaran,
				p.pihak1_text AS nama_pihak_1,
				p.pihak2_text AS nama_pihak_2,
				DATEDIFF(CURDATE(), p.tanggal_pendaftaran) AS umur_perkara_hari,
				CASE 
					WHEN DATEDIFF(CURDATE(), p.tanggal_pendaftaran) <= 90 THEN 'hijau'
					WHEN DATEDIFF(CURDATE(), p.tanggal_pendaftaran) <= 150 THEN 'kuning'
					ELSE 'merah'
				END AS status_warna
			FROM perkara p
			LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
			WHERE pp.perkara_id IS NULL
				$where_wilayah
				$where_jenis
			ORDER BY p.tanggal_pendaftaran ASC";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_aging_summary($wilayah = 'Semua', $jenis_perkara = 'semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);
		$where_jenis = $this->_get_jenis_perkara_condition($jenis_perkara);

		$sql = "SELECT 
				COUNT(*) AS total_belum_putus,
				SUM(CASE WHEN DATEDIFF(CURDATE(), p.tanggal_pendaftaran) <= 90 THEN 1 ELSE 0 END) AS hijau,
				SUM(CASE WHEN DATEDIFF(CURDATE(), p.tanggal_pendaftaran) > 90 AND DATEDIFF(CURDATE(), p.tanggal_pendaftaran) <= 150 THEN 1 ELSE 0 END) AS kuning,
				SUM(CASE WHEN DATEDIFF(CURDATE(), p.tanggal_pendaftaran) > 150 THEN 1 ELSE 0 END) AS merah,
				COALESCE(AVG(DATEDIFF(CURDATE(), p.tanggal_pendaftaran)), 0) AS rata_rata_umur
			FROM perkara p
			LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
			WHERE pp.perkara_id IS NULL
				$where_wilayah
				$where_jenis";

		$query = $this->db->query($sql);
		return $query->row();
	}

	// ================================================================
	// 2. DASHBOARD REAL-TIME HARIAN
	// ================================================================
	public function get_dashboard_hari_ini()
	{
		$result = new stdClass();

		// Perkara masuk hari ini
		$sql = "SELECT COUNT(*) as total FROM perkara WHERE DATE(tanggal_pendaftaran) = CURDATE()";
		$result->masuk_hari_ini = $this->db->query($sql)->row()->total;

		// Putusan hari ini
		$sql = "SELECT COUNT(*) as total FROM perkara_putusan WHERE DATE(tanggal_putusan) = CURDATE()";
		$result->putus_hari_ini = $this->db->query($sql)->row()->total;

		// Akta cerai terbit hari ini
		$sql = "SELECT COUNT(*) as total FROM perkara_akta_cerai WHERE DATE(tgl_akta_cerai) = CURDATE()";
		$result->akta_cerai_hari_ini = $this->db->query($sql)->row()->total;

		// Total perkara belum putus (backlog)
		$sql = "SELECT COUNT(*) as total FROM perkara p 
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id 
				WHERE pp.perkara_id IS NULL";
		$result->backlog = $this->db->query($sql)->row()->total;

		return $result;
	}

	public function get_dashboard_bulan_ini()
	{
		$result = new stdClass();

		// Perkara masuk bulan ini
		$sql = "SELECT COUNT(*) as total FROM perkara 
				WHERE YEAR(tanggal_pendaftaran) = YEAR(CURDATE()) AND MONTH(tanggal_pendaftaran) = MONTH(CURDATE())";
		$result->masuk_bulan_ini = $this->db->query($sql)->row()->total;

		// Putusan bulan ini
		$sql = "SELECT COUNT(*) as total FROM perkara_putusan 
				WHERE YEAR(tanggal_putusan) = YEAR(CURDATE()) AND MONTH(tanggal_putusan) = MONTH(CURDATE())";
		$result->putus_bulan_ini = $this->db->query($sql)->row()->total;

		// Akta cerai bulan ini
		$sql = "SELECT COUNT(*) as total FROM perkara_akta_cerai 
				WHERE YEAR(tgl_akta_cerai) = YEAR(CURDATE()) AND MONTH(tgl_akta_cerai) = MONTH(CURDATE())";
		$result->akta_cerai_bulan_ini = $this->db->query($sql)->row()->total;

		// Rata-rata harian bulan ini (perkara masuk)
		$sql = "SELECT COALESCE(COUNT(*) / DAY(CURDATE()), 0) as rata_rata 
				FROM perkara 
				WHERE YEAR(tanggal_pendaftaran) = YEAR(CURDATE()) AND MONTH(tanggal_pendaftaran) = MONTH(CURDATE())";
		$result->rata_rata_masuk_harian = round($this->db->query($sql)->row()->rata_rata, 1);

		return $result;
	}

	public function get_trend_bulanan_tahun_ini()
	{
		$sql = "SELECT 
				m.bulan,
				COALESCE(masuk.total, 0) AS perkara_masuk,
				COALESCE(putus.total, 0) AS perkara_putus,
				COALESCE(akta.total, 0) AS akta_cerai
			FROM (
				SELECT 1 AS bulan UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
				UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 
				UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12
			) m
			LEFT JOIN (
				SELECT MONTH(tanggal_pendaftaran) AS bulan, COUNT(*) AS total 
				FROM perkara WHERE YEAR(tanggal_pendaftaran) = YEAR(CURDATE()) 
				GROUP BY MONTH(tanggal_pendaftaran)
			) masuk ON m.bulan = masuk.bulan
			LEFT JOIN (
				SELECT MONTH(tanggal_putusan) AS bulan, COUNT(*) AS total 
				FROM perkara_putusan WHERE YEAR(tanggal_putusan) = YEAR(CURDATE()) 
				GROUP BY MONTH(tanggal_putusan)
			) putus ON m.bulan = putus.bulan
			LEFT JOIN (
				SELECT MONTH(tgl_akta_cerai) AS bulan, COUNT(*) AS total 
				FROM perkara_akta_cerai WHERE YEAR(tgl_akta_cerai) = YEAR(CURDATE()) 
				GROUP BY MONTH(tgl_akta_cerai)
			) akta ON m.bulan = akta.bulan
			WHERE m.bulan <= MONTH(CURDATE())
			ORDER BY m.bulan";

		$query = $this->db->query($sql);
		return $query->result();
	}

	// ================================================================
	// 3. MONITORING MINUTASI
	// ================================================================
	public function get_perkara_sudah_putus_belum_bht($wilayah = 'Semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);

		$sql = "SELECT 
				p.nomor_perkara,
				p.jenis_perkara_nama,
				p.pihak1_text AS nama_pihak_1,
				p.pihak2_text AS nama_pihak_2,
				DATE_FORMAT(pp.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATEDIFF(CURDATE(), pp.tanggal_putusan) AS hari_sejak_putusan
			FROM perkara p
			INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
			WHERE (pp.tanggal_bht IS NULL OR pp.tanggal_bht = '0000-00-00')
				$where_wilayah
			ORDER BY pp.tanggal_putusan ASC";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_perkara_sudah_bht_belum_akta($wilayah = 'Semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);

		$sql = "SELECT 
				p.nomor_perkara,
				p.jenis_perkara_nama,
				p.pihak1_text AS nama_pihak_1,
				p.pihak2_text AS nama_pihak_2,
				DATE_FORMAT(pp.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATE_FORMAT(pp.tanggal_bht, '%d-%m-%Y') AS tanggal_bht,
				DATEDIFF(CURDATE(), pp.tanggal_bht) AS hari_sejak_bht
			FROM perkara p
			INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			LEFT JOIN perkara_akta_cerai ac ON p.perkara_id = ac.perkara_id
			LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
			WHERE pp.tanggal_bht IS NOT NULL AND pp.tanggal_bht != '0000-00-00'
				AND (ac.perkara_id IS NULL OR ac.tgl_akta_cerai IS NULL OR ac.tgl_akta_cerai = '0000-00-00')
				AND (p.jenis_perkara_nama LIKE '%Cerai%')
				$where_wilayah
			ORDER BY pp.tanggal_bht ASC";

		$query = $this->db->query($sql);
		return $query->result();
	}

	public function get_minutasi_summary($wilayah = 'Semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);

		$result = new stdClass();

		// Sudah putus, belum BHT
		$sql = "SELECT COUNT(*) as total FROM perkara p
				INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
				WHERE (pp.tanggal_bht IS NULL OR pp.tanggal_bht = '0000-00-00')
				$where_wilayah";
		$result->belum_bht = $this->db->query($sql)->row()->total;

		// Sudah BHT, belum akta cerai
		$sql = "SELECT COUNT(*) as total FROM perkara p
				INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				LEFT JOIN perkara_akta_cerai ac ON p.perkara_id = ac.perkara_id
				LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
				WHERE pp.tanggal_bht IS NOT NULL AND pp.tanggal_bht != '0000-00-00'
				AND (ac.perkara_id IS NULL OR ac.tgl_akta_cerai IS NULL OR ac.tgl_akta_cerai = '0000-00-00')
				AND (p.jenis_perkara_nama LIKE '%Cerai%')
				$where_wilayah";
		$result->belum_akta = $this->db->query($sql)->row()->total;

		return $result;
	}

	// ================================================================
	// 4. MONITORING KINERJA
	// ================================================================
	public function get_kinerja($tahun, $wilayah = 'Semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);

		$result = new stdClass();

		// Perkara masuk tahun ini
		$sql = "SELECT COUNT(*) as total FROM perkara p
				LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
				WHERE YEAR(p.tanggal_pendaftaran) = ? $where_wilayah";
		$result->perkara_masuk = $this->db->query($sql, array($tahun))->row()->total;

		// Perkara putus tahun ini
		$sql = "SELECT COUNT(*) as total FROM perkara p
				INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
				WHERE YEAR(pp.tanggal_putusan) = ? $where_wilayah";
		$result->perkara_putus = $this->db->query($sql, array($tahun))->row()->total;

		// Clearance Rate
		$result->clearance_rate = ($result->perkara_masuk > 0) 
			? round(($result->perkara_putus / $result->perkara_masuk) * 100, 1) 
			: 0;

		// Backlog (sisa perkara belum putus)
		$sql = "SELECT COUNT(*) as total FROM perkara p
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
				WHERE pp.perkara_id IS NULL 
				AND YEAR(p.tanggal_pendaftaran) <= ? $where_wilayah";
		$result->backlog = $this->db->query($sql, array($tahun))->row()->total;

		// Rata-rata Disposition Time (hari dari pendaftaran ke putusan)
		$sql = "SELECT COALESCE(AVG(DATEDIFF(pp.tanggal_putusan, p.tanggal_pendaftaran)), 0) as rata_rata
				FROM perkara p
				INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
				WHERE YEAR(pp.tanggal_putusan) = ? $where_wilayah";
		$result->disposition_time = round($this->db->query($sql, array($tahun))->row()->rata_rata, 0);

		// Tepat waktu (<=150 hari) vs terlambat (>150 hari)
		$sql = "SELECT 
				COUNT(*) as total,
				SUM(CASE WHEN DATEDIFF(pp.tanggal_putusan, p.tanggal_pendaftaran) <= 150 THEN 1 ELSE 0 END) as tepat_waktu,
				SUM(CASE WHEN DATEDIFF(pp.tanggal_putusan, p.tanggal_pendaftaran) > 150 THEN 1 ELSE 0 END) as terlambat
			FROM perkara p
			INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
			WHERE YEAR(pp.tanggal_putusan) = ? $where_wilayah";
		$timing = $this->db->query($sql, array($tahun))->row();
		$result->tepat_waktu = (int)$timing->tepat_waktu;
		$result->terlambat = (int)$timing->terlambat;
		$result->persen_tepat_waktu = ($timing->total > 0) 
			? round(($timing->tepat_waktu / $timing->total) * 100, 1) 
			: 0;

		return $result;
	}

	public function get_kinerja_per_bulan($tahun, $wilayah = 'Semua')
	{
		$where_wilayah = $this->_get_wilayah_condition($wilayah);

		$sql = "SELECT 
				m.bulan,
				COALESCE(masuk.total, 0) AS perkara_masuk,
				COALESCE(putus.total, 0) AS perkara_putus,
				CASE WHEN COALESCE(masuk.total, 0) > 0 
					THEN ROUND((COALESCE(putus.total, 0) / COALESCE(masuk.total, 0)) * 100, 1) 
					ELSE 0 
				END AS clearance_rate
			FROM (
				SELECT 1 AS bulan UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 
				UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 
				UNION SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12
			) m
			LEFT JOIN (
				SELECT MONTH(p.tanggal_pendaftaran) AS bulan, COUNT(*) AS total 
				FROM perkara p
				LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
				WHERE YEAR(p.tanggal_pendaftaran) = ? $where_wilayah
				GROUP BY MONTH(p.tanggal_pendaftaran)
			) masuk ON m.bulan = masuk.bulan
			LEFT JOIN (
				SELECT MONTH(pp.tanggal_putusan) AS bulan, COUNT(*) AS total 
				FROM perkara p
				INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
				LEFT JOIN perkara_pihak1 pp1 ON pp1.perkara_id = p.perkara_id AND pp1.urutan = 1
				WHERE YEAR(pp.tanggal_putusan) = ? $where_wilayah
				GROUP BY MONTH(pp.tanggal_putusan)
			) putus ON m.bulan = putus.bulan
			ORDER BY m.bulan";

		$query = $this->db->query($sql, array($tahun, $tahun));
		return $query->result();
	}

	// ================================================================
	// HELPER METHODS
	// ================================================================
	private function _get_wilayah_condition($wilayah)
	{
		if ($wilayah === 'Semua') return '';

		$hsu_condition = "(pp1.alamat LIKE '%Hulu Sungai Utara%' OR pp1.alamat LIKE '%HSU%' 
					   OR pp1.alamat LIKE '%Amuntai%' OR pp1.alamat LIKE '%Haur Gading%' 
					   OR pp1.alamat LIKE '%Banjang%' OR pp1.alamat LIKE '%Paminggir%' 
					   OR pp1.alamat LIKE '%Babirik%' OR pp1.alamat LIKE '%Sungai Pandan%' 
					   OR pp1.alamat LIKE '%Danau Panggang%' OR pp1.alamat LIKE '%Sungai Tabukan%')";

		$balangan_condition = "(pp1.alamat LIKE '%Balangan%' OR pp1.alamat LIKE '%Paringin%' 
					   OR pp1.alamat LIKE '%Awayan%' OR pp1.alamat LIKE '%Tebing Tinggi%' 
					   OR pp1.alamat LIKE '%Juai%' OR pp1.alamat LIKE '%Lampihong%' 
					   OR pp1.alamat LIKE '%Halong%' OR pp1.alamat LIKE '%Batumandi%')";

		if ($wilayah === 'HSU') {
			return " AND {$hsu_condition}";
		} else if ($wilayah === 'Balangan') {
			return " AND {$balangan_condition} AND NOT {$hsu_condition}";
		}

		return '';
	}

	private function _get_jenis_perkara_condition($jenis_perkara)
	{
		if ($jenis_perkara === 'semua' || empty($jenis_perkara)) return '';
		return " AND p.jenis_perkara_nama = '" . $this->db->escape_str($jenis_perkara) . "'";
	}
}
