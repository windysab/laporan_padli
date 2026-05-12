<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_monitoring_dirput extends CI_Model
{
	public function get_belum_publish_anonim($tahun, $bulan = null, $jenis_perkara = 'semua')
	{
		$where_periode = $this->_get_periode_condition($bulan);
		$where_jenis = $this->_get_jenis_condition($jenis_perkara);

		$sql = "SELECT
				p.perkara_id,
				p.nomor_perkara,
				p.jenis_perkara_nama,
				p.pihak1_text AS pihak_1,
				p.pihak2_text AS pihak_2,
				DATE_FORMAT(pp.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATE_FORMAT(pp.tanggal_bht, '%d-%m-%Y') AS tanggal_bht,
				pp.status_putusan_nama,
				DATEDIFF(CURDATE(), pp.tanggal_putusan) AS hari_sejak_putusan
			FROM perkara p
			INNER JOIN perkara_putusan pp ON pp.perkara_id = p.perkara_id
			LEFT JOIN dirput_dokumen dd ON dd.perkara_id = p.perkara_id
				AND dd.filename LIKE '%anonimisasi%'
			WHERE pp.tanggal_putusan IS NOT NULL
				AND YEAR(pp.tanggal_putusan) = ?
				$where_periode
				$where_jenis
				AND dd.perkara_id IS NULL
			ORDER BY pp.tanggal_putusan ASC, p.nomor_perkara";

		$params = $bulan ? array($tahun, $bulan) : array($tahun);
		return $this->db->query($sql, $params)->result();
	}

	public function get_sudah_publish_anonim($tahun, $bulan = null, $jenis_perkara = 'semua')
	{
		$where_periode = $this->_get_periode_condition($bulan);
		$where_jenis = $this->_get_jenis_condition($jenis_perkara);

		$sql = "SELECT
				p.perkara_id,
				p.nomor_perkara,
				p.jenis_perkara_nama,
				p.pihak1_text AS pihak_1,
				p.pihak2_text AS pihak_2,
				DATE_FORMAT(pp.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATE_FORMAT(dd.created_date, '%d-%m-%Y') AS tanggal_publish,
				dd.filename,
				dd.published,
				dd.link_dirput
			FROM perkara p
			INNER JOIN perkara_putusan pp ON pp.perkara_id = p.perkara_id
			INNER JOIN dirput_dokumen dd ON dd.perkara_id = p.perkara_id
				AND dd.filename LIKE '%anonimisasi%'
			WHERE pp.tanggal_putusan IS NOT NULL
				AND YEAR(pp.tanggal_putusan) = ?
				$where_periode
				$where_jenis
			ORDER BY pp.tanggal_putusan ASC, p.nomor_perkara";

		$params = $bulan ? array($tahun, $bulan) : array($tahun);
		return $this->db->query($sql, $params)->result();
	}

	public function get_upload_gagal($tahun, $bulan = null, $jenis_perkara = 'semua')
	{
		$where_periode = $this->_get_periode_condition($bulan);
		$where_jenis = $this->_get_jenis_condition($jenis_perkara);

		$sql = "SELECT
				p.perkara_id,
				p.nomor_perkara,
				p.jenis_perkara_nama,
				p.pihak1_text AS pihak_1,
				p.pihak2_text AS pihak_2,
				DATE_FORMAT(pp.tanggal_putusan, '%d-%m-%Y') AS tanggal_putusan,
				DATE_FORMAT(pp.tanggal_bht, '%d-%m-%Y') AS tanggal_bht,
				pp.status_putusan_nama,
				DATEDIFF(CURDATE(), pp.tanggal_putusan) AS hari_sejak_putusan,
				'Belum Upload' AS keterangan
			FROM perkara p
			INNER JOIN perkara_putusan pp ON pp.perkara_id = p.perkara_id
			LEFT JOIN dirput_dokumen dd ON dd.perkara_id = pp.perkara_id
			WHERE pp.tanggal_putusan IS NOT NULL
				AND YEAR(pp.tanggal_putusan) = ?
				$where_periode
				$where_jenis
				AND (dd.filename IS NULL OR dd.filename = '')
			ORDER BY pp.tanggal_putusan DESC, p.nomor_perkara";

		$params = $bulan ? array($tahun, $bulan) : array($tahun);
		return $this->db->query($sql, $params)->result();
	}

	public function get_summary($tahun, $bulan = null, $jenis_perkara = 'semua')
	{
		$where_periode = $this->_get_periode_condition($bulan);
		$where_jenis = $this->_get_jenis_condition($jenis_perkara);

		$sql = "SELECT
				COUNT(DISTINCT p.perkara_id) AS total_putusan,
				COUNT(DISTINCT CASE WHEN dd.perkara_id IS NOT NULL THEN p.perkara_id END) AS sudah_ada_anonim,
				COUNT(DISTINCT CASE WHEN dd.perkara_id IS NULL THEN p.perkara_id END) AS belum_ada_anonim,
				COUNT(DISTINCT CASE WHEN dd.perkara_id IS NOT NULL AND dd.published = 1 THEN p.perkara_id END) AS sudah_publish,
				COUNT(DISTINCT CASE WHEN dd.perkara_id IS NOT NULL AND (dd.published IS NULL OR dd.published = 0) THEN p.perkara_id END) AS belum_publish_flag,
				COUNT(DISTINCT CASE WHEN semua_dd.perkara_id IS NULL OR semua_dd.filename IS NULL OR semua_dd.filename = '' THEN p.perkara_id END) AS upload_gagal
			FROM perkara p
			INNER JOIN perkara_putusan pp ON pp.perkara_id = p.perkara_id
			LEFT JOIN dirput_dokumen dd ON dd.perkara_id = p.perkara_id
				AND dd.filename LIKE '%anonimisasi%'
			LEFT JOIN dirput_dokumen semua_dd ON semua_dd.perkara_id = p.perkara_id
			WHERE pp.tanggal_putusan IS NOT NULL
				AND YEAR(pp.tanggal_putusan) = ?
				$where_periode
				$where_jenis";

		$params = $bulan ? array($tahun, $bulan) : array($tahun);
		return $this->db->query($sql, $params)->row();
	}

	public function get_jenis_perkara_putusan()
	{
		$sql = "SELECT DISTINCT p.jenis_perkara_nama
			FROM perkara p
			INNER JOIN perkara_putusan pp ON pp.perkara_id = p.perkara_id
			WHERE p.jenis_perkara_nama IS NOT NULL
				AND p.jenis_perkara_nama != ''
			ORDER BY p.jenis_perkara_nama";

		return $this->db->query($sql)->result();
	}

	private function _get_periode_condition($bulan)
	{
		if (empty($bulan) || $bulan === 'semua') {
			return '';
		}

		return ' AND MONTH(pp.tanggal_putusan) = ?';
	}

	private function _get_jenis_condition($jenis_perkara)
	{
		if ($jenis_perkara === 'semua' || empty($jenis_perkara)) {
			return '';
		}

		return " AND p.jenis_perkara_nama = '" . $this->db->escape_str($jenis_perkara) . "'";
	}
}
