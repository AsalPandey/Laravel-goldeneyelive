# GoldenEye Academy Production Launch Upgrade Documentation

Date: May 20, 2026  
Project: Laravel-Goldeneye  
Location: `C:\xampp\htdocs\antigravity projects\Laravel-Goldeneye`

## Executive Summary

GoldenEye Academy was upgraded through a phased production-readiness process from Phase 0 through Phase 10. The work preserved the existing Laravel architecture and focused on fixing production blockers first, then improving public conversion flow, mobile usability, trust proof, course clarity, admin reliability, and lead tracking.

The main outcome is a more stable, student-friendly, parent-friendly website that captures better-qualified inquiries and gives administrators clearer lead data for follow-up.

Core improvements included:

- Full codebase and routing audit before edits.
- Course and category admin edit errors fixed.
- Course Help submissions connected to admin enrollment visibility.
- Two-step inquiry form with Nepal phone validation.
- Lead attribution, scoring, and status classification.
- Simplified navigation and mobile flow.
- Standardized CTA labels across the public site.
- Decluttered homepage conversion arc.
- Deeper course detail pages for student and parent decision-making.
- Structured testimonials, instructor proof, and local trust markers.
- Accessibility and mobile usability improvements.
- Clean course routing and admin panel hardening.
- First-party analytics event tracking with fail-safe behavior.

## Stack Confirmed

Phase 0 confirmed this is a Laravel application, not WordPress or a separate SPA.

Confirmed stack:

- Framework: Laravel 13
- PHP: 8.4
- Authentication: Laravel Fortify
- Frontend rendering: Blade templates
- Frontend interactivity: Blade, JavaScript, and Alpine-style behavior where present
- Styling: Bootstrap-based public assets, custom CSS, Tailwind v4 available through Vite
- Admin system: Laravel admin routes, controllers, Blade views, authenticated access
- Database: Laravel migrations and Eloquent models
- Build tools: Composer, Vite, npm tooling
- Tests: PHPUnit through `php artisan test`

Key application areas:

- Public controllers: `app/Http/Controllers/Site`
- Admin controllers: `app/Http/Controllers/Admin`
- Form requests: `app/Http/Requests`
- Models: `app/Models`
- Public views: `resources/views/site`
- Admin views: `resources/views/admin`
- Routes: `routes/web.php`
- Public CSS: `public/site/css/style.css`
- Migrations: `database/migrations`
- Tests: `tests/Feature`

## Phase 0: Full Codebase Structure Audit

Phase 0 was read-only. No files were edited.

The audit mapped:

- Public routes, course routes, blog routes, contact routes, inquiry routes, and admin routes.
- Homepage, header, mobile navigation, footer, course listing, course detail, contact, about, blog, and legal view locations.
- Reusable components for course cards, CTAs, forms, popups, WhatsApp widgets, testimonials, instructors, and FAQs.
- Course Help, Ask for Course Help, contact, newsletter, and optional details form flows.
- Admin dashboard, course/category management, enrollments, inquiry/lead views, newsletter views, search, and edit/delete controls.
- Main CSS files, responsive styles, popup styles, form styles, and widget styles.

Main risks found:

- Course/category edit flows were fragile and could trigger 500 errors.
- Course Help submissions were not reliably appearing in admin enrollment lists.
- Optional "Add more details" UI did not work.
- Phone fields accepted invalid values.
- Public CTA labels were inconsistent.
- Course card routing and some legacy course URLs were inconsistent.
- Homepage content was crowded and repetitive.
- Course detail pages did not answer enough buying-decision questions.
- Popup and WhatsApp widgets could interfere with mobile flow.
- Some admin search and delete controls were unclear.
- Analytics and lead attribution were incomplete.

## Phase 0.5: Critical Functional Bug Triage

Phase 0.5 focused only on production-blocking functional bugs.

### Course And Category Editing

Root cause:

- Course and category edit/update flows had incomplete validation and fragile assumptions around fields such as slug, category, duration, and public display data.
- Missing or duplicate values could crash or fail unfriendly.

Fixes applied:

