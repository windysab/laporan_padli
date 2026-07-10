<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Check_db extends CI_Controller
{
	public function index()
	{
		echo "<h2>Pencarian Tabel Dokumen / Relas</h2>";

		// Search keywords
		$keywords = ['document', 'dokumen', 'relas', 'panggilan', 'file', 'upload', 'lampiran', 'siap'];

		$found_tables = [];
		foreach ($keywords as $kw) {
			$q = $this->db->query("SHOW TABLES LIKE ?", ["%{$kw}%"]);
			if ($q && $q->num_rows() > 0) {
				foreach ($q->result_array() as $row) {
					$tbl = implode('', $row);
					if (!in_array($tbl, $found_tables)) {
						$found_tables[] = $tbl;
					}
				}
			}
		}

		// Also search for tables containing 'perkara' that might have document columns
		$q_perkara = $this->db->query("SHOW TABLES LIKE 'perkara%'");
		$perkara_tables = [];
		if ($q_perkara && $q_perkara->num_rows() > 0) {
			foreach ($q_perkara->result_array() as $row) {
				$perkara_tables[] = implode('', $row);
			}
		}

		echo "<h3>Tabel yang mengandung kata kunci document/dokumen/relas/panggilan/file/upload/lampiran/siap:</h3>";
		if (!empty($found_tables)) {
			foreach ($found_tables as $tbl) {
				echo "<h4 style='color:blue;'>Tabel: {$tbl}</h4><pre>";
				try {
					$desc = $this->db->query("DESCRIBE {$tbl}");
					if ($desc && $desc->num_rows() > 0) {
						echo str_pad("KOLOM", 35) . str_pad("TIPE", 25) . "NULL\n";
						echo str_repeat("-", 70) . "\n";
						foreach ($desc->result() as $col) {
							echo str_pad($col->Field, 35) . str_pad($col->Type, 25) . $col->Null . "\n";
						}
					}
				} catch (Exception $e) {
					echo "Error: " . $e->getMessage();
				}
				echo "</pre>";

				// Show sample data
				echo "<pre><b>Sample data (5 rows):</b>\n";
				try {
					$sample = $this->db->query("SELECT * FROM {$tbl} LIMIT 5");
					if ($sample && $sample->num_rows() > 0) {
						foreach ($sample->result_array() as $row) {
							echo implode(' | ', array_map(function($v) { return substr($v, 0, 50); }, $row)) . "\n";
						}
					} else {
						echo "(kosong)\n";
					}
				} catch (Exception $e) {
					echo "Error: " . $e->getMessage();
				}
				echo "</pre><hr>";
			}
		} else {
			echo "<p style='color:red;'>Tidak ada tabel ditemukan.</p>";
		}

		// Check perkara tables for columns with 'path', 'file', 'dokumen', 'document'
		echo "<h3>Tabel perkara_* yang memiliki kolom berkaitan dokumen/file/path:</h3>";
		$col_keywords = ['path', 'file', 'dokumen', 'document', 'lampiran', 'upload'];
		foreach ($perkara_tables as $tbl) {
			try {
				$desc = $this->db->query("DESCRIBE {$tbl}");
				if ($desc && $desc->num_rows() > 0) {
					$matching_cols = [];
					foreach ($desc->result() as $col) {
						foreach ($col_keywords as $ck) {
							if (stripos($col->Field, $ck) !== false) {
								$matching_cols[] = $col->Field . " (" . $col->Type . ")";
							}
						}
					}
					if (!empty($matching_cols)) {
						echo "<h4 style='color:green;'>{$tbl}</h4><pre>";
						foreach ($matching_cols as $mc) echo "  - {$mc}\n";
						echo "</pre>";
					}
				}
			} catch (Exception $e) {}
		}
	}
}
