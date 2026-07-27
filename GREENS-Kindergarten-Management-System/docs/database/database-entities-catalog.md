# Database Entities Catalog

This catalog defines conceptual entities only. Field names are descriptive, not SQL definitions. Each entity belongs to one `institution` unless stated otherwise.

## Identity, access, and governance

| Entity | Purpose and important conceptual fields | Relationships, ownership, security, audit |
|---|---|---|
| users | Login identity; public ID, display name, email/phone, password hash, status, verification state. | Institution-owned; linked to roles, sessions, and optionally employee/guardian. Sensitive authentication changes are audited; soft delete/revocation, never credential history exposure. |
| roles | Approved role definitions. | Institution/system-owned; linked through user_roles and role_permissions. Role changes audited; not casually deleted. |
| permissions | Atomic server-side capabilities. | System-owned; linked to roles. Constitution-controlled and audited on change. |
| user_roles | Effective dated user-to-role assignment. | Owned by user/institution; unique active assignment; grant/revoke fully audited. |
| role_permissions | Role-to-permission mapping. | System-owned; immutable history through audit. |
| user_scopes | Limits a user's role to a class, academic year, or operational scope. | Owned by user/role assignment; authorization-critical and audited. |
| password_reset_tokens | Short-lived single-use recovery secret metadata. | Owned by user; token value protected; hard-delete on expiry/use. |
| user_sessions | Authenticated session, device context, expiry, revocation. | Owned by user; security event on creation/revocation; retention deletion. |
| audit_logs | Append-only accountability record. | Institution-owned; references actor and target; immutable, redacted, access restricted. |
| security_events | Append-only security incident/event record. | Institution-owned; immutable and restricted. |
| system_settings / setting_revisions | Controlled configuration and immutable revision evidence. | Institution-owned; administrator-only, audited, retention protected. |

## Institution and academic structure

| Entity | Purpose and important conceptual fields | Relationships, ownership, security, audit |
|---|---|---|
| institutions | Kindergarten profile; legal/display name, contact and operational settings. | Root owner of all data; administrator-only updates; revision audited. |
| academic_years | Named annual period, start/end dates, lifecycle status. | Belongs to institution; owns terms/classes/enrollments; status changes audited, soft delete prohibited once referenced. |
| terms | Optional academic-year subdivision. | Belongs to academic year; date integrity required; changes audited. |
| classes | Educational group; name, capacity, academic-year status. | Belongs to institution/academic year; linked to enrollments, assignments, schedules; soft delete only if no active operation. |
| classrooms | Physical room/facility; name, capacity, availability. | Belongs to institution; linked to classes/schedules. This is the canonical room entity; audited changes. |
| class_assignments | Staff-to-class responsibility, role, effective dates. | Belongs to class/academic year and employee; authorization-critical, audited. |
| schedules / schedule_slots | Class timetable and individual weekday/time entries. | Owned by class/academic year; slots belong to schedule; change history audited. |
| holidays | Closure date/range, reason, scope. | Institution-owned; may target classes; audit required. |

## Students, guardians, and staff

| Entity | Purpose and important conceptual fields | Relationships, ownership, security, audit |
|---|---|---|
| students | Durable child identity; public ID, legal identity data, birth date, lifecycle state. | Institution-owned; one profile, many enrollments/status records/relationships; highly restricted, soft delete only after archival policy. |
| student_profiles | Mutable operational profile and non-authentication details. | One-to-one with student; restricted; sensitive field changes audited. |
| student_enrollments | Historical placement; academic year, class, effective dates, status. | Belongs to student/class/year; one active record per year; never overwritten, status changes audited. |
| student_status_history | Immutable lifecycle transition, reason, actor, effective time. | Belongs to student; append-only. |
| guardians | Adult identity/contact profile. | Institution-owned; linked through guardian_relationships; sensitive, soft delete/merge controlled and audited. |
| guardian_relationships | Student-guardian link; relationship type/subtype, legal status, pickup authorization, emergency priority, verification. | Belongs to student and guardian; authorization-critical, effective dated, audited. |
| guardian_contact_methods | Verified phone/email/address and communication preference. | Belongs to guardian; privacy protected and audited. |
| pickup_authorizations | Approved collector, validity, identity verification, status. | Belongs to student and guardian/collector; safety-critical, approval/revocation audited. |
| emergency_contacts | Ordered emergency contact details. | Belongs to student; restricted and audited. |
| employees | Employment record; user link, employment status, job details. | Institution-owned; confidential, soft deletion restricted, audited. |
| medical_profiles | Child medical/allergy/safety information. | Belongs to student; highly restricted, access audited. |
| student_documents | Required/received student document workflow. | Belongs to student and file; access/download audited. |
| student_consents | Versioned consent decision, signer, date, withdrawal. | Belongs to student/guardian; immutable decision history, audited. |

