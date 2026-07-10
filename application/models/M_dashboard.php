<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_dashboard extends CI_Model
{
    /**
     * Count perkara diterima in a given year.
     */
    public function countPerkaraTahun($tahun)
    {
        $sql = "SELECT COUNT(*) AS jumlah FROM perkara WHERE YEAR(tanggal_pendaftaran) = ?";
        return $this->db->query($sql, [$tahun])->row()->jumlah;
    }

    /**
     * Count perkara putus in a given year.
     */
    public function countPutusTahun($tahun)
    {
        $sql = "SELECT COUNT(*) AS jumlah FROM perkara_putusan WHERE YEAR(tanggal_putusan) = ?";
        return $this->db->query($sql, [$tahun])->row()->jumlah;
    }

    /**
     * Count perkara minutasi in a given year.
     */
    public function countMinutasiTahun($tahun)
    {
        $sql = "SELECT COUNT(*) AS jumlah FROM perkara_putusan WHERE YEAR(tanggal_minutasi) = ?";
        return $this->db->query($sql, [$tahun])->row()->jumlah;
    }

    /**
     * Count sisa perkara (no putusan) in a given year.
     */
    public function countSisaTahun($tahun)
    {
        $sql = "SELECT COUNT(*) AS jumlah FROM perkara
                LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
                WHERE tanggal_putusan IS NULL AND YEAR(tanggal_pendaftaran) = ?";
        return $this->db->query($sql, [$tahun])->row()->jumlah;
    }

    /**
     * Count perkara diterima in a given month of a given year.
     */
    public function countPerkaraBulan($tahun, $bulan)
    {
        $sql = "SELECT COUNT(*) AS jumlah FROM perkara
                WHERE MONTH(tanggal_pendaftaran) = ? AND YEAR(tanggal_pendaftaran) = ?";
        return $this->db->query($sql, [$bulan, $tahun])->row()->jumlah;
    }

    /**
     * Count perkara putus in a given month of a given year.
     */
    public function countPutusBulan($tahun, $bulan)
    {
        $sql = "SELECT COUNT(*) AS jumlah FROM perkara_putusan
                WHERE MONTH(tanggal_putusan) = ? AND YEAR(tanggal_putusan) = ?";
        return $this->db->query($sql, [$bulan, $tahun])->row()->jumlah;
    }

    /**
     * Count perkara minutasi in a given month of a given year.
     */
    public function countMinutasiBulan($tahun, $bulan)
    {
        $sql = "SELECT COUNT(*) AS jumlah FROM perkara_putusan
                WHERE MONTH(tanggal_minutasi) = ? AND YEAR(tanggal_minutasi) = ?";
        return $this->db->query($sql, [$bulan, $tahun])->row()->jumlah;
    }

    /**
     * Count sisa perkara in a given month with penepatan join.
     * Uses the last day of the month as the cutoff for date comparisons.
     */
    public function countSisaBulan($tahun, $bulan)
    {
        $lastDay = date('t', strtotime("$tahun-$bulan-01"));
        $dateEnd = "$tahun-$bulan-$lastDay";

        $sql = "SELECT COUNT(*) AS jumlah FROM perkara
                INNER JOIN perkara_penetapan ON perkara.perkara_id = perkara_penetapan.perkara_id
                LEFT JOIN perkara_putusan ON perkara.perkara_id = perkara_putusan.perkara_id
                WHERE (tanggal_putusan IS NULL OR tanggal_putusan > ?)
                AND tanggal_pendaftaran <= ?";
        return $this->db->query($sql, [$dateEnd, $dateEnd])->row()->jumlah;
    }
}
