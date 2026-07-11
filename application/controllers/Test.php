<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Test Controller — Jalankan via browser/curl di server produksi
 * URL: http://your-server/laporan_padli/index.php/Test
 *
 * HANYA untuk tahap testing. Hapus file ini setelah publikasi!
 */
class Test extends MY_Controller
{
    private $passed = 0;
    private $failed = 0;
    private $errors = [];

    public function index()
    {
        echo "<!DOCTYPE html><html><head><title>Test Suite</title>";
        echo "<style>
            body{font-family:sans-serif;padding:20px;background:#f5f5f5;max-width:900px;margin:0 auto}
            h1{color:#333;border-bottom:2px solid #28a745;padding-bottom:10px}
            h2{color:#555;margin-top:25px}
            .pass{color:#28a745;font-weight:bold}
            .fail{color:#dc3545;font-weight:bold}
            .skip{color:#ffc107}
            .stats{background:#fff;padding:15px;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);margin:20px 0}
        </style></head><body>";
        echo "<h1>🚀 Test Suite — Laporan Padli</h1>";
        echo "<p class='stats'>Server: {$_SERVER['SERVER_NAME']} | Time: " . date('Y-m-d H:i:s') . "</p>";

        $this->_test_db();
        $this->_test_models();
        $this->_test_views();
        $this->_test_security();

        echo "<h2>📊 Hasil</h2>";
        echo "<div class='stats'>";
        echo "<p>✅ Pass: <span class='pass'>{$this->passed}</span></p>";
        echo "<p>❌ Fail: <span class='fail'>{$this->failed}</span></p>";
        echo "<hr>";
        if (!empty($this->errors)) {
            echo "<h3>❌ Errors:</h3><ul>";
            foreach ($this->errors as $e) {
                echo "<li class='fail'>$e</li>";
            }
            echo "</ul>";
        } else {
            echo "<h3 style='color:#28a745'>✅ Semua tes lolos — siap publikasi!</h3>";
        }
        echo "</div></body></html>";

        $this->output->_display();
        exit;
    }

    private function _pass($msg)
    {
        $this->passed++;
        echo "<p class='pass'>✅ $msg</p>";
    }

    private function _fail($msg)
    {
        $this->failed++;
        $this->errors[] = $msg;
        echo "<p class='fail'>❌ $msg</p>";
    }

    private function _test_db()
    {
        echo "<h2>1. Database</h2>";
        try {
            $this->load->database();
            $this->db->initialize();
            if ($this->db->conn_id) {
                $this->_pass("Koneksi database OK (" . $this->db->database . ")");
            } else {
                $this->_fail("Koneksi database GAGAL");
            }
        } catch (Exception $e) {
            $this->_fail("Database error: " . $e->getMessage());
        }
    }

    private function _test_models()
    {
        echo "<h2>2. Models</h2>";
        $models = [
            'M_dashboard' => [],
            'M_data_perceraian_balangan' => ['2025', '01'],
            'M_data_perceraian_hsu' => ['2025', '01'],
            'M_delegasi' => ['01', '2025'],
            'M_delegasi_k' => ['01', '2025'],
            'M_Lipa1' => [],
            'M_P_Banding' => ['2025', '01'],
            'M_penerbitan_akta_cerai' => ['2025', '01'],
            'M_penyerahan_akta_cerai' => ['2025', '01'],
        ];

        foreach ($models as $name => $params) {
            try {
                $this->load->model($name);
                $this->_pass("Model $name loaded OK");
            } catch (Exception $e) {
                $this->_fail("Model $name gagal: " . $e->getMessage());
            }
        }
    }

    private function _test_views()
    {
        echo "<h2>3. Views (file exist check)</h2>";
        $views = [
            'dashboard', 'v_delegasi', 'v_delegasi_k', 'v_lipa1',
            'v_p_banding', 'v_penertiban_akta_cerai', 'v_penyerahan_akta_cerai',
            'v_perceraian_balangan', 'v_perceraian_hsu',
            'template/new_header', 'template/new_sidebar', 'template/new_footer',
        ];
        foreach ($views as $view) {
            $path = APPPATH . 'views/' . $view . '.php';
            if (file_exists($path)) {
                $this->_pass("View $view OK");
            } else {
                $this->_fail("View $view TIDAK ADA di $path");
            }
        }
    }

    private function _test_security()
    {
        echo "<h2>4. Security</h2>";

        // Encryption key
        if (!empty($this->config->item('encryption_key'))) {
            $this->_pass("Encryption key terisi ✅");
        } else {
            $this->_fail("Encryption key KOSONG");
        }

        // CSRF
        if ($this->config->item('csrf_protection')) {
            $this->_pass("CSRF Protection ON ✅");
        } else {
            $this->_fail("CSRF Protection OFF ⚠️");
        }

        // XSS Filtering
        if ($this->config->item('global_xss_filtering')) {
            $this->_pass("XSS Filtering ON ✅");
        } else {
            $this->_fail("XSS Filtering OFF ⚠️");
        }
    }
}
