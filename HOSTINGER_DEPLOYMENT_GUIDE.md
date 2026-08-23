# Golden Eye Academy Production Release and Staff Handover

## Purpose and release boundary

This is the single deployment checklist for the authoritative `release/goldeneye-recovery` candidate based on `0e2a2af85362ae0339a0542fa4bb57b6e469e8e5` plus the Phase 6 rehearsal documentation commit. Record and approve the final immutable release commit before deployment. This guide covers release verification, backups, MySQL/MariaDB migration rehearsal, security, production CMS review, deployment, smoke testing, and rollback.

This document does not authorize a deployment. The owner must approve a deployment window after every stop/go item below is complete. Do not push, merge, deploy, access production, import an old database dump, or run production seeders as part of local release preparation.

Official references:

- Hostinger PHP version selection: https://support.hostinger.com/en/articles/1575755-how-to-change-the-php-version-of-your-hostinger-hosting-plan
- Hostinger Composer usage: https://www.hostinger.com/support/5792078-how-to-use-composer-at-hostinger/
- Hostinger Composer/PHP version troubleshooting: https://www.hostinger.com/support/5792082-how-to-solve-common-composer-issues-at-hostinger/
- Hostinger PHP extensions: https://www.hostinger.com/support/4667515-how-to-manage-php-extensions-and-options-in-hostinger/
- Hostinger Laravel support: https://support.hostinger.com/en/articles/1583301-which-laravel-versions-are-supported-at-hostinger
- Laravel 13 deployment: https://laravel.com/docs/13.x/deployment
- Laravel 13 filesystem: https://laravel.com/docs/13.x/filesystem
- Laravel 13 queues: https://laravel.com/docs/13.x/queues

## 1. Owner release gates

Record the result, reviewer, and time for every item. A failed item is a stop condition.

- [ ] Approve the exact release commit and deployment window.
- [ ] Before deployment, select PHP 8.4 for Golden Eye Academy in Hostinger.
- [ ] Confirm web and Composer commands use PHP 8.4 during deployment.
- [ ] Confirm the production domain and HTTPS URL; set `APP_URL` to that exact origin.
- [ ] Confirm a verified database backup and media backup can be restored.
- [ ] Run the read-only newsletter duplicate query in section 5; it must return no rows.
- [ ] Approve the consolidated CMS decisions in section 9.
- [ ] Confirm production mail delivery and the queue operating method.
- [ ] Confirm named staff accounts and remove or reset shared, default, or former-staff access.
- [ ] Confirm the post-deployment smoke-test owner and rollback decision-maker are available.

## 2. Verified release requirements

| Area | Locked or required value | Deployment decision |
| --- | --- | --- |
| PHP | PHP 8.4.x; root requirement `^8.4` | Required for both web requests and CLI/Composer |
| Framework | Laravel 13.12.0 from `composer.lock` | Install only from the committed lockfile |
| Composer | Composer 2 | Run under PHP 8.4; never update dependencies during deployment |
| Database | Application connection is MySQL-compatible | Record the live MySQL/MariaDB version before approval; do not change engine during deployment |
| Node build | Vite 8.0.16 and PostCSS 8.5.24 | The verified `public/build` is committed; Node is not required on production unless the owner authorizes an off-host rebuild |
| Web root | Laravel `public/` directory | Do not expose `.env`, `vendor`, `storage`, or application source through the web root |
| Writable paths | `storage/`, `bootstrap/cache/`, and CMS upload folders below `public/site/img/` | Grant the web user only the required write access |
| Queue | Database queue with `QUEUE_WORKER_STRATEGY=cron` is the shared-hosting default | Laravel schedules a bounded queue drain every minute; do not rely on queued mail until the host cron is verified |
| Scheduler | Queue drain every minute; failed-job pruning daily | Configure Hostinger cron to run Laravel `schedule:run` every minute using the account's verified PHP 8.4 binary/path |

Laravel's required extensions must be available under the same PHP 8.4 runtime used by the site: cURL, DOM/XML/libxml, fileinfo, filter, hash, mbstring, OpenSSL, PCRE, PDO with the production database driver, session, and tokenizer. Confirm with the Hostinger PHP extensions interface and `composer check-platform-reqs --no-dev` after installation.

If SSH reports a different PHP version from hPanel, stop. Select or invoke Hostinger's PHP 8.4 CLI binary for Composer and Artisan, then repeat `php -v`, `composer --version`, and the platform check. Do not bypass Composer platform requirements.

