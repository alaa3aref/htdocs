# Database Relationships

## Core ownership hierarchy

`institution → academic_year → class → student_enrollment → student`

The institution is the data-owning boundary. Academic years organize operational history. A class belongs to one academic year. An enrollment joins one student to one class for an effective period. A student is never owned by a teacher or guardian.

## Required cardinalities

- One institution has many academic years, classes, classrooms, users, students, guardians, employees, files, notifications, and audit records.
- One academic year has many terms, classes, class assignments, schedules, and student enrollments.
- One class has many enrollments, schedule records, activities, and staff assignments; it may reference one physical classroom at a time according to schedule.
- One student has one current profile, many enrollments, status-history entries, guardians, attendance events, documents, consents, observations, and care records.
- One guardian may be related to many students; one student may have many guardians, through `guardian_relationships` only.
- One employee may have one linked user account and many class assignments. Teacher status is role-and-assignment based.
- One attendance day has many immutable attendance events; one QR scan attempt may support one event but never substitutes staff verification.
- One file may link to multiple authorized records through `file_links`; physical content remains private.
- One notification may have many delivery attempts and many acknowledgement requirements where audience expansion requires it.

## Referential integrity rules

- Foreign keys are mandatory for owned relationships and use restrictive behavior for historical/safety records.
- A guardian relationship cannot exist without both an active student and guardian reference.
- An enrollment must reference a class belonging to its academic year.
- An active class assignment must reference an active employee and class in the relevant academic year.
- Attendance events must reference an enrolled student and valid attendance context.
- Media visibility requires both valid consent and approved review state before parent access.
- Generated reports and exports must reference their requester and the private generated-file record.

## History and effective dating

Time-sensitive relationships use effective start/end dates: enrollments, class assignments, guardian pickup authority, role scopes, and consent validity. Changes create new history or append events; they do not erase past responsibility, status, or access evidence.

## Deletion relationships

Soft deletion never cascades into historical records. Before a mutable parent is soft-deleted, active dependent records must be closed, reassigned, or explicitly retained. Hard deletion is performed only by approved retention action after file-link, audit, legal, and backup requirements are satisfied.

## Parent portal access path

`user → guardian → guardian_relationship → student → approved portal resource`

Every parent query must enforce this path plus current verification, relationship status, consent/visibility rules, and applicable class/institution scope. A direct student identifier is never sufficient authorization.

## Attendance and pickup access path

`authorized staff user → active role/scope → class or attendance assignment → attendance action`

For pickup: `student → active pickup_authorization → verified collector → staff visual verification → pickup_event`. QR validation is additive evidence only.
