<?php
/**
 * Laporan Padli — Test Suite
 * 
 * Usage: php test/run_tests.php [--quick] [--ci-bootstrap]
 * 
 * Modes:
 *   default     → Syntax check + sidebar links + CI bootstrap tests
 *   --quick     → Syntax check + sidebar links only (no DB/CI load)
 *   --ci-bootstrap → Full CI test with DB, models, controllers
 * 
 * Exit code: 0 = all PASS, 1 = some FAIL
 */

// ─── Configuration ───────────────────────────────────────────
define('BASE_DIR', dirname(__DIR__));
define('TEST_DIR', __DIR__);

$testsPassed = 0;
$testsFailed = 0;
$errors = [];

// ─── Helpers ─────────────────────────────────────────────────
function pass($msg) { global $testsPassed; $testsPassed++; echo "  ✅ $msg\n"; }
function fail($msg) { global $testsFailed, $errors; $testsFailed++; $errors[] = $msg; echo "  ❌ $msg\n"; }
function heading($title) { echo "\n─── $title ───\n"; }
function separator() { echo str_repeat('─', 60) . "\n"; }

function phpFilesIn($dir) {
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getRealPath();
        }
    }
    sort($files);
    return $files;
}

// ─── Test 1: PHP Syntax Check ────────────────────────────────
heading('1. PHP Syntax Check');

$checkDirs = [
    BASE_DIR . '/application/controllers',
    BASE_DIR . '/application/models',
    BASE_DIR . '/application/views',
    BASE_DIR . '/application/core',
    BASE_DIR . '/application/helpers',
    BASE_DIR . '/application/libraries',
    BASE_DIR . '/application/config',
];

$allPhpFiles = [];
foreach ($checkDirs as $dir) {
    if (is_dir($dir)) {
        $allPhpFiles = array_merge($allPhpFiles, phpFilesIn($dir));
    }
}
$allPhpFiles[] = BASE_DIR . '/index.php';

$syntaxErrors = 0;
foreach ($allPhpFiles as $file) {
    $relative = str_replace(BASE_DIR . '/', '', $file);
    $output = shell_exec("php -l " . escapeshellarg($file) . " 2>&1");
    if (strpos($output, 'No syntax errors') === false) {
        fail("Syntax error: $relative — " . trim($output));
        $syntaxErrors++;
    }
}
if ($syntaxErrors === 0) {
    pass("Semua " . count($allPhpFiles) . " file PHP valid syntax ✅");
}

// ─── Test 2: Sidebar Link Verification ───────────────────────
heading('2. Sidebar ↔ Controller Match');

$sidebarFile = BASE_DIR . '/application/views/template/new_sidebar.php';
if (file_exists($sidebarFile)) {
    $sidebar = file_get_contents($sidebarFile);
    preg_match_all("/site_url\(\s*['\"]([^'\"]+)['\"]/", $sidebar, $matches);
    $links = array_unique($matches[1]);
    
    $controllers = [];
    foreach (glob(BASE_DIR . '/application/controllers/*.php') as $ctl) {
        $controllers[] = pathinfo($ctl, PATHINFO_FILENAME);
    }
    
    $sidebarValid = 0;
    $sidebarBroken = 0;
    foreach ($links as $link) {
        // Handle sub-path like "Admin/Dashboard" → controller "Admin"
        $parts = explode('/', $link);
        $ctrlName = $parts[0];
        
        if (in_array($ctrlName, $controllers)) {
            $sidebarValid++;
        } else {
            fail("Sidebar link '$link' → controller '$ctrlName' TIDAK ADA");
            $sidebarBroken++;
        }
    }
    
    if ($sidebarBroken === 0) {
        pass("Sidebar: $sidebarValid link valid, 0 broken");
    }
} else {
    fail("File sidebar tidak ditemukan: $sidebarFile");
}

// ─── Test 3: View ↔ Controller Match ─────────────────────────
heading('3. View ↔ Controller Match');

// Build controller-to-view mapping from controller source
$controllerViews = [];
foreach (glob(BASE_DIR . '/application/controllers/*.php') as $ctlFile) {
    $content = file_get_contents($ctlFile);
    preg_match_all("/this->load->view\(\s*['\"]([^'\"]+)['\"]/", $content, $vmatches);
    preg_match("/class\s+(\w+)\s+extends/", $content, $cmatch);
    if (!empty($cmatch[1])) {
        $views = array_unique($vmatches[1]);
        $controllerViews[$cmatch[1]] = $views;
    }
}

$viewDir = BASE_DIR . '/application/views';
$existingViews = [];
foreach (phpFilesIn($viewDir) as $vf) {
    $existingViews[] = str_replace($viewDir . '/', '', $vf);
}

$viewsOk = 0;
$viewsMissing = 0;
$viewsOrphan = [];

foreach ($controllerViews as $ctrl => $views) {
    foreach ($views as $view) {
        $viewFile = $view . '.php';
        $viewPath = $viewDir . '/' . $viewFile;
        if (file_exists($viewPath) || strpos($view, '/') !== false) {
            // Check relative path
            $found = false;
            foreach ($existingViews as $ev) {
                if ($ev === $viewFile || strpos($ev, $view) !== false) {
                    $found = true;
                    break;
                }
            }
            if ($found) {
                $viewsOk++;
            } else {
                fail("View '$view' dipanggil controller '$ctrl' tapi file tidak ada");
                $viewsMissing++;
            }
        } else {
            fail("View '$view' dipanggil controller '$ctrl' tapi file tidak ada");
            $viewsMissing++;
        }
    }
}

if ($viewsMissing === 0) {
    pass("Semua view yang dipanggil controller ada ✅");
}