## Attendance, education, care, and communication

| Entity | Purpose and important conceptual fields | Relationships, ownership, security, audit |
|---|---|---|
| attendance_days | Student/class/date attendance context and final state. | Belongs to student/enrollment; derived from immutable events; restricted updates. |
| attendance_events | Check-in/out, absence, late, correction, override; time, method, operator. | Belongs to attendance day/student; append-only, safety/audit critical. |
| attendance_corrections | Requested correction, reason, approval and outcome. | Belongs to attendance event/day; append-only workflow evidence. |
| attendance_statuses | Fixed approved attendance status catalog. | System-owned; controlled change, no ordinary deletion. |
| qr_tokens | Token purpose, nonce, expiry, issuance/invalidation/use state. | Institution workflow-owned; no personal data in payload; security audited. |
| qr_scan_attempts | Every scan result, time, operator, reason. | Institution-owned; append-only security evidence. |
| pickup_events | Verified custody transfer event. | Belongs to student/pickup authorization; immutable and highly restricted. |
| attendance_rules | Cutoffs and escalation policy. | Institution-owned; controlled configuration and audited. |
| daily_activities / activity_participants | Classroom activity and optional participation. | Activity belongs to class; participants link activity/student; teacher-authored, approval/audit policy applies. |
| student_observations | Teacher child observation. | Belongs to student/class/author; restricted and audit logged. |
| development_categories / development_records | Structured developmental domains and child entries. | Categories system-owned; records belong to student; restricted, historically retained. |
| behavior_incidents / incident_actions | Sensitive incident and follow-up/resolution. | Belong to student; access and every change audited. |
| teacher_notes | Internal classroom/student notes. | Author/class scoped; restricted, retention-controlled. |
| announcements / announcement_targets | Notice and its audience definitions. | Institution-owned; target can be role/class/student/guardian; send history retained. |
| conversations / messages / message_attachments | Controlled parent-school messaging. | Conversation links authorized parties; messages append-only; attachments use files; access audited. |
| notification_templates | Versioned Arabic-first notification content. | Institution-owned; restricted editing/audit. |
| notifications / notification_deliveries | Notice and per-channel delivery state. | Recipient-scoped; private, immutable delivery evidence. |
| notification_preferences | Non-essential recipient channel preferences. | Belongs to user/guardian; changes audited. |
| acknowledgements | Required read/acceptance evidence. | Belongs to notice and recipient; append-only. |

## Files, media, reports, and operations

| Entity | Purpose and important conceptual fields | Relationships, ownership, security, audit |
|---|---|---|
| files | Private stored object metadata: storage key, original name, MIME, checksum, classification, retention. | Institution-owned; linked by file_links; never public direct path; upload/access/delete audited. |
| file_links | Approved file-to-domain-record relation. | Belongs to file and target entity; soft delete/retention coordinated. |
| media_items / media_albums | Photo/video metadata and grouping. | Institution-owned/class scoped; consent and approval required. |
| media_visibility / media_reviews | Audience scope and review decision/history. | Belong to media item; approval/removal audited. |
| generated_reports / exports | Requested report/extract, filters, requester, generated file, expiry. | Institution-owned and requester-scoped; access/export audited. |
| jobs / failed_jobs / scheduled_task_runs | Background and scheduler execution history. | System-owned; technical access restricted; failures retained. |
| backups / restore_operations | Backup metadata and restoration approval/execution evidence. | Institution-owned; highly restricted, immutable operational audit. |
| system_health_checks | Diagnostic outcome history. | System-owned; restricted operational access. |
| data_retention_actions | Archival/anonymization/deletion execution evidence. | Institution-owned; immutable compliance evidence. |
