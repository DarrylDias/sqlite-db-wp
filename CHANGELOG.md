# Changelog

## [Unreleased]

### Fixed
- **Security**: `esc_like()` no longer a no-op — now properly escapes LIKE wildcards (% and _) with backslash, and adds `ESCAPE '\'` clause to all LIKE queries for SQLite compatibility. (Issue #1)
- **Security**: `_real_escape()` now uses SQLite-native escaping (doubled single quotes) instead of `addslashes()`, which was incompatible with SQLite's string literal handling. (Issue #1)
- **Bug**: `replace_variables_with_placeholders()` no longer strips backslashes from extracted query values, preventing LIKE escape characters from being removed during PDO parameter binding. (Issue #1)

See https://github.com/aaemnnosttv/wp-sqlite-db/releases
