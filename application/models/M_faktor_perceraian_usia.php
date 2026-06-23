<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_faktor_perceraian_usia extends CI_Model
{
	/**
	 * Get divorce factors grouped by age range (Perempuan only)
	 *
	 * Age ranges:
	 *  16-19, 20-25, 26-30, 31-35, 36+
	 *
	 * @param string $tahun Tahun laporan
	 * @param string $wilayah Wilayah / pengadilan
	 * @return array
	 */
	public function get_data($tahun = null, $wilayah = null)
	{
		if (empty($tahun)) {
			$tahun = date('Y');
		}
		if (empty($wilayah)) {
			$wilayah = 'Amuntai';
		}

		// Build address filter
		$where_alamat = '';
		$params = array($tahun);

		if (stripos($wilayah, 'hulu sungai utara') !== false || $wilayah == 'Amuntai') {
			$where_alamat = "AND (pp1.alamat LIKE '%Hulu Sungai Utara%'
				OR pp1.alamat LIKE '%HSU%'
				OR pp1.alamat LIKE '%Amuntai%'
				OR pp1.alamat LIKE '%Haur Gading%'
				OR pp1.alamat LIKE '%Banjang%'
				OR pp1.alamat LIKE '%Paminggir%'
				OR pp1.alamat LIKE '%Babirik%'
				OR pp1.alamat LIKE '%Sungai Pandan%'
				OR pp1.alamat LIKE '%Danau Panggang%'
				OR pp1.alamat LIKE '%Sungai Tabukan%')";
		} elseif ($wilayah == 'Balangan') {
			$where_alamat = "AND (pp1.alamat LIKE '%Balangan%'
				OR pp1.alamat LIKE '%Paringin%'
				OR pp1.alamat LIKE '%Awayan%'
				OR pp1.alamat LIKE '%Tebing Tinggi%'
				OR pp1.alamat LIKE '%Juai%'
				OR pp1.alamat LIKE '%Lampihong%'
				OR pp1.alamat LIKE '%Halong%'
				OR pp1.alamat LIKE '%Batumandi%')";
		} else {
			$where_alamat = "AND pp1.alamat LIKE ?";
			$params = array($tahun, '%' . $wilayah . '%');
		}

		// Hitung usia saat akta cerai diterbitkan
		// Asumsi: tabel pihak punya kolom tanggal_lahir
		$sql = "
			SELECT
				faktor.nama AS faktor,
				COALESCE(SUM(CASE
					WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 16 AND 19
					THEN 1 ELSE 0
				END), 0) AS usia_16_19,
				COALESCE(SUM(CASE
					WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 20 AND 25
					THEN 1 ELSE 0
				END), 0) AS usia_20_25,
				COALESCE(SUM(CASE
					WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 26 AND 30
					THEN 1 ELSE 0
				END), 0) AS usia_26_30,
				COALESCE(SUM(CASE
					WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 31 AND 35
					THEN 1 ELSE 0
				END), 0) AS usia_31_35,
				COALESCE(SUM(CASE
					WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) >= 36
					THEN 1 ELSE 0
				END), 0) AS usia_36
			FROM
				faktor_perceraian faktor
				JOIN perkara_akta_cerai pac ON faktor.id = pac.faktor_perceraian_id
				JOIN perkara p ON pac.perkara_id = p.perkara_id
				JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
				JOIN pihak pd ON pp1.pihak_id = pd.id
			WHERE
				YEAR(pac.tgl_akta_cerai) = ?
				AND pd.jenis_kelamin = 'P'
				AND faktor.aktif = 'Y'
				AND pd.tanggal_lahir IS NOT NULL
				$where_alamat
			GROUP BY
				faktor.id, faktor.nama
			ORDER BY
				CAST(faktor.id AS UNSIGNED) ASC, faktor.id ASC
		";

		$query = $this->db->query($sql, $params);
		$result = $query->result();

		// Jika kolom 'urutan' tidak ada, fallback ke sorting manual
		if (empty($result)) {
			return $result;
		}

		return $result;
	}

	/**
	 * Get aggregated totals for summary cards
	 */
	public function get_summary($tahun = null, $wilayah = null)
	{
		if (empty($tahun)) {
			$tahun = date('Y');
		}
		if (empty($wilayah)) {
			$wilayah = 'Amuntai';
		}

		$where_alamat = '';
		$params = array($tahun);

		if (stripos($wilayah, 'hulu sungai utara') !== false || $wilayah == 'Amuntai') {
			$where_alamat = "AND (pp1.alamat LIKE '%Hulu Sungai Utara%'
				OR pp1.alamat LIKE '%HSU%'
				OR pp1.alamat LIKE '%Amuntai%'
				OR pp1.alamat LIKE '%Haur Gading%'
				OR pp1.alamat LIKE '%Banjang%'
				OR pp1.alamat LIKE '%Paminggir%'
				OR pp1.alamat LIKE '%Babirik%'
				OR pp1.alamat LIKE '%Sungai Pandan%'
				OR pp1.alamat LIKE '%Danau Panggang%'
				OR pp1.alamat LIKE '%Sungai Tabukan%')";
		} elseif ($wilayah == 'Balangan') {
			$where_alamat = "AND (pp1.alamat LIKE '%Balangan%'
				OR pp1.alamat LIKE '%Paringin%'
				OR pp1.alamat LIKE '%Awayan%'
				OR pp1.alamat LIKE '%Tebing Tinggi%'
				OR pp1.alamat LIKE '%Juai%'
				OR pp1.alamat LIKE '%Lampihong%'
				OR pp1.alamat LIKE '%Halong%'
				OR pp1.alamat LIKE '%Batumandi%')";
		} else {
			$where_alamat = "AND pp1.alamat LIKE ?";
			$params = array($tahun, '%' . $wilayah . '%');
		}

		$sql = "
			SELECT
				COUNT(*) AS total_kasus,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 16 AND 19 THEN 1 ELSE 0 END) AS total_16_19,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 20 AND 25 THEN 1 ELSE 0 END) AS total_20_25,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 26 AND 30 THEN 1 ELSE 0 END) AS total_26_30,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) BETWEEN 31 AND 35 THEN 1 ELSE 0 END) AS total_31_35,
				SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, pac.tgl_akta_cerai) >= 36 THEN 1 ELSE 0 END) AS total_36,
				SUM(CASE WHEN pd.jenis_kelamin = 'L' THEN 1 ELSE 0 END) AS total_laki,
				SUM(CASE WHEN pd.jenis_kelamin = 'P' THEN 1 ELSE 0 END) AS total_perempuan
			FROM
				perkara_akta_cerai pac
				JOIN perkara p ON pac.perkara_id = p.perkara_id
				JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
				JOIN pihak pd ON pp1.pihak_id = pd.id
			WHERE
				YEAR(pac.tgl_akta_cerai) = ?
				AND pd.tanggal_lahir IS NOT NULL
				$where_alamat
		";

		$query = $this->db->query($sql, $params);
		return $query->row();
	}
}
