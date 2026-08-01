# Golden Eye Academy Content Baseline and CMS Rollout

This document describes the approved Phase 5R–6R content baseline. It does not authorize a production rollout. Golden Eye Academy is the website's sole primary identity and was established in Pokhara in 2008. Brilliant Education Pokhara appears only on the study-abroad guidance page as Golden Eye Academy's education-consulting partner.

## Source of truth

- The shared PHP baseline supplies approved defaults to public rendering, fresh-install seeders, and the guarded rollout command.
- Existing live content remains in CMS database records. After the one-time approved rollout, Staff continue to manage routine public copy through the CMS.
- The 20 FAQ IDs, eight indexed blog slugs, 13 course slugs, five course categories, and seven service-pillar slugs are protected identities.
- Campaign popup, popup-style notice, and announcement-bar records are outside the permanent content rollout.
- Seeders represent the finished fresh-development checkpoint. They do not track later Staff edits and are never the latest live-content backup.

## Fresh non-production checkpoint

The baseline seeders are deterministic, idempotent, and production-guarded. They recreate the approved homepage, audience pages, About and footer copy, 20 FAQs, eight articles, protected course defaults, and service-pillar presentation for a fresh non-production installation.

Do not run baseline content seeders as part of routine production deployment. `DatabaseSeeder` remains restricted to its existing safe system purpose. The baseline seeders do not create users, passwords, roles, privileged credentials, secrets, or environment configuration.

## One-time CMS rollout

The rollout command is read-only unless `--apply` is supplied:

```text
php artisan goldeneye:publish-content-baseline
```

Before any future production apply:

1. Export the production database and verify that the backup can be read.
2. Back up uploaded media separately.
3. Record the deployed Git commit and current product, FAQ, article, notice, and setting inventories.
4. Run the command without `--apply` and review every proposed row and field.
5. Resolve every reported unexpected value with the owner. The command intentionally refuses the whole apply rather than overwriting an unrecognized Staff edit.
6. Use an explicit manifest path outside the public web root.
7. Apply only after the backup is verified:

```text
php artisan goldeneye:publish-content-baseline --apply --backup-confirmed --manifest=/secure/path/golden-eye-content-baseline.json
```

Production application requires `--backup-confirmed`. The command uses an exact allowlist, preserves identities and statuses, applies all changes in one database transaction, clears affected public-content caches, and writes a before-and-after manifest. It does not touch users, roles, permissions, products outside the protected copy fields, campaign records, dependencies, files, or environment settings.

Re-running the command after a successful apply must report zero guarded changes. The database backup—not the manifest or seeders—is the rollback mechanism.

## Staff ownership after rollout

Staff manage future approved public content through the existing CMS:

- Homepage sections, headings, supporting copy, audience cards, CTAs, and visibility controls
- Student, parent, study-abroad, and computer/job-skill audience pages
- About, footer, contact, catalogue, course-listing, blog-index, and FAQ-introduction copy
- Courses, articles, FAQs, faculty, testimonials, images, ordinary metadata, popup campaigns, notices, and the announcement bar

Keep unfinished articles in Draft and incomplete records Inactive. Preview articles and courses before publishing. Do not change indexed slugs, factual course fields, or sensitive SEO/configuration controls without owner approval.

Historical faculty and testimonial values that do not exactly match a current protected course or instructor remain preserved for owner review. Public proof sections stay hidden when an exact active relationship is unavailable. Staff must not guess or manufacture an association.

## Recovery

For a content-only rollback, restore the verified pre-rollout database backup and clear Laravel caches. Restore the matching media backup if media changed separately. Seeders must not be used to recover recent Staff edits, inquiries, uploads, campaign schedules, or production configuration.
