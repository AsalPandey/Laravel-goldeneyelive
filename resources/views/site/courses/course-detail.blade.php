@extends('site.layout.app')
@if($isPreview ?? false)
    @section('robots', 'noindex, nofollow, noarchive')
    @section('canonical_url', route('courses-detail', $course->slug))
@endif
@php
    $coursePageTitle = \App\Support\StructuredData::titleWithBrand($course->meta_title ?: $course->name);
    $courseMetaDescription = \App\Support\StructuredData::courseMetaDescription($course);
    $courseHeroImage = \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg');
    $breadcrumbCategoryName = $course->courseCategory?->name ?? $course->category;
    $breadcrumbCategorySlug = $course->courseCategory?->slug ?? $course->category_slug;
    $courseBreadcrumbItems = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Courses', 'url' => route('courses-all')],
    ];
    if ($breadcrumbCategoryName) {
        $courseBreadcrumbItems[] = [
            'name' => $breadcrumbCategoryName,
            'url' => $breadcrumbCategorySlug ? route('courses-all', ['category' => $breadcrumbCategorySlug]) : null,
        ];
    }
    $courseBreadcrumbItems[] = ['name' => $course->name, 'url' => route('courses-detail', $course->slug)];
@endphp
@section('page_title', $coursePageTitle)
@section('og_title', $coursePageTitle)
@section('meta_description', $courseMetaDescription)
@section('og_image', \App\Support\PublicAsset::canonicalUrl($course->photo ?? null, 'site/img/cat-1.jpg'))
@section('canonical_url', route('courses-detail', $course->slug))
@section('meta_keywords', $course->meta_keywords ?? '')
@section('aeo_summary', strip_tags($course->aeo_summary ?? ''))
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
    @jsonld(json_encode(\App\Support\StructuredData::breadcrumbSchema($courseBreadcrumbItems)))
@endsection

