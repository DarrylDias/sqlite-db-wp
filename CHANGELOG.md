# Changelog

## [v2.1] - 2026-05-23

### Performance
- **Cache `PRAGMA compile_options`** — eliminated repeated PRAGMA queries on every DELETE/UPDATE with LIMIT/ORDER BY. Results are cached in a static property after the first call. (Issue #9)
- **Reuse PDO connections** — replaced 12 `new wpsqlitedb()` instances across `PDOSQLiteDriver`, `CreateQuery`, and `AlterQuery` with shared static instances, avoiding redundant connection setup and UDF registration. (Issue #10)
- **Cache `sqlite_master` queries** — index name list and per-table schema queries are now cached per request, eliminating repeated schema lookups during CREATE TABLE and ALTER TABLE operations. (Issue #11)
- **Optimize `SQL_CALC_FOUND_ROWS`** — replaced full query re-execution with `SELECT COUNT(*) FROM (...) AS count_subq`, reducing overhead for paginated queries. (Issue #12)
- **Batch INSERT transactions** — multi-row INSERTs (legacy SQLite < 3.7.11 path) now execute in a single transaction with proper commit/rollback. (Issue #13)
- **LIKE ESCAPE deduplication** — removed dead `rewrite_like_escape()` method and added `add_like_escape()` calls to INSERT paths for complete coverage. (Issue #14)
- **Native ON CONFLICT for SQLite 3.24+** — `INSERT ... ON DUPLICATE KEY UPDATE` now uses native `ON CONFLICT ... DO UPDATE SET` syntax, replacing the slow SELECT + conditional INSERT/UPDATE emulation. Falls back for older SQLite. (Issue #15)
- **Simplify `ObjectArray`** — removed recursive constructor overhead; all usage sites pass flat arrays. (Issue #16)

## [v2.1.2] - 2026-05-23

### Bug fixes
- **Fix PCRE backtrack limit on large queries (>200KB)** — skip regex variable extraction for queries >100KB and use direct `PDO::exec()` instead, avoiding "The query is too big to parse properly" error on large serialized data like RSS feed transients. (Issue #29, PR #30)

## [v2.1.1] - 2026-05-23

### Bug fixes
- **Fix ON CONFLICT target combining separate unique constraints** — grouped unique columns by `Key_name` (constraint name) and picks exactly one constraint per `ON CONFLICT` clause, preferring a non-PRIMARY unique key whose columns are all present in the INSERT. (Issue #25, PR #26)
- **Fix `:param_n` syntax error with SQL-escaped quotes** — `extract_variables()` regex now correctly handles `''` (doubled single quotes) inside string literals, and `replace_variables_with_placeholders()` un-doubles them back to single quotes for correct parameter binding. (Issue #27, PR #28)

## [Unreleased]

See https://github.com/DarrylDias/sqlite-db-wp/releases
