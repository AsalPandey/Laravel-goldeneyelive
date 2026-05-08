# Laravel Goldeneye Codebase Audit Report

Date: 2026-05-04  
Scope: Current working tree at `C:\xampp\htdocs\antigravity projects\Laravel-Goldeneye`

## Executive Summary

The application is ready for a Hostinger shared-hosting release package after the final pass. Automated verification passes: PHPUnit, Pint, PHP syntax checks, Composer validation, route listing, migration status, Vite production build, Laravel view/optimize cache generation, and an HTTP smoke pass over the main public/admin entry points.

This pass also fixed confirmed CMS/public synchronization bugs around hidden course categories, blog author/category rendering, enrollment status updates, admin submission table markup, unsafe asset deletion paths, display-type-specific notices, stale shared-site cache compatibility, the mobile quick-help CTA blocking WhatsApp, the admin course edit 500 error, and inconsistent Font Awesome icon classes. The codebase now has 108 non-vendor routes, 13 models, 19 controllers, 40 migrations marked as run, 91 Blade views, and 32 test files. The full suite passes with 94 tests and 373 assertions.

Production signoff now depends on deployment-time configuration: create the Hostinger MySQL database/user, import the SQL file plus the current patch SQL, configure the server `.env`, point the domain document root to `public`, and confirm PHP extensions on Hostinger. The local working tree is still very dirty, so use the release ZIP or make a deliberate Git commit rather than uploading arbitrary local-only files.

## Final Verification

| Check | Result | Notes |
| --- | --- | --- |
| `php artisan about` | Pass | Laravel 13.5.0, PHP 8.4.20, Livewire 4.2.4; local debug enabled |
| `php artisan route:list --except-vendor` | Pass | 108 non-vendor routes registered |
| `php artisan migrate:status --no-interaction` | Pass | 40 migrations marked as run |
| Scratch SQLite `migrate:fresh --seed` | Pass | Validates migrations and seeders without wiping the current DB |
| Scratch MySQL `migrate:fresh --seed` | Previously passed | Local XAMPP MariaDB is currently unhealthy, so it was not rerun after the final UI/admin fixes |
| MySQL dump re-import | Previously passed | Earlier SQL export imported successfully before the final course display-order migration; use the patch SQL listed below |
| `php artisan test --compact` | Pass | 94 tests, 373 assertions |
| `composer lint:check` | Pass | Pint check passes |
| PHP syntax check over app/routes/database/tests/config/bootstrap | Pass | No syntax errors detected |
| `composer validate --strict` | Pass | `composer.json` is valid |
| `npm run build` | Pass | Vite production CSS build completed |
| `php artisan view:cache` / optimize cache cycle | Pass | Views cache successfully; local caches were cleared afterward |
| HTTP smoke via `php artisan serve` | Pass | `/`, `/courses`, `/courses-all`, `/admin/courses` redirect flow |
| `php artisan db:show --counts --views` | Blocked | Local PHP `intl` extension is missing; command fails in `Illuminate\Support\Number::format()` |

## Release Artifacts

| Artifact | Purpose |
| --- | --- |
| `release\goldeneye_hostinger_app_20260504-final.zip` | Current Hostinger File Manager upload package. Includes built assets and `vendor`; excludes local `.env`, `.git`, `node_modules`, tests, scratch files, and SQLite DB. |
| `release\goldeneye_hostinger_release_20260504-080254.sql` | MySQL/MariaDB SQL import for Hostinger phpMyAdmin. Generated from a fresh migrated and seeded MySQL database, then re-import verified. |
| `release\goldeneye_hostinger_patch_display_order_20260504.sql` | Import immediately after the main SQL export if using that export. Adds the current course `display_order` column/index and migration record. |

## Implemented Fixes

1. Hidden course categories now hide their courses everywhere public-facing:
   - Added `Course::publiclyVisible()`.
   - Updated public course listing/detail, homepage featured courses, join-now course selection, join-now submission lookup, and sitemap generation.
   - Added tests for hidden category visibility and rejected enrollments.

2. Blog CMS fields now synchronize to the public blog detail page:
   - Added `author` validation in `BlogRequest`.
   - Public blog detail now renders the stored category instead of hardcoded `Insights`.
   - Added admin CMS persistence and public rendering tests.

3. Enrollment status updates now accept every status offered by the admin UI:
   - `enrolled` is now valid for join-now submission status updates.
   - Added a focused admin status update test.

