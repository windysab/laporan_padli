# Tech Stack

## Framework

- **CodeIgniter 3.x** (PHP MVC framework)
- PHP >= 5.6 (configured platform), runs on PHP 8.2+ with deprecation suppression
- Server: XAMPP (Apache + MySQL) on Windows

## Database

- **MySQL** via `mysqli` driver
- Database: `sipp_4`
- Query Builder enabled
- Raw SQL queries used extensively in models for complex reporting

## Frontend

- **AdminLTE 3** (Bootstrap 4 admin template)
- **jQuery** + **DataTables** (with Bootstrap 4 integration, responsive, buttons plugins)
- **Font Awesome 5** (icons)
- **Chart.js** (data visualization)
- Google Fonts: Source Sans Pro

## PHP Libraries (Composer)

- `phpoffice/phpspreadsheet` 1.6.0 — Excel export functionality
- `illuminate/view` 5.1.* — Laravel Blade templating (available but not primary)

## Autoloaded Resources

- Libraries: `database`
- Helpers: `url`, `form`

## Common Commands

```bash
# Start local server (XAMPP)
# Apache and MySQL must be running via XAMPP Control Panel

# Access the application
# http://localhost:8080/laporan_padli/

# Install PHP dependencies
composer install
```

## Key Configuration

- Base URL: `http://localhost:8080/laporan_padli/`
- Index page: `index.php` (no URL rewriting)
- CSRF: disabled
- Sessions: file-based, 2-hour expiration
- Composer autoload: enabled via `FCPATH . 'vendor/autoload.php'`
- Error reporting: `E_ALL & ~E_DEPRECATED` (for PHP 8.2+ compatibility)