- Hardened course and category request validation.
- Added slug validation with uniqueness rules that ignore the current record on update.
- Ensured required course fields are validated before saving.
- Normalized slug handling.
- Ensured edit pages load existing data safely.
- Ensured update requests show validation errors instead of crashing.
- Added visible slug fields to create/edit flows where needed.

Result:

- Course edit page opens without 500.
- Course update saves correctly.
- Category edit page opens without 500.
- Category update saves correctly.
- Friendly validation errors appear for invalid input.

### Course Help Submissions

Root cause:

- The frontend Course Help form, backend storage, and admin enrollment list were not consistently aligned.
- Submitted help data could be stored in a table that the admin enrollment list did not surface clearly, or context fields were missing.

Fixes applied:

- Traced the Course Help submission path from frontend to controller, request validation, model, table, and admin display.
- Aligned form field names with backend validation.
- Stored Course Help submissions in the correct lead/enrollment table used by admin.
- Added safe defaults for optional fields.
- Updated admin enrollment display to show submitted help topic, selected course, source, status, and timestamps.
- Added success feedback after submission.

Result:

- Course Help form submissions now appear in the admin enrollments/lead list.

### Add More Details

Root cause:

- The optional details control existed visually but did not properly toggle the optional field panel.

Fixes applied:

- Converted the control into an accessible button.
- Added JavaScript behavior to reveal optional fields.
- Updated button state and accessibility attributes.
- Kept the behavior compatible with later two-step form work.

Result:

- Clicking "Add more details" reveals optional fields on desktop and mobile.
- Keyboard interaction works.
- No console errors were introduced.

### Phone Validation

Root cause:

- Phone inputs accepted random text, symbols, and unrealistic values.
- Validation was not enforced consistently on the server.

Fixes applied:

- Added client-side validation.
- Added server-side validation.
- Accepted realistic Nepal mobile formats:
  - `98XXXXXXXX`
  - `97XXXXXXXX`
  - `+97798XXXXXXXX`
  - `+97797XXXXXXXX`
- Rejected letters, random symbols, too-short values, too-long values, and obvious fake numbers.

Result:

- Valid Nepal mobile numbers pass.
- Invalid values fail with clear error messages.
- Server validation still rejects invalid phone numbers if frontend validation is bypassed.

## Phase 1: Navigation, Popup, And Mobile Flow Cleanup

Phase 1 focused only on navigation, header, mobile menu, popups, and floating widgets.

Changes applied:

- Simplified desktop navigation to:
  - Home
  - Courses
  - Study Abroad
  - Computer Skills
  - Languages
  - About
  - Contact
- Simplified Courses dropdown to:
  - All Courses
  - IELTS / PTE
  - Japanese / Korean
  - Computer & Office Skills
  - Web Development
  - Course Guidance
- Simplified mobile hamburger menu to:
  - Home
  - Courses
  - Study Abroad
  - Computer Skills
  - Languages
  - Contact
  - Message on WhatsApp
- Disabled immediate screen-blocking entry popup behavior.
- Converted popup behavior to intent-based opening instead of page-load interruption.
- Kept only one WhatsApp floating action on mobile.
- Adjusted WhatsApp placement so it does not cover forms, submit buttons, course cards, footer links, or bottom navigation areas.
- Improved tap target sizing for mobile navigation and buttons.

Result:

- Desktop navigation is shorter and clearer.
- Mobile navigation is easier to scan and tap.
- No popup appears immediately on first page load.
- WhatsApp widget no longer competes with key form actions.

## Phase 2: CTA Standardization

Phase 2 focused on button labels, hierarchy, and CTA routing behavior.

Approved public CTA labels:

- Ask for Course Guidance
- View Course Details
- Message on WhatsApp

Changes applied:

- Replaced public-facing inconsistent CTA labels such as:
  - Ask as a Student
  - Ask About Abroad Prep
  - Ask About Skills
  - Ask the Team
  - Ask What Fits Me
  - Plan My Path
  - Join The Network
  - Explore Programs
  - See Courses
  - Join Now
  - Register Now
  - View Roadmap
  - Ask About This Course
  - Enroll Now
  - Get Started
  - Contact Us where used as a primary CTA
