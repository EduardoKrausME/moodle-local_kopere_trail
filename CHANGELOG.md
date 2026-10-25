# Changelog

This changelog documents only the two latest **published** releases of `local_kopere_trail`, verified on October 24, 2026. Each entry describes the changes between the published Git tag and the immediately preceding published release tag.

## [2.2.7] - 2026-10-23

### Security and privacy
- Expanded the parent plugin's Moodle Privacy API metadata to describe trail assignments and audit events.
- Added system-context user discovery for enrolment, enrolment-source, progress, step-progress and event records, as well as direct user assignments, through `core_userlist_provider`.
- Added selective Privacy API deletion for approved users, covering enrolments, progress, audit events and user-specific assignments without deleting other users' records.
- Added null Privacy API providers and matching English/Brazilian Portuguese explanatory language strings for the bundled subplugins, whose user records are managed by the parent plugin.
- Removed `trusttext` from the HTML content editor options and changed exported HTML rendering to `trusted => false` so Moodle sanitizes untrusted trail content.
- Marked the trail-management capability with the `RISK_XSS` risk flag.

### Tests
- Added regression tests for system-context user discovery and selective deletion, checking that another user's assignments and events are preserved.
- Added a regression test ensuring executable `<script>` markup is removed when trail HTML content is rendered.

### Documentation and maintenance
- Corrected the plugin listing metadata in `.camp/listing.yml`, including the description, supported Moodle versions and `learning` category.
- Added release workflow concurrency settings in the historical `.github/workflows/release.yml` file, keyed by Git ref and configured not to cancel an active release.
- Updated the plugin's `version.php` metadata to internal version `2026102300` and release `2.2.7`.

**Full comparison:** [2.2.6...2.2.7](https://github.com/EduardoKrausME/moodle-local_kopere_trail/compare/2.2.6...2.2.7)

## [2.2.6] - 2026-10-15

### CI maintenance
- Updated the PostgreSQL CI service image from version 16 to 17.
- Updated the MariaDB CI service image from version 10 to 11.4, including its environment variables and the `healthcheck.sh --connect --innodb_initialized` startup check.

### Release metadata
- Updated `version.php` from internal version `2026101200`, release `2.2.5`, to internal version `2026101500`, release `2.2.6`.
- No application logic files changed between the two published tags.

**Full comparison:** [2.2.5...2.2.6](https://github.com/EduardoKrausME/moodle-local_kopere_trail/compare/2.2.5...2.2.6)
