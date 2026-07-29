# Phase 6 Content and Trust Handover

This document is a manual production-CMS checklist. The code defaults in Phase 6 do not overwrite live CMS records. Do not run baseline seeders against production.

## Content inventory

| Area | Classification | Phase 6 treatment |
|---|---|---|
| Homepage | Accurate but mixed with unsupported trust, schedule, result, and review language | Safe defaults added; changeable batch wording is CMS-editable; unsupported external-review blocks require actual evidence. |
| About | Useful structure with generic and unsupported experience/outcome language | Existing CMS fields receive factual defaults; live values require Staff review. |
| Audience pages | Useful decision paths with repeated and occasionally unsupported proof language | Safe student, parent, and computer-skill defaults added; study-abroad brand boundary preserved. |
| Course pages | Strong decision structure, but missing facts were filled with inferred level, mode, schedule, outline, and support claims | Inferred facts removed; verified product fields remain; empty outlines and unmatched proof remain hidden. |
| Faculty | Active profiles are useful; some course instructor values have no exact active profile | No associations created. Course relationships remain exact-name matches. Homepage no longer invents course or biography text. |
| Testimonials | Four local records match an existing course name; three known records do not | Homepage and course pages show only active exact-course matches. Consent and wording still require owner confirmation. |
| Blogs | Eight published articles are accurate in topic but too thin and weakly structured | Baseline articles expanded with headings, practical questions, and realistic limitations; slugs and publication statuses preserved. |
| FAQs | Useful index with some unsupported certificate, timing, visit, event, and change-policy answers | Baseline answers changed to conditional confirmation language; questions, ordering, and statuses preserved. |
| Footer and contact | Useful contact paths; live footer description and opening hours may be outdated | Approved footer default retained; opening hours require owner confirmation. |
| Popup and notices | Working campaign systems with live campaign-specific content | Mechanics and content untouched; Staff must review which campaign should remain active. |
| External reviews | CMS controls exist, but a note alone is not evidence | Public review sections now require a Google Business Profile URL or review screenshot. |
| Global Launchpad | Active service pillar contains consulting and application-planning language | No product meaning, slug, status, or copy changed. Owner decision required. |

## Production CMS update checklist

Apply only owner-approved rows. Use a Staff account for ordinary content fields and an Admin account for sensitive identity, analytics, map, or infrastructure fields.