// Find orphan views (not loaded by any controller)
$allLoadedViews = [];
foreach ($controllerViews as $views) {
    $allLoadedViews = array_merge($allLoadedViews, $views);
}
$allLoadedViews = array_unique($allLoadedViews);

foreach ($existingViews as $viewFile) {
    $viewBase = pathinfo($viewFile, PATHINFO_FILENAME);
    if (!in_array($viewBase, $allLoadedViews) && 
        !in_array('v_' . $viewBase, $allLoadedViews) &&
        $viewFile !== 'dashboard.php') {
        $viewsOrphan[] = $viewFile;
    }
}
if (empty($viewsOrphan)) {
    pass("Tidak ada view orphan (gak dipanggil siapa pun)");
} else {
    foreach ($viewsOrphan as $ov) {
        fail("View orphan: $ov (tidak dipanggil controller mana pun)");
    }
}

// ─── Test 4: Full CI Bootstrap Test ──────────────────────────
heading('4. CI Bootstrap Test (DB + Models + Controllers)');

$ciMode = in_array('--ci-bootstrap', $argv ?? []);
if ($ciMode) {
    // Bootstrap CodeIgniter
    $_SERVER['CI_ENVIRONMENT'] = 'testing';
    chdir(BASE_DIR);
    
    try {
        require_once BASE_DIR . '/system/core/CodeIgniter.php';
        $CI =& get_instance();
        pass("CI berhasil di-bootstrap");
        
        // Test DB connection
        try {
            $CI->load->database();
            $CI->db->initialize();
            if ($CI->db->conn_id) {
                pass("Database connection OK");
                
                // Test each model
                foreach (glob(BASE_DIR . '/application/models/*.php') as $modelFile) {
                    $modelName = pathinfo($modelFile, PATHINFO_FILENAME);
                    try {
                        $CI->load->model($modelName);
                        pass("Model $modelName loaded OK");
                    } catch (Exception $e) {
                        fail("Model $modelName gagal load: " . $e->getMessage());
                    }
                }
            } else {
                fail("Database connection FAILED");
            }
        } catch (Exception $e) {
            fail("Database error: " . $e->getMessage());
        }
        
    } catch (Exception $e) {
        fail("CI Bootstrap gagal: " . $e->getMessage());
    }
} else {
    echo "  ⏭️  Skip CI bootstrap (butuh server dengan PHP + DB). Jalankan:\n";
    echo "     php test/run_tests.php --ci-bootstrap\n";
}

// ─── Test 5: Security Checklist ──────────────────────────────
heading('5. Security Checklist');

$securityChecks = [
    'XSS Protection' => [
        'file' => 'application/config/config.php',
        'pattern' => "\$config['global_xss_filtering']",
        'expected' => 'TRUE',
    ],
    'CSRF Protection' => [
        'file' => 'application/config/config.php',
        'pattern' => "\$config['csrf_protection']",
        'expected' => 'TRUE',
    ],
    'Index Page Removed' => [
        'file' => 'application/config/config.php',
        'pattern' => "\$config['index_page']",
        'expected' => "''",
    ],
];

foreach ($securityChecks as $name => $check) {
    $configFile = BASE_DIR . '/' . $check['file'];
    if (!file_exists($configFile)) {
        fail("Security: $name — file config tidak ditemukan");
        continue;
    }
    $content = file_get_contents($configFile);
    preg_match('/' . preg_quote($check['pattern'], '/') . '\s*=\s*([^;]+)/', $content, $m);
    if (!empty($m[1])) {
        $value = trim($m[1]);
        if ($value === $check['expected'] || strpos($value, $check['expected']) !== false) {
            pass("Security: $name = $value");
        } else {
            fail("Security: $name = $value (expected: {$check['expected']})");
        }
    } else {
        fail("Security: $name — not found in config");
    }
}

// Check session encryption
$configContent = file_get_contents(BASE_DIR . '/application/config/config.php');
if (preg_match("/sess_encrypt_cookie.*TRUE/", $configContent) || 
    preg_match("/encryption_key\s*=\s*'([^']+)'/", $configContent, $ek)) {
    if (!empty($ek[1])) {
        pass("Security: encryption_key terisi ✅");
    } else {
        fail("Security: encryption_key KOSONG");
    }
} else {
    fail("Security: encryption_key tidak ditemukan");
}

// ─── Test 6: File Structure Audit ────────────────────────────
heading('6. File Structure Audit');

$expectedStructure = [
    'application/controllers' => 'dir',
    'application/models' => 'dir',
    'application/views' => 'dir',
    'application/views/template' => 'dir',
    'application/core' => 'dir',
    'application/config' => 'dir',
    'application/config/database.php' => 'file',
    'application/config/config.php' => 'file',
    'application/views/template/new_header.php' => 'file',
    'application/views/template/new_sidebar.php' => 'file',
    'application/views/template/new_footer.php' => 'file',
];

foreach ($expectedStructure as $path => $type) {
    $fullPath = BASE_DIR . '/' . $path;
    if ($type === 'dir' && is_dir($fullPath)) {
        pass("Directory exists: $path");
    } elseif ($type === 'file' && file_exists($fullPath)) {
        pass("File exists: $path");
    } elseif ($type === 'dir') {
        fail("Directory MISSING: $path");
    } else {
        fail("File MISSING: $path");
    }
}

// ─── Summary ─────────────────────────────────────────────────
heading('RESULT');
separator();
echo "  Tests passed: $testsPassed\n";
echo "  Tests failed: $testsFailed\n";
separator();

if (!empty($errors)) {
    echo "\n⚠️  FAILURES:\n";
    foreach ($errors as $e) {
        echo "  • $e\n";
    }
    echo "\n";
}

exit($testsFailed > 0 ? 1 : 0);
