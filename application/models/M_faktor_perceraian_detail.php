<?php
defined('BASEPATH') or exit;

class M_faktor_perceraian_detail extends CI_Model
{
    private $kec = [
        'HSU' => ['Hulu Sungai Utara','Amuntai','Haur Gading','Banjang','Paminggir','Babirik','Sungai Pandan','Danau Panggang','Sungai Tabukan'],
        'Balangan' => ['Balangan','Paringin','Awayan','Tebing Tinggi','Juai','Lampihong','Halong','Batumandi'],
    ];

    private function wh($w, $a = 'pp1')
    {
        if (in_array($w, ['Semua','Semua Wilayah',''])) return ['',''];
        if ($w == 'Hulu Sungai Utara') $w = 'HSU';
        $k = $this->kec[$w] ?? null;
        if ($k) {
            $in = implode(' OR ', array_fill(0, count($k), "$a.alamat LIKE ?"));
            return [" AND ($in)", array_map(fn($x)=>"%$x%",$k)];
        }
        return [" AND $a.alamat LIKE ?", ["%$w%"]];
    }

    public function data_faktor_perceraian_detail($thn = null, $wil = null)
    {
        $thn ??= date('Y');
        $wil ??= 'Balangan';
        [$where,$p] = $this->wh($wil);
        $sub = "SELECT pac.faktor_perceraian_id id,
            SUM(pd.jenis_kelamin='L') `Laki-Laki`,
            SUM(pd.jenis_kelamin='P') `Perempuan`,
            COUNT(*) `Total`
        FROM perkara_akta_cerai pac
        JOIN perkara p ON pac.perkara_id=p.perkara_id
        JOIN perkara_pihak1 pp1 ON p.perkara_id=pp1.perkara_id
        JOIN pihak pd ON pp1.pihak_id=pd.id
        WHERE YEAR(pac.tgl_akta_cerai)=? $where
        GROUP BY pac.faktor_perceraian_id";
        $sql = "SELECT COALESCE(f.nama,'TOTAL') FaktorPerceraian,
            COALESCE(SUM(a.`Laki-Laki`),0) `Laki-Laki`,
            COALESCE(SUM(a.`Perempuan`),0) `Perempuan`,
            COALESCE(SUM(a.`Total`),0) `Total`
        FROM faktor_perceraian f
        LEFT JOIN ($sub) a ON f.id=a.id
        WHERE f.aktif='Y'
        GROUP BY f.nama WITH ROLLUP";
        return $this->db->query($sql, array_merge([$thn], $p))->result();
    }

    public function data_faktor_perceraian_usia($thn = null, $wil = null, $jk = 'P')
    {
        $thn ??= date('Y');
        $wil ??= 'Balangan';
        $jk = in_array($jk,['L','P']) ? $jk : 'P';
        [$where,$p] = $this->wh($wil);
        $sql = "SELECT f.id,f.FaktorPerceraian,
            a.usia_16_19,a.usia_20_25,a.usia_26_30,a.usia_31_35,a.usia_36
        FROM (
            SELECT 9 id,'Perselisihan Terus Menerus' FaktorPerceraian UNION ALL
            SELECT 10,'Kawin Paksa' UNION ALL SELECT 11,'Murtad' UNION ALL
            SELECT 12,'Ekonomi' UNION ALL SELECT 14,'Lain-Lain'
        ) f LEFT JOIN (
            SELECT pac.faktor_perceraian_id id,
                SUM(TIMESTAMPDIFF(YEAR,pd.tanggal_lahir,p.tanggal_pendaftaran) BETWEEN 16 AND 19 AND pd.jenis_kelamin=?) usia_16_19,
                SUM(TIMESTAMPDIFF(YEAR,pd.tanggal_lahir,p.tanggal_pendaftaran) BETWEEN 20 AND 25 AND pd.jenis_kelamin=?) usia_20_25,
                SUM(TIMESTAMPDIFF(YEAR,pd.tanggal_lahir,p.tanggal_pendaftaran) BETWEEN 26 AND 30 AND pd.jenis_kelamin=?) usia_26_30,
                SUM(TIMESTAMPDIFF(YEAR,pd.tanggal_lahir,p.tanggal_pendaftaran) BETWEEN 31 AND 35 AND pd.jenis_kelamin=?) usia_31_35,
                SUM(TIMESTAMPDIFF(YEAR,pd.tanggal_lahir,p.tanggal_pendaftaran) BETWEEN 36 AND 999 AND pd.jenis_kelamin=?) usia_36
            FROM perkara_akta_cerai pac
            JOIN perkara p ON pac.perkara_id=p.perkara_id
            JOIN perkara_pihak1 pp1 ON p.perkara_id=pp1.perkara_id
            JOIN pihak pd ON pp1.pihak_id=pd.id
            WHERE YEAR(pac.tgl_akta_cerai)=? $where
            GROUP BY pac.faktor_perceraian_id
        ) a ON f.id=a.id
        ORDER BY CAST(f.id AS UNSIGNED)";
        return $this->db->query($sql, array_merge(array_fill(0,5,$jk),[$thn],$p))->result();
    }
}