| CMS section | Field or record | Current expected meaning | Approved replacement/default | Reason | Permission | Public page | Verification after saving |
|---|---|---|---|---|---|---|---|
| Page Content → Homepage | `hero_hook_headline` | Main academy offer | `Practical courses and classes in Pokhara.` | Avoid an unverified experience claim in the main headline | Staff | Homepage hero | Refresh homepage and confirm the exact heading |
| Page Content → Homepage | `hero_subtitle` | Course-selection explanation | `Compare available course details and ask the academy to confirm current batch information before enrollment.` | Avoid presenting changeable batch or faculty facts as confirmed | Staff | Homepage hero | Confirm the sentence and both hero buttons remain visible |
| Search and sharing content | `meta_title` / `meta_description` | Default search and social summary | `Golden Eye Academy \| Courses and Classes in Pokhara` plus the factual Phase 6 description | Remove an unsupported establishment-year claim without changing SEO structure | Staff | Page titles, metadata, and organization description | Inspect the page source and social metadata after saving |
| SEO controls | `aeo_summary` | Short machine-readable academy summary | `Golden Eye Academy in Pokhara provides IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes.` | Keep the summary tied to listed course areas | Admin | Site-wide metadata | Inspect the `aeo-summary` meta tag after saving |
| Website identity | `logo_subtitle` | Short location line | `Pokhara, Nepal` | Remove unsupported establishment-year wording | Staff | About heading and any identity component using this field | Confirm the location line contains no unverified date |
| Page Content → Homepage | `home_trust_items` | Compact factual trust information | Academy name/location, stored phone/email, and a reminder to confirm current timing | Remove unsupported popularity and fixed-schedule claims | Staff | Homepage trust strip | Confirm each line is factual and no obsolete schedule remains |
| Page Content → Homepage | `home_courses_batch_note` | Batch note on course cards | `Confirm current batch options with the academy` | Course records do not store a live schedule | Staff | Homepage course cards | Confirm the note appears inside each visible course card |
| Page Content → Homepage | `home_why_title` | Decision-support section heading | `Course information before enrollment` | Describe the section without a broad quality claim | Staff | Homepage | Confirm heading renders once |
| Page Content → Homepage | `home_why_description` | What visitors can verify | `Review available course descriptions, outlines, fees, durations, and instructor information. Contact the academy to confirm the current batch before enrolling.` | Keep trust content tied to stored fields | Staff | Homepage | Compare public text with course CMS fields |
| Page Content → Homepage | `home_testimonials_tagline` / `home_testimonials_title` | Active, course-linked feedback | `Published student feedback` / `Feedback linked to listed courses` | Avoid presenting feedback as verified results | Staff | Homepage testimonials | Confirm only approved exact-course records appear |
| Page Content → Homepage | `home_faculty_title` | Active faculty profiles | `Faculty profiles and course information` | Avoid unsupported experience claims | Staff | Homepage faculty | Confirm unmatched courses are not attributed to a profile |
| Page Content → Homepage | `home_reviews_tagline` / `home_reviews_title` | External evidence section | `Verified external reviews` / `Check independent review information` | The section is shown only with evidence | Staff | Homepage | Confirm the section is hidden without a URL or screenshot |
| Website Content → Page Content | `course_confirmation_note` | Shared notice for facts not stored live | `Confirm current batch timing, seat availability, and instructor details with the academy before enrollment.` | Avoid inferred schedules and availability | Staff | Course details and About comparison | Confirm exact note appears; check mobile wrapping |
| Website Content → Page Content | `about_page_content` | Academy approach | Approved factual two-paragraph default from the CMS form | Remove generic experience and outcome claims | Staff | About | Preview headings and paragraphs on desktop and mobile |
| Website Content → Page Content | `about_point_1` through `about_point_4` | Short academy information points | Enrollment support; listed subject areas; audience support; confirmation before enrollment | Replace superlatives and calculated experience wording | Staff | About | Confirm all four points are factual |
| Website Content → Page Content | `about_feat_1_desc` through `about_feat_4_desc` | Four decision-support explanations | Use the Phase 6 defaults shown in the CMS form | Avoid claims about mock tests, projects, follow-up, or outcomes across every course | Staff | About | Confirm each card describes a checkable action |
| Audience Landing Pages | `audience_students_why` / `audience_students_proof` | Student decision support | Compare stored course information and confirm current timing | Remove fixed schedule and popularity claims | Staff | For Students | Confirm proof list contains no unverified numbers |
| Audience Landing Pages | `audience_parents_proof` / `audience_parents_final_headline` | Parent decision support | Stored course details plus questions to confirm | Keep recommendations realistic | Staff | For Parents | Confirm the page asks for context rather than guaranteeing a fit |
| Audience Landing Pages | `audience_job_computer_skills_subheadline`, paths, proof, and final headline | Computer-course decision support | Use the Phase 6 factual defaults | Avoid employment and confidence guarantees | Staff | Computer and Job Skills | Confirm no job outcome is promised |
| Audience Landing Pages | `audience_study_abroad_why` / `audience_study_abroad_proof` | Academy/consultancy boundary | Retain the approved Golden Eye / Brilliant wording from Phase 5 | Preserve the verified brand boundary | Staff | Study Abroad Guidance | Confirm Golden Eye handles classes and Brilliant handles consulting |
| External Review Proof | `google_business_profile_url` | Verified external profile destination | Owner-provided Google Business Profile URL | A note alone is not evidence | Staff | Homepage and course details | Open the public link in a private browser |
| External Review Proof | `external_review_screenshot` | Current owner-approved review evidence | Owner-approved screenshot with consent and date context | Avoid fabricated social proof | Staff | Homepage and course details | Confirm image is legible and current |
| External Review Proof | `external_review_proof_note` | Context for real evidence | Leave empty until evidence exists; then describe how to verify it | Prevent unsupported proof language | Staff | Homepage and course details | Confirm the section hides if URL and screenshot are both empty |
| FAQ | `Do you provide certificates?` | Course-specific certificate question | Conditional Phase 6 answer | Certificate availability is not verified globally | Staff | FAQ and related course pages | Search public FAQ and confirm conditional wording |
| FAQ | `Are flexible class timings available?` | Current scheduling question | Conditional Phase 6 answer | Schedules change | Staff | FAQ | Confirm no fixed morning/day/evening claim remains |
| FAQ | `Do you run events and workshops?` | Current event availability | Check notices or contact academy | Event schedules change | Staff | FAQ | Confirm current notice, if any, is separate from the FAQ |
| FAQ | `Can I visit before enrollment?` | Visit planning | Contact before travelling to confirm opening time | Opening hours need owner verification | Staff | FAQ | Confirm contact actions still work |
| Blog | Eight existing slugs listed below | Published educational guides | Use the expanded Phase 6 baseline only after Staff review | Improve structure without inventing academy facts | Staff | Blog index/detail | Preview each article, then update without changing slug or status |
| Marketing Tools | Main Campaign Popup | Current academy campaign | No automatic replacement | Staff must select the active campaign | Staff | Site-wide popup | Test close, destination, image, and mobile layout |
| Notices | Active popup notice | Current short campaign | No automatic replacement | Notice and main popup are independent | Staff | Site-wide popup notice | Confirm only the intended active popup notice appears |
| Notices | Active announcement bar | Current announcement | No automatic replacement | Schedule and wording may be stale | Staff | Top announcement bar | Confirm title, link, start, and expiry |

