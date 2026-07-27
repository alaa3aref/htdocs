# Architecture Decisions

The following approved decisions are binding:

- PHP 8.3+ custom modular MVC application; no framework.
- MySQL 8.0+ with InnoDB and utf8mb4.
- Apache and Hostinger-compatible deployment.
- Server-rendered HTML, custom CSS design system, and vanilla JavaScript modules.
- Versioned JSON REST APIs under `/api/v1` when API implementation is approved.
- Private storage outside the web root; public assets only in `public/assets`.
- Arabic Egyptian and RTL are first-class requirements.
- The Constitution governs architecture freezes, security, accessibility, naming, and quality requirements.

This project foundation does not introduce routing, authentication, database connections, migrations, UI pages, or business workflows.