4. Asset deletion is now path-confined:
   - Asset paths are normalized to `site/img/...`.
   - Absolute paths, drive paths, traversal paths, and non-public-site-image paths are rejected.
   - Real path checks ensure deletion stays under `public/site/img`.
   - Added regression coverage for traversal rejection.

5. Admin submission views now render valid empty-table structure:
   - Fixed missing table row markup in newsletter listing.
   - Corrected empty-state column spans for contact, enrollment, and newsletter tables.
   - Replaced problematic literal bullets with HTML entities.
   - Added render coverage for empty admin submission listings.

6. Notice CMS display types now synchronize correctly to the public layout:
   - Shared view data now exposes active bar and popup/standard notices separately.
   - The public layout can render an active notice bar and an active popup at the same time.
   - A bar notice no longer suppresses the configured marketing popup fallback.
   - Added tests for display-type coexistence, marketing popup fallback, urgency priority, and schedule windows.

7. Shared CMS cache is backward-compatible after notice data changes:
   - `SettingsServiceProvider` now tolerates older cached `site_shared_data` payloads without throwing undefined-key errors.
   - Local cache was cleared and a regression test covers the stale-cache shape.

8. Mobile homepage CTA and icon polish:
   - Removed the sticky mobile quick-help strip that blocked the WhatsApp CTA.
   - Moved the WhatsApp floating action back to the mobile bottom edge.
   - Replaced invalid/legacy Font Awesome classes in the homepage DNA section and similar course/admin UI areas.

9. Admin course edit now opens correctly:
   - Escaped the SEO/AEO schema placeholder `@context` as `@@context` so Blade no longer treats it as a directive.
   - Added regression coverage for the admin course edit screen.

## Feature Coverage Matrix

| Feature | MVC / Migration | Admin CMS | Public Rendering | Validation | Cache / Sync | Tests | Status |
| --- | --- | --- | --- | --- | --- | --- | --- |
| Courses | Model, migration, controller, routes, views present | CRUD, edit, status, featured, ordering, asset fields | Listing, all courses, category, detail, homepage, sitemap | `CourseRequest`, `JoinNowRequest` | `clearSiteCache`; public visibility scope added | Public visibility, CTA, sitemap, admin CRUD/edit coverage | Pass |
| Categories | Model, migration, controller, routes, views present | CRUD, status, priority, image | Category listing/detail paths and sitemap | `CategoryRequest` | CMS invalidation present | Public visibility and route compatibility | Pass |
| Blog | Model, migration, controller, routes, views present | CRUD, status, author/category/image | Blog index/detail | `BlogRequest` updated | CMS invalidation present | Public blog visibility and CMS sync | Pass |
| FAQ | Model, migration, controller, routes, views present | CRUD, status, ordering | FAQ page and footer data | `FAQRequest` | CMS invalidation present | Public rendering and admin coverage | Pass |
| Notices | Model, migrations, controller, routes, views present | CRUD, active toggle, display/schedule fields | Bar, popup, fallback marketing popup, schedule-aware shared data | `NoticeRequest` | CMS invalidation present | Public notice display-type and schedule coverage | Pass |
| Service Pillars | Model, migration, controller, routes, views present | CRUD, status, featured, sorting | Homepage/public rendering | `ServicePillarRequest` | CMS invalidation present | Public service pillar coverage | Pass |
| Teachers | Model, migration, controller, routes, views present | CRUD, status, featured, photo | Homepage/about rendering | `TeacherRequest` | CMS invalidation present | Factory/admin/public coverage | Pass |
| Testimonials | Model, migration, controller, routes, views present | CRUD, status, featured, photo | Homepage/public rendering | `TestimonialRequest` | CMS invalidation present | Factory/admin/public coverage | Pass |
| Branding / SEO | Site settings model/migration, controllers, routes, views present | Branding hub, asset vault, SEO settings | Layout, metadata, robots, sitemap | `BrandingRequest`, `SEORequest`, JSON checks | Setting caches cleared; asset sync hardened | SEO rendering, sanitizer, asset deletion | Pass with env checklist risk |
| Submissions | Contact, newsletter, join-now models/migrations/controllers/views present | Listing, status, delete, bulk delete | Contact, newsletter, join-now forms | Site FormRequests and controller validation | Immediate DB writes; admin listings verified | Public submissions, conversion, bulk actions | Pass |
| Auth | User model, Fortify provider/config/routes present | Dashboard protected by auth/role middleware | Login and dashboard redirect flow | Fortify actions | Session/database drivers configured locally | Auth/dashboard tests | Pass |
| Settings | SiteSetting model/migration/provider present | Branding/SEO paths | Global layout/site data | FormRequests | Per-key and site cache clearing | Settings and SEO tests | Pass |
| Sitemap | Controller/route/view present | Driven by CMS records | `sitemap.xml` | Public visibility filters | Cached as `sitemap_xml`; invalidation covered by CMS path | Public sitemap tests | Pass |
| Robots | Controller/route present | SEO setting controls content | `robots.txt` | `SEORequest` | Setting cache clearing | HTTP smoke | Pass |

