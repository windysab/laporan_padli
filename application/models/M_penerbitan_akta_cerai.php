<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_penerbitan_akta_cerai extends CI_Model
{
    public function get_penertiban_akta_cerai($lap_tahun, $lap_bulan)
    {
        $sql = "SELECT nomor_akta_cerai, tgl_akta_cerai, no_seri_akta_cerai, nomor_perkara,
            tanggal_putusan, tanggal_bht, tgl_ikrar_talak
        FROM perkara
        LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
        LEFT JOIN perkara_ikrar_talak ON perkara.perkara_id = perkara_ikrar_talak.perkara_id
        LEFT JOIN perkara_akta_cerai ON perkara.perkara_id = perkara_akta_cerai.perkara_id
        LEFT JOIN perkara_pihak1 ON perkara.perkara_id = perkara_pihak1.perkara_id
        LEFT JOIN perkara_pihak2 ON perkara.perkara_id = perkara_pihak2.perkara_id
        WHERE YEAR(tgl_akta_cerai) = ? AND MONTH(tgl_akta_cerai) = ?
        ORDER BY perkara.perkara_id";
        return $this->db->query($sql, [$lap_tahun, $lap_bulan])->result();
    }
}
