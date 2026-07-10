<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Input Validation Helper
 * 
 * Provides common validation functions for sanitizing and validating
 * user input parameters used across controllers.
 */

/**
 * Validate and sanitize tahun (year) parameter
 * Must be a 4-digit number between 2000 and current year + 1
 *
 * @param mixed $tahun
 * @return string Valid year or current year as default
 */
function validate_tahun($tahun)
{
	$tahun = trim($tahun);
	if (preg_match('/^\d{4}$/', $tahun)) {
		$year = (int) $tahun;
		if ($year >= 2000 && $year <= (int) date('Y') + 1) {
			return $tahun;
		}
	}
	return date('Y');
}

/**
 * Validate and sanitize bulan (month) parameter
 * Must be a number between 1 and 12
 *
 * @param mixed $bulan
 * @return string Valid month (01-12) or current month as default
 */
function validate_bulan($bulan)
{
	$bulan = trim($bulan);
	if (preg_match('/^\d{1,2}$/', $bulan)) {
		$month = (int) $bulan;
		if ($month >= 1 && $month <= 12) {
			return str_pad($month, 2, '0', STR_PAD_LEFT);
		}
	}
	return date('m');
}

/**
 * Validate wilayah parameter against allowed values
 *
 * @param mixed $wilayah
 * @param string $default Default value if invalid
 * @return string Valid wilayah
 */
function validate_wilayah($wilayah, $default = 'HSU')
{
	$allowed = array('HSU', 'Balangan', 'Semua', 'Semua Wilayah');
	return in_array($wilayah, $allowed) ? $wilayah : $default;
}

/**
 * Validate jenis_laporan parameter against allowed values
 *
 * @param mixed $jenis_laporan
 * @return string Valid jenis_laporan
 */
function validate_jenis_laporan($jenis_laporan)
{
	$allowed = array('bulanan', 'tahunan', 'custom');
	return in_array($jenis_laporan, $allowed) ? $jenis_laporan : 'bulanan';
}

/**
 * Validate report_type parameter against allowed values
 *
 * @param mixed $report_type
 * @return string Valid report_type
 */
function validate_report_type($report_type)
{
	$allowed = array('summary', 'yearly', 'monthly', 'comparison', 'faktor', 'faktor_detail', 'custom_range', 'yearly_comparison');
	return in_array($report_type, $allowed) ? $report_type : 'summary';
}

/**
 * Validate jenis_kelamin parameter
 *
 * @param mixed $jenis_kelamin
 * @return string 'L' or 'P'
 */
function validate_jenis_kelamin($jenis_kelamin)
{
	return in_array($jenis_kelamin, array('L', 'P')) ? $jenis_kelamin : 'L';
}

/**
 * Validate tanggal (date) parameter in Y-m-d format
 *
 * @param mixed $tanggal
 * @param string $default Default value if invalid
 * @return string Valid date string or default
 */
function validate_tanggal($tanggal, $default = null)
{
	if ($default === null) {
		$default = date('Y-m-d');
	}
	$tanggal = trim($tanggal);
	if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
		$parts = explode('-', $tanggal);
		if (checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
			return $tanggal;
		}
	}
	return $default;
}

/**
 * Validate jenis_perkara parameter
 * Only allows alphanumeric, spaces, and common punctuation
 *
 * @param mixed $jenis_perkara
 * @param string $default Default value if empty
 * @return string Sanitized jenis_perkara
 */
function validate_jenis_perkara($jenis_perkara, $default = 'Cerai Gugat')
{
	$jenis_perkara = trim($jenis_perkara);
	if (empty($jenis_perkara)) {
		return $default;
	}
	// Only allow letters, numbers, spaces, dots, slashes, and hyphens
	if (preg_match('/^[a-zA-Z0-9\s\.\-\/]+$/', $jenis_perkara)) {
		return $jenis_perkara;
	}
	return $default;
}

/**
 * Validate nomor_perkara pattern for LIKE queries
 * Only allows patterns like: Pdt.G, Pdt.P, etc.
 *
 * @param mixed $pattern
 * @param string $default
 * @return string Sanitized pattern
 */
function validate_perkara_pattern($pattern, $default = 'Pdt.G')
{
	$pattern = trim($pattern);
	if (empty($pattern)) {
		return $default;
	}
	// Only allow letters, dots, slashes, and hyphens
	if (preg_match('/^[a-zA-Z0-9\.\-\/]+$/', $pattern)) {
		return $pattern;
	}
	return $default;
}

/**
 * Validate status_putusan parameter
 *
 * @param mixed $status
 * @return string Valid status or 'semua'
 */
function validate_status_putusan($status)
{
	$status = trim($status);
	if (empty($status)) {
		return 'semua';
	}
	// Allow 'semua' or numeric ID
	if ($status === 'semua' || preg_match('/^\d+$/', $status)) {
		return $status;
	}
	return 'semua';
}

/**
 * Load template header, sidebar, main view, and footer in one call.
 * Reduces 4 lines of view loading to 1 line.
 *
 * @param string $view  Main view name
 * @param array  $data  Data passed to the view
 */
function view_load($view, $data = [])
{
	$CI =& get_instance();
	$CI->load->view('template/new_header');
	$CI->load->view('template/new_sidebar');
	$CI->load->view($view, $data);
	$CI->load->view('template/new_footer');
}

/**
 * Output JSON response and exit.
 */
function json_output($data)
{
	header('Content-Type: application/json');
	echo json_encode($data);
	exit;
}

/**
 * Map wilayah shorthand to full database name.
 */
function wilayah_map($wilayah)
{
	$map = [
		'HSU' => 'Hulu Sungai Utara',
		'Hulu Sungai Utara' => 'Hulu Sungai Utara',
		'Balangan' => 'Balangan',
		'Amuntai' => 'Amuntai',
		'Semua' => 'Semua',
		'Semua Wilayah' => 'Semua',
	];
	return $map[$wilayah] ?? $wilayah;
}

/**
 * Get wilayah label for display, normalized.
 */
function wilayah_label($wilayah)
{
	$labels = [
		'SEMUA' => 'HSU dan Balangan',
		'SEMUA WILAYAH' => 'HSU dan Balangan',
		'HSU' => 'HSU',
		'HULU SUNGAI UTARA' => 'HSU',
		'BALANGAN' => 'Balangan',
	];
	$key = strtoupper(trim($wilayah));
	return $labels[$key] ?? $wilayah;
}