@section('content')
    @php
        $descriptionText = trim(strip_tags((string) $course->description));
        $courseConfirmationNote = trim((string) ($settings['course_confirmation_note'] ?? 'Confirm current batch timing, seat availability, and instructor details with the academy before enrollment.'))
            ?: 'Confirm current batch timing, seat availability, and instructor details with the academy before enrollment.';
        $outcome = trim(\Illuminate\Support\Str::before($descriptionText, '.')) ?: $courseConfirmationNote;
        $hasExplicitBestFor = \Illuminate\Support\Str::contains($descriptionText, 'Best for');
        $descriptionBestFor = $hasExplicitBestFor
            ? trim((string) \Illuminate\Support\Str::of($descriptionText)->after('Best for')->before('.')->trim(' :'))
            : '';
        $bestFor = $descriptionBestFor !== '' ? ucfirst($descriptionBestFor) : '';
        $nextBatch = 'Ask about current timings';
        $courseImage = $courseHeroImage;
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
        $externalReviewNote = trim((string) ($settings['external_review_proof_note'] ?? ''));
        $instructorCourseList = isset($instructorCourses) && $instructorCourses->isNotEmpty()
            ? $instructorCourses->implode(', ')
            : '';
        $outlineWithBreaks = preg_replace('/<(?:br\s*\/?|\/li|\/p|\/div)>/i', "\n", (string) $course->course_outline);
        $outlineItems = collect(preg_split('/\r\n|\r|\n/', strip_tags((string) $outlineWithBreaks)))
            ->map(fn ($item) => trim(html_entity_decode($item)))
            ->filter()
            ->values();

        $quickFacts = [
            ['icon' => 'fa fa-clock', 'label' => 'Duration', 'value' => $course->duration ?: 'Confirm with academy'],
            ['icon' => 'fa fa-tag', 'label' => 'Fee', 'value' => $course->price ?: 'Fee available on request'],
            ['icon' => 'fa fa-layer-group', 'label' => 'Category', 'value' => $breadcrumbCategoryName ?: 'Confirm with academy'],
            ['icon' => 'fa fa-user', 'label' => 'Instructor', 'value' => $course->instructor ?: 'Ask the academy'],
            ['icon' => 'fa fa-map-marker-alt', 'label' => 'Academy location', 'value' => $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal'],
            ['icon' => 'fa fa-calendar-alt', 'label' => 'Current schedule', 'value' => $courseConfirmationNote],
        ];
        $localTrustMarkers = [
            ['icon' => 'fa fa-map-marker-alt', 'label' => 'Location', 'value' => $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal'],
            ['icon' => 'fa fa-phone', 'label' => 'Phone', 'value' => $settings['site_phone'] ?? '061-572599'],
            ['icon' => 'fa fa-envelope', 'label' => 'Email', 'value' => $settings['site_email'] ?? 'goldeneyeacademy2008@gmail.com'],
            ['icon' => 'fa fa-calendar-check', 'label' => 'Plan your visit', 'value' => 'Call or message before visiting to discuss the course and current timings.'],
        ];
        $whoFor = collect([
            $bestFor,
            'Read what the course covers and check that it matches your goal.',
            'Ask whether your current level is suitable for the class.',
            'Contact the academy for current timings and availability.',
        ])->filter()->values();
        $studentViewItems = [
            ['label' => 'About this course', 'value' => $descriptionText ?: 'Ask the academy what the course covers.'],
            ['label' => 'What you will practise', 'value' => $outlineItems->isNotEmpty() ? $outlineItems->count().' learning areas are shown below.' : 'Ask the academy about the learning areas before enrollment.'],
            ['label' => 'Before you join', 'value' => $courseConfirmationNote],
        ];
        $parentViewItems = [
            ['label' => 'Total fee', 'value' => $course->price ?: 'Confirm current fee with the academy team.'],
            ['label' => 'Duration', 'value' => $course->duration ?: 'Confirm duration before enrollment.'],
            ['label' => 'Current schedule', 'value' => $courseConfirmationNote],
            ['label' => 'Academy location', 'value' => $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal'],
            ['label' => 'Instructor', 'value' => $course->instructor ?: 'Ask the academy'],
            ['label' => 'Learning progress', 'value' => 'Progress depends on the learner’s starting point, attendance, practice and continued effort. Ask the academy how progress can be discussed for this course.'],
        ];

        if ($instructor) {
            $instructorProfile = collect([
                $instructor->name,
                $instructor->designation,
                \Illuminate\Support\Str::limit(strip_tags((string) $instructor->bio), 110),
            ])->filter(fn ($value) => filled($value))->implode(' — ');

            array_splice($parentViewItems, 3, 0, [[
                'label' => 'Faculty profile',
                'value' => $instructorProfile,
            ]]);
        }
        $testimonialResult = $testimonial
            ? \Illuminate\Support\Str::limit(trim(\Illuminate\Support\Str::before(strip_tags($testimonial->content), '.')) ?: strip_tags($testimonial->content), 100)
            : '';
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
                    <span class="badge responsive-cms-badge rounded-pill bg-brand-gold text-brand-dark px-4 py-2 fw-black text-uppercase tracking-[0.3em] mb-4" style="font-size: 9px;">{{ $course->badge_text ?? 'Course Details' }}</span>
                    <h1 class="font-black text-white mb-4" style="font-size: clamp(2rem, 5vw, 4.2rem); line-height: 1.04; letter-spacing: 0;">{{ $course->name }}</h1>
                    <p class="text-white/90 mb-4" style="font-size: 16px; line-height: 1.7; max-width: 760px;">{{ $outcome }}</p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @if($bestFor !== '')
                            <span class="course-hero-chip">Best for: {{ $bestFor }}</span>
                        @endif
                        <span class="course-hero-chip">Duration: {{ $course->duration ?: 'Confirm with academy' }}</span>
                        <span class="course-hero-chip">Fee: {{ $course->price ?: 'Available on request' }}</span>
                        <span class="course-hero-chip">Class schedule: {{ $nextBatch }}</span>
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
                                <div class="d-flex align-items-start gap-3 course-contact-row">
                                    <span class="course-fact-icon"><i class="{{ $fact['icon'] }}"></i></span>
                                    <div class="course-contact-copy">
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
                        <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">Review the published course information, then ask the academy to confirm current timing, faculty, and availability before enrollment.</p>
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
                                            <div class="d-flex align-items-start gap-3 course-contact-row">
                                                <i class="{{ $marker['icon'] }} text-brand-gold mt-1" aria-hidden="true"></i>
                                                <div class="course-contact-copy">
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

            @if($outlineItems->isNotEmpty())
                <section class="mb-5" aria-labelledby="curriculum-heading">
                    <div class="text-center mb-4">
                        <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Course outline</span>
                        <h2 id="curriculum-heading" class="h3 fw-black text-brand-dark mt-2 mb-2">What you’ll learn</h2>
                        <p class="text-zinc-600 mb-0" style="font-size: 13px;">The main topics and activities covered in this course.</p>
                    </div>
                    <div class="row g-3">
                        @foreach($outlineItems as $index => $item)
                            <div class="col-md-6 col-xl-3">
                                <div class="h-100 p-4 bg-zinc-50 border border-zinc-100 rounded-xl">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-brand-gold text-brand-dark fw-black mb-3" style="width: 38px; height: 38px; font-size: 11px;">{{ $index + 1 }}</span>
                                    <p class="text-zinc-500 fw-black text-uppercase tracking-widest mb-1" style="font-size: 9px;">Learning area {{ $index + 1 }}</p>
                                    <h3 class="h6 fw-black text-brand-dark mb-0" style="line-height: 1.4;">{{ $item }}</h3>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="mb-5" aria-labelledby="fee-heading">
                <div class="row g-4">
                    <div class="{{ $instructor ? 'col-lg-6' : 'col-12' }}">
                        <div class="h-100 p-4 p-lg-5 bg-brand-dark text-white rounded-xl">
                            <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Course fee and timing</span>
                            <h2 id="fee-heading" class="h3 fw-black text-white mt-2 mb-4">Ask about current batch timings</h2>
                            <div class="d-grid gap-3">
                                <div class="course-dark-row"><span>Fee</span><strong>{{ $course->price ?: 'Available on request' }}</strong></div>
                                <div class="course-dark-row"><span>Duration</span><strong>{{ $course->duration ?: 'Confirm with academy' }}</strong></div>
                                <div class="course-dark-row"><span>Batch timing</span><strong>{{ $courseConfirmationNote }}</strong></div>
                                <div class="course-dark-row"><span>Seat availability</span><strong>Confirm with the academy</strong></div>
                            </div>
                        </div>
                    </div>
                    @if($instructor)
                        <div class="col-lg-6">
                            <div class="h-100 p-4 p-lg-5 bg-white border border-zinc-100 rounded-xl shadow-sm">
                                <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Instructor profile</span>
                                <div class="d-flex align-items-start gap-4 mt-3">
                                    <img src="{{ \App\Support\PublicAsset::url($instructor->photo, 'site/img/team-1.jpg') }}" alt="{{ $instructor->name }}" class="rounded-circle object-cover flex-shrink-0" loading="lazy" decoding="async" width="86" height="86" style="width: 86px; height: 86px;">
                                    <div class="course-contact-copy">
                                        <h2 class="h5 fw-black text-brand-dark mb-1">{{ $instructor->name }}</h2>
                                        @if(filled($instructor->designation))
                                            <p class="text-brand-gold fw-black mb-2" style="font-size: 12px;">{{ $instructor->designation }}</p>
                                        @endif
                                        @if(filled($instructor->bio))
                                            <p class="text-zinc-600 mb-3" style="font-size: 13px; line-height: 1.65;">{{ \Illuminate\Support\Str::limit(strip_tags($instructor->bio), 150) }}</p>
                                        @endif
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($instructorCourseList !== '')
                                                <span class="course-info-pill">Courses taught: {{ $instructorCourseList }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            @if($testimonial)
                <section class="mb-5" aria-labelledby="proof-heading">
                    <div class="p-4 p-lg-5 bg-zinc-50 border border-zinc-100 rounded-xl">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-4">
                                <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Student feedback</span>
                                <h2 id="proof-heading" class="h3 fw-black text-brand-dark mt-2 mb-0">A learner’s experience</h2>
                            </div>
                            <div class="col-lg-8">
                                <div class="d-flex flex-column flex-md-row gap-4 align-items-md-center bg-white border border-zinc-100 rounded-xl p-4">
                                    <x-testimonial-avatar :name="$testimonial->student_name" :photo="$testimonial->photo" :size="82" />
                                    <div>
                                        <h3 class="h6 fw-black text-brand-dark mb-1">{{ $testimonial->student_name }}</h3>
                                        <p class="text-brand-gold fw-black mb-2" style="font-size: 11px;">Course: {{ $testimonial->course_name }}</p>
                                        <p class="text-zinc-700 fw-bold mb-2" style="font-size: 13px;">{{ $testimonialResult }}</p>
                                        <p class="text-zinc-600 mb-0" style="font-size: 13px; line-height: 1.65;">"{{ \Illuminate\Support\Str::limit(strip_tags($testimonial->content), 180) }}"</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if($externalReviewUrl !== '' || $externalReviewScreenshot !== '')
                <section class="mb-5" aria-labelledby="external-proof-heading">
                    <div class="p-4 p-lg-5 bg-white border border-zinc-100 rounded-xl shadow-sm">
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-5">
                                <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Verified external reviews</span>
                                <h2 id="external-proof-heading" class="h3 fw-black text-brand-dark mt-2 mb-3">Independent review information</h2>
                                @if($externalReviewNote !== '')
                                    <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">{{ $externalReviewNote }}</p>
                                @endif
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
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if($helpfulBlogs->isNotEmpty())
                <section class="mb-5" aria-labelledby="helpful-guides-heading">
                    <div class="mb-4">
                        <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Related reading</span>
                        <h2 id="helpful-guides-heading" class="h3 fw-black text-brand-dark mt-2 mb-2">Helpful Guides</h2>
                        <p class="text-zinc-600 mb-0" style="font-size: 13px;">Read more about this subject and how to prepare for learning.</p>
                    </div>
                    <div class="row g-4">
                        @foreach($helpfulBlogs as $helpfulBlog)
                            <div class="col-lg-4 col-md-6">
                                <article class="premium-card h-100 overflow-hidden d-flex flex-column">
                                    <img src="{{ \App\Support\PublicAsset::url($helpfulBlog->image ?? null, 'site/img/carousel-1.png') }}" alt="{{ $helpfulBlog->title }}" class="w-100 object-cover" loading="lazy" decoding="async" width="640" height="360" style="aspect-ratio: 16 / 9;">
                                    <div class="p-4 d-flex flex-column flex-grow-1">
                                        @if(filled($helpfulBlog->category))
                                            <small class="text-brand-gold fw-black text-uppercase tracking-widest mb-2" style="font-size: 8px;">{{ $helpfulBlog->category }}</small>
                                        @endif
                                        <h3 class="h6 fw-black text-brand-dark mb-2">{{ $helpfulBlog->title }}</h3>
                                        <p class="text-zinc-600 mb-4" style="font-size: 12px; line-height: 1.65;">{{ \Illuminate\Support\Str::limit(strip_tags($helpfulBlog->content), 105) }}</p>
                                        <a href="{{ route('blog-detail', $helpfulBlog->slug) }}" data-cta="helpful-guide-from-course" data-track-event="course_helpful_guide_click" data-source-page="course-detail" data-source-section="helpful-guides" data-selected-course="{{ $course->slug }}" class="text-brand-dark fw-black text-decoration-none mt-auto" style="font-size: 10px;">Read Helpful Guide <i class="fa fa-arrow-right ms-1" aria-hidden="true"></i></a>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($faqs->isNotEmpty())
                <section class="mb-5" aria-labelledby="faq-heading">
                    <div class="text-center mb-4">
                        <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">FAQs</span>
                        <h2 id="faq-heading" class="h3 fw-black text-brand-dark mt-2 mb-2">Questions before joining this course</h2>
                    </div>
                    <div class="accordion" id="courseFaqAccordion">
                        @foreach($faqs as $index => $faq)
                            <div class="faq-premium-item mb-3" id="course-faq-{{ $faq->id }}">
                                <h3 class="accordion-header" id="courseFaqHeading{{ $faq->id }}">
                                    <button class="faq-premium-btn {{ $index !== 0 ? 'collapsed' : '' }} rounded-xl shadow-sm py-3 px-4"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#courseFaqCollapse{{ $faq->id }}"
                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                            aria-controls="courseFaqCollapse{{ $faq->id }}">
                                        <span class="font-black tracking-tight">{{ $faq->question }}</span>
                                        <i class="fas fa-plus text-[9px]" aria-hidden="true"></i>
                                    </button>
                                </h3>
                                <div id="courseFaqCollapse{{ $faq->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#courseFaqAccordion" aria-labelledby="courseFaqHeading{{ $faq->id }}">
                                    <div class="faq-premium-body bg-zinc-50/50 p-4 rounded-b-xl border-x border-b border-zinc-100">
                                        @sanitize($faq->answer)
                                    </div>
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
                            <span class="text-brand-gold fw-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Need help deciding?</span>
                            <h2 id="inquiry-heading" class="h3 fw-black text-white mt-2 mb-3">Want to check if {{ $course->name }} fits you?</h2>
                            <p class="text-white/70 mb-0" style="font-size: 14px; line-height: 1.7;">Tell us your goal, current level and preferred time to study. We will explain whether this course may be a suitable next step.</p>
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
