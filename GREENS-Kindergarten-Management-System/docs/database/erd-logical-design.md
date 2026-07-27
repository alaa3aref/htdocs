# Logical ERD Design

## Purpose and notation

This is the approved logical relationship design for Phase 2.2. It is not SQL and does not prescribe physical column types. Every entity has an internal conceptual identifier (`id`), an immutable opaque public identifier where externally exposed (`public_id`), and the ownership/audit metadata required by the Constitution. `||` means exactly one, `o{` means zero or many, and `|{` means one or many.

## Ownership boundaries

`institutions` is the root business ownership boundary. All child, guardian, staff, attendance, care, file, media, notification, report, and operational records are institution-scoped. System-controlled catalogs are still used only within this institution boundary. `students` are institution-owned: neither guardians nor teachers own student data.

```mermaid
erDiagram
    INSTITUTIONS ||--o{ ACADEMIC_YEARS : owns
    INSTITUTIONS ||--o{ USERS : owns
    INSTITUTIONS ||--o{ STUDENTS : owns
    INSTITUTIONS ||--o{ GUARDIANS : owns
    INSTITUTIONS ||--o{ EMPLOYEES : employs
    INSTITUTIONS ||--o{ CLASSROOMS : provides
    ACADEMIC_YEARS ||--o{ TERMS : contains
    ACADEMIC_YEARS ||--o{ CLASSES : contains
    CLASSES ||--o{ STUDENT_ENROLLMENTS : receives
    STUDENTS ||--o{ STUDENT_ENROLLMENTS : has
    CLASSES ||--o{ CLASS_ASSIGNMENTS : has
    EMPLOYEES ||--o{ CLASS_ASSIGNMENTS : holds
    CLASSROOMS ||--o{ SCHEDULE_SLOTS : hosts
    CLASSES ||--o{ SCHEDULES : follows
    SCHEDULES ||--o{ SCHEDULE_SLOTS : contains
    STUDENTS ||--|| STUDENT_PROFILES : has
    STUDENTS ||--o{ STUDENT_STATUS_HISTORY : transitions
    STUDENTS ||--o{ GUARDIAN_RELATIONSHIPS : linked
    GUARDIANS ||--o{ GUARDIAN_RELATIONSHIPS : linked
    GUARDIANS ||--o{ GUARDIAN_CONTACT_METHODS : uses
    STUDENTS ||--o{ PICKUP_AUTHORIZATIONS : permits
    STUDENTS ||--o{ EMERGENCY_CONTACTS : has
    STUDENTS ||--o| MEDICAL_PROFILES : has
```

```mermaid
erDiagram
    USERS ||--o{ USER_ROLES : receives
    ROLES ||--o{ USER_ROLES : assigns
    ROLES ||--o{ ROLE_PERMISSIONS : grants
    PERMISSIONS ||--o{ ROLE_PERMISSIONS : maps
    USERS ||--o{ USER_SCOPES : restricted_by
    USERS ||--o{ USER_SESSIONS : opens
    USERS ||--o{ PASSWORD_RESET_TOKENS : requests
    USERS ||--o{ AUDIT_LOGS : acts_in
    USERS ||--o{ SECURITY_EVENTS : triggers
    USERS o|--o| EMPLOYEES : represents
    USERS o|--o| GUARDIANS : represents
    STUDENTS ||--o{ ATTENDANCE_DAYS : has
    STUDENT_ENROLLMENTS ||--o{ ATTENDANCE_DAYS : contextualizes
    ATTENDANCE_DAYS ||--o{ ATTENDANCE_EVENTS : records
    ATTENDANCE_EVENTS ||--o{ ATTENDANCE_CORRECTIONS : corrected_by
    QR_TOKENS ||--o{ QR_SCAN_ATTEMPTS : scanned_as
    QR_SCAN_ATTEMPTS o|--o| ATTENDANCE_EVENTS : supports
    STUDENTS ||--o{ PICKUP_EVENTS : collected_in
    PICKUP_AUTHORIZATIONS ||--o{ PICKUP_EVENTS : authorizes
```

```mermaid
erDiagram
    CLASSES ||--o{ DAILY_ACTIVITIES : hosts
    DAILY_ACTIVITIES ||--o{ ACTIVITY_PARTICIPANTS : includes
    STUDENTS ||--o{ ACTIVITY_PARTICIPANTS : participates
    STUDENTS ||--o{ STUDENT_OBSERVATIONS : receives
    DEVELOPMENT_CATEGORIES ||--o{ DEVELOPMENT_RECORDS : categorizes
    STUDENTS ||--o{ DEVELOPMENT_RECORDS : receives
    STUDENTS ||--o{ BEHAVIOR_INCIDENTS : involved_in
    BEHAVIOR_INCIDENTS ||--o{ INCIDENT_ACTIONS : resolved_by
    STUDENTS ||--o{ TEACHER_NOTES : noted_in
    STUDENTS ||--o{ STUDENT_DOCUMENTS : requires
    STUDENTS ||--o{ STUDENT_CONSENTS : governed_by
    FILES ||--o{ FILE_LINKS : linked_by
    FILES ||--o{ STUDENT_DOCUMENTS : supplies
    FILES ||--o{ MESSAGE_ATTACHMENTS : supplies
    MEDIA_ALBUMS ||--o{ MEDIA_ITEMS : groups
    MEDIA_ITEMS ||--o{ MEDIA_VISIBILITY : scopes
    MEDIA_ITEMS ||--o{ MEDIA_REVIEWS : reviewed_in
```