## 3. Pre-deployment evidence and fingerprints

Preserve the release record with:

- exact Git commit and branch;
- `composer.lock`, `package-lock.json`, `public/build/manifest.json`, and referenced asset SHA-256 hashes;
- route inventory and application version;
- counts of courses, categories, service pillars, faculty, testimonials, blogs, FAQs, notices, site settings, inquiries, newsletter subscriptions, users, and roles;
- database, `storage/app`, and public CMS-media backup filenames and hashes;
- the person who approved CMS facts, testimonials, photographs, review links, partnerships, licences, results, and contact information.

Do not treat seeders as production backups. They are recovery checkpoints for known application content and must not overwrite the live database.

## 4. Backup checklist

Complete and verify all backups before maintenance mode or file replacement.

- [ ] Export the complete production database with schema, data, indexes, character set, and routines required by the host.
- [ ] Record database server type/version, database name, export time, size, and SHA-256 hash.
- [ ] Test the export in a disposable database or at minimum verify it can be read and contains expected table/count checks.
- [ ] Back up `public/site/img/` in full, including CMS upload subdirectories.
- [ ] Back up `storage/app/` in full and preserve the public-storage target if used.
- [ ] Back up the production `.env` securely outside the web root; never add it to Git or a public archive.
- [ ] Preserve the current deployed source commit, `vendor`/lockfile pairing, and `public/build` manifest/assets.
- [ ] Record the restore location, person responsible, and estimated restore time.
- [ ] Verify the backups are readable before continuing.

## 5. Newsletter duplicate preflight and migration gate

The Phase 2 migration normalizes newsletter emails and creates a uniqueness constraint. Run this **read-only** query against production before any migration:

```sql
SELECT LOWER(TRIM(email)) AS normalized_email,
       COUNT(*) AS duplicate_count
FROM news_letters
GROUP BY LOWER(TRIM(email))
HAVING COUNT(*) > 1
ORDER BY duplicate_count DESC, normalized_email;
```

- No rows: record the result and continue.
- Any row: stop the deployment. The owner must decide which subscription record to retain after reviewing consent and provenance. Back up the table, resolve duplicates deliberately, rerun the query, and obtain approval. Do not delete or merge subscribers automatically.

The Phase 6 rehearsal verified the complete migration chain on MariaDB 10.4.32 with strict SQL mode and `utf8mb4_unicode_ci`. It also reproduced the legacy `course_faq` schema from repository migration history, loaded representative operational rows from a checksum-verified read-only source, and applied the four remaining release migrations without truncation, duplicate-key, foreign-key, or strict-mode failure. Fresh and legacy rehearsals do not replace the production backup, restore test, newsletter duplicate preflight, or live MySQL/MariaDB version check.

Before deployment, review pending migrations under the PHP 8.4 production configuration:

```bash
php artisan migrate:status
```

Apply only after the backup and duplicate gates pass:

```bash
php artisan migrate --force
```

Do not run `db:seed`, `migrate:fresh`, `migrate:refresh`, `db:wipe`, or an unapproved content-normalization command in production. The only approved release-data command is `course-faq:apply-deployment-data`, and it must follow the dry-run/apply/second-dry-run sequence in section 10.

## 6. Production configuration checklist