## Blog records preserved

The following slugs and `published` statuses are preserved:

1. `which-course-should-i-choose-after-see-or-plus-two`
2. `ielts-or-pte-how-to-choose-the-right-test`
3. `why-office-skills-still-matter-for-job-seekers`
4. `how-web-development-builds-a-career-portfolio`
5. `korean-eps-topik-preparation-what-beginners-should-know`
6. `japanese-jlpt-n5-a-practical-starting-plan`
7. `parents-guide-how-to-evaluate-a-training-institute`
8. `why-you-should-ask-before-enrollment`

Staff must preview each revised article before manually replacing live content. Do not change a live slug while applying the copy.

## Owner-information register

| Needed information | Why it is required | Safe action until confirmed |
|---|---|---|
| Current active courses | Confirm that the local 13-course snapshot matches production | Do not add, rename, remove, or unpublish products |
| Current fees and schedules | Prices exist locally, but schedules and availability are changeable | Show stored fees; ask visitors to confirm current schedule and availability |
| `Professional Web Development` → `Prasun Paudel` | No exact active faculty profile matches the protected instructor value | Keep the course faculty-profile section hidden |
| `Chinese Language Starter` → `Chham Maya Rai` | No exact active faculty profile matches the protected instructor value | Keep the course faculty-profile section hidden |
| `Apekshya Chhetri` → `Korean Language` | Testimonial course name does not exactly match a current product | Keep it off course pages and the homepage |
| `Nirmala Thapa` → `IELTS Masterclass` | Testimonial course name does not exactly match a current product | Keep it off course pages and the homepage |
| `Sharap Dorje Gurung` → `Computer and Office Skills` | Testimonial course name does not exactly match a current product | Keep it off course pages and the homepage |
| Testimonial consent and wording | Identity, wording, image permission, and course association need confirmation | Keep a record inactive unless Staff has approval and an exact course name |
| German-class availability | No German course exists in the protected local 13-course snapshot | Do not create a course; retain only the approved service-role clarification |
| `Global Launchpad` intended purpose | Its local content combines language preparation with destination/application/intake planning | Do not change title, slug, status, or meaning until the owner decides the boundary |
| Course-description claims | Some protected descriptions refer to scores, applications, visa processing, employment, or destination-specific goals | Preserve product content in Phase 6 and request a separate owner-approved factual correction |
| Licences and registration wording | No documentary wording was supplied | Publish no licence or authorization claim |
| Partnership and employer/college claims | No supporting documentation was supplied | Publish no partnership, exclusivity, placement, or access claim |
| Google Business Profile link | Required for verifiable external review linking | Keep external review sections hidden without a real URL or approved screenshot |
| Real photographs and consent | Images should represent real classes, staff, or events with permission | Reuse current assets only; do not label an image as proof without confirmation |
| Opening hours | The local CMS contains a schedule that has not been owner-confirmed | Ask visitors to confirm before travelling |
| Response-time commitment | The local CMS contains a `2 hr` metric that is not verified | Keep response-time metrics empty until the owner approves one |
| Experience and learner-count metrics | Local CMS values include `15+` and `5,000+` without supplied evidence | Keep metric fields empty until documentation is available |
| Founding date and establishment-year wording | No supporting registration record was supplied for the public `2008` claim | Phase 6 removes it from defaults and structured data; add it back only after owner verification |
| Main popup, popup notice, and announcement bar | Local content may represent different campaigns | Staff must choose the current campaign and update it through its existing CMS control |

## Final production check

1. Take a production database and media backup.
2. Record the deployed commit.
3. Apply approved changes manually through the CMS; do not run seeders.
4. Preview blogs and course pages as Staff.
5. Test homepage, About, audience pages, every active course, FAQ, blog, contact, WhatsApp, phone, map, popup, popup notice, and announcement bar.
6. Check mobile and desktop layouts.
7. Verify that no product name, slug, status, price, duration, instructor, category, or URL changed.
8. Record who approved testimonials, photographs, external reviews, licences, and partnership wording.
