<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_penyerahan_akta_cerai extends CI_Model
{
	// Extracted wilayah condition builder — reused 5x instead of inline duplication
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
}
