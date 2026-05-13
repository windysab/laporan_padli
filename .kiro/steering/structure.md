# Project Structure

## Directory Layout

```
laporan_padli/
├── application/              # CodeIgniter application directory
│   ├── config/               # Framework and app configuration
│   │   ├── config.php        # Base URL, sessions, security settings
│   │   ├── database.php      # MySQL connection settings
│   │   ├── routes.php        # URL routing (default: Dashboard)
│   │   └── autoload.php      # Auto-loaded libraries/helpers
│   ├── controllers/          # Controller classes (entry points)
│   ├── models/               # Database query models (M_*.php)
│   ├── views/                # PHP view templates
│   │   ├── template/         # Layout partials (header, sidebar, footer)
│   │   ├── pages/            # Page-specific views
│   │   └── v_*.php           # Main content views
│   ├── libraries/            # Custom libraries
│   ├── helpers/              # Custom helper functions
│   └── hooks/                # CI hooks
├── assets/                   # Frontend static assets (AdminLTE)
│   ├── dist/                 # AdminLTE compiled CSS/JS
│   ├── plugins/              # Third-party plugins (DataTables, Chart.js, etc.)
│   └── pages/                # Page-specific assets
├── system/                   # CodeIgniter core (do not modify)
├── template/                 # Excel report templates (.xlsx)
├── vendor/                   # Composer dependencies
├── index.php                 # Application entry point
└── composer.json             # PHP dependency definitions
```

## Naming Conventions

| Layer       | Pattern                          | Example                          |
|-------------|----------------------------------|----------------------------------|
| Controller  | `PascalCase` or `Snake_Case`     | `Dashboard.php`, `Data_Perkara_Gugatan.php` |
| Model       | `M_` prefix + snake_case         | `M_data_perkara_gugatan.php`     |
| View        | `v_` prefix + snake_case         | `v_data_perkara_gugatan.php`     |
| Layout      | Descriptive name in `template/`  | `new_header.php`, `new_sidebar.php` |

## Architecture Pattern

Standard CodeIgniter 3 MVC:

1. **Controller** loads model(s), calls data methods, passes data to views
2. **Model** contains database queries (mostly raw SQL for complex reports)
3. **View** renders HTML with embedded PHP, receives data via `$data` array

## View Layout Pattern

Every page follows this structure in the controller:
```php
$this->load->view('template/new_header');
$this->load->view('template/new_sidebar');
$this->load->view('v_page_name', $data);
$this->load->view('template/new_footer');
```

## Code Style

- Indentation: tabs (per `.editorconfig`)
- Charset: UTF-8
- Line endings: LF
- PHP: `defined('BASEPATH') or exit(...)` guard at top of every file
- SQL: raw queries with `$this->db->query()` and parameter binding for complex reports
