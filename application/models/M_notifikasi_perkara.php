<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Model Notifikasi Perkara
 * 
 * Menangani query untuk perkara yang mendekati atau melewati batas waktu:
 * - Perkara > 5 bulan belum putus (batas SEMA)
 * - Perkara BHT yang belum terbit akta cerai > 7 hari
 * - Perkara mendekati batas 5 bulan (4-5 bulan)
 */
class M_notifikasi_perkara extends CI_Model
{

	/**
	 * Perkara yang sudah melewati batas 5 bulan belum putus
	 * Berdasarkan SEMA No. 2 Tahun 2014: batas penyelesaian perkara 5 bulan
	 */
	public function get_perkara_lewat_batas($limit = 20)
	{
		$sql = "SELECT 
				p.perkara_id,
				p.nomor_perkara,
				p.jenis_perkara_nama,
				p.tanggal_pendaftaran,
				p.pihak1_text AS penggugat,
				p.pihak2_text AS tergugat,
				DATEDIFF(CURDATE(), p.tanggal_pendaftaran) AS umur_hari,
				ROUND(DATEDIFF(CURDATE(), p.tanggal_pendaftaran) / 30, 1) AS umur_bulan
			FROM perkara p
			LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			WHERE pp.perkara_id IS NULL
				AND p.tanggal_pendaftaran IS NOT NULL
				AND p.tanggal_pendaftaran >= '2020-01-01'
				AND DATEDIFF(CURDATE(), p.tanggal_pendaftaran) > 150
				AND p.alur_perkara_id <> 114
			ORDER BY p.tanggal_pendaftaran ASC
			LIMIT ?";

		return $this->db->query($sql, array($limit))->result();
	}

	/**
	 * Perkara yang mendekati batas 5 bulan (120-150 hari / 4-5 bulan)
	 * Sebagai peringatan dini
	 */
	public function get_perkara_mendekati_batas($limit = 20)
	{
		$sql = "SELECT 
				p.perkara_id,
				p.nomor_perkara,
				p.jenis_perkara_nama,
				p.tanggal_pendaftaran,
				p.pihak1_text AS penggugat,
				p.pihak2_text AS tergugat,
				DATEDIFF(CURDATE(), p.tanggal_pendaftaran) AS umur_hari,
				ROUND(DATEDIFF(CURDATE(), p.tanggal_pendaftaran) / 30, 1) AS umur_bulan
			FROM perkara p
			LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			WHERE pp.perkara_id IS NULL
				AND p.tanggal_pendaftaran IS NOT NULL
				AND p.tanggal_pendaftaran >= '2020-01-01'
				AND DATEDIFF(CURDATE(), p.tanggal_pendaftaran) BETWEEN 120 AND 150
				AND p.alur_perkara_id <> 114
			ORDER BY p.tanggal_pendaftaran ASC
			LIMIT ?";

		return $this->db->query($sql, array($limit))->result();
	}

	/**
	 * Perkara BHT yang belum terbit akta cerai > 7 hari
	 * Berdasarkan ketentuan: akta cerai harus diterbitkan max 7 hari setelah BHT
	 */
	public function get_perkara_bht_belum_akta($limit = 20)
	{
		$sql = "SELECT 
				p.perkara_id,
				p.nomor_perkara,
				p.jenis_perkara_nama,
				p.pihak1_text AS penggugat,
				p.pihak2_text AS tergugat,
				DATE_FORMAT(pp.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATE_FORMAT(pp.tanggal_bht, '%d-%m-%Y') AS tanggal_bht,
				DATEDIFF(CURDATE(), pp.tanggal_bht) AS hari_sejak_bht
			FROM perkara p
			INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id
			LEFT JOIN perkara_akta_cerai pac ON p.perkara_id = pac.perkara_id
			WHERE pp.tanggal_bht IS NOT NULL
				AND pp.tanggal_bht >= '2020-01-01'
				AND (pac.perkara_id IS NULL OR pac.tgl_akta_cerai IS NULL)
				AND DATEDIFF(CURDATE(), pp.tanggal_bht) > 7
				AND p.jenis_perkara_nama IN ('Cerai Gugat', 'Cerai Talak')
				AND p.alur_perkara_id <> 114
			ORDER BY pp.tanggal_bht ASC
			LIMIT ?";

		return $this->db->query($sql, array($limit))->result();
	}

	/**
	 * Ringkasan jumlah notifikasi untuk badge/counter
	 */
	public function get_notifikasi_summary()
	{
		$sql = "SELECT 
			(SELECT COUNT(*) FROM perkara p 
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id 
				WHERE pp.perkara_id IS NULL 
				AND p.tanggal_pendaftaran IS NOT NULL
				AND p.tanggal_pendaftaran >= '2020-01-01'
				AND DATEDIFF(CURDATE(), p.tanggal_pendaftaran) > 150
				AND p.alur_perkara_id <> 114
			) AS lewat_batas,
			(SELECT COUNT(*) FROM perkara p 
				LEFT JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id 
				WHERE pp.perkara_id IS NULL 
				AND p.tanggal_pendaftaran IS NOT NULL
				AND p.tanggal_pendaftaran >= '2020-01-01'
				AND DATEDIFF(CURDATE(), p.tanggal_pendaftaran) BETWEEN 120 AND 150
				AND p.alur_perkara_id <> 114
			) AS mendekati_batas,
			(SELECT COUNT(*) FROM perkara p 
				INNER JOIN perkara_putusan pp ON p.perkara_id = pp.perkara_id 
				LEFT JOIN perkara_akta_cerai pac ON p.perkara_id = pac.perkara_id 
				WHERE pp.tanggal_bht IS NOT NULL
				AND pp.tanggal_bht >= '2020-01-01'
				AND (pac.perkara_id IS NULL OR pac.tgl_akta_cerai IS NULL)
				AND DATEDIFF(CURDATE(), pp.tanggal_bht) > 7
				AND p.jenis_perkara_nama IN ('Cerai Gugat', 'Cerai Talak')
				AND p.alur_perkara_id <> 114
			) AS bht_belum_akta";

		$result = $this->db->query($sql)->row();

		if (!$result) {
			return (object) [
				'lewat_batas' => 0,
				'mendekati_batas' => 0,
				'bht_belum_akta' => 0,
				'total' => 0
			];
		}

		$result->total = (int)$result->lewat_batas + (int)$result->mendekati_batas + (int)$result->bht_belum_akta;
		return $result;
	}
}