- Standardized inquiry actions to "Ask for Course Guidance".
- Standardized course browsing actions to "View Course Details".
- Standardized WhatsApp/direct message actions to "Message on WhatsApp".
- Updated course cards:
  - Primary action: View Course Details
  - Secondary action: Ask for Course Guidance
- Preserved lead context on inquiry links:
  - `selected_course`
  - `source_page`
  - `source_section`
  - `audience_type`
  - `inquiry_intent`
- Improved button consistency, spacing, contrast, and mobile behavior.

Result:

- Public CTAs now use one predictable three-label system.
- Course cards send users to course detail pages before asking them to inquire.
- Inquiry CTAs preserve source context for admin follow-up and analytics.

## Phase 3: Homepage Conversion Arc And Layout Decluttering

Phase 3 focused only on homepage structure, content density, visual hierarchy, and homepage flow.

Homepage flow implemented:

1. Hero Section
2. Trust Strip
3. Audience Selector
4. Popular Courses
5. Course Categories
6. Why GoldenEye / Local Advantage
7. Student Results and Testimonials
8. Instructor Credentials
9. Parent Clarity Section
10. FAQ
11. Final CTA

Hero copy applied:

- Headline: Study Abroad, Language & Computer Courses in Pokhara
- Subheadline: GoldenEye Academy helps students choose the right course, prepare with expert guidance, and build practical skills for study, work, and career growth.

Hero CTAs:

- Ask for Course Guidance
- View Course Details

Trust strip content:

- Trusted by students in Pokhara since 2008
- 15+ years of guidance
- IELTS, PTE, Japanese, Korean, Computer & Web courses
- Morning, day, and evening batches

Audience selector cards:

- I am a Student
- I am a Parent
- I am planning Study Abroad
- I want Job/Computer Skills

Decluttering changes:

- Replaced long paragraphs with short blocks.
- Used bullets and compact cards where useful.
- Added more whitespace between sections.
- Kept each section focused on one decision-making purpose.
- Removed repeated sales claims.
- Avoided showing forms before users saw course and trust context.
- Removed aggressive or unverifiable claims.

Final CTA:

- Headline: Still confused about which course fits you?
- Body: Send your goal. Our team will guide you before enrollment.
- Buttons:
  - Ask for Course Guidance
  - Message on WhatsApp

Result:

- Homepage is lighter, more skimmable, and more conversion-focused.
- Students and parents both get relevant information before being asked to inquire.

## Phase 4: Course Detail Page Deepening

Phase 4 focused only on course detail pages and reusable course components.

Course detail structure implemented:

1. Course Hero
2. Quick Facts
3. Who This Course Is For
4. Student View / Parent View Toggle
5. What You Will Learn
6. Week-by-Week Curriculum
7. Batch Timing and Fee
8. Instructor Profile
9. Student Result / Testimonial
10. FAQs
11. Inquiry CTA

Course Hero now shows:

- Course name
- One-line outcome
- Best for
- Duration
- Fee when available
- Next batch when available
- Ask for Course Guidance
- Message on WhatsApp

Quick Facts now show:

- Duration
- Fee
- Level
- Batch timing
- Mode
- Certificate or support details when available

Student View highlights:

- Curriculum
- Practical assignments
- Mock tests or practice
- Feedback/support
- Career or academic outcome

Parent View highlights:

- Total fee
- Duration
- Batch timing
- Instructor credentials
- Safety/trust
- Included support
- What is not guaranteed

Instructor profile structure:

- Name
- Photo
- Subject expertise
- Years of experience
- Short credibility note

Student proof structure:

- Student photo
- Name
- Course completed
- Specific progress/result
- Short testimonial

Result:

- Course pages now answer the practical questions students and parents ask before contacting an academy.
- Inquiry CTAs pass selected course context.
- Student/Parent view toggle keeps the page detailed without making it feel overloaded.

## Phase 5: Lead Form, Course Help, And Enrollment Flow Optimization

Phase 5 focused only on inquiry forms, Course Help form, validation, submission storage, admin visibility, and optional details behavior.

### Two-Step Inquiry Form

