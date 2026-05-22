# Changelog

## 2026-05-22

### Fixed

- **Issue #2** — SQL injection in query rewriter: Fixed `handle_show_columns_query()`, `handle_show_query()`, and `handle_show_index()` to use SQLite-native escaping (`''` for single quotes) and whitelist-based sanitization instead of incomplete `str_replace()`-based sanitization. The LIKE path in `handle_show_columns_query()` now uses `pragma_table_info()` instead of `SELECT sql FROM sqlite_master` to return correct column structure compatible with `process_results()`. Tests added to `SqlInjectionTest`.

- **Issue #1** — `esc_like()` no-op / `_real_escape()` uses `addslashes`: Fixed `esc_like()` to use `addcslashes()` for proper LIKE escaping. Fixed `_real_escape()` to use SQLite-native `str_replace("'", "''", ...)` instead of `addslashes()`. Added `add_like_escape()` method to append `ESCAPE '\'` to LIKE clauses. Removed faulty `stripslashes()` from `replace_variables_with_placeholders()`. Tests added to `EscLikeTest`.

See https://github.com/aaemnnosttv/wp-sqlite-db/releases
