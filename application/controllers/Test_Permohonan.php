<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test-Driven Development (TDD) Controller
 * 
 * Verifikasi kebenaran TOTAL pada data permohonan:
 * 1. TOTAL row == sum of semua kecamatan rows (per kolom)
 * 2. TOTAL "Semua" == TOTAL "HSU" + TOTAL "Balangan"
 * 3. Sisa Perkara = Sisa Sebelumnya + Perkara Masuk - Perkara Putus
 * 
 * Akses: http://localhost/laporan_padli/index.php/Test_Permohonan
 */
class Test_Permohonan extends CI_Controller
{
	private $passed = 0;
	private $failed = 0;
	private $results = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_data_permohonan');
	}

	public function index()
	{
		echo "<html><head><title>TDD - Test Permohonan TOTAL</title>";
		echo "<style>
			body { font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; background: #f5f5f5; }
			h1 { color: #333; }
			h2 { color: #555; border-bottom: 2px solid #007bff; padding-bottom: 5px; }
			.pass { color: #28a745; font-weight: bold; }
			.fail { color: #dc3545; font-weight: bold; }
			.test-block { background: #fff; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #007bff; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
			.summary { background: #343a40; color: #fff; padding: 20px; border-radius: 8px; margin-top: 20px; font-size: 18px; }
			table { border-collapse: collapse; margin: 10px 0; }
			th, td { border: 1px solid #dee2e6; padding: 6px 12px; text-align: right; }
			th { background: #343a40; color: #fff; }
			td:first-child { text-align: left; }
			.mismatch { background: #f8d7da; }
		</style></head><body>";
		echo "<h1>TDD - Verifikasi TOTAL Data Permohonan</h1>";
		echo "<p>Waktu test: " . date('Y-m-d H:i:s') . "</p>";

		// Test parameters
		$lap_bulan = date('m');
		$lap_tahun = date('Y');
		$jenis_perkara = 'Dispensasi Kawin';

		echo "<hr>";

		// ============================================================
		// TEST GROUP 1: Laporan Bulanan
		// ============================================================
		echo "<h2>TEST GROUP 1: Laporan Bulanan ({$lap_bulan}/{$lap_tahun} - {$jenis_perkara})</h2>";

		$data_semua   = $this->M_data_permohonan->data_permohonan($lap_bulan, $lap_tahun, $jenis_perkara, 'Semua');
		$data_hsu     = $this->M_data_permohonan->data_permohonan($lap_bulan, $lap_tahun, $jenis_perkara, 'HSU');
		$data_balangan = $this->M_data_permohonan->data_permohonan($lap_bulan, $lap_tahun, $jenis_perkara, 'Balangan');

		$this->test_total_equals_sum_rows($data_semua, 'Bulanan - Semua Wilayah', ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_BULAN_LALU', 'SISA_TAHUN_LALU']);
		$this->test_total_equals_sum_rows($data_hsu, 'Bulanan - HSU', ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_BULAN_LALU', 'SISA_TAHUN_LALU']);
		$this->test_total_equals_sum_rows($data_balangan, 'Bulanan - Balangan', ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_BULAN_LALU', 'SISA_TAHUN_LALU']);

		$this->test_semua_equals_hsu_plus_balangan(
			$data_semua, $data_hsu, $data_balangan,
			'Bulanan', ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_BULAN_LALU', 'SISA_TAHUN_LALU']
		);

		// ============================================================
		// TEST GROUP 2: Laporan Tahunan
		// ============================================================
		echo "<h2>TEST GROUP 2: Laporan Tahunan ({$lap_tahun} - {$jenis_perkara})</h2>";

		$data_semua_t   = $this->M_data_permohonan->data_permohonan_tahunan($lap_tahun, $jenis_perkara, 'Semua');
		$data_hsu_t     = $this->M_data_permohonan->data_permohonan_tahunan($lap_tahun, $jenis_perkara, 'HSU');
		$data_balangan_t = $this->M_data_permohonan->data_permohonan_tahunan($lap_tahun, $jenis_perkara, 'Balangan');

		$this->test_total_equals_sum_rows($data_semua_t, 'Tahunan - Semua Wilayah', ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_TAHUN_LALU', 'SISA_PERKARA']);
		$this->test_total_equals_sum_rows($data_hsu_t, 'Tahunan - HSU', ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_TAHUN_LALU', 'SISA_PERKARA']);
		$this->test_total_equals_sum_rows($data_balangan_t, 'Tahunan - Balangan', ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_TAHUN_LALU', 'SISA_PERKARA']);

		$this->test_semua_equals_hsu_plus_balangan(
			$data_semua_t, $data_hsu_t, $data_balangan_t,
			'Tahunan', ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_TAHUN_LALU', 'SISA_PERKARA']
		);

		// ============================================================
		// TEST GROUP 3: Sisa Perkara Formula
		// ============================================================
		echo "<h2>TEST GROUP 3: Verifikasi Formula Sisa Perkara</h2>";

		// Tahunan: SISA_PERKARA = SISA_TAHUN_LALU + PERKARA_MASUK - PERKARA_PUTUS
		$this->test_sisa_formula($data_semua_t, 'Tahunan - Semua', 'SISA_TAHUN_LALU');
		$this->test_sisa_formula($data_hsu_t, 'Tahunan - HSU', 'SISA_TAHUN_LALU');
		$this->test_sisa_formula($data_balangan_t, 'Tahunan - Balangan', 'SISA_TAHUN_LALU');

		// ============================================================
		// TEST GROUP 4: Multiple jenis perkara
		// ============================================================
		echo "<h2>TEST GROUP 4: Test dengan Jenis Perkara Lain</h2>";
		$jenis_list = ['Istbat Nikah', 'P3HP/Penetapan Ahli Waris'];

		foreach ($jenis_list as $jp) {
			$d_semua = $this->M_data_permohonan->data_permohonan($lap_bulan, $lap_tahun, $jp, 'Semua');
			$d_hsu   = $this->M_data_permohonan->data_permohonan($lap_bulan, $lap_tahun, $jp, 'HSU');
			$d_bal   = $this->M_data_permohonan->data_permohonan($lap_bulan, $lap_tahun, $jp, 'Balangan');

			$this->test_total_equals_sum_rows($d_semua, "Bulanan {$jp} - Semua", ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_BULAN_LALU', 'SISA_TAHUN_LALU']);
			$this->test_total_equals_sum_rows($d_hsu, "Bulanan {$jp} - HSU", ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_BULAN_LALU', 'SISA_TAHUN_LALU']);
			$this->test_total_equals_sum_rows($d_bal, "Bulanan {$jp} - Balangan", ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_BULAN_LALU', 'SISA_TAHUN_LALU']);

			$this->test_semua_equals_hsu_plus_balangan(
				$d_semua, $d_hsu, $d_bal,
				"Bulanan {$jp}", ['PERKARA_MASUK', 'PERKARA_PUTUS', 'SISA_BULAN_LALU', 'SISA_TAHUN_LALU']
			);
		}

		// ============================================================
		// SUMMARY
		// ============================================================
		$total = $this->passed + $this->failed;
		$status_class = ($this->failed === 0) ? 'pass' : 'fail';
		echo "<div class='summary'>";
		echo "<strong>HASIL AKHIR:</strong> {$total} tests — ";
		echo "<span class='pass'>{$this->passed} PASSED</span>, ";
		echo "<span class='fail'>{$this->failed} FAILED</span>";
		if ($this->failed > 0) {
			echo "<br><br><strong>⚠ Ada masalah pada perhitungan TOTAL. Lihat detail FAIL di atas.</strong>";
		} else {
			echo "<br><br><strong>✓ Semua perhitungan TOTAL sudah benar.</strong>";
		}
		echo "</div>";
		echo "</body></html>";
	}

	/**
	 * TEST: Baris TOTAL harus sama dengan SUM semua baris kecamatan
	 */
	private function test_total_equals_sum_rows($data, $label, $columns)
	{
		echo "<div class='test-block'>";
		echo "<strong>Test: TOTAL == SUM(kecamatan) [{$label}]</strong><br>";

		if (empty($data)) {
			$this->record_fail();
			echo "<span class='fail'>FAIL</span> — Data kosong<br>";
			echo "</div>";
			return;
		}

		// Separate TOTAL row and kecamatan rows
		$total_row = null;
		$kecamatan_rows = [];
		foreach ($data as $row) {
			if ($row->KECAMATAN === 'TOTAL') {
				$total_row = $row;
			} else {
				$kecamatan_rows[] = $row;
			}
		}

		if ($total_row === null) {
			$this->record_fail();
			echo "<span class='fail'>FAIL</span> — Baris TOTAL tidak ditemukan<br>";
			echo "</div>";
			return;
		}

		// Build comparison table
		echo "<table><tr><th>Kolom</th><th>TOTAL (dari query)</th><th>SUM(kecamatan)</th><th>Selisih</th><th>Status</th></tr>";

		$all_pass = true;
		foreach ($columns as $col) {
			$total_val = isset($total_row->$col) ? (int)$total_row->$col : 0;
			$sum_val = 0;
			foreach ($kecamatan_rows as $kr) {
				$sum_val += isset($kr->$col) ? (int)$kr->$col : 0;
			}
			$diff = $total_val - $sum_val;
			$match = ($diff === 0);
			$class = $match ? '' : 'mismatch';
			$status = $match ? "<span class='pass'>OK</span>" : "<span class='fail'>MISMATCH</span>";
			if (!$match) $all_pass = false;

			echo "<tr class='{$class}'><td>{$col}</td><td>{$total_val}</td><td>{$sum_val}</td><td>{$diff}</td><td>{$status}</td></tr>";
		}
		echo "</table>";

		if ($all_pass) {
			$this->record_pass();
			echo "<span class='pass'>PASSED</span><br>";
		} else {
			$this->record_fail();
			echo "<span class='fail'>FAILED</span> — TOTAL tidak sama dengan SUM kecamatan!<br>";
		}
		echo "</div>";
	}

	/**
	 * TEST: TOTAL "Semua" harus == TOTAL "HSU" + TOTAL "Balangan"
	 */
	private function test_semua_equals_hsu_plus_balangan($data_semua, $data_hsu, $data_balangan, $label, $columns)
	{
		echo "<div class='test-block'>";
		echo "<strong>Test: TOTAL Semua == TOTAL HSU + TOTAL Balangan [{$label}]</strong><br>";

		$total_semua = $this->extract_total_row($data_semua);
		$total_hsu = $this->extract_total_row($data_hsu);
		$total_bal = $this->extract_total_row($data_balangan);

		if (!$total_semua || !$total_hsu || !$total_bal) {
			$this->record_fail();
			echo "<span class='fail'>FAIL</span> — Baris TOTAL tidak ditemukan pada salah satu dataset<br>";
			echo "</div>";
			return;
		}

		echo "<table><tr><th>Kolom</th><th>TOTAL Semua</th><th>TOTAL HSU</th><th>TOTAL Balangan</th><th>HSU+Balangan</th><th>Selisih</th><th>Status</th></tr>";

		$all_pass = true;
		foreach ($columns as $col) {
			$val_semua = isset($total_semua->$col) ? (int)$total_semua->$col : 0;
			$val_hsu = isset($total_hsu->$col) ? (int)$total_hsu->$col : 0;
			$val_bal = isset($total_bal->$col) ? (int)$total_bal->$col : 0;
			$val_sum = $val_hsu + $val_bal;
			$diff = $val_semua - $val_sum;
			$match = ($diff === 0);
			$class = $match ? '' : 'mismatch';
			$status = $match ? "<span class='pass'>OK</span>" : "<span class='fail'>MISMATCH</span>";
			if (!$match) $all_pass = false;

			echo "<tr class='{$class}'><td>{$col}</td><td>{$val_semua}</td><td>{$val_hsu}</td><td>{$val_bal}</td><td>{$val_sum}</td><td>{$diff}</td><td>{$status}</td></tr>";
		}
		echo "</table>";

		if ($all_pass) {
			$this->record_pass();
			echo "<span class='pass'>PASSED</span><br>";
		} else {
			$this->record_fail();
			echo "<span class='fail'>FAILED</span> — TOTAL Semua != TOTAL HSU + TOTAL Balangan!<br>";
		}
		echo "</div>";
	}

	/**
	 * TEST: SISA_PERKARA = sisa_base + PERKARA_MASUK - PERKARA_PUTUS
	 */
	private function test_sisa_formula($data, $label, $sisa_col)
	{
		echo "<div class='test-block'>";
		echo "<strong>Test: Formula SISA_PERKARA [{$label}]</strong><br>";
		echo "<em>Formula: SISA_PERKARA = {$sisa_col} + PERKARA_MASUK - PERKARA_PUTUS</em><br>";

		if (empty($data)) {
			$this->record_fail();
			echo "<span class='fail'>FAIL</span> — Data kosong<br>";
			echo "</div>";
			return;
		}

		echo "<table><tr><th>Kecamatan</th><th>{$sisa_col}</th><th>MASUK</th><th>PUTUS</th><th>SISA (query)</th><th>SISA (hitung)</th><th>Status</th></tr>";

		$all_pass = true;
		foreach ($data as $row) {
			if (!isset($row->SISA_PERKARA)) continue;
			$base = isset($row->$sisa_col) ? (int)$row->$sisa_col : 0;
			$masuk = (int)$row->PERKARA_MASUK;
			$putus = (int)$row->PERKARA_PUTUS;
			$sisa_query = (int)$row->SISA_PERKARA;
			$sisa_calc = $base + $masuk - $putus;
			$match = ($sisa_query === $sisa_calc);
			$class = $match ? '' : 'mismatch';
			$status = $match ? "<span class='pass'>OK</span>" : "<span class='fail'>MISMATCH</span>";
			if (!$match) $all_pass = false;

			echo "<tr class='{$class}'><td>{$row->KECAMATAN}</td><td>{$base}</td><td>{$masuk}</td><td>{$putus}</td><td>{$sisa_query}</td><td>{$sisa_calc}</td><td>{$status}</td></tr>";
		}
		echo "</table>";

		if ($all_pass) {
			$this->record_pass();
			echo "<span class='pass'>PASSED</span><br>";
		} else {
			$this->record_fail();
			echo "<span class='fail'>FAILED</span><br>";
		}
		echo "</div>";
	}

	private function extract_total_row($data)
	{
		if (empty($data)) return null;
		foreach ($data as $row) {
			if ($row->KECAMATAN === 'TOTAL') return $row;
		}
		return null;
	}

	private function record_pass()
	{
		$this->passed++;
	}

	private function record_fail()
	{
		$this->failed++;
	}
}