The main Course Help / Ask for Course Help form was converted into a two-step flow.

Step 1 fields:

- Name
- Phone / WhatsApp
- What do you need help with?

Help options:

- Choosing a course
- IELTS / PTE
- Japanese / Korean
- Computer skills
- Web development
- Fees and timing
- Parent inquiry
- Other

Step 2 optional fields:

- Email
- Current education level
- Preferred course
- Preferred batch time
- Goal
- Message

Result:

- Users can submit quickly with low friction.
- Motivated users can add more information without being forced into a long form.

### Contextual Prefill

Inquiry links now preserve:

- `source_page`
- `source_section`
- `selected_course`
- `audience_type`
- `inquiry_intent`

This context can come from:

- Course cards
- Course detail pages
- Audience cards
- Parent section
- Study abroad section
- Homepage CTAs

### Lead Scoring

Lead score rules implemented:

- +5 if preferred course selected
- +5 if phone provided
- +4 if batch timing selected
- +4 if fee/timing inquiry
- +3 if parent inquiry
- +3 if study abroad selected
- +2 if message length is more than 30 characters

Lead status classification:

- Hot: 15+
- Warm: 8-14
- Basic: 0-7

### Admin Visibility

Admin enrollment/lead displays now include useful context such as:

- Name
- Phone
- Help topic
- Preferred course
- Source page
- Source section
- Audience type
- Inquiry intent
- Lead score
- Lead status
- Created timestamp

Result:

- Course Help submissions are visible in admin.
- Staff can prioritize stronger leads.
- Staff can understand why the user contacted the academy.

## Phase 6: Trust Proof And Credibility

Phase 6 focused on homepage trust sections, course trust sections, testimonials, instructor proof, local credibility, and external proof.

### Student Testimonials

Added structured testimonial support for:

- Real student photo
- Student name
- Course completed
- Specific result/progress
- Short testimonial

### Instructor Credentials

Added structured instructor proof for:

- Instructor name
- Photo
- Subject expertise
- Years of experience
- Courses taught
- Short credibility note

### Local Trust Markers

Added or surfaced local proof:

- Trusted by students in Pokhara since 2008
- 15+ years of guidance
- Srijana Chowk, Pokhara, Nepal
- Phone number and email
- Morning, day, and evening batches

### Removed Or Replaced Unverifiable Claims

Removed or replaced fake-looking or risky claims such as:

- Unverified `4.9/5` rating-style claims.
- Placement percentage claims unless verified.
- Overbroad "best academy", "guaranteed success", or similar marketing-heavy phrasing.

Safer replacements focused on:

- Local experience.
- Course support.
- Mock test completion.
- Student progress.
- Instructor guidance.
- Transparent next steps.

### Google Review Proof Support

Added support for external proof through:

- Google Business Profile URL when available.
- Admin-editable proof area or review screenshot support when a direct profile link is not available.

Result:

- Trust sections are more believable.
- Parents see local, practical credibility signals.
- Public pages avoid claims that could damage trust or create reputation risk.

## Phase 7: Accessibility And Mobile QA

Phase 7 focused only on readability, contrast, tap targets, forms, dropdowns, widgets, and responsive layout.

Changes applied:

- Improved dropdown text contrast.
- Improved contact form button readability.
- Added clearer input borders and focus states.
- Improved text contrast on colored backgrounds.
- Ensured key tap targets meet at least 44px height.
- Reduced cramped button layouts on mobile.
- Improved form field spacing and usability on small screens.
- Ensured WhatsApp widget does not overlap key actions.
- Confirmed no screen-blocking popup appears on first page load.
- Kept mobile menu simple.
- Added or improved alt text for meaningful images.
- Improved semantic heading flow where relevant.
- Connected labels and errors more clearly on forms.
- Improved visible keyboard focus states.

Result:

- Contact and inquiry forms are more readable.
- Buttons and navigation are easier to use on mobile.
- Keyboard users have clearer focus visibility.
- Mobile users can complete inquiries without layout obstruction.

## Phase 8: Routing And Admin Panel Fixes

Phase 8 focused on public routing, course URLs, breadcrumbs, admin course/category management, admin search, and backend stability.

