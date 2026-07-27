# GREENS Database Migrations

Migrations are custom PHP 8.3 classes that return `Database\MigrationInterface`. `MigrationRunner` discovers them in numeric order, enables `utf8mb4` and foreign keys, creates `migration_history`, and runs each unapplied migration in its own transaction using prepared statements.

Order is fixed: institution; identity/authorization; academic structure; students/employees; guardians; enrollment/restricted records; attendance/QR; teaching; communication; files/media; reports/operations; audit/monitoring.

Migrations are forward-only in production. `rollback()` is intended only for the latest local/development batch or a formally approved recovery procedure. Never alter an applied migration. Use a new compensating migration for production corrections, after a verified backup and review of foreign-key, lock, retention, and privacy impact.
