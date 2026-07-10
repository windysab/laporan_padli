<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_lipa1 extends CI_Model
{
    /**
     * Build reusable base query with bound parameters to prevent SQL injection.
     *
     * @param string $select       SELECT clause content (columns or aggregate)
     * @param string $date_mode    'standard' or 'sisa' (special date WHERE for sisa perkara)
     * @param string $where_extra  Extra WHERE conditions appended before ORDER BY
     * @param array  $params       Bound parameters for ? placeholders in order:
     *                             [t, b, t, b, t, b, t, b, t, b, like_jenis_perkara]
     *                             For 'sisa' mode: [lt_t, eq_t, lt_b, t, b, t, b, t, b, t, b, like]
     * @return CI_DB_result
     */
    private function _baseQuery($select, $date_mode = 'standard', $where_extra = '', $params = [])
    {
        if ($date_mode === 'sisa') {
            $date_where = "(
                (YEAR(tanggal_pendaftaran) < ? OR (YEAR(tanggal_pendaftaran) = ? AND MONTH(tanggal_pendaftaran) < ?)) AND tanggal_putusan IS NULL
                OR YEAR(penetapan_majelis_hakim) = ? AND MONTH(penetapan_majelis_hakim) = ?
                OR YEAR(penetapan_hari_sidang) = ? AND MONTH(penetapan_hari_sidang) = ?
                OR YEAR(sidang_pertama) = ? AND MONTH(sidang_pertama) = ?
                OR YEAR(tanggal_putusan) = ? AND MONTH(tanggal_putusan) = ?
                OR tanggal_pendaftaran IS NULL
            )";
        } else {
            $date_where = "(
                YEAR(tanggal_pendaftaran) = ? AND MONTH(tanggal_pendaftaran) = ?
                OR YEAR(penetapan_majelis_hakim) = ? AND MONTH(penetapan_majelis_hakim) = ?
                OR YEAR(penetapan_hari_sidang) = ? AND MONTH(penetapan_hari_sidang) = ?
                OR YEAR(sidang_pertama) = ? AND MONTH(sidang_pertama) = ?
                OR YEAR(tanggal_putusan) = ? AND MONTH(tanggal_putusan) = ?
                OR tanggal_pendaftaran IS NULL
            )";
        }

        $sql = "SELECT {$select} FROM perkara
            LEFT JOIN perkara_penetapan ON perkara.perkara_id = perkara_penetapan.perkara_id
            LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
            LEFT JOIN status_putusan ON status_putusan.id = perkara_putusan.status_putusan_id
            LEFT JOIN perkara_pihak1 ON perkara.perkara_id = perkara_pihak1.perkara_id
            LEFT JOIN perkara_pihak2 ON perkara.perkara_id = perkara_pihak2.perkara_id
            LEFT JOIN pihak ON perkara_pihak1.pihak_id = pihak.id
            LEFT JOIN perkara_efiling_id ON perkara.perkara_id = perkara_efiling_id.perkara_id
            WHERE {$date_where}
            AND perkara_pihak1.pihak_id != '1'
            AND perkara.nomor_perkara LIKE ?
            AND perkara_pihak1.urutan = '1'
            {$where_extra}
            ORDER BY tanggal_pendaftaran";

        return $this->db->query($sql, $params);
    }

    /**
     * Get full data rows filtered by year, month, and jenis perkara.
     * Excludes pensiunan records.
     *
     * @param string $lap_tahun
     * @param string $lap_bulan
     * @param string $jenis_perkara
     * @return array  Result rows
     */
    public function getData($lap_tahun, $lap_bulan, $jenis_perkara)
    {
        $select = "nomor_perkara, jenis_perkara_nama, majelis_hakim_nama, panitera_pengganti_text,
                   tanggal_pendaftaran, penetapan_majelis_hakim, penetapan_hari_sidang, sidang_pertama,
                   tanggal_putusan, status_putusan.`nama` AS amar, pekerjaan,
                   perkara_pihak2.alamat as alamat_pihak2, prodeo, pihak.email as email_pihak1";

        $params = [
            $lap_tahun, $lap_bulan,
            $lap_tahun, $lap_bulan,
            $lap_tahun, $lap_bulan,
            $lap_tahun, $lap_bulan,
            $lap_tahun, $lap_bulan,
            "%{$jenis_perkara}%",
        ];

        return $this->_baseQuery($select, 'standard', "AND pekerjaan NOT LIKE '%Pensiunan%'", $params)->result();
    }

    /**
     * Generic count query with configurable WHERE extra and date mode.
     * Replaces getJumlah, getJumlahPensiunan, getJumlahPNS.
     *
     * Usage examples:
     *   // Sisa perkara (non-pensiunan) — was getJumlah()
     *   getJumlah("AND pekerjaan NOT LIKE '%Pensiunan%'",
     *       [$lt_t, $eq_t, $lt_b, $t, $b, $t, $b, $t, $b, $t, $b, "%{$jp}%"], 'sisa')
     *
     *   // Standard count pensiunan — was getJumlahPensiunan()
     *   getJumlah("AND pekerjaan LIKE '%Pensiunan%'",
     *       [$t, $b, $t, $b, $t, $b, $t, $b, $t, $b, "%{$jp}%"])
     *
     *   // Standard count PNS — was getJumlahPNS()
     *   getJumlah("AND pekerjaan LIKE '%PNS%'",
     *       [$t, $b, $t, $b, $t, $b, $t, $b, $t, $b, "%{$jp}%"])
     *
     * @param string $where_extra  Extra WHERE conditions (e.g. "AND pekerjaan LIKE ?")
     * @param array  $params       Bound parameters for all ? placeholders
     * @param string $date_mode    'standard' (default) or 'sisa'
     * @return object|null         Row with ->jumlah property
     */
    public function getJumlah($where_extra = '', $params = [], $date_mode = 'standard')
    {
        return $this->_baseQuery("COUNT(perkara.perkara_id) AS jumlah", $date_mode, $where_extra, $params)->row();
    }
}