### Public Routing

Changes applied:

- Course detail pages use clean URLs: `/courses/{slug}`.
- Course detail pages no longer load under `/blog`.
- Course card "View Course Details" actions open the correct course detail pages.
- "Ask for Course Guidance" opens the inquiry form with selected course context.
- Fixed or redirected suspicious/broken route patterns such as:
  - All Courses routes
  - All Career Paths routes
  - `/courses-all`
  - legacy catalogue route variations
- Breadcrumbs now:
  - Use clickable segments.
  - Show real category names.
  - Avoid misleading generic categories.

### Admin Course And Category Management

Changes applied:

- Course/category edit 500 errors were kept resolved from earlier fixes.
- Course table includes visible Edit and Delete controls.
- Category table includes visible Edit and Delete controls.
- Delete actions include confirmation.
- Admin search was made clearer and more direct.
- Course create/edit validation now prevents empty or invalid records.

Course validation includes:

- Course name required.
- Slug required and unique.
- Category required.
- Duration required where publicly displayed.
- Fee required where publicly displayed.
- Empty course records blocked.

### Lead, Newsletter, And Enrollment Storage

Confirmed or improved:

- Newsletter submissions save.
- Inquiry forms save.
- Course Help submissions save.
- Admin dashboard/list pages display submissions.

Result:

- Course URLs are predictable and SEO-friendly.
- Course detail pages do not conflict with blog routes.
- Admin can manage courses and categories without developer help.
- Validation prevents fragile content records.

## Phase 9: Student And Parent Messaging Cleanup

Phase 9 focused only on copywriting and messaging clarity.

Messaging changes:

- Rewrote generic marketing-heavy copy into simple student-benefit language.
- Added parent-specific language around trust, safety, fees, timing, course fit, and realistic outcomes.
- Kept the three approved CTA labels unchanged.
- Removed overpromising and fake urgency.

Student messaging examples:

- "Not sure which course to choose?"
- "Tell us your goal. We will help you compare options before enrollment."
- "Prepare with mock tests, practical assignments, instructor feedback, and clear weekly progress."

Parent messaging examples:

- "For parents, we explain course fit, fees, timing, expected outcomes, and realistic next steps before enrollment."
- "No pressure. Visit, call, or message us to understand the best option for your child."

Course page copy now answers:

- Who is this course for?
- What will the student learn?
- What is the fee?
- What is the duration?
- What are the batch timings?
- Who teaches it?
- What support is included?
- What is not guaranteed?

Seed/content cleanup examples:

- "IELTS Masterclass for Band 7+" became "IELTS Preparation for Band 7 Goal".
- "PTE Elite Academic Training" became "PTE Academic Preparation".
- "Free Course Roadmap Help" became "Course Guidance Before Enrollment".
- "New Batches Open This Month" became "Ask About Current Batch Timings".
- "Admissions Open" became "Batch Help".

Avoided wording:

- Guaranteed success.
- Best academy.
- World-class.
- Life-changing.
- Fake urgency.
- Overpromising placement or visa outcomes.

Result:

- Copy feels more local, useful, and believable.
- Parent concerns are addressed directly.
- Students can understand what they will learn and what to do next.

## Phase 10: Analytics And Lead Tracking

Phase 10 focused on useful conversion tracking and lead source attribution.

### Existing Analytics Check

Found existing Google Analytics support through a configurable `google_analytics_id` setting. No active Meta Pixel implementation was identified.

### First-Party Analytics Layer

Added fail-safe first-party analytics support:

- Analytics event model.
- Analytics events migration.
- Analytics event controller.
- Analytics helper Blade partial.
- Public analytics event route.
- Client-side `window.goldenEyeTrack` helper.

Tracking is optional and fail-safe. If tracking fails, the public website continues to work.

### Events Tracked

Implemented tracking for:

- `cta_click`
- `course_detail_view`
- `inquiry_step_1_submit`
- `inquiry_step_2_submit`
- `course_help_submit`
- `add_more_details_click`
- `whatsapp_click`
- `phone_click`
- `course_filter_used`
- `parent_inquiry_click`
- `study_abroad_inquiry_click`
- `admin_lead_status_change`
- `phone_validation_error`

