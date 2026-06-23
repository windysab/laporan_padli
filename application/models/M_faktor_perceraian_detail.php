<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_faktor_perceraian_detail extends CI_Model
{
	private function get_wilayah_condition($wilayah, $alias = 'pp1')
	{
		if ($wilayah == 'Semua Wilayah' || $wilayah == 'Semua') {
			return array('sql' => '', 'params' => array());
		}

		if ($wilayah == 'Hulu Sungai Utara' || $wilayah == 'HSU') {
			$sql = " AND ({$alias}.alamat LIKE '%Hulu Sungai Utara%' OR {$alias}.alamat LIKE '%HSU%' 
							OR {$alias}.alamat LIKE '%Amuntai%' OR {$alias}.alamat LIKE '%Haur Gading%' 
							OR {$alias}.alamat LIKE '%Banjang%' OR {$alias}.alamat LIKE '%Paminggir%' 
							OR {$alias}.alamat LIKE '%Babirik%' OR {$alias}.alamat LIKE '%Sungai Pandan%' 
							OR {$alias}.alamat LIKE '%Danau Panggang%' OR {$alias}.alamat LIKE '%Sungai Tabukan%')";
			return array('sql' => $sql, 'params' => array());
		}

		if ($wilayah == 'Balangan') {
			$sql = " AND ({$alias}.alamat LIKE '%Balangan%' OR {$alias}.alamat LIKE '%Paringin%' 
							OR {$alias}.alamat LIKE '%Awayan%' OR {$alias}.alamat LIKE '%Tebing Tinggi%' 
							OR {$alias}.alamat LIKE '%Juai%' OR {$alias}.alamat LIKE '%Lampihong%' 
							OR {$alias}.alamat LIKE '%Halong%' OR {$alias}.alamat LIKE '%Batumandi%')";
			return array('sql' => $sql, 'params' => array());
		}

		return array(
			'sql' => " AND {$alias}.alamat LIKE ?",
			'params' => array('%' . $wilayah . '%')
		);
	}

	public function data_faktor_perceraian_detail($lap_tahun = null, $wilayah = null)
	{
		// Set default values if not provided
		if (empty($lap_tahun)) {
			$lap_tahun = date('Y');
		}
		if (empty($wilayah)) {
			$wilayah = 'Balangan';
		}

		// Handle 'Semua Wilayah' case - aggregate data from all regions
		if ($wilayah == 'Semua Wilayah') {
			$sql = "
				SELECT
					faktor.nama AS FaktorPerceraian,
					COALESCE(agg.`Laki-Laki`, 0) AS `Laki-Laki`,
					COALESCE(agg.`Perempuan`, 0) AS `Perempuan`,
					COALESCE(agg.`Total`, 0) AS `Total`
				FROM
					faktor_perceraian faktor
					LEFT JOIN (
						SELECT
							pac.faktor_perceraian_id,
							SUM(
								CASE
									WHEN pd.jenis_kelamin = 'L' THEN 1
									ELSE 0
								END
							) AS `Laki-Laki`,
							SUM(
								CASE
									WHEN pd.jenis_kelamin = 'P' THEN 1
									ELSE 0
								END
							) AS `Perempuan`,
							COUNT(*) AS `Total`
						FROM
							perkara_akta_cerai pac
							JOIN perkara p ON pac.perkara_id = p.perkara_id
							JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
							JOIN pihak pd ON pp1.pihak_id = pd.id
						WHERE
							YEAR(pac.tgl_akta_cerai) = ?
						GROUP BY
							pac.faktor_perceraian_id
					) AS agg ON faktor.id = agg.faktor_perceraian_id
				WHERE
					faktor.aktif = 'Y'
				UNION ALL
				SELECT
					'TOTAL' AS FaktorPerceraian,
					SUM(COALESCE(agg.`Laki-Laki`, 0)) AS `Laki-Laki`,
					SUM(COALESCE(agg.`Perempuan`, 0)) AS `Perempuan`,
					SUM(COALESCE(agg.`Total`, 0)) AS `Total`
				FROM
					faktor_perceraian faktor
					LEFT JOIN (
						SELECT
							pac.faktor_perceraian_id,
							SUM(
								CASE
									WHEN pd.jenis_kelamin = 'L' THEN 1
									ELSE 0
								END
							) AS `Laki-Laki`,
							SUM(
								CASE
									WHEN pd.jenis_kelamin = 'P' THEN 1
									ELSE 0
								END
							) AS `Perempuan`,
							COUNT(*) AS `Total`
						FROM
							perkara_akta_cerai pac
							JOIN perkara p ON pac.perkara_id = p.perkara_id
							JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
							JOIN pihak pd ON pp1.pihak_id = pd.id
						WHERE
							YEAR(pac.tgl_akta_cerai) = ?
						GROUP BY
							pac.faktor_perceraian_id
					) AS agg ON faktor.id = agg.faktor_perceraian_id
				WHERE
					faktor.aktif = 'Y'
			";
			$query = $this->db->query($sql, array($lap_tahun, $lap_tahun));
		} else {
			// Handle specific region case with improved filtering
			$where_alamat = "";
			$params = array($lap_tahun, $lap_tahun);

			if ($wilayah == 'Hulu Sungai Utara' || $wilayah == 'HSU') {
				$where_alamat = "AND (pp1.alamat LIKE '%Hulu Sungai Utara%' OR pp1.alamat LIKE '%HSU%' 
								OR pp1.alamat LIKE '%Amuntai%' OR pp1.alamat LIKE '%Haur Gading%' 
								OR pp1.alamat LIKE '%Banjang%' OR pp1.alamat LIKE '%Paminggir%' 
								OR pp1.alamat LIKE '%Babirik%' OR pp1.alamat LIKE '%Sungai Pandan%' 
								OR pp1.alamat LIKE '%Danau Panggang%' OR pp1.alamat LIKE '%Sungai Tabukan%')";
			} elseif ($wilayah == 'Balangan') {
				$where_alamat = "AND (pp1.alamat LIKE '%Balangan%' OR pp1.alamat LIKE '%Paringin%' 
								OR pp1.alamat LIKE '%Awayan%' OR pp1.alamat LIKE '%Tebing Tinggi%' 
								OR pp1.alamat LIKE '%Juai%' OR pp1.alamat LIKE '%Lampihong%' 
								OR pp1.alamat LIKE '%Halong%' OR pp1.alamat LIKE '%Batumandi%')";
			} else {
				$where_alamat = "AND pp1.alamat LIKE ?";
				$params = array($lap_tahun, '%' . $wilayah . '%', $lap_tahun, '%' . $wilayah . '%');
			}

			$sql = "
				SELECT
					faktor.nama AS FaktorPerceraian,
					COALESCE(agg.`Laki-Laki`, 0) AS `Laki-Laki`,
					COALESCE(agg.`Perempuan`, 0) AS `Perempuan`,
					COALESCE(agg.`Total`, 0) AS `Total`
				FROM
					faktor_perceraian faktor
					LEFT JOIN (
						SELECT
							pac.faktor_perceraian_id,
							SUM(
								CASE
									WHEN pd.jenis_kelamin = 'L' THEN 1
									ELSE 0
								END
							) AS `Laki-Laki`,
							SUM(
								CASE
									WHEN pd.jenis_kelamin = 'P' THEN 1
									ELSE 0
								END
							) AS `Perempuan`,
							COUNT(*) AS `Total`
						FROM
							perkara_akta_cerai pac
							JOIN perkara p ON pac.perkara_id = p.perkara_id
							JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
							JOIN pihak pd ON pp1.pihak_id = pd.id
						WHERE
							YEAR(pac.tgl_akta_cerai) = ?
							$where_alamat
						GROUP BY
							pac.faktor_perceraian_id
					) AS agg ON faktor.id = agg.faktor_perceraian_id
				WHERE
					faktor.aktif = 'Y'
				UNION ALL
				SELECT
					'TOTAL' AS FaktorPerceraian,
					SUM(COALESCE(agg.`Laki-Laki`, 0)) AS `Laki-Laki`,
					SUM(COALESCE(agg.`Perempuan`, 0)) AS `Perempuan`,
					SUM(COALESCE(agg.`Total`, 0)) AS `Total`
				FROM
					faktor_perceraian faktor
					LEFT JOIN (
						SELECT
							pac.faktor_perceraian_id,
							SUM(
								CASE
									WHEN pd.jenis_kelamin = 'L' THEN 1
									ELSE 0
								END
							) AS `Laki-Laki`,
							SUM(
								CASE
									WHEN pd.jenis_kelamin = 'P' THEN 1
									ELSE 0
								END
							) AS `Perempuan`,
							COUNT(*) AS `Total`
						FROM
							perkara_akta_cerai pac
							JOIN perkara p ON pac.perkara_id = p.perkara_id
							JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
							JOIN pihak pd ON pp1.pihak_id = pd.id
						WHERE
							YEAR(pac.tgl_akta_cerai) = ?
							$where_alamat
						GROUP BY
							pac.faktor_perceraian_id
					) AS agg ON faktor.id = agg.faktor_perceraian_id
				WHERE
					faktor.aktif = 'Y'
			";
			$query = $this->db->query($sql, $params);
		}

		return $query->result();
	}

	public function data_faktor_perceraian_usia($lap_tahun = null, $wilayah = null, $jenis_kelamin = 'P')
	{
		if (empty($lap_tahun)) {
			$lap_tahun = date('Y');
		}
		if (empty($wilayah)) {
			$wilayah = 'Balangan';
		}

		$jenis_kelamin = in_array($jenis_kelamin, array('L', 'P')) ? $jenis_kelamin : 'P';
		$wilayah_data = $this->get_wilayah_condition($wilayah, 'pp1');

		$sql = "
			SELECT
				faktor.id,
				faktor.FaktorPerceraian,
				COALESCE(agg.usia_16_19, 0) AS usia_16_19,
				COALESCE(agg.usia_20_25, 0) AS usia_20_25,
				COALESCE(agg.usia_26_30, 0) AS usia_26_30,
				COALESCE(agg.usia_31_35, 0) AS usia_31_35,
				COALESCE(agg.usia_36, 0) AS usia_36
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
					SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, p.tanggal_pendaftaran) BETWEEN 16 AND 19 AND pd.jenis_kelamin = ? THEN 1 ELSE 0 END) AS usia_16_19,
					SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, p.tanggal_pendaftaran) BETWEEN 20 AND 25 AND pd.jenis_kelamin = ? THEN 1 ELSE 0 END) AS usia_20_25,
					SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, p.tanggal_pendaftaran) BETWEEN 26 AND 30 AND pd.jenis_kelamin = ? THEN 1 ELSE 0 END) AS usia_26_30,
					SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, p.tanggal_pendaftaran) BETWEEN 31 AND 35 AND pd.jenis_kelamin = ? THEN 1 ELSE 0 END) AS usia_31_35,
					SUM(CASE WHEN TIMESTAMPDIFF(YEAR, pd.tanggal_lahir, p.tanggal_pendaftaran) >= 36 AND pd.jenis_kelamin = ? THEN 1 ELSE 0 END) AS usia_36
				FROM perkara_akta_cerai pac
				JOIN perkara p ON pac.perkara_id = p.perkara_id
				JOIN perkara_pihak1 pp1 ON p.perkara_id = pp1.perkara_id
				JOIN pihak pd ON pp1.pihak_id = pd.id
				WHERE YEAR(pac.tgl_akta_cerai) = ?
				{$wilayah_data['sql']}
				GROUP BY pac.faktor_perceraian_id
			) AS agg ON faktor.id = agg.faktor_perceraian_id
			ORDER BY CAST(faktor.id AS UNSIGNED)
		";

		$params = array(
			$jenis_kelamin,
			$jenis_kelamin,
			$jenis_kelamin,
			$jenis_kelamin,
			$jenis_kelamin,
			$lap_tahun,
		);

		if (!empty($wilayah_data['params'])) {
			$params = array_merge($params, $wilayah_data['params']);
		}

		$query = $this->db->query($sql, $params);
		return $query->result();
	}
}
