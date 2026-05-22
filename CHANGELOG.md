# Changelog

## [Unreleased]

### Fixed
- **Security**: `esc_like()` no longer a no-op — now properly escapes LIKE wildcards (% and _) with backslash, and adds `ESCAPE '\'` clause to all LIKE queries for SQLite compatibility. (Issue #1)
- **Security**: `_real_escape()` now uses SQLite-native escaping (doubled single quotes) instead of `addslashes()`, which was incompatible with SQLite's string literal handling. (Issue #1)
- **Bug**: `replace_variables_with_placeholders()` no longer strips backslashes from extracted query values, preventing LIKE escape characters from being removed during PDO parameter binding. (Issue #1)
- **Security**: SQL injection in `handle_show_columns_query()`, `handle_show_query()`, and `handle_show_index()` — fixed to use SQLite-native string escaping and whitelist sanitization. (Issue #2)
- **Security**: XSS in database error pages — all query and error output now HTML-escaped via `htmlspecialchars()`. (Issue #3)
- **PHP 8.0**: Fixed `end()` TypeError in `deriveInterval()` (uninitialized array), array-to-string conversion in `least()`/`greatest()`, and `$parts`/`$_parts` typo. (Issue #4)

See https://github.com/DarrylDias/sqlite-db-wp/releases
