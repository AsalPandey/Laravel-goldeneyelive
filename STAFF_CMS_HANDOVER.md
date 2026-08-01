# Golden Eye Academy CMS Staff Guide

This guide covers the existing staff-managed website tools. Staff accounts can create, edit, preview, publish, hide, and schedule public content. Permanent deletion remains an Admin-only responsibility.

## Login and password reset

- Use the academy's Admin login page with your assigned Staff account.
- Never share accounts or passwords.
- Use **Forgot password** on the login page if you cannot sign in, then follow the emailed reset link.
- Ask an Admin to check your account if the reset email does not arrive; do not ask anyone to send you their password.

## Before changing public content

1. Confirm you are editing the correct page or record.
2. Keep unfinished work in Draft or Inactive status.
3. Use Preview for blog articles and courses before publishing.
4. Check headings, links, images, spelling, mobile readability, and the inquiry action.
5. Publish only after the content owner approves the final version.

Validation errors appear above the form or beside the affected field. Correct every listed field before submitting again; the form keeps submitted values where supported.

## Homepage and audience pages

Open **Website Content → Page Content**.

- **Homepage Sections & Cards** controls homepage headings, descriptions, actions, audience cards, and section visibility.
- **Audience Landing Pages** controls the student, parent, IELTS/PTE/language, and computer/job-skill pages.
- Sections and pages are visible by default.
- Hiding a section retains its saved copy.
- Hiding a whole audience page returns a public 404 and removes that page from the sitemap.
- Use the Media Library picker or upload field for supported page images.

Do not leave a visible section with incomplete text. Hide it until it is ready.

## Blog workflow

1. Open **Blog → Compose Article**.
2. Add the title, article body, author, featured image, and supported SEO fields.
3. Keep Status as **Draft** while writing.
4. Save, then use **Preview** from the article list or edit page.
5. Confirm the real public layout, image, headings, links, and mobile readability.
6. Set Status to **Published** when approved.
7. Leave the publish time blank for immediate publication, or enter a future Nepal time to schedule it.
8. Return to Draft to remove an article from public view without permanently deleting it.

## Course, FAQ, faculty, and testimonial workflow

- Courses: keep incomplete courses **Inactive**, use Preview, and activate only when the public details are complete. Confirm the category, slug, fee, duration, instructor value, image, outline, and public actions.
- Categories: keep incomplete categories **Inactive**, use lower order numbers for earlier display, and do not change a category slug after public links use it without owner approval.
- FAQs: use a lower order number to display an FAQ earlier. Keep incomplete FAQs **Inactive**.
- Faculty: a faculty profile appears on a course page only when the active faculty name exactly matches the course instructor value.
- Testimonials: a testimonial appears on a course page only when its active course name exactly matches the course name.
- If an exact faculty or testimonial relationship is not available, the public section stays hidden.

## Campaign popup and notices

There are three existing display controls:

1. **Main Campaign Popup** — the image-led campaign popup under **Website Content → Marketing Tools**.
2. **Popup-style notice** — a notice record whose display style is Popup.
3. **Top announcement bar** — a notice record whose display style is Top announcement bar.

Only one active notice per display style is used. Keep a new notice Inactive until its title, button, destination, image, and schedule have been checked. Changing notice content does not change the Main Campaign Popup.

## Scheduling and timezone

- Staff enter and view blog and notice schedules in **Asia/Kathmandu (Nepal time)**.
- The application stores schedule timestamps in **UTC**.
- Production must keep the application timezone in `config/app.php` set to `UTC`; do not change it to Nepal time.
- Public visibility starts and ends according to the converted UTC timestamp.

## Links, media, and SEO

- Use public internal paths such as `/courses-all` or a complete `https://` URL.
- Never use an `/admin` destination in public content.
- Preview every new destination before publishing.
- Reuse an existing Media Library asset when suitable.
- Do not remove an asset that is still in use.
- Add a clear SEO title and description when those fields are available.

## Role handover

- **Staff:** routine content creation, editing, preview, publish/hide status, ordering, scheduling, and approved media selection.
- **Admin:** Staff capabilities plus permanent CMS record deletion, destructive asset operations, SEO infrastructure, analytics, CAPTCHA, and other sensitive configuration.

Use Draft or Inactive status for normal content withdrawal. Permanent removal should be rare and must be handled by an Admin after checking dependencies and backups.

Contact the owner or developer before changing a public slug, replacing an in-use media file, correcting factual course details, changing sensitive configuration, or acting on an unexpected error. Also escalate any legacy faculty/testimonial mismatch instead of inventing an association.

## Backup and recovery checklist

Before a release, major content import, destructive Admin action, or dependency change:

- Export the live database with a timestamp and verify the export is readable.
- Back up uploaded/public media, especially `public/site/img` and any user-upload directory under `storage/app`.
- Record the deployed Git commit and preserve the matching source release.
- Back up production environment and server configuration securely; never commit secrets.
- Store backups outside the web root with restricted access.
- Keep more than one dated recovery point according to the academy's retention policy.
- Periodically restore a backup into a disposable environment and verify public pages, CMS login, content, and media.

Seeders are baseline and recovery checkpoints for known project content. They are **not** a backup of current live CMS records, inquiries, uploaded media, or production configuration.

## Phase 5R–6R baseline ownership

The approved content baseline establishes Golden Eye Academy as the website's sole primary identity and uses the owner-confirmed wording **Established in Pokhara since 2008** selectively. Brilliant Education Pokhara is mentioned only on the study-abroad guidance page as Golden Eye Academy's education-consulting partner.

The baseline seeders are for fresh non-production installations. They are deterministic checkpoints for the development-finished website; they do not update themselves when Staff edit live content and must not be run during routine production deployment. Current database and media backups remain necessary to recover recent Staff work.

The one-time `goldeneye:publish-content-baseline` command is dry-run by default and refuses unexpected CMS values. A future production apply requires a verified backup, explicit `--apply` and `--backup-confirmed` flags, and an applied-change manifest. See `PHASE_5R_6R_CONTENT_BASELINE.md` for the controlled rollout and recovery checklist.
