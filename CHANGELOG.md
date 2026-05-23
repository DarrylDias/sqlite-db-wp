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

## [Unreleased]

See https://github.com/DarrylDias/sqlite-db-wp/releases
