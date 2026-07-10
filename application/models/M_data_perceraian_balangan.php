<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_data_perceraian_balangan extends CI_Model
{

    /**
     * Returns the CASE WHEN fragment for mapping addresses to kecamatan names.
     */
    private function _kecamatan_case()
    {
        return "CASE 
            WHEN perkara_pihak1.`alamat` LIKE '%Paringin Selatan%' THEN 'Paringin Selatan'
            WHEN perkara_pihak1.`alamat` LIKE '%Paringin%' THEN 'Paringin'
            WHEN perkara_pihak1.`alamat` LIKE '%Lampihong%' THEN 'Lampihong'
            WHEN perkara_pihak1.`alamat` LIKE '%Batumandi%' THEN 'Batumandi'
            WHEN perkara_pihak1.`alamat` LIKE '%Awayan%' THEN 'Awayan'
            WHEN perkara_pihak1.`alamat` LIKE '%Halong%' THEN 'Halong'
            WHEN perkara_pihak1.`alamat` LIKE '%Tebing Tinggi%' THEN 'Tebing Tinggi'
            WHEN perkara_pihak1.`alamat` LIKE '%Juai%' THEN 'Juai'
            ELSE 'HULU SUNGAI UTARA'
        END";
    }

    /**
     * Builds one UNION ALL branch for a given date field.
     *
     * @param string $date_field  Column name (e.g. 'tgl_akta_cerai')
     * @param string $extra_joins Additional LEFT JOIN clauses needed for this date branch
     * @return string SQL fragment with ? placeholders for tahun and bulan
     */
    private function _build_date_branch($date_field, $extra_joins = '')
    {
        $kecamatan = $this->_kecamatan_case();
        return "SELECT 
            {$kecamatan} AS KECAMATAN,
            '{$date_field}' AS date_type, COUNT(*) AS COUNT
        FROM perkara
        LEFT JOIN perkara_pihak1 ON perkara.`perkara_id` = perkara_pihak1.`perkara_id`
        {$extra_joins}
        WHERE YEAR({$date_field}) = ? AND MONTH({$date_field}) = ?
        AND perkara_pihak1.`urutan` = '1'
        GROUP BY KECAMATAN";
    }

    function data_perceraian_balangan($lap_bulan, $lap_tahun)
    {
        // Build each date branch with its specific joins
        $branches = [
            $this->_build_date_branch('tgl_akta_cerai', 'LEFT JOIN perkara_akta_cerai ON perkara.`perkara_id` = perkara_akta_cerai.`perkara_id`'),
            $this->_build_date_branch('tanggal_pendaftaran'),
            $this->_build_date_branch('tanggal_putusan', 'LEFT JOIN perkara_putusan ON perkara.`perkara_id` = perkara_putusan.`perkara_id`'),
            $this->_build_date_branch('tanggal_bht', 'LEFT JOIN perkara_putusan ON perkara.`perkara_id` = perkara_putusan.`perkara_id`
            LEFT JOIN perkara_akta_cerai ON perkara.`perkara_id` = perkara_akta_cerai.`perkara_id`'),
        ];

        $unpivoted = implode("\n\t\tUNION ALL\n\t\t", $branches);

        // 4 branches each with 2 ? placeholders = 8 bindings total
        $bindings = array_merge(
            [$lap_tahun, $lap_bulan],
            [$lap_tahun, $lap_bulan],
            [$lap_tahun, $lap_bulan],
            [$lap_tahun, $lap_bulan]
        );

        $sql = "WITH agg AS (
            SELECT 
                KECAMATAN,
                date_type,
                COUNT
            FROM (
                {$unpivoted}
            ) AS raw
        ),
        pivoted AS (
            SELECT 
                KECAMATAN,
                SUM(CASE WHEN date_type = 'tanggal_pendaftaran' THEN COUNT ELSE 0 END) AS PERKARA_MASUK,
                SUM(CASE WHEN date_type = 'tanggal_putusan' THEN COUNT ELSE 0 END) AS PERKARA_PUTUS,
                SUM(CASE WHEN date_type = 'tanggal_bht' THEN COUNT ELSE 0 END) AS PERKARA_TELAH_BHT,
                SUM(CASE WHEN date_type = 'tgl_akta_cerai' THEN COUNT ELSE 0 END) AS JUMLAH_AKTA_CERAI
            FROM agg
            GROUP BY KECAMATAN
        )
        SELECT 
            all_kecamatan.KECAMATAN,
            COALESCE(pivoted.PERKARA_MASUK, 0) AS PERKARA_MASUK,
            COALESCE(pivoted.PERKARA_PUTUS, 0) AS PERKARA_PUTUS,
            COALESCE(pivoted.PERKARA_TELAH_BHT, 0) AS PERKARA_TELAH_BHT,
            COALESCE(pivoted.JUMLAH_AKTA_CERAI, 0) AS JUMLAH_AKTA_CERAI
        FROM (
            SELECT 'Paringin' AS KECAMATAN
            UNION ALL SELECT 'Paringin Selatan'
            UNION ALL SELECT 'Lampihong'
            UNION ALL SELECT 'Batumandi'
            UNION ALL SELECT 'Awayan'
            UNION ALL SELECT 'Halong'
            UNION ALL SELECT 'Tebing Tinggi'
            UNION ALL SELECT 'Juai'
        ) AS all_kecamatan
        LEFT JOIN pivoted ON all_kecamatan.KECAMATAN = pivoted.KECAMATAN

        UNION ALL

        SELECT 
            'TOTAL' AS KECAMATAN,
            SUM(PERKARA_MASUK) AS PERKARA_MASUK,
            SUM(PERKARA_PUTUS) AS PERKARA_PUTUS,
            SUM(PERKARA_TELAH_BHT) AS PERKARA_TELAH_BHT,
            SUM(JUMLAH_AKTA_CERAI) AS JUMLAH_AKTA_CERAI
        FROM pivoted";

        $query = $this->db->query($sql, $bindings);
        return $query->result();
    }
}