### Event Data Captured

Every event can capture:

- `source_page`
- `source_section`
- `cta_label`
- `selected_course`
- `audience_type`
- `inquiry_intent`
- `device_type`
- `timestamp`

Additional metadata is stored in a flexible metadata field.

### Inquiry Tracking

Inquiry submissions store:

- Lead score
- Lead status
- Selected course
- Source page
- Source section
- Audience type
- Inquiry intent

Lead status values:

- Hot
- Warm
- Basic

### Privacy-Safe Validation Tracking

Invalid phone attempts are tracked only as anonymous validation error counts. Raw invalid phone numbers are not stored in analytics events.

Result:

- CTA clicks, course views, form progress, WhatsApp clicks, phone clicks, Course Help submissions, and Add More Details clicks can be tracked.
- Admin lead source and lead status are visible.
- Tracking failure does not break website behavior.

## Database Changes

### `join_now_queries`

Lead qualification and attribution fields were added:

- `help_topic`
- `current_education_level`
- `preferred_batch_time`
- `goal`
- `selected_course`
- `source_page`
- `source_section`
- `audience_type`
- `inquiry_intent`
- `lead_score`
- `lead_status`

### `analytics_events`

New first-party analytics event table added with fields for:

- Event name
- Source page
- Source section
- CTA label
- Selected course
- Audience type
- Inquiry intent
- Device type
- Metadata
- Timestamp

## Admin Changes

Admin improvements included:

- Course edit stability.
- Category edit stability.
- Clear validation on create/update.
- Visible edit/delete actions.
- Delete confirmation.
- Clearer course/category search.
- Enrollment/lead list showing submitted Course Help data.
- Lead score and lead status visibility.
- Source and CTA context visibility.
- Newsletter/contact submissions retained and displayed.
- Admin lead status change tracking.

## Public UX Changes

Public website improvements included:

- Shorter desktop navigation.
- Simpler mobile menu.
- No immediate entry popup.
- One mobile WhatsApp floating action.
- CTA label standardization.
- Homepage conversion arc.
- More skimmable homepage sections.
- Deeper course detail pages.
- Student/Parent course page toggle.
- Two-step inquiry form.
- Better phone validation.
- Structured proof and credibility sections.
- More accessible forms, buttons, dropdowns, and widgets.
- Cleaner course URLs.
- Context-preserving inquiry links.

## Testing And QA Evidence

The final full automated test run passed:

```text
php artisan test --compact
120 passed
687 assertions
```

Formatting was run after PHP changes:

```text
vendor\bin\pint --dirty --format agent
```

Notable test coverage included:

- Analytics event tracking.
- Course Help submission storage.
- Lead scoring and lead status.
- Phone validation.
- Public CTA rendering.
- Public course visibility.
- Public route compatibility.
- Admin course/category management.
- Admin cache invalidation.
- Admin bulk actions.
- Newsletter/contact submission behavior.

## Manual QA Checklist

Use this checklist before production launch.

### Public Navigation

- Desktop navigation shows only the simplified links.
- Courses dropdown contains the approved category labels.
- Mobile menu is short and tap-friendly.
- Message on WhatsApp appears once on mobile.
- No popup appears immediately on page load.

### Homepage

- Hero clearly explains GoldenEye Academy.
- Trust strip appears near the top.
- Audience selector cards are visible and usable.
- Popular course cards show practical details.
- Parent clarity section is present.
- Final CTA uses approved labels.
- Mobile layout has no overlapping sections.

### Course Pages

- Course URLs use `/courses/{slug}`.
- No course detail page appears under `/blog`.
- Breadcrumbs are clickable and accurate.
- Course hero shows fee, duration, timing, and best-for details where available.
- Student View and Parent View toggle works.
- Inquiry CTA preserves selected course context.

### Forms

- Course Help form submits successfully.
- Add More Details reveals optional fields.
- Valid Nepal mobile numbers pass.
- Invalid phone values fail.
- Success message appears after submission.
- Submitted lead appears in admin.
- Source, selected course, help topic, lead score, and lead status appear in admin.

