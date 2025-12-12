# Copilot Instructions for Sistem Laporan Pengadilan Agama

## Architecture Overview

This is a **CodeIgniter 2.x MVC application** for court case reporting at Indonesian Religious Courts (Pengadilan Agama). The system manages divorce case data across two regions: **Hulu Sungai Utara (HSU)** and **Balangan**, with integration to the national SIPP (Sistem Informasi Penelusuran Perkara) system.

### Core Database Schema
```sql
perkara              -- Main case table (primary entity)
perkara_putusan      -- Court decisions and BHT (legally binding) status
perkara_akta_cerai   -- Divorce certificate issuance
perkara_pihak1/2     -- Case parties (husband/wife)
perkara_ikrar_talak  -- Divorce oath ceremony (talak)
```

### Key Business Logic
- **BHT Validation**: `Perkara_Telah_BHT ≤ Perkara_Putus` (BHT count cannot exceed decided cases)
- **Sisa Perkara Formula**: `sisa_bulan_lalu + perkara_masuk - perkara_putus = sisa_perkara`
- **Regional Mapping**: Address-based district categorization using `CASE WHEN perkara_pihak1.alamat LIKE '%Kecamatan%'`

## Development Patterns

### SQL Query Architecture
Models use **UNION ALL subquery patterns** for aggregating data across different date fields:
```sql
SELECT locations.KECAMATAN,
    COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_pendaftaran' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_MASUK,
    COALESCE(SUM(CASE WHEN subquery.date_type = 'tanggal_putusan' THEN subquery.COUNT ELSE 0 END), 0) AS PERKARA_PUTUS
FROM (kecamatan_list) AS locations
LEFT JOIN (subquery1 UNION ALL subquery2 UNION ALL subquery3) AS subquery
```

### Regional Controller Pattern
Each region has separate models: `M_data_perceraian_hsu.php` vs `M_data_perceraian_balangan.php` with identical structure but different district lists. Use wilayah parameter to switch between regions.

### Dynamic Column Display
Views implement conditional column visibility based on report types:
```php
<?php if ($selected_report == 'bulanan'): ?>
    <!-- Show sisa_bulan_lalu column -->
<?php elseif ($selected_report == 'tahunan'): ?>
    <!-- Show sisa_tahun_lalu column -->
<?php endif; ?>
```

## Critical Workflows

### Report Generation
1. **Filter Selection**: Region (HSU/Balangan) + Date + Case Type + Report Type
2. **Data Aggregation**: Model calls appropriate `_build_subquery()` methods
3. **Percentage Validation**: Apply `min(100%, calculated_percentage)` for BHT rates
4. **Export**: PHPExcel integration with dynamic headers based on report type

### Debug Database Issues
Use `error_log()` for BHT validation warnings. Check for duplicate JOIN results causing inflated counts:
```php
if ($raw_percentage > 100) {
    error_log("WARNING: BHT > Putus detected! BHT: $card_total_bht, Putus: $card_total_putus");
}
```

## Integration Points

### SIPP Synchronization
Models validate data consistency with national SIPP system. Key fields: `nomor_perkara`, `tanggal_pendaftaran`, `status_putusan_id`.

### Database Configuration
```php
$db['default'] = array(
    'database' => 'sipp_2',  // Local SIPP database replica
    'db_debug' => FALSE,     // Disable debug in production
);
```

## File Organization

- **Controllers**: Regional controllers (`Data_Perceraian_hsu.php`) + functional controllers (`Data_Perkara_Gugatan.php`)
- **Models**: Regional models with shared helper methods (`_build_subquery`, `_get_kecamatan_list`)
- **Views**: Conditional templating with Bootstrap 4 + DataTables integration
- **SQL**: Database schema documented in `application/views/kode.sql`

## Common Pitfalls

- **JOIN Multiplication**: Use `DISTINCT` or proper JOIN conditions to prevent duplicate counting
- **Date Format**: MySQL DATE fields vs CodeIgniter date helpers - use `YEAR()` and `MONTH()` functions
- **Regional Logic**: Always validate district mapping logic when modifying address-based queries
- **BHT Logic**: Never allow BHT percentages > 100% - indicates data integrity issues

When modifying queries, test with both HSU and Balangan regions to ensure consistent behavior across different district lists.
