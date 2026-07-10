<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_P_Banding extends CI_Model
{
    public function getData($lap_tahun, $lap_bulan)
    {
        $sql = "SELECT nomor_perkara_pn, putusan_pn, permohonan_banding, pemberitahuan_inzage,
            pengiriman_berkas_banding, putusan_banding, penerimaan_kembali_berkas_banding,
            pemberitahuan_putusan_banding
        FROM perkara
        LEFT JOIN perkara_banding ON perkara.perkara_id = perkara_banding.perkara_id
        LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
        WHERE (
            YEAR(perkara_banding.permohonan_banding)=? AND MONTH(perkara_banding.permohonan_banding)=?
            OR YEAR(perkara_banding.pemberitahuan_inzage)=? AND MONTH(perkara_banding.pemberitahuan_inzage)=?
            OR YEAR(perkara_banding.pengiriman_berkas_banding)=? AND MONTH(perkara_banding.pengiriman_berkas_banding)=?
            OR YEAR(perkara_banding.putusan_banding)=? AND MONTH(perkara_banding.putusan_banding)=?
            OR YEAR(perkara_banding.pemberitahuan_putusan_banding)=? AND MONTH(perkara_banding.pemberitahuan_putusan_banding)=?
        )
        ORDER BY perkara.perkara_id";
        return $this->db->query($sql, [$lap_tahun, $lap_bulan, $lap_tahun, $lap_bulan,
            $lap_tahun, $lap_bulan, $lap_tahun, $lap_bulan, $lap_tahun, $lap_bulan])->result();
    }
}