### Admin

- Course edit page opens.
- Course update saves.
- Category edit page opens.
- Category update saves.
- Validation errors are friendly.
- Search is clear.
- Edit/delete controls are visible.
- Delete confirmation appears.
- Newsletter submissions appear.
- Inquiry/Course Help submissions appear.

### Analytics

- CTA clicks create analytics events.
- Course detail views create analytics events.
- WhatsApp clicks create analytics events.
- Add More Details clicks create analytics events.
- Course Help submissions create analytics events.
- Invalid phone attempts are tracked without storing the invalid phone value.
- Tracking failure does not interrupt user flow.

## Deployment Checklist

Before production launch:

1. Run migrations:

   ```text
   php artisan migrate
   ```

2. Build frontend assets if deployment requires compiled assets:

   ```text
   npm run build
   ```

3. Confirm production environment:

   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - Correct database credentials
   - Correct mail configuration
   - Correct queue/cache/session drivers

4. Configure analytics settings:

   - Add `google_analytics_id` if Google Analytics is desired.
   - Keep analytics tracking enabled only if the academy wants first-party event storage.

5. Confirm public content:

   - Fees
   - Batch timings
   - Instructor names
   - Course durations
   - Phone number
   - Email
   - Srijana Chowk location details

6. Replace placeholder proof content:

   - Real student photos
   - Real testimonials
   - Real instructor photos
   - Google Business Profile link or review screenshots

7. Run final smoke tests:

   - Homepage
   - Course listing
   - Course detail
   - Contact form
   - Course Help form
   - WhatsApp link
   - Admin course edit
   - Admin category edit
   - Admin enrollment list
   - Analytics event capture

## Files And Areas Most Affected

Primary public views:

- `resources/views/site/index.blade.php`
- `resources/views/site/pages/courses/course-detail.blade.php`
- `resources/views/site/layout/app.blade.php`
- `resources/views/site/layout/navbar.blade.php`
- `resources/views/site/pages/join-now.blade.php`

Primary admin views:

- `resources/views/admin/courses`
- `resources/views/admin/course-category`
- `resources/views/admin/join-now-display`
- `resources/views/admin/contact`
- `resources/views/admin/newsletter`

Primary controllers and requests:

- `app/Http/Controllers/Site/CoursesController.php`
- `app/Http/Controllers/Site/ContactController.php`
- `app/Http/Controllers/Site/AnalyticsEventController.php`
- `app/Http/Controllers/Admin/CourseController.php`
- `app/Http/Controllers/Admin/CourseCategoryController.php`
- `app/Http/Requests/JoinNowRequest.php`
- `app/Http/Requests/CourseRequest.php`
- `app/Http/Requests/CategoryRequest.php`

Primary models:

- `app/Models/Course.php`
- `app/Models/CourseCategory.php`
- `app/Models/JoinNowQuery.php`
- `app/Models/AnalyticsEvent.php`

Primary styling:

- `public/site/css/style.css`

Primary routing:

- `routes/web.php`

Primary migrations:

- Lead qualification fields migration for `join_now_queries`
- Analytics events migration for `analytics_events`

## Remaining Recommendations

Recommended follow-up items:

- Replace all placeholder student and instructor photos with real approved images.
- Add a verified Google Business Profile link when available.
- Add review screenshot management if Google reviews are not directly embedded.
- Review privacy policy language for first-party analytics event storage.
- Consider adding an admin analytics dashboard for event summaries.
- Confirm all course fees and batch timings with the academy before launch.
- Monitor lead quality after launch and adjust scoring thresholds if needed.
- Add periodic cleanup/retention policy for analytics events if data volume grows.

## Final Status

The website is now structurally stronger for production launch:

- Admin course and category management is more stable.
- Course Help submissions are visible for follow-up.
- Public navigation and CTAs are clearer.
- Homepage and course pages answer student and parent questions better.
- Forms capture better lead context.
- Phone validation protects lead quality.
- Admin can see lead score and status.
- Analytics can track useful conversion behavior without breaking the site.
- Automated tests passed after the final implementation phase.