```mermaid
erDiagram
    ANNOUNCEMENTS ||--o{ ANNOUNCEMENT_TARGETS : targets
    CONVERSATIONS ||--o{ MESSAGES : contains
    MESSAGES ||--o{ MESSAGE_ATTACHMENTS : has
    NOTIFICATION_TEMPLATES ||--o{ NOTIFICATIONS : renders
    NOTIFICATIONS ||--o{ NOTIFICATION_DELIVERIES : attempts
    NOTIFICATIONS ||--o{ ACKNOWLEDGEMENTS : requires
    USERS ||--o{ NOTIFICATION_PREFERENCES : configures
    FILES ||--o{ GENERATED_REPORTS : stores
    USERS ||--o{ GENERATED_REPORTS : requests
    FILES ||--o{ EXPORTS : stores
    USERS ||--o{ EXPORTS : requests
    INSTITUTIONS ||--o{ BACKUPS : protects
    BACKUPS ||--o{ RESTORE_OPERATIONS : source_for
    INSTITUTIONS ||--o{ DATA_RETENTION_ACTIONS : records
    INSTITUTIONS ||--o{ SYSTEM_HEALTH_CHECKS : monitors
    INSTITUTIONS ||--o{ JOBS : queues
    JOBS ||--o{ FAILED_JOBS : may_fail_as
```

## Complete entity register and conceptual identifiers

| Domain | Entities and primary conceptual identifiers |
|---|---|
| Identity | `users(user public_id)`, `roles(role code)`, `permissions(permission code)`, `user_roles(assignment id)`, `role_permissions(mapping id)`, `user_scopes(scope id)`, `password_reset_tokens(token id)`, `user_sessions(session id)`, `audit_logs(audit event id)`, `security_events(security event id)`, `system_settings(setting key)`, `setting_revisions(revision id)` |
| Structure | `institutions(institution public_id)`, `academic_years(year public_id)`, `terms(term public_id)`, `classes(class public_id)`, `classrooms(classroom public_id)`, `class_assignments(assignment id)`, `schedules(schedule id)`, `schedule_slots(slot id)`, `holidays(holiday id)` |
| Student/guardian/staff | `students(student public_id)`, `student_profiles(profile id)`, `student_enrollments(enrollment public_id)`, `student_status_history(status event id)`, `guardians(guardian public_id)`, `guardian_relationships(relationship id)`, `guardian_contact_methods(contact method id)`, `pickup_authorizations(pickup authorization id)`, `emergency_contacts(contact id)`, `employees(employee public_id)`, `medical_profiles(medical profile id)`, `student_documents(document record id)`, `student_consents(consent record id)` |
| Attendance/care | `attendance_days(attendance day id)`, `attendance_events(attendance event id)`, `attendance_corrections(correction id)`, `attendance_statuses(status code)`, `qr_tokens(token id)`, `qr_scan_attempts(scan attempt id)`, `pickup_events(pickup event id)`, `attendance_rules(rule id)`, `daily_activities(activity id)`, `activity_participants(participation id)`, `student_observations(observation id)`, `development_categories(category code)`, `development_records(record id)`, `behavior_incidents(incident id)`, `incident_actions(action id)`, `teacher_notes(note id)` |
| Communication/media | `announcements(announcement id)`, `announcement_targets(target id)`, `conversations(conversation id)`, `messages(message id)`, `message_attachments(attachment id)`, `notification_templates(template/version id)`, `notifications(notification id)`, `notification_deliveries(delivery id)`, `notification_preferences(preference id)`, `acknowledgements(acknowledgement id)`, `files(file public_id)`, `file_links(link id)`, `media_items(media id)`, `media_albums(album id)`, `media_visibility(visibility id)`, `media_reviews(review id)` |
| Operations | `generated_reports(report request id)`, `exports(export id)`, `jobs(job id)`, `failed_jobs(failure id)`, `scheduled_task_runs(run id)`, `backups(backup id)`, `restore_operations(restore id)`, `system_health_checks(check id)`, `data_retention_actions(retention action id)` |

## Relationship policies

- All user-facing and sensitive relationships use opaque public identifiers externally; internal IDs are implementation details.
- Historical links use effective dates where access or assignment changes over time: enrollment, staff assignment, guardian authority, pickup authority, role scope, and consent.
- `classrooms` is the physical-room entity; `classes` is the academic-group entity. No duplicate rooms entity exists.
- File ownership remains with the institution; `file_links` grants contextual use and never makes storage public.
- Parent portal entitlement is always evaluated through an active verified guardian relationship, then the relevant consent/visibility and policy scope.
