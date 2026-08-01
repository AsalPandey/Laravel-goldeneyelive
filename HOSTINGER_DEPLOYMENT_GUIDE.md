# Golden Eye Academy Production Release and Staff Handover

## Purpose and release boundary

This is the single deployment checklist for the release based on commit `2b29130e857827c93745cc3e94cc5eb1d53c567f` and the Phase 9 documentation commit that follows it. It covers release verification, backups, migration rehearsal, security, production CMS review, deployment, smoke testing, and rollback.

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
| Queue | Database queue is the documented default | Configure a persistent worker or an approved scheduled worker before relying on queued mail |
| Scheduler | No scheduled tasks are currently registered | No scheduler is required for this release; reassess when scheduled tasks are added |

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

The isolated rehearsal verified that the migration preserves representative users, roles, courses, categories, blogs, FAQs, faculty, testimonials, inquiries, and subscribers; normalizes newsletter email casing/whitespace; adds the unique index; and archives inquiry records through soft-delete fields. A deliberate duplicate caused the migration to stop before making those schema changes. This rehearsal used SQLite and does not replace the production backup or live MySQL/MariaDB preflight.

Before deployment, review pending migrations under the PHP 8.4 production configuration:

```bash
php artisan migrate:status
```

Apply only after the backup and duplicate gates pass:

```bash
php artisan migrate --force
```

Do not run `db:seed`, `migrate:fresh`, `migrate:refresh`, or any content-normalization command in production.

## 6. Production configuration checklist

Set secrets directly in the production environment. Never paste values into tickets, logs, this guide, Git, deployment archives, or screenshots.

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` is the exact canonical HTTPS production origin, with no staging hostname.
- [ ] `APP_KEY` is the existing production key; do not regenerate it on an existing encrypted application.
- [ ] Database host, port, name, user, password, charset, and collation are production-only and tested.
- [ ] `SESSION_SECURE_COOKIE=true`; session domain/path match the canonical host; HTTPS is enforced at the server/proxy.
- [ ] Cache, session, and queue drivers match available infrastructure and required database tables.
- [ ] SMTP host, port, encryption, username, password, sender name, and sender address are verified without exposing credentials.
- [ ] CAPTCHA public/secret keys belong to the exact production domain. A missing or partial key pair is a stop condition for protected forms.
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

### Mail and queue

Contact and course-interest records are saved before queued notification mail is attempted, but staff still need a reliable handover channel.

- [ ] Send a controlled SMTP test to an owner-approved address.
- [ ] Confirm password-reset mail and inquiry notifications arrive and are not marked as spam.
- [ ] Choose and document a persistent queue worker, Hostinger-supported worker/cron pattern, or the explicit `sync` fallback.
- [ ] If using a worker, start/restart it after deployment and monitor failed jobs.
- [ ] If using `sync`, acknowledge that mail runs during the visitor request even though inquiry persistence is protected.

No scheduled tasks are registered in this release. Do not add an unnecessary scheduler cron. Revisit this when `php artisan schedule:list` shows application tasks.

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

Run commands from the application directory under PHP 8.4. Adjust only the executable name/path required by Hostinger; do not alter command intent.

1. Reconfirm backups, duplicate preflight, CMS decision log, exact release commit, and rollback owner.
2. In hPanel, select PHP 8.4 for Golden Eye Academy. Confirm both the web runtime and SSH/Composer runtime report PHP 8.4.
3. Put the current site in maintenance mode during the approved window:

   ```bash
   php artisan down
   ```

4. Deploy only tracked application files from the approved commit. Preserve the production `.env`, persistent media, and storage.
5. Install exactly the locked production dependencies:

   ```bash
   composer install --no-dev --optimize-autoloader --no-interaction
   composer check-platform-reqs --no-dev
   composer audit --locked --no-dev
   ```

6. Confirm `public/build/manifest.json` and its referenced asset are present. Do not run `npm update`, `composer update`, or an unreviewed production build.
7. Clear stale caches, run approved migrations, create/verify the storage link, then cache production configuration:

   ```bash
   php artisan optimize:clear
   php artisan migrate --force
   php artisan storage:link
   php artisan optimize
   ```

8. Apply least-privilege write permissions to `storage/`, `bootstrap/cache/`, and the existing CMS upload folders.
9. Start/restart the approved queue mechanism and verify no immediate failed jobs.
10. Bring the site online:

   ```bash
   php artisan up
   ```

11. Execute every smoke test in section 11. Do not make broad CMS edits during smoke testing.

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

Do not use `git reset --hard`, `migrate:fresh`, database seeders, or destructive bulk deletion as a production rollback method.

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

## 14. Release decision record

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

Local Phase 9 verification proves the repository, isolated migrations, production dependency installation, committed frontend build, route rendering, and test suite at the recorded commit. It does not prove Hostinger account configuration, live PHP/database versions, live SMTP/DNS, production secrets, production data quality, or third-party account ownership; those remain explicit owner deployment gates above.
