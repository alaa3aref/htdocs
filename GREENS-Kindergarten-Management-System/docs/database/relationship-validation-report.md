# Relationship Validation Report

## Result

**Approved for documentation stage.** The Phase 2.1 logical domain model has clear ownership boundaries, normalized relationship paths, historical preservation, and expansion boundaries. No database implementation is created by this report.

## Domain validation

| Domain | Ownership and cardinality validation | Deletion/history/authorization validation | Expansion result |
|---|---|---|---|
| Authentication and permissions | Institution owns users; user-role and role-permission mappings support many-to-many access; user scopes constrain assignments. | Session/recovery data is retention-deleted; role and security changes are audited; policy checks are server-side. | Supports added roles, scoped branches, and future clients without changing user identity. |
| Kindergarten structure | Institution owns academic years, classes, classrooms, holidays; class assignment separates staff responsibility from child ownership. | Referenced years/classes are historically retained; schedule/class changes audited; staff scope derives from assignment. | Supports multiple years, terms, rooms, and future branches. |
| Student lifecycle | Institution owns student; profile is one-to-one; enrollments and statuses are one-to-many history. | Status history is append-only; enrollment is effective-dated; soft delete follows archive/retention policy. | Supports transfers, readmission, graduation, and complete historical reporting. |
| Guardian relationships | Student and guardian are independent institution-owned people linked only through guardian_relationships. | Relationship rights are effective-dated and audited; revocation stops portal/pickup access without erasing history. | Supports multiple guardians, guardians connected to siblings, and additional approved relationship types. |
| Employee and teacher assignments | Employee is a staff profile; teacher capability comes from user role plus active class assignment. | Assignment end dates retain past responsibility; permissions are role-and-scope checked. | Supports assistants, substitute teachers, and additional staff roles. |
| QR workflow | Token is a workflow object and scan attempts are separate immutable evidence. | No QR personal data; expiry, nonce, invalidation, replay control, operator, and result are recorded. | Supports future token purposes/devices without altering child identity design. |
| Attendance history | Attendance day provides daily context; attendance events provide append-only evidence; corrections are separate workflow records. | No silent overwrite; corrections require reason/approval; staff scope is class/attendance-assignment based. | Supports future attendance methods, cutoffs, and analytical reporting. |
| Parent portal access | User-to-guardian-to-student relationship is explicit and many-to-many at guardian/student layer. | Verified active relationship, visibility/consent, and policy scope are mandatory for each resource query. | Supports one guardian with multiple children and future co-parent/guardian cases. |
| Health and daily care | Student owns medical, observation, development, behavior, and care histories; classes contextualize authored activity. | Medical/behavior access is restricted and audited; records are retained as historical evidence. | Supports additional care programs and structured assessment categories. |
| Media and documents | Institution owns private files; contextual links attach files to student/media/message/report entities. | Storage remains outside public root; consent, review, audience scope, and access logs govern visibility. | Supports new document/media types and multiple approved references to one file. |
| Notifications | Notification is distinct from template, delivery attempt, target, and acknowledgement. | Delivery history is retained; preferences apply only to non-essential notices; access is recipient-scoped. | Supports additional delivery channels and notification categories. |
| Reporting | Generated reports/exports belong to institution and requester, with private file links and expiry. | Requests, access, and exports are audited; generated data is scope-filtered. | Supports new report definitions without changing underlying ownership model. |

## Special guardian-system validation

The required relationship is complete:

`student 1 → many guardian_relationships ← 1 guardian`

Each relationship contains the relationship type `Father`, `Mother`, or `Other`. An Other relationship requires exactly one approved subtype—Grandfather, Grandmother, Uncle, Aunt, Guardian, Brother, Sister, Relative—or a mandatory custom description. The relationship additionally records legal guardian status, pickup permission, emergency-contact priority, verification state, effective period, and audit history.

This correctly permits one child to have a father, mother, and any number of other approved guardians without duplicating guardian identity data. It also allows one guardian to be connected to more than one child.

## QR validation

- **No personal data:** QR payloads contain only signed opaque token metadata; child, guardian, attendance, and contact data are excluded.
- **Attendance linkage:** a validated QR scan may support an attendance or pickup workflow, but only an authorized staff member can finalize the action.
- **Auditability:** issued, invalidated, expired, successful, duplicate, and rejected scans are represented by token/scan audit records.
- **Failed scans:** every failed scan is retained in `qr_scan_attempts` with result/reason and operator/session context when available.
- **Custody safety:** active pickup authorization and staff visual verification remain mandatory.

## Relationship risk review

| Risk class | Validation finding | Required control |
|---|---|---|
| Missing relationship risks | No missing required domain relationship identified. Polymorphic file linkage is constrained through controlled file_links rather than ad hoc references. | Maintain entity catalog and relationship review before each migration. |
| Security risks | Parent/teacher access could be over-broad if relationship and scope checks are bypassed. | Enforce server-side policies using guardian relationship, verification, class assignment, consent, and institution scope. |
| Data integrity risks | Duplicate active enrollment, overlapping pickup authority, or attendance overwrite could cause operational harm. | Use foreign keys, uniqueness/effective-date checks, transactions, append-only events, and correction workflows. |
| Future-expansion risks | Multi-branch, new roles, channels, and report types may add scale/complexity. | Preserve institution boundary, role scopes, versioned templates/consents, opaque IDs, and extensible target/status catalogs. |

## DATABASE RELATIONSHIP APPROVAL CHECKLIST

- [x] Each named domain has an institution-owned relationship path.
- [x] Student identity, profile, enrollment, and lifecycle history are separated.
- [x] Guardian relationship supports Father, Mother, Other/subtype, legal status, pickup permission, and emergency priority.
- [x] Teacher responsibility is represented by employee, role, and effective-dated class assignment.
- [x] QR contains no personal data, supports attendance, records all scans, and does not replace staff verification.
- [x] Attendance uses immutable events and separate correction records.
- [x] Parent portal access has a verifiable, restrictive authorization path.
- [x] Health, behavior, media, documents, reports, exports, notifications, and audit records have defined access boundaries.
- [x] Soft deletion and immutable-history boundaries are documented.
- [x] Foreign-key, normalization, MySQL 8/InnoDB, utf8mb4, audit, and future-expansion requirements are ready for migration design.
