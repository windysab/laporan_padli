#!/bin/bash
# Pre-deploy checklist — run BEFORE publikasi ke server
# Usage: bash test/pre_deploy.sh

set -e
cd "$(dirname "$0")/.."
PASS=0
FAIL=0
ERR_MSGS=()

pass() { PASS=$((PASS+1)); echo -e "  [PASS] $1"; }
fail() { FAIL=$((FAIL+1)); ERR_MSGS+=("$1"); echo -e "  [FAIL] $1"; }
heading() { echo -e "\n─── $1 ───"; }

heading "1. PHP Syntax Check"
for f in $(find application/ -name "*.php" -type f | sort); do
    result=$(php -l "$f" 2>&1)
    if echo "$result" | grep -q "Parse error\|Fatal error"; then
        fail "$f — syntax error"
    fi
done
[ $FAIL -eq 0 ] && pass "Semua file PHP valid"

heading "2. Sidebar Link Check"
SIDEBAR="application/views/template/new_sidebar.php"
LINKS=$(grep -oP "site_url\(['\"][^'\"]+" "$SIDEBAR" | sed "s/site_url(//;s/['\"]//g" | sort -u)
SIDEBAR_OK=0
SIDEBAR_BROKEN=0
for link in $LINKS; do
    controller=$(echo "$link" | cut -d'/' -f1)
    if [ -f "application/controllers/${controller}.php" ]; then
        SIDEBAR_OK=$((SIDEBAR_OK+1))
    else
        fail "Sidebar: '$link' → controller '$controller' MISSING"
        SIDEBAR_BROKEN=$((SIDEBAR_BROKEN+1))
    fi
done
[ $SIDEBAR_BROKEN -eq 0 ] && pass "Sidebar: $SIDEBAR_OK link valid"

heading "3. Controller → View Referensi"
VIEW_ERR=0
for ctl in application/controllers/*.php; do
    name=$(basename "$ctl" .php)
    grep -oP "this->load->view\(['\"]([^'\"]+)" "$ctl" | sed "s/this->load->view(//;s/['\"]//g" | while read view; do
        if [ ! -f "application/views/${view}.php" ]; then
            fail "Controller $name → view $view NOT FOUND"
            VIEW_ERR=$((VIEW_ERR+1))
        fi
    done
done
[ $VIEW_ERR -eq 0 ] && pass "Semua view yang dipanggil ada"

heading "4. Controller Exist Check (sidebar vs actual)"
for link in $LINKS; do
    controller=$(echo "$link" | cut -d'/' -f1)
    [ ! -f "application/controllers/${controller}.php" ] && fail "Controller $controller NOT FOUND"
done
pass "Semua controller yang ada di list valid"

heading "5. Directory Structure"
DIRS=("application/controllers" "application/models" "application/views" "application/views/template" "application/core")
for d in "${DIRS[@]}"; do
    [ -d "$d" ] && pass "Directory $d OK" || fail "Directory $d MISSING"
done

heading "6. Security"
if grep -qP "\$config\['encryption_key'\]\s*=\s*'[^']{10,}" application/config/config.php 2>/dev/null; then
    pass "encryption_key terisi"
else
    fail "encryption_key kosong / terlalu pendek"
fi
if grep -qP "\$config\['global_xss_filtering'\]\s*=\s*TRUE" application/config/config.php 2>/dev/null; then
    pass "global_xss_filtering ON"
else
    fail "global_xss_filtering OFF"
fi

heading "7. File Count Summary"
echo "  Controllers: $(ls application/controllers/*.php 2>/dev/null | wc -l)"
echo "  Models:      $(ls application/models/*.php 2>/dev/null | wc -l)"
echo "  Views:       $(ls application/views/v_*.php 2>/dev/null | wc -l)"
echo "  Template:    $(ls application/views/template/*.php 2>/dev/null | wc -l)"
echo "  Total lines: $(cat application/controllers/*.php application/models/*.php application/views/*.php application/views/template/*.php 2>/dev/null | wc -l)"

echo -e "\n─── RESULT ───"
echo "  PASS: $PASS | FAIL: $FAIL"

if [ $FAIL -gt 0 ]; then
    echo -e "\n⚠️  FAILURES:"
    for e in "${ERR_MSGS[@]}"; do echo "  • $e"; done
    exit 1
fi
echo "  ✅ Semua tes lolos — siap publikasi!"
