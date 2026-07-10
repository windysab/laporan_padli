<?php defined('BASEPATH') or exit('No direct script access allowed');

class M_data_perceraian_hsu extends CI_Model
{
    private $kecamatan = [
        'Danau Panggang',
        'Babirik',
        'Sungai Pandan',
        'Amuntai Selatan',
        'Amuntai Tengah',
        'Amuntai Utara',
        'Banjang',
        'Haur Gading',
        'Paminggir',
        'Sungai Tabukan',
    ];

    private $default_kecamatan = 'BALANGAN';

    private $date_fields = [
        'tanggal_pendaftaran' => ['joins' => []],
        'tanggal_putusan'     => ['joins' => ['perkara_putusan']],
        'tanggal_bht'         => ['joins' => ['perkara_putusan', 'perkara_akta_cerai']],
        'tgl_akta_cerai'      => ['joins' => ['perkara_akta_cerai']],
    ];

    private function _kecamatan_case(): string
    {
        $parts = [];
        foreach ($this->kecamatan as $nama) {
            $nama_esc = str_replace("'", "\\'", $nama);
            $parts[] = "WHEN perkara_pihak1.`alamat` LIKE '%{$nama_esc}%' THEN '{$nama_esc}'";
        }
        return 'CASE ' . implode("\n\t\t\t\t", $parts) . "\n\t\t\t\tELSE '{$this->default_kecamatan}'\n\t\t\tEND";
    }

    private function _subquery_date(string $date_field): string
    {
        $kec_case = $this->_kecamatan_case();
        $joins = $this->date_fields[$date_field]['joins'] ?? [];

        $join_sql = '';
        foreach ($joins as $table) {
            $join_sql .= "\n\t\tLEFT JOIN {$table} ON perkara.`perkara_id`={$table}.`perkara_id`";
        }

        return <<<SQL
        SELECT 
            {$kec_case} AS KECAMATAN,
            '{$date_field}' AS date_type, COUNT(*) AS COUNT
        FROM perkara
        LEFT JOIN perkara_pihak1 ON perkara.`perkara_id`=perkara_pihak1.`perkara_id`{$join_sql}
        WHERE YEAR({$date_field})=? AND MONTH({$date_field})=?
        AND perkara_pihak1.`urutan`='1'
        GROUP BY KECAMATAN
SQL;
    }

    function data_perceraian_hsu($lap_bulan, $lap_tahun)
    {
        $subqueries = [];
        foreach (array_keys($this->date_fields) as $date_field) {
            $subqueries[] = $this->_subquery_date($date_field);
        }
        $union_sql = implode("\n\t\tUNION ALL\n\t\t", $subqueries);

        $kec_in = implode("', '", $this->kecamatan);

        $sql = "SELECT 
            KECAMATAN,
            SUM(CASE WHEN date_type = 'tanggal_pendaftaran' THEN COUNT ELSE 0 END) AS PERKARA_MASUK,
            SUM(CASE WHEN date_type = 'tanggal_putusan' THEN COUNT ELSE 0 END) AS PERKARA_PUTUS,
            SUM(CASE WHEN date_type = 'tanggal_bht' THEN COUNT ELSE 0 END) AS PERKARA_TELAH_BHT,
            SUM(CASE WHEN date_type = 'tgl_akta_cerai' THEN COUNT ELSE 0 END) AS JUMLAH_AKTA_CERAI
        FROM (
            {$union_sql}
        ) AS subquery
        WHERE KECAMATAN IN ('{$kec_in}')
        GROUP BY KECAMATAN WITH ROLLUP
        ORDER BY KECAMATAN IS NULL, KECAMATAN";

        $bindings = [];
        foreach (array_keys($this->date_fields) as $date_field) {
            $bindings[] = $lap_tahun;
            $bindings[] = $lap_bulan;
        }

        $query = $this->db->query($sql, $bindings);
        return $query->result();
    }
}
