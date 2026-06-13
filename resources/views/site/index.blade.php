@extends('site.layout.app')
@section('page_title', 'Golden Eye Academy | Established Academy in Pokhara Since 2008')
@section('meta_description', 'Established in 2008, Golden Eye Academy offers IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes in Pokhara, Nepal.')
@php
    $homeHeroImage = \App\Support\PublicAsset::url($settings['hero_image'] ?? null, 'site/img/carousel-1.png');
@endphp
@section('preload_assets')
    <link rel="preload" as="image" href="{{ $homeHeroImage }}" fetchpriority="high">
@endsection

	@section('content')
	    @php
	        $heroImage = $homeHeroImage;
	        $heroBadge = trim((string) ($settings['hero_badge_text'] ?? 'Golden Eye Academy, Pokhara')) ?: 'Golden Eye Academy, Pokhara';
	        $heroTitle = trim((string) ($settings['hero_hook_headline'] ?? $settings['hero_title'] ?? 'Established academy in Pokhara since 2008.')) ?: 'Established academy in Pokhara since 2008.';
	        $heroBody = trim(strip_tags((string) ($settings['hero_hook_body'] ?? $settings['hero_subtitle'] ?? 'Golden Eye Academy offers practical classes and skill-based batches for IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT learners in Pokhara.'))) ?: 'Golden Eye Academy offers practical classes and skill-based batches for IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT learners in Pokhara.';
	        $heroPrimaryCta = trim((string) ($settings['hero_cta_1_text'] ?? $settings['hero_cta_text'] ?? 'Ask for Course Help')) ?: 'Ask for Course Help';
	        $heroPrimaryCta = strtolower($heroPrimaryCta) === 'ask for course guidance' ? 'Ask for Course Help' : $heroPrimaryCta;
	        $heroSecondaryCta = trim((string) ($settings['hero_cta_2_text'] ?? 'View Course Details')) ?: 'View Course Details';
	        $guidanceUrl = fn (string $sourceSection, string $selectedCourse = 'undecided') => route('join-now', [
	            'course' => $selectedCourse,
	            'selected_course' => $selectedCourse,
	            'source_page' => 'home',
            'source_section' => $sourceSection,
            'inquiry_intent' => 'course_guidance',
        ]);
        $whatsappCleanNumber = str_replace(['+', ' ', '-'], '', $settings['whatsapp_number'] ?? '9779856058599');
        $whatsappMessage = rawurlencode($settings['whatsapp_prefill_message'] ?? 'Hi Golden Eye Academy, I have a question about classes and enrollment.');
        $courseBenefit = function ($course): string {
            $description = trim(strip_tags((string) $course->description));
            $sentence = trim(\Illuminate\Support\Str::before($description, '.'));

            return \Illuminate\Support\Str::limit($sentence ?: 'Build practical skills with guided classes and clear next steps', 92);
        };
        $bestFor = function ($course): string {
            $text = strtolower($course->name.' '.$course->category.' '.$course->badge_text);

            return match (true) {
                str_contains($text, 'ielts'), str_contains($text, 'pte') => 'Exam preparation learners',
                str_contains($text, 'japanese'), str_contains($text, 'korean') => 'Language learners',
                str_contains($text, 'office'), str_contains($text, 'computer') => 'Job and office skills',
                str_contains($text, 'web'), str_contains($text, 'it') => 'IT career starters',
                default => 'Students choosing a practical next step',
            };
        };
        $testimonialProgress = function ($testimonial): string {
            $content = trim(strip_tags((string) ($testimonial->content ?? '')));
            $sentence = trim(\Illuminate\Support\Str::before($content, '.'));

            return \Illuminate\Support\Str::limit($sentence ?: $content ?: 'Progress details can be added from testimonials.', 105);
        };
        $teacherCourseNames = function ($teacher) use ($courses): string {
            $matches = collect($courses ?? [])
                ->filter(fn ($course) => trim((string) $course->instructor) === trim((string) $teacher->name))
                ->pluck('name')
                ->take(3)
                ->implode(', ');

            return $matches ?: 'Classroom practice and academic support';
        };
        $externalReviewUrl = trim((string) ($settings['google_business_profile_url'] ?? ''));
        $externalReviewScreenshot = trim((string) ($settings['external_review_screenshot'] ?? ''));
        $externalReviewNote = trim((string) ($settings['external_review_proof_note'] ?? 'Ask the academy team for current Google review proof or verified review screenshots before enrollment.'));
        $audienceSegments = [
            [
                'icon' => 'fa fa-graduation-cap',
                'title' => 'I am a Student',
                'problem' => 'Not sure which course to choose after school or college?',
                'benefit' => 'Compare options by goal, timing, and current level.',
                'source' => 'audience-student',
                'route' => route('for-students'),
            ],
            [
                'icon' => 'fa fa-users',
                'title' => 'I am a Parent',
                'problem' => 'Need clarity on fees, timing, safety, and course fit?',
                'benefit' => 'We explain course fit, fees, timing, expected outcomes, and next steps.',
                'source' => 'audience-parent',
                'route' => route('for-parents'),
            ],
            [
                'icon' => 'fa fa-plane',
                'title' => 'I need IELTS / PTE',
                'problem' => 'Comparing IELTS, PTE, Japanese, or Korean classes?',
                'benefit' => 'Match exam preparation with your goal and batch timing.',
                'source' => 'audience-study-abroad',
                'route' => route('study-abroad-guidance'),
            ],
            [
                'icon' => 'fa fa-laptop-code',
                'title' => 'I want Job/Computer Skills',
                'problem' => 'Want practical skills for work, office, or IT?',
                'benefit' => 'Start with a skill path that fits your level.',
                'source' => 'audience-job-computer-skills',
                'route' => route('job-computer-skills'),
            ],
        ];
        $trustItems = [
            'Trusted by students in Pokhara since 2008',
            'Established in 2008',
            $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal',
            'Phone: '.($settings['site_phone'] ?? '061-572599'),
            'Email: '.($settings['site_email'] ?? 'goldeneyeacademy2008@gmail.com'),
            'Morning, day, and evening batches',
        ];
        $localAdvantages = [
            $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal',
            'Academic support before enrollment',
            'IELTS/PTE, language, computer, office, and IT classes',
            'Phone and WhatsApp follow-up: '.($settings['site_phone'] ?? '061-572599'),
        ];
        $parentClarityItems = [
            'Course fit before payment',
            'Fee and duration clarity',
            'Batch timing support',
            'Realistic outcomes, not guarantees',
        ];
    @endphp

	    <section class="container-fluid p-0 position-relative overflow-hidden home-hero" style="background: linear-gradient(135deg, rgba(5, 12, 28, 0.94), rgba(5, 12, 28, 0.78)), url('{{ $heroImage }}'); background-size: cover; background-position: center; color: white;">
	        <div class="container py-5">
	            <div class="row align-items-center g-4 home-hero-row">
	                <div class="col-lg-8 py-5 home-hero-copy">
	                    <span class="badge rounded-pill bg-brand-gold px-4 py-2 text-[8px] fw-black text-brand-dark text-uppercase tracking-[4px] mb-4">
	                        {{ $heroBadge }}
	                    </span>
	                    <h1 class="hero-hook-title font-black mb-4 text-white" style="font-size: clamp(2.2rem, 5vw, 4.4rem); line-height: 1.02; letter-spacing: 0;">
	                        {{ $heroTitle }}
	                    </h1>
	                    <p class="hero-hook-body mb-5 text-white/90" style="font-weight: 500; max-width: 760px; line-height: 1.7; font-size: 16px;">
	                        {{ $heroBody }}
	                    </p>
	                    <div class="d-flex flex-column flex-sm-row gap-3 home-hero-actions">
	                        <a href="{{ $guidanceUrl('hero') }}" data-cta="hero-course-help" data-cta-label="{{ $heroPrimaryCta }}" class="btn btn-primary py-3 px-5 rounded-pill shadow-xl font-black uppercase tracking-widest" style="font-size: 10px;">
	                            {{ $heroPrimaryCta }}
	                        </a>
	                        <a href="{{ route('courses-all') }}" data-cta="hero-course-details" data-cta-label="{{ $heroSecondaryCta }}" class="btn btn-outline-light py-3 px-5 rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">
	                            {{ $heroSecondaryCta }}
	                        </a>
	                    </div>
	                </div>
            </div>
        </div>
    </section>

    <section class="py-4 bg-brand-dark border-y border-brand-gold/10">
        <div class="container">
            <div class="row g-3 align-items-center">
                @foreach($trustItems as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="d-flex align-items-start gap-2 text-white h-100">
                            <i class="fa fa-check-circle text-brand-gold mt-1" style="font-size: 13px;"></i>
                            <span class="fw-black" style="font-size: 12px; line-height: 1.45;">{{ $item }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-4">
                <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Start here</span>
                <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Which class are you interested in?</h2>
            </div>
            <div class="row g-3">
                @foreach($audienceSegments as $segment)
                    <div class="col-md-6 col-xl-3">
                        <a href="{{ $segment['route'] }}" data-cta="{{ $segment['source'] }}" class="audience-card-link-wrapper d-block h-100 text-decoration-none" aria-label="{{ $segment['title'] }} information page">
                            <article class="audience-card h-100 p-4 bg-zinc-50 border border-zinc-100">
                                <div class="audience-card-icon mb-3">
                                    <i class="{{ $segment['icon'] }}"></i>
                                </div>
                                <h3 class="h6 fw-black text-brand-dark mb-3">{{ $segment['title'] }}</h3>
                                <p class="text-zinc-600 mb-2" style="font-size: 12px; line-height: 1.6;">{{ $segment['problem'] }}</p>
                                <p class="text-brand-dark fw-bold mb-4" style="font-size: 12px; line-height: 1.5;">{{ $segment['benefit'] }}</p>
                                <span class="audience-card-link">View Course Details</span>
                            </article>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if(isset($courses) && $courses->count() > 0)
        <section class="py-5 bg-zinc-50/60">
            <div class="container">
                <div class="row justify-content-between align-items-end mb-4 g-3">
                    <div class="col-lg-7">
                        <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Popular courses</span>
                        <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Courses students ask about most</h2>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('courses-all') }}" data-cta="homepage-all-course-details" class="btn btn-outline-brand-dark px-5 py-3 rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">View Course Details</a>
                    </div>
                </div>
                <div class="row g-4">
                    @foreach($courses->take(4) as $course)
                        <div class="col-lg-3 col-md-6">
                            <article class="premium-card p-0 border border-zinc-100 shadow-sm rounded-xl overflow-hidden h-100 d-flex flex-column bg-white">
                                <div class="aspect-[16/9] overflow-hidden bg-zinc-100">
                                    <img class="img-fluid w-100 h-100 object-cover" loading="lazy" decoding="async" width="640" height="360" src="{{ \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg') }}" alt="{{ $course->name }}" onerror="this.src='{{ asset('site/img/cat-1.jpg') }}'">
                                </div>
                                <div class="p-4 flex-grow-1 d-flex flex-column">
                                    <h3 class="h6 fw-black text-brand-dark mb-2" style="line-height: 1.3;">{{ $course->name }}</h3>
                                    <p class="text-zinc-600 mb-3" style="font-size: 12px; line-height: 1.55;">{{ $courseBenefit($course) }}</p>
                                    <div class="d-grid gap-2 mb-4 mt-auto">
                                        <span class="text-brand-dark fw-bold" style="font-size: 11px;"><i class="fa fa-clock text-brand-gold me-2"></i>{{ $course->duration }}</span>
                                        <span class="text-brand-dark fw-bold" style="font-size: 11px;"><i class="fa fa-tag text-brand-gold me-2"></i>{{ $course->price ?: 'Fee available on request' }}</span>
                                        <span class="text-brand-dark fw-bold" style="font-size: 11px;"><i class="fa fa-calendar text-brand-gold me-2"></i>Morning, day, or evening batch</span>
                                        <span class="text-zinc-500" style="font-size: 11px;">Best for: {{ $bestFor($course) }}</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('courses-detail', $course->slug) }}" data-cta="homepage-course-details" class="btn btn-primary py-2.5 rounded-lg font-black uppercase tracking-widest" style="font-size: 9px;">View Course Details</a>
                                        <a href="{{ $guidanceUrl('homepage-course-card', $course->slug) }}" data-cta="homepage-course-guidance" class="btn btn-outline-brand-dark py-2.5 rounded-lg font-black uppercase tracking-widest" style="font-size: 9px;">Ask for Course Help</a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(isset($categories) && count($categories) > 0)
        <section class="py-5 bg-white">
            <div class="container">
                <div class="mb-4">
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Course categories</span>
                    <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Browse by learning goal</h2>
                </div>
                <div class="row g-3">
                    @foreach($categories->take(6) as $category)
                        <div class="col-md-6 col-lg-4">
                            <a href="{{ route('courses-all', ['category' => $category->slug]) }}" class="text-decoration-none">
                                <article class="h-100 p-4 border border-zinc-100 rounded-xl bg-zinc-50">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <h3 class="h6 fw-black text-brand-dark mb-2">{{ $category->name }}</h3>
                                            <p class="text-zinc-500 mb-0" style="font-size: 12px;">{{ $category->courses_count ?? 0 }} course{{ (($category->courses_count ?? 0) === 1) ? '' : 's' }}</p>
                                        </div>
                                        <span class="w-10 h-10 rounded-lg bg-brand-gold text-brand-dark d-inline-flex align-items-center justify-content-center flex-shrink-0">
                                            <i class="fa fa-arrow-right" style="font-size: 11px;"></i>
                                        </span>
                                    </div>
                                </article>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="py-5 bg-brand-dark text-white">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Why Golden Eye Academy</span>
                    <h2 class="h3 fw-black text-white mt-2 mb-3">Practical classes with academic support</h2>
                    <p class="text-white/70 mb-0" style="font-size: 14px; line-height: 1.7;">Students can prepare with mock tests, practical assignments, instructor feedback, and clear weekly progress where the course requires it.</p>
                </div>
                <div class="col-lg-7">
                    <div class="row g-3">
                        @foreach($localAdvantages as $advantage)
                            <div class="col-sm-6">
                                <div class="d-flex gap-3 align-items-start p-3 bg-white/10 border border-white/10 rounded-xl h-100">
                                    <i class="fa fa-check text-brand-gold mt-1"></i>
                                    <span class="fw-bold" style="font-size: 13px; line-height: 1.5;">{{ $advantage }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(isset($testimonials) && $testimonials->count() > 0)
        <section class="py-5 bg-white">
            <div class="container">
                <div class="text-center mb-4">
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Student results</span>
                    <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Real students. Practical progress.</h2>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach($testimonials->take(3) as $testimonial)
                        <div class="col-lg-4 col-md-6">
                            <article class="premium-card p-4 h-100 border border-zinc-100 shadow-sm rounded-xl bg-zinc-50">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <img src="{{ \App\Support\PublicAsset::url($testimonial->photo ?? null, 'site/img/testimonial-1.jpg') }}" onerror="this.src='{{ asset('site/img/testimonial-1.jpg') }}'" alt="{{ $testimonial->student_name ?? 'Golden Eye student' }}" class="rounded-circle object-cover border border-white shadow-sm" loading="lazy" decoding="async" width="54" height="54" style="width: 54px; height: 54px;">
                                    <div>
                                        <h3 class="mb-1 fw-black text-brand-dark" style="font-size: 13px;">{{ $testimonial->student_name ?? 'Student' }}</h3>
                                        <small class="text-zinc-500 fw-bold d-block" style="font-size: 10px;">Course completed: {{ $testimonial->course_name ?? 'Golden Eye learner' }}</small>
                                    </div>
                                </div>
                                <p class="text-brand-dark fw-black mb-2" style="font-size: 12px; line-height: 1.55;">Specific progress/result: {{ $testimonialProgress($testimonial) }}</p>
                                <p class="text-zinc-600 mb-0" style="font-size: 13px; line-height: 1.7;">"{{ \Illuminate\Support\Str::limit(strip_tags($testimonial->content ?? ''), 135) }}"</p>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(isset($teachers) && $teachers->count() > 0)
        <section class="py-5 bg-zinc-50/60">
            <div class="container">
                <div class="row justify-content-between align-items-end mb-4 g-3">
                    <div class="col-lg-8">
                        <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Instructors</span>
                        <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Experienced faculty for practical classes</h2>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach($teachers->take(4) as $teacher)
                        <div class="col-md-6 col-lg-3">
                            <article class="h-100 p-4 bg-white border border-zinc-100 rounded-xl">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <img src="{{ \App\Support\PublicAsset::url($teacher->photo ?? null, 'site/img/team-1.jpg') }}" alt="{{ $teacher->name }}" class="rounded-circle object-cover" loading="lazy" decoding="async" width="58" height="58" style="width: 58px; height: 58px;">
                                    <div>
                                        <h3 class="mb-1 fw-black text-brand-dark" style="font-size: 13px;">{{ $teacher->name }}</h3>
                                        <p class="mb-0 text-zinc-500 fw-bold" style="font-size: 10px; line-height: 1.4;">{{ $teacher->designation }}</p>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <p class="mb-0 text-zinc-600" style="font-size: 11px; line-height: 1.55;"><strong class="text-brand-dark">Subject expertise:</strong> {{ $teacher->designation ?: 'Academic support' }}</p>
                                    <p class="mb-0 text-zinc-600" style="font-size: 11px; line-height: 1.55;"><strong class="text-brand-dark">Class support:</strong> Practical lessons, student questions, and progress feedback</p>
                                    <p class="mb-0 text-zinc-600" style="font-size: 11px; line-height: 1.55;"><strong class="text-brand-dark">Courses taught:</strong> {{ $teacherCourseNames($teacher) }}</p>
                                    <p class="mb-0 text-zinc-600" style="font-size: 11px; line-height: 1.55;"><strong class="text-brand-dark">Credibility note:</strong> {{ \Illuminate\Support\Str::limit(strip_tags($teacher->bio ?? 'Supports students with classroom practice, academic support, and feedback.'), 110) }}</p>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if($externalReviewNote !== '' || $externalReviewUrl !== '' || $externalReviewScreenshot !== '')
        <section class="py-5 bg-white">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-5">
                        <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">External social proof</span>
                        <h2 class="h3 fw-black text-brand-dark mt-2 mb-3">Review proof can be verified before enrollment</h2>
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

    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">For parents</span>
                    <h2 class="h3 fw-black text-brand-dark mt-2 mb-3">Clear answers before your child enrolls</h2>
                    <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">For parents, we explain course fit, fees, timing, expected outcomes, and realistic next steps before enrollment. No pressure. Visit, call, or message us to understand the right option for your child.</p>
                </div>
                <div class="col-lg-7">
                    <div class="row g-3">
                        @foreach($parentClarityItems as $item)
                            <div class="col-sm-6">
                                <div class="p-4 bg-zinc-50 border border-zinc-100 rounded-xl h-100">
                                    <i class="fa fa-check-circle text-brand-gold mb-3"></i>
                                    <p class="mb-0 fw-black text-brand-dark" style="font-size: 13px;">{{ $item }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(isset($faqs) && $faqs->count() > 0)
        <section class="py-5 bg-zinc-50/60">
            <div class="container">
                <div class="text-center mb-4">
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">FAQ</span>
                    <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Common questions before enrollment</h2>
                </div>
                <div class="row g-3 justify-content-center">
                    @foreach($faqs as $faq)
                        <div class="col-lg-6">
                            <article class="p-4 bg-white border border-zinc-100 rounded-xl h-100">
                                <h3 class="h6 fw-black text-brand-dark mb-2">{{ $faq->question }}</h3>
                                <p class="text-zinc-600 mb-0" style="font-size: 13px; line-height: 1.65;">{{ \Illuminate\Support\Str::limit(strip_tags($faq->answer), 150) }}</p>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="conversion-final py-5">
        <div class="container">
            <div class="row align-items-center justify-content-between g-4">
                <div class="col-lg-8">
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Next step</span>
                    <h2 class="h3 fw-black text-white mt-3 mb-3">Need course and batch information?</h2>
                    <p class="text-white/70 mb-0" style="font-size: 14px; line-height: 1.7;">Send your goal. Our academy team will explain suitable classes before enrollment.</p>
                </div>
                <div class="col-lg-4 d-flex flex-column gap-2">
                    <a href="{{ $guidanceUrl('homepage-final') }}" data-cta="homepage-final-course-guidance" class="btn btn-primary py-3 rounded-xl font-black uppercase tracking-widest">Ask for Course Help</a>
                    <a href="https://wa.me/{{ $whatsappCleanNumber }}?text={{ $whatsappMessage }}" target="_blank" rel="noopener" data-cta="homepage-final-whatsapp" class="btn btn-outline-light py-3 rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">Message on WhatsApp</a>
                </div>
            </div>
        </div>
    </section>
@endsection
