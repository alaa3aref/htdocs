# Database Migration Plan

## Principles

Migrations are forward-only, versioned, reviewed, tested, and applied once per environment. This plan is documentation only and does not create migration files. Each future migration must include purpose, dependencies, reversible/compensating action, data-impact assessment, lock-risk assessment, test evidence, deployment order, and rollback instructions. Production migration begins only after verified backup and approved maintenance/online-change plan.

## Safe creation sequence

1. Database baseline: character set/collation policy, migration ledger, and institution root.
2. Identity/reference catalogs: roles, permissions, users, settings, audit/security foundations.
3. Access mappings: user roles, role permissions, scopes, sessions, password recovery.
4. Academic structure: academic years, terms, classrooms, classes, schedules, slots, holidays, staff employment and assignments.
5. Student/guardian core: students, profiles, guardians, contact methods, relationships, emergency contacts, pickup authorization, status history, enrollment.
6. Restricted child records: medical profiles, consent, files, document workflows.
7. Attendance/QR: status catalog, rules, attendance days/events, correction workflow, QR tokens/scans, pickup events.
8. Teaching/care: activities/participants, observations, development categories/records, incidents/actions, teacher notes.
9. Communication and media: announcements/targets, conversations/messages/attachments, templates/notifications/deliveries/preferences/acknowledgements, albums/media/visibility/reviews.
10. Reporting and operations: reports, exports, jobs/failures/scheduler, backups/restores, health checks, retention actions.
11. Non-FK secondary indexes, performance verification, data-integrity validation, and monitoring configuration.

## Dependencies and creation safeguards

- Create referenced parent tables before children; add foreign keys only after compatible parent keys and indexes exist.
- Create immutable audit/security foundations before any workflow that must be auditable.
- Create `files` before documents, attachments, reports, exports, and media.
- Create users/employees before authorship, approvals, assignments, or operational records.
- Create students, guardians, and enrollment before attendance, pickup, care, portal, and consent records.
- Add high-write secondary indexes after bulk baseline data only when that lowers deployment risk; validate before enabling workloads.
- Never combine destructive schema changes, data backfill, and application behavior changes in one unreviewed deployment.

## Seed data requirements

Seed only approved non-personal reference data:

- Institution bootstrap profile supplied securely at deployment.
- System roles: Administrator, Employee, Teacher, Parent.
- Approved permission catalog and initial administrator assignment through a secure deployment procedure.
- Attendance statuses, development categories, relationship types and Other subtypes, status codes, notification categories, and configuration defaults.
- No child, guardian, employee, production credential, medical, document, media, attendance, or QR data may be seeded.

## Rollback and recovery

- Prefer additive, backward-compatible migrations.
- For a failed additive migration, deploy a documented compensating migration rather than editing migration history.
- Before destructive/transformative work, take and verify an encrypted database and file backup.
- New nullable columns may be introduced, backfilled in controlled batches, validated, then constrained in later releases.
- Renames use expand–migrate–contract: add new representation, dual-read/write only through approved transition logic, backfill, validate, remove old representation only after retention and rollback window.
- Never roll back by deleting historical, audit, attendance, QR, consent, or security data. Restore uses the Constitution’s restore workflow where needed.

## Validation gates before production

- Schema matches approved entity catalog, ERD, naming standards, and this schema design.
- Every FK, uniqueness rule, check, soft-delete rule, audit column, and required index is tested.
- Migration works from an empty database and from a representative prior version.
- Test fixtures contain synthetic/anonymized data only.
- Query-plan checks cover login, class roster, attendance, QR validation, parent portal, files/media, notifications, reports, audit, and backup views.
- Backup, restore, application health, and access-control checks pass after migration.

## DATABASE SCHEMA APPROVAL CHECKLIST

- [x] Security review: migration order establishes identity, audit, private-file, and restricted-data controls before dependent workflows.
- [x] Privacy review: seed restrictions, anonymized testing, retention, and safe rollback protect real child data.
- [x] Performance review: FK and workload indexes are planned with query-plan validation.
- [x] Data integrity review: parent-first order, constraints, effective dating, immutable history, and controlled transitions are defined.
- [x] Migration readiness: dependencies, creation sequence, additive strategy, compensating rollback, and validation gates are documented.
- [x] Production readiness: verified backup/restore, deployment approval, monitoring, and no unreviewed destructive change are required.
