#!/usr/bin/env python3
"""Ponytail Ultra: Bulk view refactor for CI3 views"""

import os
import re
import sys

views_dir = '/home/ubuntu/.hermes/home/laporan_padli/application/views'

def convert_file(filepath):
    """Process a single view file - mostly <?php echo -> <?= conversion"""
    try:
        with open(filepath, 'r', encoding='utf-8', errors='replace') as f:
            content = f.read()
    except Exception as e:
        print(f"  SKIP (read error): {e}", file=sys.stderr)
        return False
    
    original = content
    
    # Fix corrupt patterns
    content = content.replace('?>?>', '?>')
    content = content.replace('?><?', '<?')
    
    # Step 1: <?php echo base_url() ?> (with or without semicolon)
    content = re.sub(
        r'<\?php\s+echo\s+base_url\s*\(\s*\)\s*;?\s*\?>',
        '<?= base_url() ?>',
        content
    )
    content = re.sub(
        r'<\?php\s+echo\s+base_url\s*\(([^)]+)\)\s*;?\s*\?>',
        r'<?= base_url(\1) ?>',
        content
    )
    
    # Step 2: <?php echo site_url(...) ?>
    content = re.sub(
        r'<\?php\s+echo\s+site_url\s*\(([^)]+)\)\s*;?\s*\?>',
        r'<?= site_url(\1) ?>',
        content
    )
    
    # Step 3: Any remaining <?php echo -> <?= 
    # Capture anything between <?php echo and ?>
    content = re.sub(
        r'<\?php\s+echo\s+',
        '<?= ',
        content
    )
    
    # Step 4: Clean up <?= echo  -> <?= 
    content = content.replace('<?= echo ', '<?= ')
    
    # Step 5: Clean up empty/extra semicolons before ?>
    content = re.sub(r'\s*;\s*\?>', ' ?>', content)
    
    # Step 6: Fix missing space <?=expr?> -> <?= expr ?>
    content = re.sub(r'<\?=\s*(\S)', r'<?= \1', content)
    
    if content != original:
        try:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            return True
        except Exception as e:
            print(f"  WRITE ERROR: {e}", file=sys.stderr)
            return False
    return False

def main():
    processed = 0
    changed = 0
    
    for fname in sorted(os.listdir(views_dir)):
        fpath = os.path.join(views_dir, fname)
        if not os.path.isfile(fpath) or not fname.endswith('.php'):
            continue
        if fname == 'index.html' or fname == 'kode.sql':
            continue
        try:
            if convert_file(fpath):
                print(f"Fixed: {fname}")
                changed += 1
            processed += 1
        except Exception as e:
            print(f"Error on {fname}: {e}", file=sys.stderr)
    
    # Also process template files
    template_dir = os.path.join(views_dir, 'template')
    if os.path.isdir(template_dir):
        for fname in sorted(os.listdir(template_dir)):
            fpath = os.path.join(template_dir, fname)
            if not os.path.isfile(fpath) or not fname.endswith('.php'):
                continue
            try:
                if convert_file(fpath):
                    print(f"Fixed: template/{fname}")
                    changed += 1
                processed += 1
            except Exception as e:
                print(f"Error on template/{fname}: {e}", file=sys.stderr)
    
    # Check for remaining <?php echo
    remaining = 0
    for root, dirs, files in os.walk(views_dir):
        if 'errors' in root:
            continue
        for f in files:
            if not f.endswith('.php'):
                continue
            fpath = os.path.join(root, f)
            with open(fpath, 'r') as fh:
                for line in fh:
                    if '<?php echo ' in line or '<?php\techo ' in line:
                        remaining += 1
    
    print(f"\nDone! Processed {processed} total files, changed {changed} files.")
    print(f"Remaining <?php echo patterns: {remaining}")

if __name__ == '__main__':
    main()
