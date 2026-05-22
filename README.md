# Modern SQLite DB for WordPress

A fully secure, PHP 8.x-optimized drop-in for using SQLite with WordPress.

A security-hardened rebuild of [wp-sqlite-db](https://github.com/DarrylDias/wp-sqlite-db) with proper SQL escaping, XSS prevention, and PHP 8.x compatibility.

## Features

- **Single file drop-in** — copy `src/db.php` to `wp-content/db.php`
- **PHP 8.x optimized** — tested on PHP 8.5, no deprecated function usage
- **Security hardened** — SQL injection, XSS, LIKE wildcard injection fixed
- **Full MySQL compatibility** — `SHOW TABLES`, `SHOW COLUMNS`, `SHOW INDEX`, `DATE_ADD`, `LEAST`/`GREATEST`, `INTERVAL`, and more
- **WAL mode** — better concurrent read performance
- **Zero configuration** — works out of the box

## Installation

#### Quick Start
- Download `src/db.php` from the latest release
- Copy it to your site's `wp-content/db.php`

#### Via Composer
```json
{
    "require": {
        "darryldias/sqlite-db-wp": "^2.0"
    },
    "extra": {
        "dropin-paths": {
            "wp-content/": ["package:darryldias/sqlite-db-wp:src/db.php"]
        }
    }
}
```

## Configuration

The database file is created at `wp-content/database/.ht.sqlite` by default. Customize with constants:

```php
define('DB_DIR', '/absolute/path/to/db/directory/');
define('DB_FILE', 'custom_filename.sqlite');
```

## What's Fixed

### Security
- **LIKE wildcard injection** — `esc_like()` now properly escapes `%`, `_` with `ESCAPE '\'` clause
- **SQL injection in query rewriter** — `SHOW COLUMNS`, `SHOW INDEX`, `SHOW TABLES` now use proper SQLite escaping + whitelist sanitization
- **XSS in error pages** — all SQL/error output HTML-escaped via `htmlspecialchars()`

### PHP 8.x Compatibility
- `end()` TypeError in `deriveInterval()` — fixed uninitialized array
- `least()`/`greatest()` — fixed array-to-string conversion (was returning `min(Array)`)
- `$parts` vs `$_parts` typo in `day_minute` interval case

### Reliability
- Database errors logged safely (no raw HTML)
- Proper SQLite string escaping (`''` instead of `addslashes()`)

## Credit

Originally based on the [SQLite Integration](https://wordpress.org/plugins/sqlite-integration/) plugin by Kojima Toshiyasu.
Rebuilt and hardened by [Darryl Dias](https://github.com/DarrylDias).
