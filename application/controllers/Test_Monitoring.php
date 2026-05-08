<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test_Monitoring extends CI_Controller
{
	private $passed = 0;
	private $failed = 0;

	public function __construct()
	{
		parent::__construct();
		if (!ini_get('date.timezone')) {
			date_default_timezone_set('Asia/Jakarta');
		}
		$this->load->model('M_monitoring_sipp');
		$this->load->helper('url');
	}

	public function index()
	{
		echo "<!DOCTYPE html><html><head><title>TDD - Monitoring SIPP</title>";
		echo "<style>
			body { font-family: 'Segoe UI', sans-serif; margin: 20px; background: #f5f5f5; }
			h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
			h2 { color: #555; margin-top: 30px; background: #fff; padding: 10px 15px; border-left: 4px solid #007bff; }
			.pass { color: #28a745; font-weight: bold; }
			.fail { color: #dc3545; font-weight: bold; }
			.test-result { padding: 8px 15px; margin: 5px 0; background: #fff; border-radius: 4px; border-left: 4px solid #ccc; }
			.test-result.pass-bg { border-left-color: #28a745; }
			.test-result.fail-bg { border-left-color: #dc3545; background: #fff5f5; }
			.debug-box { padding: 15px; margin: 10px 0; background: #e9ecef; border-radius: 6px; font-family: monospace; font-size: 0.85em; white-space: pre-wrap; }
			.summary { margin-top: 30px; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-size: 1.2em; }
			table { border-collapse: collapse; margin: 10px 0; width: 100%; }
			th, td { border: 1px solid #ddd; padding: 6px 10px; text-align: left; font-size: 0.85em; }
			th { background: #007bff; color: #fff; }
			tr:nth-child(even) { background: #f9f9f9; }
		</style></head><body>";
		echo "<h1>TDD - Monitoring SIPP</h1>";

		// ============================================================
		// TEST GROUP 1: Dashboard Hari Ini - Data Valid
		// ============================================================
		echo "<h2>TEST GROUP 1: Dashboard Hari Ini</h2>";

		$dashboard = $this->M_monitoring_sipp->get_dashboard_hari_ini();

		$this->assert_true(
			is_object($dashboard),
			"get_dashboard_hari_ini() returns object"
		);
		$this->assert_true(
			isset($dashboard->masuk_hari_ini) && is_numeric($dashboard->masuk_hari_ini),
			"masuk_hari_ini is numeric (value: {$dashboard->masuk_hari_ini})"
		);
		$this->assert_true(
			isset($dashboard->putus_hari_ini) && is_numeric($dashboard->putus_hari_ini),
			"putus_hari_ini is numeric (value: {$dashboard->putus_hari_ini})"
		);
		$this->assert_true(
			isset($dashboard->akta_cerai_hari_ini) && is_numeric($dashboard->akta_cerai_hari_ini),
			"akta_cerai_hari_ini is numeric (value: {$dashboard->akta_cerai_hari_ini})"
		);
		$this->assert_true(
			isset($dashboard->backlog) && is_numeric($dashboard->backlog),
			"backlog is numeric (value: {$dashboard->backlog})"
		);

		// ============================================================
		// TEST GROUP 2: Dashboard Bulan Ini
		// ============================================================
		echo "<h2>TEST GROUP 2: Dashboard Bulan Ini</h2>";

		$bulan_ini = $this->M_monitoring_sipp->get_dashboard_bulan_ini();

		$this->assert_true(is_object($bulan_ini), "get_dashboard_bulan_ini() returns object");
		$this->assert_true(
			is_numeric($bulan_ini->masuk_bulan_ini),
			"masuk_bulan_ini is numeric (value: {$bulan_ini->masuk_bulan_ini})"
		);
		$this->assert_true(
			is_numeric($bulan_ini->putus_bulan_ini),
			"putus_bulan_ini is numeric (value: {$bulan_ini->putus_bulan_ini})"
		);
		$this->assert_true(
			$bulan_ini->masuk_bulan_ini >= $dashboard->masuk_hari_ini,
			"masuk_bulan_ini ({$bulan_ini->masuk_bulan_ini}) >= masuk_hari_ini ({$dashboard->masuk_hari_ini})"
		);

		// ============================================================
		// TEST GROUP 3: Trend Bulanan - Chart Data
		// ============================================================
		echo "<h2>TEST GROUP 3: Trend Bulanan (Data untuk Grafik)</h2>";

		$trend = $this->M_monitoring_sipp->get_trend_bulanan_tahun_ini();

		$this->assert_true(is_array($trend), "get_trend_bulanan_tahun_ini() returns array");
		$this->assert_true(count($trend) > 0, "Trend has data (count: " . count($trend) . ")");
		$this->assert_true(
			count($trend) <= 12,
			"Trend max 12 bulan (count: " . count($trend) . ")"
		);

		// Verify each month has valid data
		$trend_valid = true;
		$trend_debug = [];
		foreach ($trend as $t) {
			if (!isset($t->bulan) || !isset($t->perkara_masuk) || !isset($t->perkara_putus) || !isset($t->akta_cerai)) {
				$trend_valid = false;
			}
			if (!is_numeric($t->perkara_masuk) || !is_numeric($t->perkara_putus) || !is_numeric($t->akta_cerai)) {
				$trend_valid = false;
			}
			$trend_debug[] = "Bln {$t->bulan}: masuk={$t->perkara_masuk}, putus={$t->perkara_putus}, akta={$t->akta_cerai}";
		}
		$this->assert_true($trend_valid, "Semua trend data valid (bulan, perkara_masuk, perkara_putus, akta_cerai numeric)");

		echo "<div class='debug-box'><strong>DATA TREND (untuk chart):</strong>\n" . implode("\n", $trend_debug) . "</div>";

		// Check chart labels would generate correctly
		$nama_bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
		$labels = [];
		$data_masuk = [];
		$data_putus = [];
		$data_akta = [];
		foreach ($trend as $t) {
			$labels[] = "'" . $nama_bulan[$t->bulan] . "'";
			$data_masuk[] = $t->perkara_masuk;
			$data_putus[] = $t->perkara_putus;
			$data_akta[] = $t->akta_cerai;
		}

		$this->assert_true(
			count($labels) > 0,
			"Chart labels generated: [" . implode(', ', $labels) . "]"
		);
		$this->assert_true(
			count($data_masuk) > 0,
			"Chart data masuk: [" . implode(', ', $data_masuk) . "]"
		);

		// ============================================================
		// TEST GROUP 4: Aging Report
		// ============================================================
		echo "<h2>TEST GROUP 4: Aging Report</h2>";

		$aging_data = $this->M_monitoring_sipp->get_perkara_belum_putus('Semua', 'semua');
		$aging_summary = $this->M_monitoring_sipp->get_aging_summary('Semua', 'semua');

		$this->assert_true(is_object($aging_summary), "get_aging_summary() returns object");
		$this->assert_equal(
			count($aging_data),
			(int)$aging_summary->total_belum_putus,
			"Aging: COUNT rows (" . count($aging_data) . ") == summary.total_belum_putus ({$aging_summary->total_belum_putus})"
		);

		// Verify color categorization
		$count_hijau = 0;
		$count_kuning = 0;
		$count_merah = 0;
		foreach ($aging_data as $row) {
			if ($row->status_warna === 'hijau') $count_hijau++;
			elseif ($row->status_warna === 'kuning') $count_kuning++;
			elseif ($row->status_warna === 'merah') $count_merah++;
		}

		$this->assert_equal(
			(int)$aging_summary->hijau,
			$count_hijau,
			"Aging hijau: summary ({$aging_summary->hijau}) == counted ({$count_hijau})"
		);
		$this->assert_equal(
			(int)$aging_summary->kuning,
			$count_kuning,
			"Aging kuning: summary ({$aging_summary->kuning}) == counted ({$count_kuning})"
		);
		$this->assert_equal(
			(int)$aging_summary->merah,
			$count_merah,
			"Aging merah: summary ({$aging_summary->merah}) == counted ({$count_merah})"
		);

		$total_colors = $count_hijau + $count_kuning + $count_merah;
		$this->assert_equal(
			(int)$aging_summary->total_belum_putus,
			$total_colors,
			"Aging: hijau ({$count_hijau}) + kuning ({$count_kuning}) + merah ({$count_merah}) = {$total_colors} == total ({$aging_summary->total_belum_putus})"
		);

		// Wilayah test
		$aging_hsu = $this->M_monitoring_sipp->get_aging_summary('HSU', 'semua');
		$aging_bal = $this->M_monitoring_sipp->get_aging_summary('Balangan', 'semua');

		$this->assert_true(
			(int)$aging_summary->total_belum_putus >= ((int)$aging_hsu->total_belum_putus + (int)$aging_bal->total_belum_putus),
			"Aging Semua ({$aging_summary->total_belum_putus}) >= HSU ({$aging_hsu->total_belum_putus}) + Balangan ({$aging_bal->total_belum_putus})"
		);

		// ============================================================
		// TEST GROUP 5: Minutasi
		// ============================================================
		echo "<h2>TEST GROUP 5: Minutasi</h2>";

		$min_summary = $this->M_monitoring_sipp->get_minutasi_summary('Semua');
		$belum_bht = $this->M_monitoring_sipp->get_perkara_sudah_putus_belum_bht('Semua');
		$belum_akta = $this->M_monitoring_sipp->get_perkara_sudah_bht_belum_akta('Semua');

		$this->assert_true(is_object($min_summary), "get_minutasi_summary() returns object");
		$this->assert_equal(
			count($belum_bht),
			(int)$min_summary->belum_bht,
			"Minutasi belum BHT: COUNT rows (" . count($belum_bht) . ") == summary ({$min_summary->belum_bht})"
		);
		$this->assert_equal(
			count($belum_akta),
			(int)$min_summary->belum_akta,
			"Minutasi belum akta: COUNT rows (" . count($belum_akta) . ") == summary ({$min_summary->belum_akta})"
		);

		// ============================================================
		// TEST GROUP 6: Kinerja
		// ============================================================
		echo "<h2>TEST GROUP 6: Kinerja (" . date('Y') . ")</h2>";

		$tahun = date('Y');
		$kinerja = $this->M_monitoring_sipp->get_kinerja($tahun, 'Semua');

		$this->assert_true(is_object($kinerja), "get_kinerja() returns object");
		$this->assert_true(is_numeric($kinerja->perkara_masuk), "perkara_masuk is numeric ({$kinerja->perkara_masuk})");
		$this->assert_true(is_numeric($kinerja->perkara_putus), "perkara_putus is numeric ({$kinerja->perkara_putus})");
		$this->assert_true(is_numeric($kinerja->clearance_rate), "clearance_rate is numeric ({$kinerja->clearance_rate})");
		$this->assert_true(is_numeric($kinerja->disposition_time), "disposition_time is numeric ({$kinerja->disposition_time})");
		$this->assert_true(is_numeric($kinerja->backlog), "backlog is numeric ({$kinerja->backlog})");

		// Verify clearance rate formula
		if ($kinerja->perkara_masuk > 0) {
			$expected_cr = round(($kinerja->perkara_putus / $kinerja->perkara_masuk) * 100, 1);
			$this->assert_equal(
				$expected_cr,
				$kinerja->clearance_rate,
				"Clearance Rate: putus ({$kinerja->perkara_putus}) / masuk ({$kinerja->perkara_masuk}) * 100 = {$expected_cr}%"
			);
		}

		// Verify tepat waktu + terlambat = total putus this year
		$total_timing = $kinerja->tepat_waktu + $kinerja->terlambat;
		$this->assert_equal(
			(int)$kinerja->perkara_putus,
			$total_timing,
			"Kinerja: tepat_waktu ({$kinerja->tepat_waktu}) + terlambat ({$kinerja->terlambat}) = {$total_timing} == perkara_putus ({$kinerja->perkara_putus})"
		);

		// ============================================================
		// TEST GROUP 7: Kinerja Per Bulan (Chart Data)
		// ============================================================
		echo "<h2>TEST GROUP 7: Kinerja Per Bulan (Data untuk Grafik)</h2>";

		$kinerja_bln = $this->M_monitoring_sipp->get_kinerja_per_bulan($tahun, 'Semua');

		$this->assert_true(is_array($kinerja_bln), "get_kinerja_per_bulan() returns array");
		$this->assert_equal(12, count($kinerja_bln), "Kinerja per bulan has 12 rows (got: " . count($kinerja_bln) . ")");

		$sum_masuk = 0;
		$sum_putus = 0;
		$bln_debug = [];
		foreach ($kinerja_bln as $k) {
			$sum_masuk += $k->perkara_masuk;
			$sum_putus += $k->perkara_putus;
			$bln_debug[] = "Bln {$k->bulan}: masuk={$k->perkara_masuk}, putus={$k->perkara_putus}, CR={$k->clearance_rate}%";
		}

		$this->assert_equal(
			(int)$kinerja->perkara_masuk,
			$sum_masuk,
			"Sum per-bulan masuk ({$sum_masuk}) == total kinerja masuk ({$kinerja->perkara_masuk})"
		);
		$this->assert_equal(
			(int)$kinerja->perkara_putus,
			$sum_putus,
			"Sum per-bulan putus ({$sum_putus}) == total kinerja putus ({$kinerja->perkara_putus})"
		);

		echo "<div class='debug-box'><strong>DATA KINERJA PER BULAN (untuk chart):</strong>\n" . implode("\n", $bln_debug) . "</div>";

		// ============================================================
		// TEST GROUP 8: Chart.js Rendering Check
		// ============================================================
		echo "<h2>TEST GROUP 8: Chart.js Rendering Diagnostics</h2>";

		// Check if data would produce valid JS
		$js_labels_trend = "[" . implode(",", $labels) . "]";
		$js_data_masuk = "[" . implode(",", $data_masuk) . "]";

		$this->assert_true(
			!empty($labels) && !in_array("''", $labels),
			"Trend labels tidak kosong dan tidak ada empty string"
		);

		$has_null_masuk = false;
		$has_null_putus = false;
		foreach ($trend as $t) {
			if ($t->perkara_masuk === null) $has_null_masuk = true;
			if ($t->perkara_putus === null) $has_null_putus = true;
		}
		$this->assert_true(!$has_null_masuk, "Trend: Tidak ada NULL di perkara_masuk");
		$this->assert_true(!$has_null_putus, "Trend: Tidak ada NULL di perkara_putus");

		$has_null_k_masuk = false;
		$has_null_k_putus = false;
		foreach ($kinerja_bln as $k) {
			if ($k->perkara_masuk === null) $has_null_k_masuk = true;
			if ($k->perkara_putus === null) $has_null_k_putus = true;
		}
		$this->assert_true(!$has_null_k_masuk, "Kinerja bulanan: Tidak ada NULL di perkara_masuk");
		$this->assert_true(!$has_null_k_putus, "Kinerja bulanan: Tidak ada NULL di perkara_putus");

		echo "<div class='debug-box'><strong>Generated JS Labels (Trend):</strong>\n{$js_labels_trend}\n\n<strong>Generated JS Data (Masuk):</strong>\n{$js_data_masuk}</div>";

		echo "<div class='debug-box'><strong>POSSIBLE CHART ISSUES:</strong>\n";
		echo "1. Chart.js CDN blocked? Check browser console for network errors\n";
		echo "2. Canvas in hidden tab? Charts need visible container to render\n";
		echo "3. \$nama_bulan defined AFTER chart JS code? (Should be defined before)\n";
		echo "4. jQuery conflict? Check if \$(document).ready fires properly\n";
		echo "\n<strong>FIX APPLIED:</strong> Move \$nama_bulan definition to top of file, and render charts on tab switch</div>";

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
			echo "<br><br><strong>✓ Semua test PASSED - data monitoring sudah benar.</strong>";
		}
		echo "</div>";
		echo "</body></html>";
	}

	private function assert_equal($expected, $actual, $message)
	{
		if ($expected === $actual || (is_numeric($expected) && is_numeric($actual) && (float)$expected === (float)$actual)) {
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
