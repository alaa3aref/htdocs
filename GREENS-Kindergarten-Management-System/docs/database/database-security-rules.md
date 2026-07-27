# Database Security Rules

## Data classification and access

- Child identity, guardian contacts, medical data, documents, photos, videos, attendance, pickup data, and messages are confidential.
- Medical profiles, behavior incidents, pickup authorizations, and security records are restricted-sensitive data and require explicit permission checks.
- Access is least-privilege, role-based, scope-based, and enforced by server-side policies and database query boundaries.
- Every query and export must select only fields needed for the authorized purpose.

## Personal-data protection

- Use `utf8mb4` for Arabic names and content.
- Never store passwords, reset tokens, session tokens, or QR token secrets in plaintext.
- Never place personal data in QR payloads, URLs, logs, browser storage, or external notification bodies unless necessary and approved.
- Store files by non-guessable storage key outside the web root; serve them only through authorized access paths.
- Redact sensitive values in logs and audit representations.

## Integrity and audit

- InnoDB foreign keys, transactions, unique constraints, and application validation preserve relationship integrity.
- Attendance events, QR scans, pickup events, audit logs, security events, notification deliveries, backup records, and restore records are append-only.
- Changes to lifecycle status, enrollments, guardian rights, permissions, medical information, documents, consent, attendance, and media approval require actor, time, reason where applicable, and before/after audit evidence.
- Audit records are never editable through normal application operations.

## Authentication and authorization records

- Login identity is separate from guardian and employee business profiles.
- Password recovery tokens are single-use, short-lived, hashed/protected, and removed after expiry or use.
- Sessions record creation, expiry, revocation, and minimal device context; revocation is immediate for password, role, or security changes.
- Role assignments and user scopes are effective dated and audited.

## QR and attendance rules

- QR tokens contain signed opaque metadata only, with nonce, expiry, purpose, and replay protection.
- Scan attempts are logged whether successful or failed.
- QR cannot authorize custody by itself; staff visual verification and active pickup authorization are mandatory.
- Attendance corrections require reason and approval according to lock policy.

## Privacy, retention, and deletion

- Business records use soft delete where defined by the Constitution; immutable historical records are retained.
- Retention actions are documented in `data_retention_actions`, authorized, auditable, and coordinated with file storage and backup retention.
- Parent access ends promptly when the guardian relationship, account status, or permission scope is revoked, while historical school records remain protected.
- Exports and generated reports are requester-scoped, time-limited, private, and logged.

## DATABASE DESIGN REVIEW CHECKLIST

- [ ] Missing entities check: every required constitutional domain and workflow has a canonical entity; no duplicate room/teacher/guardian model exists.
- [ ] Security review: permissions, scopes, sensitive fields, private files, token handling, and audit coverage are defined.
- [ ] Privacy review: child data minimization, consent, media visibility, retention, export controls, and redaction are defined.
- [ ] Relationship review: cardinality, foreign keys, lifecycle state, effective dating, and deletion behavior are documented.
- [ ] Future-expansion readiness: institution boundary, academic-year history, versioned templates/consents, opaque public IDs, and operational records are preserved.
- [ ] Migration readiness: every future migration must implement this design using MySQL 8/InnoDB/utf8mb4, forward-only versioning, transaction safety, indexes, constraints, rollback/compensating-migration documentation, and tests.
