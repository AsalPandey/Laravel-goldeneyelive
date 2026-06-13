@extends('site.layout.app')
@section('page_title', 'Golden Eye Academy Catalogue - Courses, Classes & Learning Paths')
@section('meta_description', 'Explore Golden Eye Academy courses, service areas, and learning paths. Compare class options, then message the academy team before enrolling.')

@section('content')
    @php
        $whatsappCleanNumber = str_replace(['+', ' ', '-'], '', $settings['whatsapp_number'] ?? '9779856058599');
        $whatsappMessage = rawurlencode($settings['whatsapp_prefill_message'] ?? 'Hi Golden Eye Academy, I have a question about classes and enrollment.');
        $guidanceUrl = fn (string $sourceSection, string $selectedCourse = 'undecided') => route('join-now', [
            'course' => $selectedCourse,
            'selected_course' => $selectedCourse,
            'source_page' => 'catalogue',
            'source_section' => $sourceSection,
            'inquiry_intent' => 'course_guidance',
        ]);
    @endphp

    <section class="py-5 bg-brand-dark text-white">
        <div class="container py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Full Catalogue</span>
                    <h1 class="display-5 fw-black mt-3 mb-3 text-white" style="letter-spacing: 0;">Courses, services, and next-step paths in one place.</h1>
                    <p class="text-white/75 mb-0" style="font-size: 15px; line-height: 1.8; max-width: 760px;">Browse the full Golden Eye Academy offer, compare classes that fit your goal, then send a quick message before you enroll.</p>
                </div>
                <div class="col-lg-4 d-flex flex-column gap-2">
                    <a href="{{ $guidanceUrl('catalogue-hero') }}" data-cta="catalogue-course-guidance" class="btn btn-primary py-3 rounded-xl font-black uppercase tracking-widest">Ask for Course Help</a>
                    <a href="https://wa.me/{{ $whatsappCleanNumber }}?text={{ $whatsappMessage }}" target="_blank" rel="noopener" data-cta="catalogue-whatsapp" class="btn btn-outline-light py-3 rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">Message on WhatsApp</a>
                </div>
            </div>
        </div>
    </section>

    @if(isset($servicePillars) && $servicePillars->count() > 0)
        <section class="py-5 bg-white">
            <div class="container">
                <div class="row justify-content-between align-items-end mb-4 g-3">
                    <div class="col-lg-8">
                        <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Service Catalogue</span>
                        <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Choose by support area.</h2>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ $guidanceUrl('catalogue-service') }}" data-cta="catalogue-service-guidance" class="btn btn-outline-brand-dark px-5 py-3 rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">Ask for Course Help</a>
                    </div>
                </div>
                <div class="row g-4">
                    @foreach($servicePillars as $pillar)
                        <div class="col-lg-4 col-md-6">
                            <article class="premium-card h-100 p-4">
                                <div class="w-11 h-11 rounded-xl bg-brand-dark text-brand-gold d-flex align-items-center justify-content-center mb-4">
                                    <i class="{{ $pillar->icon ?: 'fa fa-star' }}"></i>
                                </div>
                                <h3 class="h6 fw-black text-brand-dark mb-3" style="line-height: 1.35;">{{ $pillar->title }}</h3>
                                @if($pillar->summary)
                                    <p class="text-zinc-600 mb-4" style="font-size: 12px; line-height: 1.65;">{{ $pillar->summary }}</p>
                                @endif
                                @if(is_array($pillar->bullets) && count($pillar->bullets) > 0)
                                    <div class="d-grid gap-2 mb-4">
                                        @foreach(array_slice($pillar->bullets, 0, 3) as $bullet)
                                            <div class="d-flex gap-2 align-items-start">
                                                <i class="fa fa-check text-brand-gold mt-1" style="font-size: 10px;"></i>
                                                <span class="text-zinc-600" style="font-size: 11px; line-height: 1.55;">{{ $bullet }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <a href="{{ $pillar->cta_url ?: $guidanceUrl('catalogue-service-'.$pillar->slug) }}" data-cta="catalogue-service-{{ $pillar->slug }}" class="text-brand-dark font-black uppercase tracking-widest text-decoration-none hover:text-brand-gold transition-all" style="font-size: 9px;">
                                    Ask for Course Help <i class="fa fa-arrow-right ms-1"></i>
                                </a>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(isset($catalogueCategories) && $catalogueCategories->count() > 0)
        <section class="py-5 bg-zinc-50/50">
            <div class="container">
                <div class="mb-4">
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Course Catalogue</span>
                    <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Browse by category.</h2>
                </div>
                <div class="row g-4">
                    @foreach($catalogueCategories as $category)
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('courses-all', ['category' => $category->slug]) }}" data-cta="catalogue-category-{{ $category->slug }}" class="text-decoration-none">
                                <article class="premium-card h-100 overflow-hidden">
                                    <div class="aspect-[16/9] overflow-hidden">
                                        <img class="w-100 h-100 object-cover" src="{{ \App\Support\PublicAsset::url($category->image ?? null, 'site/img/cat-1.jpg') }}" alt="{{ $category->name }}">
                                    </div>
                                    <div class="p-4">
                                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                            <h3 class="h6 fw-black text-brand-dark mb-0" style="line-height: 1.35;">{{ $category->name }}</h3>
                                            <span class="bg-brand-gold text-brand-dark px-2 py-1 rounded-full font-black uppercase tracking-widest" style="font-size: 8px;">{{ $category->courses_count }} Courses</span>
                                        </div>
                                        @if($category->description)
                                            <p class="text-zinc-600 mb-0" style="font-size: 12px; line-height: 1.65;">{{ Str::limit(strip_tags($category->description), 125) }}</p>
                                        @endif
                                    </div>
                                </article>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(isset($catalogueCourses) && $catalogueCourses->count() > 0)
        <section class="py-5 bg-white">
            <div class="container">
                <div class="row justify-content-between align-items-end mb-4 g-3">
                    <div class="col-lg-8">
                        <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Course Information</span>
                        <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Open a course, or ask us to compare options.</h2>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('courses-all') }}" data-cta="catalogue-all-courses" class="btn btn-outline-brand-dark px-5 py-3 rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">View Course Details</a>
                    </div>
                </div>
                <div class="row g-4">
                    @foreach($catalogueCourses as $course)
                        <div class="col-lg-4 col-md-6">
                            <article class="premium-card p-0 overflow-hidden h-100 d-flex flex-column">
                                <div class="aspect-[16/9] overflow-hidden position-relative">
                                    <span class="position-absolute top-0 start-0 bg-brand-gold text-brand-dark px-2.5 py-1 m-3 rounded-full text-[7.5px] font-black uppercase tracking-[0.3em] shadow-lg z-10">
                                        {{ $course->badge_text ?? 'Available' }}
                                    </span>
                                    <img class="img-fluid w-100 h-100 object-cover" src="{{ \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg') }}" alt="{{ $course->name }}">
                                </div>
                                <div class="p-4 flex-grow-1 d-flex flex-column">
                                    <small class="text-brand-gold font-black uppercase tracking-[0.25em] mb-2" style="font-size: 8px;">{{ $course->courseCategory?->name ?? $course->category }}</small>
                                    <h3 class="h6 fw-black text-brand-dark mb-2" style="line-height: 1.35;">{{ $course->name }}</h3>
                                    <p class="text-zinc-600 mb-4" style="font-size: 12px; line-height: 1.65;">{{ Str::limit(strip_tags($course->description), 120) }}</p>
                                    <div class="d-flex flex-wrap gap-2 mb-4 mt-auto">
                                        <span class="bg-zinc-50 rounded-full px-3 py-1.5 border border-zinc-100 fw-black text-brand-dark uppercase tracking-widest" style="font-size: 8px;">{{ $course->duration }}</span>
                                        <span class="bg-zinc-50 rounded-full px-3 py-1.5 border border-zinc-100 fw-black text-brand-dark uppercase tracking-widest" style="font-size: 8px;">{{ $course->price }}</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('courses-detail', $course->slug) }}" data-cta="catalogue-course-details" class="btn btn-primary py-2 rounded-xl font-black uppercase tracking-widest" style="font-size: 9px;">View Course Details</a>
                                        <a href="{{ $guidanceUrl('catalogue-course-card', $course->slug) }}" data-cta="catalogue-course-guidance" class="btn btn-outline-brand-dark py-2 rounded-xl font-black uppercase tracking-widest" style="font-size: 9px;">Ask for Course Help</a>
                                    </div>
                                </div>
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
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Still comparing?</span>
                    <h2 class="h3 fw-black text-white mt-3 mb-3">Send your goal. We will point you to the right catalogue item.</h2>
                    <p class="text-white/70 mb-0" style="font-size: 14px; line-height: 1.7;">A short question is enough. Tell us your class level, goal, and preferred timing.</p>
                </div>
                <div class="col-lg-4 d-flex flex-column gap-2">
                    <a href="{{ $guidanceUrl('catalogue-final') }}" data-cta="catalogue-final-course-guidance" class="btn btn-primary py-3 rounded-xl font-black uppercase tracking-widest">Ask for Course Help</a>
                    <a href="https://wa.me/{{ $whatsappCleanNumber }}?text={{ $whatsappMessage }}" target="_blank" rel="noopener" data-cta="catalogue-final-whatsapp" class="btn btn-outline-light py-3 rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">Message on WhatsApp</a>
                </div>
            </div>
        </div>
    </section>
@endsection
