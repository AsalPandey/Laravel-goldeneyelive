# GoldenEye Academy Service Catalogue

## Service Model

GoldenEye Academy services are designed to convert uncertainty into inquiries. The service offer should feel practical, human, and low-risk: visitors can talk to the team before choosing.

## 1. Course Roadmap Help

Hook: If you are confused, start here.

Audience: all visitors.

Service outcome:

- Understand the visitor's goal.
- Identify current level.
- Recommend course sequence.
- Capture lead details for follow-up.

CMS source: `Course`, `CourseCategory`, `SiteSetting`, `JoinNowQuery`.

Primary CTA: Ask for Course Help.

## 2. Admissions and Enrollment Support

Hook: Enrollment should confirm a decision, not create pressure.

Audience: students and parents ready to join.

Service outcome:

- Confirm selected course.
- Explain fees, timing, batch availability, and next steps.
- Record inquiry in admin submissions.

CMS source: `JoinNowQuery`, admin submissions.

Primary CTA: Ask for Course Help.

## 3. Study Abroad Test Selection

Hook: IELTS, PTE, Japanese, or Korean? Choose based on destination and timeline.

Audience: abroad applicants.

Service outcome:

- Compare test options.
- Explain preparation timeline.
- Recommend the right course.
- Move visitor into test-prep inquiry.

CMS source: study abroad categories and courses.

Primary CTA: Get Test Prep Roadmap.

## 4. Language Learning Support

Hook: Start speaking with structure, not random memorization.

Audience: beginner and continuing language learners.

Service outcome:

- Match learner to English, Japanese, Korean, or Chinese track.
- Explain level, duration, and class style.
- Capture inquiry for batch follow-up.

CMS source: language courses and teacher records.

Primary CTA: Ask Which Language Track Fits.

## 5. Computer and Office Skills Training

Hook: Build the workplace basics employers expect.

Audience: job seekers, students, professionals.

Service outcome:

- Assess current computer confidence.
- Recommend foundation, office package, or diploma route.
- Capture inquiry for class timing and enrollment.

CMS source: computer category and course records.

Primary CTA: Check My Skill Level.

## 6. Web Development and IT Career Guidance

Hook: Do not just learn code. Build work you can show.

Audience: IT-curious learners, beginners, career switchers.

Service outcome:

- Explain beginner requirements.
- Recommend project-based learning path.
- Capture inquiry for web development batch.

CMS source: web development category and course records.

Primary CTA: View Career Roadmap.

## 7. Parent and Guardian Advisory

Hook: Clear answers before families invest.

Audience: parents and guardians.

Service outcome:

- Explain course fit, fees, timing, and outcomes.
- Clarify student readiness.
- Capture parent contact for team follow-up.

CMS source: contact submissions and course-help request.

Primary CTA: Ask the Team.

## 8. Events, Workshops, and Community Sessions

Hook: Let students experience the academy before they enroll.

Audience: students, schools, colleges, professionals.

Service outcome:

- Promote workshops.
- Capture event interest.
- Move attendees into a course-help request or course inquiry.

CMS source: service pillars, notices, blog posts, contact submissions.

Primary CTA: Ask About Events.

## Service QA Checklist

- Public CTA opens route `join-now` or `contact`.
- Form submission stores lead source and CTA ID where available.
- Admin can view, update status, and delete submissions.
- Seeded service content has real public records, not hard-coded filler cards.
- Branding CMS controls copy, phone, WhatsApp, maps, and SEO defaults.
