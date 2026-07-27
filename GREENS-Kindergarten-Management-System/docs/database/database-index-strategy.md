# Database Index Strategy

## Mandatory index rules

Every primary key, foreign key, opaque `public_id`, unique business identity, active-status lookup, and high-volume date filter requires an index. Composite indexes follow the most selective equality predicates first, then range/sort columns. Indexes are validated using production-like query plans before release. JSON is not used for primary operational filters; frequently queried JSON values require normalized columns or generated indexed columns approved in schema review.

## Unique indexes

- `users`: normalized email and normalized phone when present; `public_id`.
- `roles.code`, `permissions.code`, `attendance_statuses.code`, `development_categories.code`.
- `institutions.public_id`, all externally exposed entity `public_id` values.
- `academic_years(institution_id, name)`, `classes(academic_year_id, name)`, `classrooms(institution_id, name)`.
- `guardian_contact_methods(guardian_id, type, normalized_value)`.
- `user_roles(user_id, role_id, starts_at)`, `role_permissions(role_id, permission_id)`.
- `student_enrollments`: unique active student/year business rule using a MySQL-compatible generated active-key strategy.
- `attendance_days(student_id, attendance_date)`.
- `qr_tokens.token_hash`, `qr_tokens.nonce`; `user_sessions.session_hash`; `password_reset_tokens.token_hash`.
- `files.storage_key`; `media_items.file_id`; `message_attachments(message_id, file_id)`.

## Foreign-key and relationship indexes

Create indexes for every FK, including `institution_id`, student/guardian/class/employee/user references, file references, report/export requester, notification recipient, and all audit actor references. For association tables, use a composite unique key plus reverse-direction lookup index where both traversal directions are common, especially guardian relationships, class assignments, role assignments, and activity participants.

## Search optimization

- Student directory: index normalized legal names separately from privacy-safe public ID; support prefix search only. Do not enable unrestricted full-text search over medical, behavior, messages, documents, or audit evidence.
- Guardian lookup: index normalized verified phone/email and names; national identity uses hash equality only, never display/search text.
- Class/academic navigation: `academic_year_id, status, name`.
- Audit/security: `institution_id, occurred_at DESC`; `actor_user_id, occurred_at DESC`; `target_type, target_id, occurred_at DESC`; `event_code, occurred_at DESC`.

## Attendance and QR optimization

- Attendance dashboard: `attendance_days(student_enrollment_id, attendance_date)`, `attendance_days(attendance_date, status_id)`, and `attendance_events(attendance_day_id, occurred_at)`.
- Class/day roster: enrollment index beginning `class_id, starts_on, ends_on, status`; attendance lookup by student/date.
- Corrections: `attendance_corrections(attendance_event_id, outcome, requested_at)`.
- QR validation: unique hash/nonce indexes, plus `qr_tokens(expires_at, invalidated_at, used_at)` for cleanup and `qr_scan_attempts(qr_token_id, scanned_at DESC)` for audit.
- Pickup: `pickup_authorizations(student_id, status, starts_at, ends_at)` and `pickup_events(student_id, occurred_at DESC)`.

## Parent portal optimization

- Authorization path: `guardian_relationships(guardian_id, verified_at, starts_on, ends_on)` and reverse `guardian_relationships(student_id, guardian_id, starts_on)`.
- Parent attendance: `attendance_days(student_id, attendance_date DESC)` with events by day.
- Portal media: `media_visibility(scope_type, scope_reference_id, media_item_id)` and `media_reviews(media_item_id, reviewed_at DESC)`.
- Messages: `conversations(guardian_id, student_id, status)` and `messages(conversation_id, sent_at DESC)`.
- Notifications: `notifications(recipient_user_id, created_at DESC)`, `notifications(recipient_guardian_id, created_at DESC)`, and deliveries by notification/status.

## Reporting and operational optimization

- Enrollment/status reporting: `student_enrollments(academic_year_id, class_id, status)` and `student_status_history(student_id, effective_at DESC)`.
- Care reports: student/date indexes on observations, development records, incidents, and activities.
- Reports/exports: `generated_reports(requested_by, created_at DESC, status)` and `exports(requested_by, created_at DESC, status)`; never index large report payload JSON indiscriminately.
- Files: `file_links(target_type, target_id, purpose)` and `files(classification, retention_until)`.
- Operations: jobs by `(queue, available_at, reserved_at)`, failed jobs by failure time, backups by status/retention, health checks by code/time.

## Index lifecycle controls

- No index is removed without measured query-plan evidence and production impact review.
- Use covering indexes only when query frequency justifies write cost.
- Monitor slow queries, index cardinality, write amplification, and report-query impact.
- Large historical tables—audit logs, scan attempts, attendance events, deliveries—are partitioning candidates only after measured growth; partitioning requires a constitutional amendment/review because it changes operational architecture.
