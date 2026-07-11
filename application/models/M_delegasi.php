<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_delegasi extends CI_Model
{
	function delegasi($lap_bulan, $lap_tahun)
	{
		$this->db->select("
			dm.perkara_id,
			dm.tgl_surat,
			dm.nomor_surat,
			MAX(dm.pn_asal_text) AS pn_asal_text,
			MAX(dm.nomor_perkara) AS nomor_perkara,
			MAX(dm.pihak) AS pihak,
			MAX(dm.tgl_sidang) AS tgl_sidang,
			MAX(dm.tgl_delegasi) AS tgl_delegasi,
			MAX(dm.jenis_delegasi_text) AS jenis_delegasi_text,
			dpm.tgl_surat_diterima,
			dpm.tgl_penunjukan_jurusita,
			dpm.tgl_relaas,
			dpm.tgl_pengiriman_relaas,
			MAX(dpm.jurusita_nama) AS jurusita_nama
		");

		$this->db->from('delegasi_masuk dm');
		$this->db->join('delegasi_proses_masuk dpm', 'dm.id = dpm.delegasi_id', 'inner');
		$this->db->where("dm.tgl_surat >=", $lap_tahun . '-' . $lap_bulan . '-01');
		$this->db->where("dm.tgl_surat <=", $lap_tahun . '-' . $lap_bulan . '-31');
		$this->db->group_by(array("dm.tgl_surat", "dm.nomor_surat"));
		$this->db->having("COUNT(dpm.perkara_id) >", 0);
		$this->db->order_by('dm.perkara_id', 'DESC');
		$query = $this->db->get();
		return $query->result();
	}

	function delegasi_k($lap_bulan, $lap_tahun)
	{
		$this->db->select('
			dm.perkara_id,
			dm.pn_tujuan_text,
			dm.`nomor_perkara`,
			dm.pihak,
			dm.`nomor_surat`,
			tanggal_pendaftaran,
			dm.`tgl_surat`,
			dm.`tgl_sidang`,
			dm.`tgl_delegasi`,
			dpm.`tgl_surat_diterima`,
			dpm.`tgl_pengiriman_relaas`,
			dpm.`tgl_resi`,
			dpm.`diinput_tanggal`,
			pe.`jurusita_text`,
			dm.`jenis_delegasi_text`
		');
		$this->db->from('perkara da');
		$this->db->join('perkara_penetapan pe', 'da.`perkara_id` = pe.perkara_id', 'LEFT');
		$this->db->join('delegasi_keluar dm', 'da.`perkara_id` = dm.perkara_id', 'LEFT');
		$this->db->join('delegasi_proses_keluar dpm', 'dm.id = dpm.`delegasi_id`', 'LEFT');
		$this->db->where('dm.tgl_surat >=', $lap_tahun . '-' . $lap_bulan . '-01');
		$this->db->where('dm.tgl_surat <=', $lap_tahun . '-' . $lap_bulan . '-31');
		$this->db->group_by('dm.tgl_surat, dm.nomor_surat');
		$this->db->order_by('dm.perkara_id', 'DESC');
		$query = $this->db->get();
		return $query->result();
	}
}
