<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model E-Court
 * 
 * Menangani query perbandingan perkara e-Court vs Non e-Court
 * Data dari tabel perkara_efiling_id
 */
class M_ecourt extends CI_Model
{

	/**
	 * Data bulanan: e-Court vs Non e-Court
	 */
	public function get_data_bulanan($tahun, $bulan)
	{
		$sql = "SELECT 
				COUNT(p.perkara_id) AS total_perkara,
				COUNT(pe.perkara_id) AS total_ecourt,
				(COUNT(p.perkara_id) - COUNT(pe.perkara_id)) AS total_non_ecourt,
				CASE WHEN COUNT(p.perkara_id) > 0 
					THEN ROUND(COUNT(pe.perkara_id) * 100.0 / COUNT(p.perkara_id), 2)
					ELSE 0 
				END AS persen_ecourt
			FROM perkara p
			LEFT JOIN perkara_efiling_id pe ON p.perkara_id = pe.perkara_id
			WHERE YEAR(p.tanggal_pendaftaran) = ?
				AND MONTH(p.tanggal_pendaftaran) = ?
				AND p.alur_perkara_id <> 114";

		return $this->db->query($sql, array($tahun, $bulan))->row();
	}

	/**
	 * Data tahunan: e-Court vs Non e-Court
	 */
	public function get_data_tahunan($tahun)
	{
		$sql = "SELECT 
				COUNT(p.perkara_id) AS total_perkara,
				COUNT(pe.perkara_id) AS total_ecourt,
				(COUNT(p.perkara_id) - COUNT(pe.perkara_id)) AS total_non_ecourt,
				CASE WHEN COUNT(p.perkara_id) > 0 
					THEN ROUND(COUNT(pe.perkara_id) * 100.0 / COUNT(p.perkara_id), 2)
					ELSE 0 
				END AS persen_ecourt
			FROM perkara p
			LEFT JOIN perkara_efiling_id pe ON p.perkara_id = pe.perkara_id
			WHERE YEAR(p.tanggal_pendaftaran) = ?
				AND p.alur_perkara_id <> 114";

		return $this->db->query($sql, array($tahun))->row();
	}

	/**
	 * Breakdown per bulan dalam satu tahun (untuk chart)
	 */
	public function get_breakdown_per_bulan($tahun)
	{
		$sql = "SELECT 
				MONTH(p.tanggal_pendaftaran) AS bulan,
				COUNT(p.perkara_id) AS total_perkara,
				COUNT(pe.perkara_id) AS total_ecourt,
				(COUNT(p.perkara_id) - COUNT(pe.perkara_id)) AS total_non_ecourt,
				CASE WHEN COUNT(p.perkara_id) > 0 
					THEN ROUND(COUNT(pe.perkara_id) * 100.0 / COUNT(p.perkara_id), 2)
					ELSE 0 
				END AS persen_ecourt
			FROM perkara p
			LEFT JOIN perkara_efiling_id pe ON p.perkara_id = pe.perkara_id
			WHERE YEAR(p.tanggal_pendaftaran) = ?
				AND p.alur_perkara_id <> 114
			GROUP BY MONTH(p.tanggal_pendaftaran)
			ORDER BY bulan";

		return $this->db->query($sql, array($tahun))->result();
	}

	/**
	 * Breakdown per jenis perkara
	 */
	public function get_breakdown_per_jenis($tahun, $bulan = null)
	{
		$params = array($tahun);
		$where_bulan = "";
		if (!empty($bulan) && $bulan !== 'semua') {
			$where_bulan = "AND MONTH(p.tanggal_pendaftaran) = ?";
			$params[] = $bulan;
		}

		$sql = "SELECT 
				p.jenis_perkara_nama,
				COUNT(p.perkara_id) AS total_perkara,
				COUNT(pe.perkara_id) AS total_ecourt,
				(COUNT(p.perkara_id) - COUNT(pe.perkara_id)) AS total_non_ecourt,
				CASE WHEN COUNT(p.perkara_id) > 0 
					THEN ROUND(COUNT(pe.perkara_id) * 100.0 / COUNT(p.perkara_id), 2)
					ELSE 0 
				END AS persen_ecourt
			FROM perkara p
			LEFT JOIN perkara_efiling_id pe ON p.perkara_id = pe.perkara_id
			WHERE YEAR(p.tanggal_pendaftaran) = ?
				$where_bulan
				AND p.alur_perkara_id <> 114
				AND p.jenis_perkara_nama IS NOT NULL
			GROUP BY p.jenis_perkara_nama
			ORDER BY total_perkara DESC";

		return $this->db->query($sql, $params)->result();
	}

	/**
	 * Perbandingan antar tahun (tren adopsi e-Court)
	 */
	public function get_tren_tahunan($tahun_mulai = null)
	{
		if (!$tahun_mulai) {
			$tahun_mulai = (int)date('Y') - 4;
		}

		$sql = "SELECT 
				YEAR(p.tanggal_pendaftaran) AS tahun,
				COUNT(p.perkara_id) AS total_perkara,
				COUNT(pe.perkara_id) AS total_ecourt,
				(COUNT(p.perkara_id) - COUNT(pe.perkara_id)) AS total_non_ecourt,
				CASE WHEN COUNT(p.perkara_id) > 0 
					THEN ROUND(COUNT(pe.perkara_id) * 100.0 / COUNT(p.perkara_id), 2)
					ELSE 0 
				END AS persen_ecourt
			FROM perkara p
			LEFT JOIN perkara_efiling_id pe ON p.perkara_id = pe.perkara_id
			WHERE YEAR(p.tanggal_pendaftaran) >= ?
				AND p.alur_perkara_id <> 114
			GROUP BY YEAR(p.tanggal_pendaftaran)
			ORDER BY tahun";

		return $this->db->query($sql, array($tahun_mulai))->result();
	}

	/**
	 * Detail perkara e-Court per bulan
	 */
	public function get_detail_ecourt($tahun, $bulan = null, $mode = 'ecourt')
	{
		$params = array($tahun);
		$where_bulan = "";
		if (!empty($bulan) && $bulan !== 'semua') {
			$where_bulan = "AND MONTH(p.tanggal_pendaftaran) = ?";
			$params[] = $bulan;
		}

		$where_ecourt = ($mode === 'ecourt')
			? "AND pe.perkara_id IS NOT NULL"
			: "AND pe.perkara_id IS NULL";

		$sql = "SELECT 
				p.nomor_perkara,
				p.jenis_perkara_nama,
				DATE_FORMAT(p.tanggal_pendaftaran, '%d-%m-%Y') AS tanggal_pendaftaran,
				p.pihak1_text AS penggugat,
				p.pihak2_text AS tergugat
			FROM perkara p
			LEFT JOIN perkara_efiling_id pe ON p.perkara_id = pe.perkara_id
			WHERE YEAR(p.tanggal_pendaftaran) = ?
				$where_bulan
				$where_ecourt
				AND p.alur_perkara_id <> 114
			ORDER BY p.tanggal_pendaftaran DESC";

		return $this->db->query($sql, $params)->result();
	}
}
