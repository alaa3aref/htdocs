# Project Structure

The repository structure is fixed by the GREENS Engineering Constitution.

- `app/` contains modular application layers only.
- `config/` contains non-secret application configuration.
- `database/` contains future migrations and seeds; Phase 1 creates neither.
- `public/` is the only web-accessible directory.
- `resources/` holds source views, translations, CSS, and JavaScript.
- `routes/` is reserved for approved route definitions.
- `storage/` contains private runtime data and must never be public.
- `tests/` contains automated tests.
- `docs/` contains versioned engineering documentation.
- `api/` is reserved for approved API documentation and contracts.

No directory may be renamed, repurposed, or expanded outside the Constitution without an approved amendment.
