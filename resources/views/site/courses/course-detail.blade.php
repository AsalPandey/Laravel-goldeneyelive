@extends('site.layout.app')
@php
    $coursePageTitle = \App\Support\StructuredData::titleWithBrand($course->meta_title ?: $course->name);
    $courseMetaDescription = \App\Support\StructuredData::courseMetaDescription($course);
    $courseHeroImage = \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg');
@endphp
@section('page_title', $coursePageTitle)
@section('og_title', $course->name . ' Course at Golden Eye Academy')
@section('meta_description', $courseMetaDescription)
@section('meta_keywords', $course->meta_keywords ?? '')
@section('aeo_summary', strip_tags($course->aeo_summary ?? ''))
@section('og_image', \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg'))
@section('preload_assets')
    <link rel="preload" as="image" href="{{ $courseHeroImage }}" fetchpriority="high">
@endsection
@section('tracking_event', 'course_detail_view')
@section('tracking_source_page', 'course-detail')
@section('tracking_source_section', 'course-detail-view')
@section('tracking_selected_course', $course->slug)
@section('tracking_inquiry_intent', 'course_guidance')

@section('schema_markup')
    @jsonld(json_encode(\App\Support\StructuredData::courseSchema($course, $settings ?? [])))
@endsection

@section('content')
    @php
        $courseText = strtolower($course->name.' '.$course->category.' '.$course->badge_text);
        $descriptionText = trim(strip_tags((string) $course->description));
        $outcome = \Illuminate\Support\Str::limit(trim(\Illuminate\Support\Str::before($descriptionText, '.')) ?: 'Build practical skills with guided classes, practice, and feedback.', 120);
        $descriptionBestFor = trim((string) \Illuminate\Support\Str::of($descriptionText)->after('Best for')->before('.')->trim(' :'));
        $bestFor = $descriptionBestFor !== '' && $descriptionBestFor !== $descriptionText
            ? ucfirst($descriptionBestFor)
            : match (true) {
                str_contains($courseText, 'ielts'), str_contains($courseText, 'pte') => 'Learners who need exam-focused IELTS/PTE preparation and score planning',
                str_contains($courseText, 'japanese'), str_contains($courseText, 'korean'), str_contains($courseText, 'language') => 'Students preparing for language, study, or work goals',
                str_contains($courseText, 'office'), str_contains($courseText, 'computer') => 'Learners who need practical computer confidence for study or office work',
                str_contains($courseText, 'web'), str_contains($courseText, 'it') => 'Beginners who want project-based IT and web development skills',
                default => 'Students who want clear class information before enrollment',
            };
        $level = match (true) {
            str_contains($courseText, 'basic'), str_contains($courseText, 'starter'), str_contains($courseText, 'foundation'), str_contains($courseText, 'n5') => 'Beginner',
            str_contains($courseText, 'advanced'), str_contains($courseText, 'professional'), str_contains($courseText, 'n4') => 'Intermediate',
            default => 'Beginner to intermediate',
        };
        $mode = 'In-person classes in Pokhara with guided practice';
        $batchTiming = 'Morning, day, and evening batch options. Confirm current timing before enrollment.';
        $supportDetails = 'Practice, feedback, progress support, and completion support where applicable.';
        $nextBatch = 'Ask for current intake and available seats';
        $courseImage = $courseHeroImage;
        $breadcrumbCategoryName = $course->courseCategory?->name ?? $course->category;
        $breadcrumbCategorySlug = $course->courseCategory?->slug ?? $course->category_slug;
        $sectionGuidanceUrl = fn (string $sourceSection) => route('join-now', [
            'course' => $course->slug,
            'selected_course' => $course->slug,
            'source_page' => 'course-detail',
            'source_section' => $sourceSection,
            'inquiry_intent' => 'course_guidance',
        ]);
        $whatsappCleanNumber = str_replace(['+', ' ', '-'], '', $settings['whatsapp_number'] ?? '9779856058599');
        $whatsappMessage = rawurlencode('Hi Golden Eye Academy, I have a question about '.$course->name.' classes.');
        $externalReviewUrl = trim((string) ($settings['google_business_profile_url'] ?? ''));
        $externalReviewScreenshot = trim((string) ($settings['external_review_screenshot'] ?? ''));
        $externalReviewNote = trim((string) ($settings['external_review_proof_note'] ?? 'Ask the academy team for current Google review proof or verified review screenshots before enrollment.'));
        $instructorCourseList = isset($instructorCourses) && $instructorCourses->isNotEmpty()
            ? $instructorCourses->implode(', ')
            : ($course->name ?: 'Classroom practice and academic support');
        $outlineWithBreaks = preg_replace('/<(?:br\s*\/?|\/li|\/p|\/div)>/i', "\n", (string) $course->course_outline);
        $outlineItems = collect(preg_split('/\r\n|\r|\n/', strip_tags((string) $outlineWithBreaks)))
            ->map(fn ($item) => trim(html_entity_decode($item)))
            ->filter()
            ->values();

        if ($outlineItems->isEmpty()) {
            $outlineItems = collect([
                'Course orientation and level check',
                'Core concepts and guided examples',
                'Practice tasks with feedback',
                'Review, improvement plan, and next steps',
            ]);
        }

        $quickFacts = [
            ['icon' => 'fa fa-clock', 'label' => 'Duration', 'value' => $course->duration ?: 'Confirm with academy'],
            ['icon' => 'fa fa-tag', 'label' => 'Fee', 'value' => $course->price ?: 'Fee available on request'],
            ['icon' => 'fa fa-layer-group', 'label' => 'Level', 'value' => $level],
            ['icon' => 'fa fa-calendar-alt', 'label' => 'Batch timing', 'value' => $batchTiming],
            ['icon' => 'fa fa-map-marker-alt', 'label' => 'Mode', 'value' => $mode],
            ['icon' => 'fa fa-hands-helping', 'label' => 'Support', 'value' => $supportDetails],
        ];
        $localTrustMarkers = [
            ['icon' => 'fa fa-map-marker-alt', 'label' => 'Location', 'value' => $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal'],
            ['icon' => 'fa fa-history', 'label' => 'Local experience', 'value' => 'Trusted by students in Pokhara since 2008'],
            ['icon' => 'fa fa-phone', 'label' => 'Phone', 'value' => $settings['site_phone'] ?? '061-572599'],
            ['icon' => 'fa fa-envelope', 'label' => 'Email', 'value' => $settings['site_email'] ?? 'goldeneyeacademy2008@gmail.com'],
            ['icon' => 'fa fa-calendar-check', 'label' => 'Batches', 'value' => 'Morning, day, and evening batches'],
        ];
        $whoFor = [
            $bestFor,
            'Students who want structure instead of random self-study',
            'Learners who can attend regular classes and complete practice work',
            'Parents who want fee, duration, timing, and support clarity before enrollment',
        ];
        $studentViewItems = [
            ['label' => 'Curriculum', 'value' => 'Clear lessons based on the course outline below.'],
            ['label' => 'Practical assignments', 'value' => 'Regular tasks help students apply what they learn in class.'],
            ['label' => 'Mock tests/practice', 'value' => str_contains($courseText, 'ielts') || str_contains($courseText, 'pte') || str_contains($courseText, 'topik') || str_contains($courseText, 'jlpt') ? 'Exam-style practice and mock review are part of the learning path.' : 'Practice sessions and guided exercises build confidence step by step.'],
            ['label' => 'Feedback/support', 'value' => 'Students can ask questions, review weak areas, and get next-step support.'],
            ['label' => 'Outcome', 'value' => $outcome],
        ];
        $parentViewItems = [
            ['label' => 'Total fee', 'value' => $course->price ?: 'Confirm current fee with the academy team.'],
            ['label' => 'Duration', 'value' => $course->duration ?: 'Confirm duration before enrollment.'],
            ['label' => 'Batch timing', 'value' => $batchTiming],
            ['label' => 'Instructor credentials', 'value' => ($instructor?->designation ?: $course->instructor).' - '.\Illuminate\Support\Str::limit(strip_tags($instructor?->bio ?? 'course-specific academic support and classroom practice'), 110)],
            ['label' => 'Safety/trust', 'value' => 'Local Pokhara academy with enrollment support, parent-friendly communication, and no-pressure course discussion.'],
            ['label' => 'Support included', 'value' => $supportDetails],
            ['label' => 'Not guaranteed', 'value' => 'Scores, visas, jobs, or placements are not guaranteed. Progress depends on attendance, practice, and student readiness.'],
        ];
        $testimonialResult = $testimonial
            ? \Illuminate\Support\Str::limit(trim(\Illuminate\Support\Str::before(strip_tags($testimonial->content), '.')) ?: strip_tags($testimonial->content), 100)
            : 'Students use classroom practice and academic support to improve confidence before their next academic or career step.';
    @endphp

    <section class="container-fluid p-0 overflow-hidden" style="background: linear-gradient(135deg, rgba(5, 12, 28, 0.94), rgba(5, 12, 28, 0.76)), url('{{ $courseImage }}'); background-size: cover; background-position: center; color: white;">
        <div class="container py-5">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a class="text-white opacity-75" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a class="text-white opacity-75" href="{{ route('courses-all') }}">Courses</a></li>
                    @if($breadcrumbCategoryName && $breadcrumbCategorySlug)
                        <li class="breadcrumb-item"><a class="text-white opacity-75" href="{{ route('courses-all', ['category' => $breadcrumbCategorySlug]) }}">{{ $breadcrumbCategoryName }}</a></li>
                    @elseif($breadcrumbCategoryName)
                        <li class="breadcrumb-item text-white opacity-75">{{ $breadcrumbCategoryName }}</li>
                    @endif
                    <li class="breadcrumb-item text-brand-gold active fw-bold" aria-current="page">{{ $course->name }}</li>
                </ol>
            </nav>
            <div class="row align-items-end g-4" style="min-height: 55vh;">
                <div class="col-lg-8">
                    <span class="badge rounded-pill bg-brand-gold text-brand-dark px-4 py-2 fw-black text-uppercase tracking-[0.3em] mb-4" style="font-size: 9px;">{{ $course->badge_text ?? 'Course Details' }}</span>
                    <h1 class="font-black text-white mb-4" style="font-size: clamp(2rem, 5vw, 4.2rem); line-height: 1.04; letter-spacing: 0;">{{ $course->name }}</h1>
                    <p class="text-white/90 mb-4" style="font-size: 16px; line-height: 1.7; max-width: 760px;">{{ $outcome }}</p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <span class="course-hero-chip">Best for: {{ $bestFor }}</span>
                        <span class="course-hero-chip">Duration: {{ $course->duration ?: 'Confirm with academy' }}</span>
                        <span class="course-hero-chip">Fee: {{ $course->price ?: 'Available on request' }}</span>
                        <span class="course-hero-chip">Next batch: {{ $nextBatch }}</span>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a href="{{ $sectionGuidanceUrl('course-detail-hero') }}" data-cta="course-detail-course-guidance" class="btn btn-primary py-3 px-5 rounded-xl fw-black text-uppercase tracking-widest" style="font-size: 10px;">Ask for Course Help</a>
                        <a href="https://wa.me/{{ $whatsappCleanNumber }}?text={{ $whatsappMessage }}" target="_blank" rel="noopener" data-cta="course-detail-whatsapp" class="btn btn-outline-light py-3 px-5 rounded-xl fw-black text-uppercase tracking-widest" style="font-size: 10px;">Message on WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <article class="container-xxl py-5">
        <div class="container">
            <section class="mb-5" aria-labelledby="quick-facts-heading">
                <div class="d-flex align-items-end justify-content-between gap-3 mb-4">
                    <div>
                        <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Quick facts</span>
                        <h2 id="quick-facts-heading" class="h3 fw-black text-brand-dark mt-2 mb-0">Course details at a glance</h2>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach($quickFacts as $fact)
                        <div class="col-md-6 col-xl-4">
                            <div class="h-100 p-4 bg-white border border-zinc-100 rounded-xl shadow-sm">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="course-fact-icon"><i class="{{ $fact['icon'] }}"></i></span>
                                    <div>
                                        <p class="mb-1 text-zinc-500 fw-black text-uppercase tracking-widest" style="font-size: 9px;">{{ $fact['label'] }}</p>
                                        <p class="mb-0 text-brand-dark fw-bold" style="font-size: 13px; line-height: 1.55;">{{ $fact['value'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="mb-5" aria-labelledby="who-for-heading">
                <div class="row g-4 align-items-start">
                    <div class="col-lg-5">
                        <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Who this course is for</span>
                        <h2 id="who-for-heading" class="h3 fw-black text-brand-dark mt-2 mb-3">Check fit before you enroll</h2>
                        <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">This page answers the main decision questions: who the course is for, what students learn, fee, duration, timing, instructor, support, and what is not guaranteed.</p>
                    </div>
                    <div class="col-lg-7">
                        <div class="row g-3">
                            @foreach($whoFor as $item)
                                <div class="col-md-6">
                                    <div class="h-100 p-4 bg-zinc-50 border border-zinc-100 rounded-xl">
                                        <i class="fa fa-check-circle text-brand-gold mb-3"></i>
                                        <p class="mb-0 fw-bold text-brand-dark" style="font-size: 13px; line-height: 1.55;">{{ $item }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-5" aria-labelledby="course-local-trust-heading">
                <div class="p-4 p-lg-5 bg-brand-dark text-white rounded-xl">
                    <div class="row g-4 align-items-start">
                        <div class="col-lg-4">
                            <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Local trust</span>
                            <h2 id="course-local-trust-heading" class="h3 fw-black text-white mt-2 mb-3">Pokhara-based course support</h2>
                            <p class="text-white/70 mb-0" style="font-size: 14px; line-height: 1.7;">Students and parents can check location, fee, timing, support, and course fit before enrollment.</p>
                        </div>
                        <div class="col-lg-8">
                            <div class="row g-3">
                                @foreach($localTrustMarkers as $marker)
                                    <div class="col-md-6">
                                        <div class="h-100 p-3 bg-white/10 border border-white/10 rounded-xl">
                                            <div class="d-flex align-items-start gap-3">
                                                <i class="{{ $marker['icon'] }} text-brand-gold mt-1" aria-hidden="true"></i>
                                                <div>
                                                    <p class="mb-1 text-white/50 fw-black text-uppercase tracking-widest" style="font-size: 8px;">{{ $marker['label'] }}</p>
                                                    <p class="mb-0 fw-bold" style="font-size: 13px; line-height: 1.55;">{{ $marker['value'] }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-5" aria-labelledby="audience-view-heading">
                <div class="bg-white border border-zinc-100 rounded-xl p-4 p-lg-5 shadow-sm">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                        <div>
                            <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Student view / Parent view</span>
                            <h2 id="audience-view-heading" class="h3 fw-black text-brand-dark mt-2 mb-0">See the details that matter to you</h2>
                        </div>
                        <div class="nav nav-pills course-view-toggle" id="course-view-tabs" role="tablist" aria-label="Course detail view">
                            <button class="nav-link active" id="student-view-tab" data-bs-toggle="pill" data-bs-target="#student-view" type="button" role="tab" aria-controls="student-view" aria-selected="true">Student View</button>
                            <button class="nav-link" id="parent-view-tab" data-bs-toggle="pill" data-bs-target="#parent-view" type="button" role="tab" aria-controls="parent-view" aria-selected="false">Parent View</button>
                        </div>
                    </div>
                    <div class="tab-content" id="course-view-content">
                        <div class="tab-pane fade show active" id="student-view" role="tabpanel" aria-labelledby="student-view-tab" tabindex="0">
                            <div class="row g-3">
                                @foreach($studentViewItems as $item)
                                    <div class="col-md-6 col-xl-4">
                                        <div class="h-100 p-4 bg-zinc-50 border border-zinc-100 rounded-xl">
                                            <p class="mb-2 fw-black text-brand-dark">{{ $item['label'] }}</p>
                                            <p class="mb-0 text-zinc-600" style="font-size: 13px; line-height: 1.65;">{{ $item['value'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="tab-pane fade" id="parent-view" role="tabpanel" aria-labelledby="parent-view-tab" tabindex="0">
                            <div class="row g-3">
                                @foreach($parentViewItems as $item)
                                    <div class="col-md-6">
                                        <div class="h-100 p-4 bg-zinc-50 border border-zinc-100 rounded-xl">
                                            <p class="mb-2 fw-black text-brand-dark">{{ $item['label'] }}</p>
                                            <p class="mb-0 text-zinc-600" style="font-size: 13px; line-height: 1.65;">{{ $item['value'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-5" aria-labelledby="learn-heading">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">What you will learn</span>
                        <h2 id="learn-heading" class="h3 fw-black text-brand-dark mt-2 mb-3">Skills and practice areas</h2>
                        <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">The outline is written as practical learning areas so students know what they will study, practice, and improve.</p>
                    </div>
                    <div class="col-lg-7">
                        <div class="row g-3">
                            @foreach($outlineItems->take(6) as $item)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-3 p-3 bg-white border border-zinc-100 rounded-xl h-100">
                                        <i class="fa fa-check text-brand-gold mt-1"></i>
                                        <span class="fw-bold text-brand-dark" style="font-size: 13px; line-height: 1.55;">{{ $item }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-5" aria-labelledby="curriculum-heading">
                <div class="text-center mb-4">
                    <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Week-by-week curriculum</span>
                    <h2 id="curriculum-heading" class="h3 fw-black text-brand-dark mt-2 mb-2">A structured path from basics to practice</h2>
                </div>
                <div class="row g-3">
                    @foreach($outlineItems as $index => $item)
                        <div class="col-md-6 col-xl-3">
                            <div class="h-100 p-4 bg-zinc-50 border border-zinc-100 rounded-xl">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-brand-gold text-brand-dark fw-black mb-3" style="width: 38px; height: 38px; font-size: 11px;">{{ $index + 1 }}</span>
                                <p class="text-zinc-500 fw-black text-uppercase tracking-widest mb-1" style="font-size: 9px;">Week {{ $index + 1 }}</p>
                                <h3 class="h6 fw-black text-brand-dark mb-0" style="line-height: 1.4;">{{ $item }}</h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="mb-5" aria-labelledby="fee-heading">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="h-100 p-4 p-lg-5 bg-brand-dark text-white rounded-xl">
                            <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Batch timing and fee</span>
                            <h2 id="fee-heading" class="h3 fw-black text-white mt-2 mb-4">Confirm schedule before enrollment</h2>
                            <div class="d-grid gap-3">
                                <div class="course-dark-row"><span>Fee</span><strong>{{ $course->price ?: 'Available on request' }}</strong></div>
                                <div class="course-dark-row"><span>Duration</span><strong>{{ $course->duration ?: 'Confirm with academy' }}</strong></div>
                                <div class="course-dark-row"><span>Batch timing</span><strong>{{ $batchTiming }}</strong></div>
                                <div class="course-dark-row"><span>Seat availability</span><strong>{{ $course->capacity ?: 'Ask for current availability' }}</strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="h-100 p-4 p-lg-5 bg-white border border-zinc-100 rounded-xl shadow-sm">
                            <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Instructor profile</span>
                            <div class="d-flex align-items-start gap-4 mt-3">
                                <img src="{{ \App\Support\PublicAsset::url($instructor?->photo ?? null, 'site/img/team-1.jpg') }}" alt="{{ $instructor?->name ?? $course->instructor }}" class="rounded-circle object-cover flex-shrink-0" loading="lazy" decoding="async" width="86" height="86" style="width: 86px; height: 86px;">
                                <div>
                                    <h2 class="h5 fw-black text-brand-dark mb-1">{{ $instructor?->name ?? $course->instructor }}</h2>
                                    <p class="text-brand-gold fw-black mb-2" style="font-size: 12px;">{{ $instructor?->designation ?? 'Course Instructor' }}</p>
                                    <p class="text-zinc-600 mb-3" style="font-size: 13px; line-height: 1.65;">{{ \Illuminate\Support\Str::limit(strip_tags($instructor?->bio ?? 'Supports students with course-specific classroom practice, academic support, and feedback.'), 150) }}</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="course-info-pill">Subject expertise: {{ $course->category ?: $course->name }}</span>
                                        <span class="course-info-pill">Class support: Practical lessons, student questions, and progress feedback</span>
                                        <span class="course-info-pill">Courses taught: {{ $instructorCourseList }}</span>
                                        <span class="course-info-pill">Credibility note: {{ \Illuminate\Support\Str::limit(strip_tags($instructor?->bio ?? 'Course-specific academic support and classroom practice.'), 90) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @if($testimonial)
                <section class="mb-5" aria-labelledby="proof-heading">
                    <div class="p-4 p-lg-5 bg-zinc-50 border border-zinc-100 rounded-xl">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-4">
                                <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Student result</span>
                                <h2 id="proof-heading" class="h3 fw-black text-brand-dark mt-2 mb-0">Proof from a learner</h2>
                            </div>
                            <div class="col-lg-8">
                                <div class="d-flex flex-column flex-md-row gap-4 align-items-md-center bg-white border border-zinc-100 rounded-xl p-4">
                                    <img src="{{ \App\Support\PublicAsset::url($testimonial->photo ?? null, 'site/img/user.png') }}" alt="{{ $testimonial->student_name }}" class="rounded-circle object-cover flex-shrink-0" loading="lazy" decoding="async" width="82" height="82" style="width: 82px; height: 82px;">
                                    <div>
                                        <h3 class="h6 fw-black text-brand-dark mb-1">{{ $testimonial->student_name }}</h3>
                                        <p class="text-brand-gold fw-black mb-2" style="font-size: 11px;">Course completed: {{ $testimonial->course_name }}</p>
                                        <p class="text-zinc-700 fw-bold mb-2" style="font-size: 13px;">Specific progress/result: {{ $testimonialResult }}</p>
                                        <p class="text-zinc-600 mb-0" style="font-size: 13px; line-height: 1.65;">"{{ \Illuminate\Support\Str::limit(strip_tags($testimonial->content), 180) }}"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if($externalReviewNote !== '' || $externalReviewUrl !== '' || $externalReviewScreenshot !== '')
                <section class="mb-5" aria-labelledby="external-proof-heading">
                    <div class="p-4 p-lg-5 bg-white border border-zinc-100 rounded-xl shadow-sm">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-5">
                                <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">External social proof</span>
                                <h2 id="external-proof-heading" class="h3 fw-black text-brand-dark mt-2 mb-3">Review proof for parent checks</h2>
                                <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">{{ $externalReviewNote }}</p>
                            </div>
                            <div class="col-lg-7">
                                <div class="p-4 bg-zinc-50 border border-zinc-100 rounded-xl h-100">
                                    @if($externalReviewScreenshot !== '')
                                        <img src="{{ \App\Support\PublicAsset::url($externalReviewScreenshot, 'site/img/testimonial-1.jpg') }}" alt="Verified review proof for Golden Eye Academy" class="img-fluid rounded-xl mb-3" loading="lazy" decoding="async" width="640" height="360">
                                    @endif
                                    @if($externalReviewUrl !== '')
                                        <a href="{{ $externalReviewUrl }}" target="_blank" rel="noopener" class="text-brand-dark fw-black text-decoration-none">
                                            Google Business Profile <i class="fa fa-external-link-alt ms-1" aria-hidden="true"></i>
                                        </a>
                                    @else
                                        <p class="mb-0 text-zinc-600" style="font-size: 13px; line-height: 1.65;">The academy can share verified Google review proof or screenshots on request until the public profile link is added.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if($faqs->isNotEmpty())
                <section class="mb-5" aria-labelledby="faq-heading">
                    <div class="text-center mb-4">
                        <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">FAQs</span>
                        <h2 id="faq-heading" class="h3 fw-black text-brand-dark mt-2 mb-2">Questions before joining this course</h2>
                    </div>
                    <div class="row g-3">
                        @foreach($faqs as $faq)
                            <div class="col-lg-6">
                                <div class="h-100 p-4 bg-white border border-zinc-100 rounded-xl shadow-sm">
                                    <h3 class="h6 fw-black text-brand-dark mb-2">{{ $faq->question }}</h3>
                                    <p class="text-zinc-600 mb-0" style="font-size: 13px; line-height: 1.65;">{{ \Illuminate\Support\Str::limit(strip_tags($faq->answer), 155) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section aria-labelledby="inquiry-heading">
                <div class="p-4 p-lg-5 bg-brand-dark text-white rounded-xl">
                    <div class="row g-4 align-items-center justify-content-between">
                        <div class="col-lg-8">
                            <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Inquiry CTA</span>
                            <h2 id="inquiry-heading" class="h3 fw-black text-white mt-2 mb-3">Want to check if {{ $course->name }} fits you?</h2>
                            <p class="text-white/70 mb-0" style="font-size: 14px; line-height: 1.7;">Send your goal, current level, preferred timing, and parent questions. We will explain suitable class options before enrollment.</p>
                        </div>
                        <div class="col-lg-4 d-flex flex-column gap-2">
                            <a href="{{ $sectionGuidanceUrl('course-detail-final') }}" data-cta="course-detail-final-guidance" class="btn btn-primary py-3 rounded-xl fw-black text-uppercase tracking-widest">Ask for Course Help</a>
                            <a href="https://wa.me/{{ $whatsappCleanNumber }}?text={{ $whatsappMessage }}" target="_blank" rel="noopener" data-cta="course-detail-final-whatsapp" class="btn btn-outline-light py-3 rounded-xl fw-black text-uppercase tracking-widest" style="font-size: 10px;">Message on WhatsApp</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </article>
@endsection
