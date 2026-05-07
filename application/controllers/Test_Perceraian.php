<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test_Perceraian extends CI_Controller
{
	private $passed = 0;
	private $failed = 0;

	public function __construct()
	{
		parent::__construct();
		if (!ini_get('date.timezone')) {
			date_default_timezone_set('Asia/Jakarta');
		}
		$this->load->model('M_laporan_perceraian');
		$this->load->helper('url');
	}

	public function index()
	{
		$lap_bulan = date('m');
		$lap_tahun = date('Y');

		echo "<!DOCTYPE html><html><head><title>TDD - Laporan Perceraian</title>";
		echo "<style>
			body { font-family: 'Segoe UI', sans-serif; margin: 20px; background: #f5f5f5; }
			h1 { color: #333; border-bottom: 3px solid #dc3545; padding-bottom: 10px; }
			h2 { color: #555; margin-top: 30px; background: #fff; padding: 10px 15px; border-left: 4px solid #007bff; }
			.pass { color: #28a745; font-weight: bold; }
			.fail { color: #dc3545; font-weight: bold; }
			.test-result { padding: 8px 15px; margin: 5px 0; background: #fff; border-radius: 4px; border-left: 4px solid #ccc; }
			.test-result.pass-bg { border-left-color: #28a745; }
			.test-result.fail-bg { border-left-color: #dc3545; background: #fff5f5; }
			.summary { margin-top: 30px; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-size: 1.2em; }
			table { border-collapse: collapse; margin: 10px 0; width: 100%; }
			th, td { border: 1px solid #ddd; padding: 6px 10px; text-align: left; font-size: 0.85em; }
			th { background: #007bff; color: #fff; }
			tr:nth-child(even) { background: #f9f9f9; }
		</style></head><body>";
		echo "<h1>TDD - Laporan Perceraian (Bulan: {$lap_bulan}, Tahun: {$lap_tahun})</h1>";

		// ============================================================
		// TEST GROUP 1: Summary equals count of data rows
		// ============================================================
		echo "<h2>TEST GROUP 1: Summary = Jumlah Baris Data (Bulanan)</h2>";

		$data_semua = $this->M_laporan_perceraian->get_laporan_perceraian_bulanan($lap_tahun, $lap_bulan, 'Semua', 'semua');
		$summary_semua = $this->M_laporan_perceraian->get_summary_perceraian_bulanan($lap_tahun, $lap_bulan, 'Semua', 'semua');

		$this->assert_equal(
			count($data_semua),
			(int)$summary_semua->total_perceraian,
			"Bulanan Semua: COUNT rows == summary.total_perceraian"
		);

		$data_hsu = $this->M_laporan_perceraian->get_laporan_perceraian_bulanan($lap_tahun, $lap_bulan, 'HSU', 'semua');
		$summary_hsu = $this->M_laporan_perceraian->get_summary_perceraian_bulanan($lap_tahun, $lap_bulan, 'HSU', 'semua');

		$this->assert_equal(
			count($data_hsu),
			(int)$summary_hsu->total_perceraian,
			"Bulanan HSU: COUNT rows == summary.total_perceraian"
		);

		$data_bal = $this->M_laporan_perceraian->get_laporan_perceraian_bulanan($lap_tahun, $lap_bulan, 'Balangan', 'semua');
		$summary_bal = $this->M_laporan_perceraian->get_summary_perceraian_bulanan($lap_tahun, $lap_bulan, 'Balangan', 'semua');

		$this->assert_equal(
			count($data_bal),
			(int)$summary_bal->total_perceraian,
			"Bulanan Balangan: COUNT rows == summary.total_perceraian"
		);

		// ============================================================
		// TEST GROUP 2: Semua >= HSU + Balangan (some records may not match either)
		// ============================================================
		echo "<h2>TEST GROUP 2: Total Semua >= HSU + Balangan</h2>";

		$total_semua = (int)$summary_semua->total_perceraian;
		$total_hsu = (int)$summary_hsu->total_perceraian;
		$total_bal = (int)$summary_bal->total_perceraian;

		$this->assert_true(
			$total_semua >= ($total_hsu + $total_bal),
			"Bulanan: Semua ({$total_semua}) >= HSU ({$total_hsu}) + Balangan ({$total_bal}) = " . ($total_hsu + $total_bal)
		);

		// ============================================================
		// TEST GROUP 3: Cerai Gugat + Cerai Talak = Total
		// ============================================================
		echo "<h2>TEST GROUP 3: Cerai Gugat + Cerai Talak <= Total Perceraian</h2>";

		$cg = (int)$summary_semua->cerai_gugat;
		$ct = (int)$summary_semua->cerai_talak;

		$this->assert_true(
			($cg + $ct) <= $total_semua,
			"Bulanan Semua: Cerai Gugat ({$cg}) + Cerai Talak ({$ct}) = " . ($cg + $ct) . " <= Total ({$total_semua})"
		);

		// Count from data rows
		$count_cg_rows = 0;
		$count_ct_rows = 0;
		foreach ($data_semua as $row) {
			if (strpos($row->jenis_perkara_nama, 'Cerai Gugat') !== false) $count_cg_rows++;
			if (strpos($row->jenis_perkara_nama, 'Cerai Talak') !== false) $count_ct_rows++;
		}

		$this->assert_equal($count_cg_rows, $cg, "Bulanan Semua: Row count Cerai Gugat ({$count_cg_rows}) == summary.cerai_gugat ({$cg})");
		$this->assert_equal($count_ct_rows, $ct, "Bulanan Semua: Row count Cerai Talak ({$count_ct_rows}) == summary.cerai_talak ({$ct})");

		// ============================================================
		// TEST GROUP 4: Data integrity - akta cerai harus ada
		// ============================================================
		echo "<h2>TEST GROUP 4: Data Integrity - Setiap Row punya Akta Cerai</h2>";

		$missing_akta = 0;
		$missing_putusan = 0;
		foreach ($data_semua as $row) {
			if (empty($row->nomor_akta_cerai) && empty($row->tgl_akta_cerai)) $missing_akta++;
			if (empty($row->tanggal_putusan)) $missing_putusan++;
		}

		$this->assert_equal(0, $missing_akta, "Bulanan Semua: Tidak ada row tanpa akta cerai (missing: {$missing_akta})");
		$this->assert_equal(0, $missing_putusan, "Bulanan Semua: Tidak ada row tanpa tanggal putusan (missing: {$missing_putusan})");

		// ============================================================
		// TEST GROUP 5: No duplicate rows (nomor_perkara unique)
		// ============================================================
		echo "<h2>TEST GROUP 5: Tidak Ada Duplikat Nomor Perkara</h2>";

		$nomor_list = [];
		$duplicates = [];
		foreach ($data_semua as $row) {
			if (in_array($row->nomor_perkara, $nomor_list)) {
				$duplicates[] = $row->nomor_perkara;
			}
			$nomor_list[] = $row->nomor_perkara;
		}

		$dup_count = count($duplicates);
		$this->assert_equal(0, $dup_count, "Bulanan Semua: Tidak ada duplikat nomor perkara (duplikat: {$dup_count})");
		if ($dup_count > 0 && $dup_count <= 10) {
			echo "<div class='test-result fail-bg'>Duplikat: " . implode(', ', array_unique($duplicates)) . "</div>";
		}

		// ============================================================
		// TEST GROUP 6: Tahunan tests
		// ============================================================
		echo "<h2>TEST GROUP 6: Tahunan - Summary vs Row Count</h2>";

		$data_semua_t = $this->M_laporan_perceraian->get_laporan_perceraian_tahunan($lap_tahun, 'Semua', 'semua');
		$summary_semua_t = $this->M_laporan_perceraian->get_summary_perceraian_tahunan($lap_tahun, 'Semua', 'semua');

		$this->assert_equal(
			count($data_semua_t),
			(int)$summary_semua_t->total_perceraian,
			"Tahunan Semua: COUNT rows (" . count($data_semua_t) . ") == summary.total_perceraian ({$summary_semua_t->total_perceraian})"
		);

		$data_hsu_t = $this->M_laporan_perceraian->get_laporan_perceraian_tahunan($lap_tahun, 'HSU', 'semua');
		$summary_hsu_t = $this->M_laporan_perceraian->get_summary_perceraian_tahunan($lap_tahun, 'HSU', 'semua');

		$this->assert_equal(
			count($data_hsu_t),
			(int)$summary_hsu_t->total_perceraian,
			"Tahunan HSU: COUNT rows (" . count($data_hsu_t) . ") == summary.total_perceraian ({$summary_hsu_t->total_perceraian})"
		);

		$data_bal_t = $this->M_laporan_perceraian->get_laporan_perceraian_tahunan($lap_tahun, 'Balangan', 'semua');
		$summary_bal_t = $this->M_laporan_perceraian->get_summary_perceraian_tahunan($lap_tahun, 'Balangan', 'semua');

		$this->assert_equal(
			count($data_bal_t),
			(int)$summary_bal_t->total_perceraian,
			"Tahunan Balangan: COUNT rows (" . count($data_bal_t) . ") == summary.total_perceraian ({$summary_bal_t->total_perceraian})"
		);

		// Tahunan: Semua >= HSU + Balangan
		$t_semua = (int)$summary_semua_t->total_perceraian;
		$t_hsu = (int)$summary_hsu_t->total_perceraian;
		$t_bal = (int)$summary_bal_t->total_perceraian;

		$this->assert_true(
			$t_semua >= ($t_hsu + $t_bal),
			"Tahunan: Semua ({$t_semua}) >= HSU ({$t_hsu}) + Balangan ({$t_bal}) = " . ($t_hsu + $t_bal)
		);

		// Tahunan duplicates check
		$nomor_list_t = [];
		$duplicates_t = [];
		foreach ($data_semua_t as $row) {
			if (in_array($row->nomor_perkara, $nomor_list_t)) {
				$duplicates_t[] = $row->nomor_perkara;
			}
			$nomor_list_t[] = $row->nomor_perkara;
		}
		$dup_count_t = count($duplicates_t);
		$this->assert_equal(0, $dup_count_t, "Tahunan Semua: Tidak ada duplikat nomor perkara (duplikat: {$dup_count_t})");
		if ($dup_count_t > 0 && $dup_count_t <= 10) {
			echo "<div class='test-result fail-bg'>Duplikat: " . implode(', ', array_unique($duplicates_t)) . "</div>";
		}

		// ============================================================
		// TEST GROUP 7: Jenis perkara filter
		// ============================================================
		echo "<h2>TEST GROUP 7: Filter Jenis Perkara</h2>";

		$data_cg = $this->M_laporan_perceraian->get_laporan_perceraian_tahunan($lap_tahun, 'Semua', 'Cerai Gugat');
		$summary_cg = $this->M_laporan_perceraian->get_summary_perceraian_tahunan($lap_tahun, 'Semua', 'Cerai Gugat');

		$this->assert_equal(
			count($data_cg),
			(int)$summary_cg->total_perceraian,
			"Tahunan Cerai Gugat: COUNT rows (" . count($data_cg) . ") == summary ({$summary_cg->total_perceraian})"
		);

		// All rows should be Cerai Gugat
		$wrong_jenis = 0;
		foreach ($data_cg as $row) {
			if (strpos($row->jenis_perkara_nama, 'Cerai Gugat') === false) $wrong_jenis++;
		}
		$this->assert_equal(0, $wrong_jenis, "Tahunan Cerai Gugat: Semua rows adalah Cerai Gugat (salah: {$wrong_jenis})");

		$data_ct = $this->M_laporan_perceraian->get_laporan_perceraian_tahunan($lap_tahun, 'Semua', 'Cerai Talak');
		$summary_ct = $this->M_laporan_perceraian->get_summary_perceraian_tahunan($lap_tahun, 'Semua', 'Cerai Talak');

		$this->assert_equal(
			count($data_ct),
			(int)$summary_ct->total_perceraian,
			"Tahunan Cerai Talak: COUNT rows (" . count($data_ct) . ") == summary ({$summary_ct->total_perceraian})"
		);

		$wrong_jenis_ct = 0;
		foreach ($data_ct as $row) {
			if (strpos($row->jenis_perkara_nama, 'Cerai Talak') === false) $wrong_jenis_ct++;
		}
		$this->assert_equal(0, $wrong_jenis_ct, "Tahunan Cerai Talak: Semua rows adalah Cerai Talak (salah: {$wrong_jenis_ct})");

		// ============================================================
		// TEST GROUP 8: Data sample display
		// ============================================================
		echo "<h2>DATA SAMPLE - 10 Row Pertama (Tahunan Semua)</h2>";
		echo "<table><tr><th>No</th><th>Nomor Perkara</th><th>Jenis</th><th>Pihak 1</th><th>NIK 1</th><th>Pekerjaan 1</th><th>Pihak 2</th><th>NIK 2</th><th>Pekerjaan 2</th><th>Tgl Putusan</th><th>Tgl BHT</th><th>Status</th><th>No Akta</th><th>Tgl Akta</th></tr>";
		$sample = array_slice($data_semua_t, 0, 10);
		$no = 1;
		foreach ($sample as $row) {
			echo "<tr>";
			echo "<td>{$no}</td>";
			echo "<td>{$row->nomor_perkara}</td>";
			echo "<td>{$row->jenis_perkara_nama}</td>";
			echo "<td>" . mb_substr($row->nama_pihak_1, 0, 20) . "</td>";
			echo "<td>{$row->nik_pihak_1}</td>";
			echo "<td>{$row->pekerjaan_pihak_1}</td>";
			echo "<td>" . mb_substr($row->nama_pihak_2, 0, 20) . "</td>";
			echo "<td>{$row->nik_pihak_2}</td>";
			echo "<td>{$row->pekerjaan_pihak_2}</td>";
			echo "<td>{$row->tanggal_putusan}</td>";
			echo "<td>{$row->tanggal_bht}</td>";
			echo "<td>{$row->status_putusan}</td>";
			echo "<td>{$row->nomor_akta_cerai}</td>";
			echo "<td>{$row->tgl_akta_cerai}</td>";
			echo "</tr>";
			$no++;
		}
		echo "</table>";

		// ============================================================
		// SUMMARY
		// ============================================================
		$total = $this->passed + $this->failed;
		echo "<div class='summary'>";
		echo "<strong>HASIL AKHIR:</strong> {$total} tests — ";
		echo "<span class='pass'>{$this->passed} PASSED</span>, ";
		echo "<span class='fail'>{$this->failed} FAILED</span>";
		if ($this->failed > 0) {
			echo "<br><br><strong>⚠ Ada masalah pada data/perhitungan. Lihat detail FAIL di atas.</strong>";
		} else {
			echo "<br><br><strong>✓ Semua test PASSED - data dan jumlah sudah benar.</strong>";
		}
		echo "</div>";
		echo "</body></html>";
	}

	// ========================
	// Helper assertion methods
	// ========================

	private function assert_equal($expected, $actual, $message)
	{
		if ($expected === $actual) {
			$this->passed++;
			echo "<div class='test-result pass-bg'><span class='pass'>✓ PASS</span> — {$message}</div>";
		} else {
			$this->failed++;
			echo "<div class='test-result fail-bg'><span class='fail'>✗ FAIL</span> — {$message} (expected: {$expected}, got: {$actual})</div>";
		}
	}

	private function assert_true($condition, $message)
	{
		if ($condition) {
			$this->passed++;
			echo "<div class='test-result pass-bg'><span class='pass'>✓ PASS</span> — {$message}</div>";
		} else {
			$this->failed++;
			echo "<div class='test-result fail-bg'><span class='fail'>✗ FAIL</span> — {$message}</div>";
		}
	}
}
