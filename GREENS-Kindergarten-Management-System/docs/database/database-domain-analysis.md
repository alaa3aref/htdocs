# Database Domain Analysis

## Scope and governing rules

This is the Phase 2.1 logical database design for GREENS. It is documentation only: it creates no SQL, migrations, tables, models, or application code. The Engineering Constitution remains authoritative.

The production datastore will use MySQL 8, InnoDB, `utf8mb4`, foreign-key integrity, normalized operational data, UTC timestamps, and Egypt-local presentation time (`Africa/Cairo`). Every mutable business entity has `created_at`, `updated_at`, and appropriate actor tracking. Public-facing records use opaque public identifiers; internal numeric identifiers are not exposed as business identifiers.

## Identity and Access Management

`users` are login identities for administrators, employees, teachers, and eligible guardians. Students do not log in. A guardian receives portal access only when linked to a child through a verified guardian relationship and granted a user account.

Roles are assigned through `user_roles`; their capabilities are granted through `role_permissions`. `permissions` are granular and `user_scopes` restrict otherwise valid permissions to an institution, academic year, class, or operational assignment. Authorization is always evaluated server-side. `user_sessions` records active and revoked authenticated sessions. `password_reset_tokens` are short-lived, single-use, and retention-deleted after use or expiry.

`audit_logs` and `security_events` are append-only records. Sensitive actions—including login, access denial, role changes, exports, attendance changes, QR scans, document access, consent changes, and media decisions—must record actor, time, action, target, result, correlation identifier, and redacted change evidence.

## Kindergarten Structure

`institutions` holds the kindergarten profile and owns all business data. The initial deployment has one institution, but this boundary is retained for future expansion. `academic_years` belong to the institution and define the annual operating period. `terms` optionally divide an academic year.

`classes` are educational groups for an academic year. `classrooms` are physical rooms/facilities. The constitutional name `classrooms` is the canonical representation of the requested “rooms”; no duplicate `rooms` entity is introduced. `class_assignments` connect teacher/employee staff to a class and academic year. `schedules` and `schedule_slots` define the class timetable. `holidays` belong to the institution and may affect all or selected classes.

## Student Domain

`students` is the durable child identity. `student_profiles` contains mutable profile, contact, and operational information. `student_enrollments` creates the historical placement of a student in a class for an academic year. `student_status_history` is an immutable record of lifecycle changes.

Lifecycle states are: Draft Registration, Submitted, Under Review, Accepted, Enrolled, Active, Transferred, Withdrawn, Graduated, and Archived. Profile corrections may be changed with audit evidence. Legal identity, enrollment, status, medical, consent, guardian, pickup, attendance, and graduation changes require history. A student cannot have more than one active enrollment in an academic year.

## Guardian Domain

`guardians` stores the adult person profile. `guardian_relationships` is the sole link between a guardian and a student. It records Father, Mother, or Other; Other requires Grandfather, Grandmother, Uncle, Aunt, Guardian, Brother, Sister, Relative, or a mandatory custom description. It also records legal-guardian status, pickup authorization, emergency priority, verification, and communication eligibility. `guardian_contact_methods` stores normalized channels and preferences. `pickup_authorizations` controls time-bound approved collection by a guardian or approved collector; `emergency_contacts` records ordered contacts.

## Employee and Teacher Domain

`employees` stores staff employment information and links eligible staff to `users`. Teachers are employees whose user-role assignment includes Teacher; a separate teacher identity is not duplicated. `class_assignments` gives a teacher a class role, ownership level, effective period, and academic-year context. Class ownership is operational responsibility, not ownership of student records. Teacher permissions are the intersection of Teacher role permissions, active assignment, and scope.

## QR and Attendance Domain

`qr_tokens` stores token metadata only: purpose, expiry, nonce, signature reference, issuance, invalidation, and use state. It never stores personal data in the QR payload. `attendance_days` is the student/class/date attendance context. `attendance_events` is immutable evidence of check-in, check-out, absence, late arrival, correction, and override events. `attendance_corrections` records a reasoned request and approval workflow. `qr_scan_attempts` records every scan outcome. `pickup_events` records supervised custody handover.

QR is a secure assistance mechanism, never proof of custody. Each successful check-in or pickup requires staff visual verification and an authorized operator. A failed or repeated scan must not alter attendance.

## Parent Portal Domain

Parent access derives from an active, verified `guardian_relationships` record and an authorized `users` account. A parent may access only linked children and only approved scope: profile fields permitted by policy, attendance history, released reports, notifications, conversation messages, and approved media. The database must enforce ownership through relationship joins and policy scope; client filtering is never sufficient.

## Daily Care Domain

`daily_activities` and `activity_participants` capture classroom activity and optional child participation. `student_observations` records teacher observations. `development_categories` and `development_records` support structured child development reporting. `behavior_incidents` and `incident_actions` record sensitive behavior events and their resolution. `medical_profiles` stores controlled-access medical and allergy information; safety-relevant alerts are exposed only to authorized classroom staff.

## Media and Files Domain

`files` stores private object metadata and security classification, while physical content remains outside the web root. `file_links` attaches a file to an approved domain record. `student_documents` is the student-facing document workflow. `media_items`, `media_albums`, `media_visibility`, and `media_reviews` support photos/videos with consent, review, audience scope, and removal history. `student_consents` stores consent type, version, decision, signer, and withdrawal evidence.

## Notifications and Communication

`notification_templates` are versioned Arabic-first templates. `notifications` are generated business notices; `notification_deliveries` records per-channel send/delivery attempts; `notification_preferences` controls non-essential delivery preferences; `acknowledgements` records required reads. `announcements` and `announcement_targets` support institution/class/guardian targeting. `conversations`, `messages`, and `message_attachments` support policy-controlled parent-school communication.

## Reporting and Operations

`generated_reports` records a report request, scope, filters, requester, expiry, and linked generated file. `exports` records controlled data extracts. `jobs`, `failed_jobs`, and `scheduled_task_runs` are operational execution history. `backups`, `restore_operations`, `system_health_checks`, and `data_retention_actions` document operational resilience and retention evidence.

## Historical and deletion policy

Business history is preserved through status history and append-only event entities, not overwritten. Students, guardians, classes, enrollments, files, media, messages where legally permissible, and configurable operational records use soft deletion with deletion actor, timestamp, and reason. Audit logs, security events, attendance events, QR scan attempts, backups, and restore operations are not ordinary soft-delete candidates and cannot be removed through application workflows.