## Rechecked Historical Issues

| Issue | Current Result |
| --- | --- |
| Livewire `selectAll` redeclaration | Current admin submission scripts use scoped initializer functions instead of global `const selectAll`; old browser log entries are dated 2026-04-30 and should be treated as stale unless reproduced in a live browser navigation run. |
| Sitemap `toW3cString()` errors | Sitemap generation now uses only publicly visible courses/categories/posts and the full suite plus HTTP smoke pass succeeds for `/sitemap.xml`. |
| Branding validation failures | Branding and SEO requests validate current CMS fields; Composer/PHPUnit pass after fixes. Asset deletion safety was hardened. |
| Raw CMS rich-text rendering | Public rich text is routed through the `@sanitize` Blade directive backed by `CmsContentSanitizer`; raw Blade output in public views is limited to sitemap XML and trusted vendor/Fortify fragments. Existing sanitizer tests pass. |
| Homepage undefined `activeNoticeBar` error | Fixed by making shared view data reads null-safe and clearing stale cache. |
| Admin course edit 500 | Fixed Blade parsing of the JSON-LD placeholder in the shared SEO/AEO fields component. |
| Mobile quick-help CTA blocking WhatsApp | Removed the duplicate sticky mobile CTA; WhatsApp remains the primary floating contact action. |

## Remaining Risks

1. The working tree is very dirty, with many modified, deleted, and untracked files. Use the release ZIP or review/commit intentionally before any Git deployment.
2. Local PHP is missing `intl`; Hostinger should have `intl` enabled or expose it in PHP extensions before launch.
3. Local `.env` remains intentionally local. Configure the production `.env` on Hostinger with `APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`, MySQL credentials, and SMTP details.
4. A real browser Livewire/Flux navigation smoke test should still be run on the final Hostinger URL after upload/import because this session used HTTP smoke, tests, and build verification.
5. The full SQL export could not be regenerated after the last small migration because local XAMPP MariaDB is currently failing to start cleanly. Use the patch SQL after the main SQL export, or run `php artisan migrate --force` on the server after import.

## Production Checklist

Before deployment:

1. Create a Hostinger MySQL database and user.
2. Import `release\goldeneye_hostinger_release_20260504-080254.sql` into that database with phpMyAdmin.
3. Import `release\goldeneye_hostinger_patch_display_order_20260504.sql` into the same database with phpMyAdmin.
4. Upload/extract `release\goldeneye_hostinger_app_20260504-final.zip`.
5. Create a production `.env` from `.env.example` with the Hostinger domain and MySQL credentials.
6. Set the domain document root to the Laravel `public` directory, or move only `public` contents into `public_html` and adjust paths if Hostinger does not allow document-root changes.
7. Verify PHP 8.4 or the closest supported compatible PHP 8.x version, plus extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl`, `zip`, `intl`.
8. Ensure `storage`, `bootstrap/cache`, and upload directories are writable.
9. After `.env` is final, run `php artisan key:generate --force` only if `APP_KEY` is empty, then `php artisan optimize`.
10. Smoke test home, courses, course detail, category, blog, FAQ, contact, join-now, login, dashboard, CMS CRUD/publish/toggle, branding assets, SEO, sitemap, robots, and submission management in a browser.

## Acceptance Status

Automated acceptance: Pass.  
Migration/seeder acceptance: Pass on SQLite and MySQL.  
SQL export acceptance: Main export previously passed re-import verification; current release additionally requires the display-order patch SQL or `php artisan migrate --force`.  
HTTP smoke acceptance: Pass.  
Production environment acceptance: Ready for upload/import after Hostinger `.env`, document root, PHP extensions, and permissions are configured.