Set secrets directly in the production environment. Never paste values into tickets, logs, this guide, Git, deployment archives, or screenshots.

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` is the exact canonical HTTPS production origin, with no staging hostname.
- [ ] `APP_KEY` is the existing production key; do not regenerate it on an existing encrypted application.
- [ ] `DB_CONNECTION=mysql`; `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` are production-only and tested. Do not commit or print their values.
- [ ] `SESSION_SECURE_COOKIE=true`; session domain/path match the canonical host; HTTPS is enforced at the server/proxy.
- [ ] Cache and session drivers match available infrastructure and required tables.
- [ ] `QUEUE_CONNECTION=database` and `QUEUE_WORKER_STRATEGY=cron` are set for the documented shared-hosting strategy.
- [ ] `MAIL_MAILER=smtp`; `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`/TLS choice, `MAIL_FROM_ADDRESS`, and `MAIL_FROM_NAME` are owner-verified without exposing credentials.
- [ ] `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` belong to the exact production domain. A missing or partial key pair is a stop condition for protected forms. Local/testing bypass is never enabled in production.
- [ ] `CSP_REPORT_ONLY=true` for first-live acceptance. Review violation reports, fix legitimate sources, then change to enforcement only in a separately approved change.
- [ ] `LOG_LEVEL` and log rotation are suitable for production; logs are outside public access.
- [ ] Staging remains globally protected from indexing. Production robots and canonicals must not inherit staging settings.
- [ ] `APP_URL` alone supplies the production origin used by canonical and sitemap URLs; verify after configuration caching.

After setting `.env`, run `php artisan optimize:clear`, then build production caches only after database and service connectivity are correct. Remember that clearing a database-backed cache requires a working database connection.

## 7. Filesystem, public root, mail, queue, and scheduler

### Public root

Point the domain document root to the deployed application's `public/` directory. This also ensures `/robots.txt` reaches Laravel's dynamic route rather than an unrelated static file. If the hosting plan cannot point to `public/`, use Hostinger's documented Laravel layout and have a qualified deployer verify the `index.php` paths; never expose the project root.

Verify:

- `/robots.txt` is served by Laravel and matches the production/staging indexing decision;
- `/sitemap.xml` uses the production HTTPS host;
- `public/build/manifest.json` references an existing committed asset;
- the old asset named by the previous manifest is absent;
- `php artisan storage:link` resolves `public/storage` to `storage/app/public` where symlinks are supported;
- CMS-managed image directories are writable by the web user without making the whole application writable.

### Mail, queue, and scheduler

Contact and course-interest records are saved before queued notification mail is attempted, but staff still need a reliable handover channel.

- [ ] Send a controlled SMTP test to an owner-approved address.
- [ ] Confirm password-reset mail and inquiry notifications arrive and are not marked as spam.
- [ ] Set `QUEUE_CONNECTION=database` and `QUEUE_WORKER_STRATEGY=cron` for shared hosting.
- [ ] Confirm `php artisan schedule:list` includes `queue:work --stop-when-empty --tries=3 --timeout=60` every minute with overlap protection and `queue:prune-failed --hours=720` daily.
- [ ] Configure the Hostinger cron to invoke `php artisan schedule:run` every minute. Confirm the account-specific PHP 8.4 binary and absolute application path in Phase 7; do not guess them.
- [ ] Submit one controlled inquiry, confirm its database row exists before notification processing, run the queue drain, and inspect `jobs`, `failed_jobs`, and application logs.
- [ ] Monitor failed jobs after deployment. A mail failure must not remove the already-persisted inquiry.

The Phase 6 disposable MariaDB rehearsal inserted and processed database jobs, demonstrated the three-attempt failure path, recorded the failed job, and retained the inquiry. This is evidence for application behavior, not evidence that the live SMTP account or Hostinger cron is configured.

### Database backup and restore command pattern

Use the Hostinger/account-approved MySQL tools and credential mechanism. Never put a password directly in shell history or this guide. Substitute verified deployment-time variables for every angle-bracket value:

```bash
mysqldump --host=<DB_HOST> --port=<DB_PORT> --user=<DB_USERNAME> --single-transaction --routines --triggers --default-character-set=utf8mb4 <DB_DATABASE> > <EXTERNAL_BACKUP_PATH>/goldeneye-predeploy-<TIMESTAMP>.sql
sha256sum <EXTERNAL_BACKUP_PATH>/goldeneye-predeploy-<TIMESTAMP>.sql
mysql --host=<RESTORE_HOST> --port=<RESTORE_PORT> --user=<RESTORE_USERNAME> <DISPOSABLE_RESTORE_DATABASE> < <EXTERNAL_BACKUP_PATH>/goldeneye-predeploy-<TIMESTAMP>.sql
```

Before approval, restore into a separately named disposable database, compare critical row counts, run table/foreign-key consistency checks, and require exactly 68 approved Course–FAQ assignments after the approved data command. Never test a restore over the live database.

## 8. Security and access checklist

- [ ] Public registration remains disabled; login, logout, password reset, and intended verification flows work.
- [ ] Inventory all user accounts and roles. Disable former-staff access and replace shared/default credentials with named accounts.
- [ ] Provision staff only through the approved command/workflow after SMTP is working; staff must set their own password through a time-limited reset link.
- [ ] Confirm `student`, `staff`, `admin`, and `super_admin` boundaries with representative accounts.
- [ ] Confirm staff cannot reach super-admin-only destructive operations and cannot permanently delete protected records.
- [ ] Keep `.env`, database exports, logs, `storage`, `.git`, Composer metadata, and source outside public access.
- [ ] Confirm HTTPS, secure cookies, CSRF protection, login/form rate limiting, CAPTCHA, and security headers.
- [ ] Review admin-managed links and raw structured-data fields before publishing; allow only owner-approved destinations and valid JSON-LD.
- [ ] Confirm no secret, password, access token, production database, or private backup is included in the release archive or Git diff.
- [ ] Do not deploy local untracked folders or ad-hoc deployment copies, including `.htaccess.txt`, `outputs/`, or `to deploy2/`.
- [ ] Define log review, failed-job review, database backup frequency, restore testing, and account offboarding ownership.

## 9. Consolidated production CMS-update checklist

CMS updates are manual owner/staff decisions after backup. Preview each change before publishing, preserve protected products and slugs, and never invent a fact to fill a blank. Use `STAFF_CMS_HANDOVER.md`, `PHASE_6_CONTENT_HANDOVER.md`, and `PHASE_7_SEO_HANDOVER.md` for field-level guidance.

| CMS area | Owner-approved value or safe direction | Public verification |
| --- | --- | --- |
| Organisation identity | Use **Golden Eye Academy** for academy classes and learning support. Mention Brilliant Consultancy only for approved study-abroad counselling/application-planning services. | Header, footer, About, audience pages, service pages, metadata, structured data |
| Contact/local facts | Verify official address, phone, WhatsApp, email, opening hours, map coordinates/link, and social profiles. | Tap call/WhatsApp/email; open map and each social link on mobile |
| Unsupported claims | Remove or keep unpublished any unverified founding year, years of experience, student count, ranking, result, placement, partnership, licence, or guaranteed-outcome claim. | Homepage, About, courses, blogs, FAQs, testimonials, metadata |
| Course facts | Preserve all products. Confirm fees, durations, current status, delivery mode, categories, descriptions, and exact names. Use “Confirm current batch options with the academy” where batch facts are not live. | Catalogue, all-courses page, each active course, search, inquiry preselection |
| Shared course notice | `course_confirmation_note`: “Confirm current batch timing, seat availability, and instructor details with the academy before enrollment.” | Course details and About comparison; check mobile wrapping |
| Faculty | Verify consent, photo, title, bio, and exact course association. Do not invent missing matches for Professional Web Development → Prasun Paudel or Chinese Language Starter → Chham Maya Rai. | Homepage and relevant course pages; unmatched sections stay hidden |
| Testimonials | Obtain consent and verify wording, image, rating, and exact current course name. Resolve or keep hidden the unmatched Apekshya Chhetri/Korean Language, Nirmala Thapa/IELTS Masterclass, and Sharap Dorje Gurung/Computer and Office Skills records. | Homepage/course page only after exact, approved association |
| External reviews | Add a real owner-approved Google Business Profile URL or approved review evidence. Do not imply verified ratings without evidence. | Open from a private/mobile browser |
| Blogs/articles | Preserve all existing articles/slugs. Check draft/published state, title, excerpt, body, featured image, author/date, links, SEO/AEO fields, and public detail page. | Blog index, direct URL, sitemap, sharing preview |
| FAQs | Verify active status, priority order, answer accuracy, direct anchors, and both CMS reveal labels. Review conditional claims about certificates, schedules, visits, events, fees, and outcomes. | Homepage/FAQ page, keyboard and direct-anchor tests |
| Global Launchpad | Owner must define whether this service is language preparation, destination guidance, application planning, or an approved combination. Do not change its meaning, slug, or status until decided. | Service listing/detail and related CTAs |
| Main campaign popup | Select the current owner-approved image, title, CTA, destination, and active state. | Fresh/private mobile and desktop session; close and CTA work |
| Popup notice | Decide independently from the main campaign popup; verify image/link/status/schedule. | Only the intended popup-style notice appears |
| Announcement bar | Decide independently; verify title, link, start, expiry, display type, and active state. | Top bar appears only in the approved period |
| Footer/newsletter | Verify footer description, contact details, privacy link, newsletter consent context, and success/error copy. | Desktop/mobile footer and duplicate subscription flow |
| SEO/local search | Verify production canonical host, titles/descriptions, organization/local-business details, Google Business link, robots, and sitemap. Keep staging noindex. | Page source, `/robots.txt`, `/sitemap.xml`, representative rich-result validation |
| Media | Use relevant, consented, optimized images with meaningful alt text. Do not overwrite an in-use file without a recoverable backup. | Mobile/desktop layouts, missing-image check, upload/edit cycle |

For each CMS change, record the editor, approver, old value, new value, preview URL, publish time, and rollback value. Staff should stop and escalate on unclear validation, a 403/419/500 response, an unexpected public result, or any request to alter a protected product/slug.

## 10. Deployment sequence

Run commands from the application directory under the account's verified PHP 8.4 CLI. Replace only deployment-time paths and credentials; do not alter command intent.

1. Obtain owner approval for the exact release commit, deployment window, smoke-test owner, and rollback decision-maker.
2. Record current release/commit, database server/version, migration status, critical table counts, and media fingerprints.
3. Create the complete live database backup outside the web root. Hash it and verify restore into a disposable database.
4. Back up `public/site/img/`, `storage/app/`, the public-storage target if used, and the production `.env` to protected storage.
5. Require the newsletter duplicate preflight to return no rows and confirm the protected-source/reference fingerprints have no discrepancy.
6. Put the site into the approved maintenance/deployment state: `php artisan down`.
7. Upload or switch to only the tracked files from the approved release. Preserve the production `.env`, uploads, and persistent storage.
8. Confirm both web and CLI runtimes use PHP 8.4; run `composer check-platform-reqs --no-dev`.
9. Install exactly the lockfile state: `composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction`.
10. Confirm `public/build/manifest.json`, every referenced asset, and the approved hashes. Do not run dependency updates or an unreviewed host build.
11. Configure the environment contract in section 6 without printing secrets, then run `php artisan optimize:clear`.
12. Run `php artisan app:production-readiness --skip-dependency-audits` before migration. Database/data warnings expected only because release migrations or approved mappings are pending must be reviewed; every other critical check must pass.
13. Review `php artisan migrate:status`, reconcile it with the approved release, then run `php artisan migrate --force` once.
14. Run `php artisan course-faq:apply-deployment-data` in default dry-run mode.
15. Review resolved courses/FAQs, metadata differences, additions, exclusions, and unexpected assignments. Stop on ambiguity, German A1 creation, Free Course metadata change, or unrelated content change.
16. Run `php artisan course-faq:apply-deployment-data --apply` only after the dry-run is approved.
17. Run `php artisan course-faq:apply-deployment-data` again and require the exact message `NO CHANGES REQUIRED`, 68 approved assignments, no German A1 creation, and untouched Free Course metadata.
18. Apply least-privilege write permissions to `storage/app`, `storage/framework`, `storage/logs`, `bootstrap/cache`, and the existing CMS upload folders under `public/site/img/`.
19. Run `php artisan storage:link` if `storage/app/public` is used; verify `public/storage` resolves to it. Do not overwrite an unrelated path.
20. Configure the verified Hostinger cron to run Laravel `schedule:run` every minute; require the two expected entries in `php artisan schedule:list`.
21. Confirm `QUEUE_CONNECTION=database`, `QUEUE_WORKER_STRATEGY=cron`, queue tables, overlap protection, and failed-job monitoring.
22. Confirm owner-approved SMTP settings with a controlled recipient and production-domain reCAPTCHA keys without printing their values.
23. Build production caches with `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache` (or the equivalent `php artisan optimize`).
24. Run final `php artisan app:production-readiness` and require zero critical failures. Separately require `composer audit --locked` and `npm audit` to report zero advisories.
25. While still in maintenance mode, smoke-test authenticated CMS access through the approved maintenance bypass and verify logs, migrations, queue, storage, and critical database counts.
26. Bring the site online with `php artisan up`, then run the public HTTPS, form, CAPTCHA, CMS, media, and mobile smoke tests in section 11.
27. Verify logs, queue/failed jobs, inquiry counts/context, canonical/robots/sitemap output, and owner acceptance. Keep rollback backups until the retention owner approves disposal.

Never use `migrate:fresh`, `migrate:refresh`, `db:wipe`, seeders, manual bulk SQL, `composer update`, or `npm update` in this sequence.

## 11. Post-deployment smoke tests

Use a private browser and representative mobile and desktop widths. Record status, result, tester, and time.

### Public journeys

- [ ] Homepage loads over HTTPS with correct academy identity, header, footer, popup decisions, announcement decision, images, and no mixed content.
- [ ] `/about`, `/catalogue`, `/courses-all`, `/for-students`, `/for-parents`, `/study-abroad-guidance`, and `/job-computer-skills` load.
- [ ] Representative course detail loads and preserves the selected course through the inquiry journey.
- [ ] Blog index and representative article load; published content appears and draft content does not.
- [ ] FAQ page, show-more/show-fewer button, keyboard interaction, ordering/status, and a direct FAQ anchor work.
- [ ] Search, navigation, hamburger, sticky controls, call links, WhatsApp links, map, social links, and all primary CTAs work.
- [ ] Contact and join-now forms validate, reject an invalid CAPTCHA, accept one controlled submission, and show a useful confirmation.
- [ ] The controlled inquiry appears once in the admin with course/source context and remains present if notification delivery is simulated as unavailable.
- [ ] Newsletter accepts one controlled subscription and handles a normalized duplicate without creating another record.
- [ ] `/robots.txt`, `/sitemap.xml`, canonical tags, page metadata, and structured data use the intended production host; staging remains noindex.
- [ ] A missing URL returns the branded 404, not a 500 or directory listing.

### Staff/CMS and operations

- [ ] Named admin and staff accounts can log in; unauthorized roles are denied.
- [ ] Password reset email works and does not expose account existence beyond the intended response.
- [ ] Staff can create a draft article, preview it, add formatted text/image/SEO data, publish, edit, unpublish/archive, and find it publicly only while published.
- [ ] Staff can create/reorder/hide an FAQ and confirm cache invalidation on public pages.
- [ ] Staff can edit approved course content without changing its protected identity or losing media.
- [ ] Main popup, popup notice, and announcement bar remain separately controlled.
- [ ] Logs contain no new exception, failed job, credential, or debug trace; queue and mail monitoring are operational.
- [ ] Database/content counts and media fingerprints remain consistent except for documented smoke-test records.

Remove or archive controlled smoke-test records only through the approved CMS workflow and record the action. Do not run SQL cleanup shortcuts.

## 12. Rollback procedure

Rollback is owner-approved incident handling, not an improvised code change.

1. Stop the release and enable maintenance mode if the site is unsafe, inquiries fail, authentication is exposed, or data integrity is uncertain.
2. Capture error logs, failed-job information, current commit, migration state, and affected record counts before changing state.
3. Stop the new queue worker so it cannot process code/schema combinations from different releases.
4. Restore the immediately previous approved source release and its matching `composer.lock`, `vendor`, and `public/build`. A versioned release-directory/symlink switch is preferred.
5. Restore the pre-deployment database backup when a migration changed data/schema and its documented rollback is not proven safe. Do not guess with manual SQL and do not run seeders.
6. Restore `public/site/img/`, `storage/app/`, and the previous production `.env` only when their verified backups are required; preserve user uploads created after the backup for reconciliation.
7. Under the previous release's required PHP runtime, run its locked `composer install --no-dev --optimize-autoloader --no-interaction`, clear/rebuild caches, verify storage linkage, and restart its queue worker.
8. Bring the previous release online and repeat the critical smoke tests: homepage, catalogue/course, contact/join inquiry persistence, login/reset, CMS read access, call/WhatsApp, robots/sitemap, logs, and queue.
9. Reconcile any inquiries or uploads received during the window. Record the incident, rollback point, restored backup hashes, and owner approval.

Code rollback and database rollback are separate decisions. Code rollback restores the prior release files, lockfile, vendor set, and assets. Database rollback restores the verified pre-deployment MySQL backup whenever a migration or approved data command changed schema/data and forward recovery is not explicitly approved. The conservative `2026_08_06_230629_rebuild_course_faq_table`, `2026_08_23_173930_align_production_data_contracts`, `2026_08_23_211802_add_stable_public_trust_relationships`, and `2026_08_23_220350_create_blog_course_table` release migrations must not be treated as proof that `php artisan migrate:rollback` alone can restore the previous production state.

Do not use `git reset --hard`, `migrate:fresh`, migration rollback by assumption, database seeders, or destructive bulk deletion as a production rollback method.

## 13. Staff handover and ongoing ownership

The owner should assign named people for:

- release approval and rollback decisions;
- database/media backup and restore testing;
- account provisioning/offboarding and role review;
- inquiry monitoring, counselling handover, and duplicate review;
- SMTP, queue, failed jobs, logs, and incident escalation;
- course/faculty/testimonial factual approval and consent records;
- article/FAQ drafting, preview, publishing, and archiving;
- popup, popup notice, announcement, contact, map, and social accuracy;
- privacy, CAPTCHA, analytics consent, local search, canonical/robots/sitemap checks;
- quarterly broken-link, mobile, accessibility, account, content, and recovery review.

Staff must use draft/preview states where available, keep a change log, and escalate protected-product, slug, permission, database, validation, or production-error questions. Developer assistance is still required for deployments, migrations, dependency changes, schema changes, new CMS fields, recovery from failed releases, or defects that cannot be reversed through existing CMS controls.

## 14. STOP / DO NOT DEPLOY conditions

Stop and retain maintenance state if any of these is true:

- No complete pre-deployment database/media backup, missing checksum, or unverified disposable restore.
- Wrong web or CLI PHP version, wrong/unknown database server or connection, or protected-source/hash discrepancy.
- Failed fresh/legacy migration rehearsal, unexpected pending/unknown migration, duplicate newsletter preflight result, truncation, duplicate-key, strict-mode, or foreign-key error.
- Ambiguous Phase 3 dry-run, missing target course/FAQ, unexpected assignment, anything other than exactly 68 approved assignments, German A1 creation, or Free Course metadata change.
- Any critical `app:production-readiness` failure, invalid/missing `APP_KEY`, `APP_ENV` not production, `APP_DEBUG=true`, or incorrect non-HTTPS `APP_URL`.
- Missing production-domain reCAPTCHA keys, evidence that testing bypass works in production, or a protected form that does not fail closed.
- Missing/unwritable storage/cache/log/media path, wrong public document root, broken required storage link, or unrecoverable media.
- Invalid queue strategy, absent scheduler cron, missing database queue tables, uncontrolled failed jobs, or required SMTP notification transport misconfiguration.
- Missing/invalid build manifest, missing manifest asset, non-reproducible approved build, or Composer/npm advisory.
- Critical CMS create/edit/relationship/media failure, inquiry/contact persistence/context failure, or double submission creating duplicate inquiries.
- HTTPS/header/cookie configuration conflict, exposed `.env`/backup/source path, unexplained exception, or an unavailable rollback owner.

Do not override a STOP condition by disabling a safety control. Correct it, repeat the affected rehearsal, and obtain a new owner decision.

## 15. Owner actions still required for live deployment

Code readiness does not supply or prove account-owned production configuration. The owner/deployer must provide and verify:

- Hostinger MySQL/MariaDB host, port, database name, username, password, server version, backup access, and restore permissions.
- Canonical domain, HTTPS certificate/proxy behavior, document root pointing to Laravel `public/`, and public-storage/symlink capability.
- The existing production `APP_KEY`, production `.env` custody, secure-cookie/domain settings, and log retention/access.
- Production-domain `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY`; do not create or expose them in this runbook.
- SMTP host, port, encryption/TLS mode, username, password, from identity, owner-approved controlled recipient, and delivery monitoring.
- The exact Hostinger PHP 8.4 CLI binary, absolute application path, and cron command that invokes `schedule:run` every minute.
- Verified Google Business Profile URL and official name/address/phone facts if they are to be published.
- Named deployer, content approver, smoke-test owner, queue/log monitor, backup/restore owner, rollback decision-maker, deployment window, and prior release recovery point.

## 16. Release decision record

Complete this immediately before deployment:

| Decision | Recorded value |
| --- | --- |
| Approved release commit | |
| PHP web version / PHP CLI version | |
| Composer version / Laravel locked version | |
| Database engine/version | |
| Database backup filename/hash/restore check | |
| Media and storage backup filename/hash/restore check | |
| Newsletter duplicate query result | |
| SMTP and queue verification | |
| CMS/content approver | |
| Deployment window and deployer | |
| Smoke-test owner | |
| Rollback owner and previous release | |
| Final owner go/no-go approval | |

Phase 6 verification proves the recorded repository state, fresh and legacy strict-MariaDB migrations, approved data reconciliation, disposable backup/restore, relationship and CMS persistence, queue failure isolation, production cache compilation, locked dependency installation, reproducible frontend build, route rendering, and automated suite. It does not prove Hostinger account configuration, the live database/data quality, live SMTP/DNS/cron, production secrets, HTTPS proxy behavior, or third-party account ownership; those remain explicit owner deployment gates above.
