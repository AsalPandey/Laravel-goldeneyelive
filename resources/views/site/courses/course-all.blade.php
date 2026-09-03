@extends('site.layout.app')

@section('page_title', $settings['courses_title'] ?? 'Computer, Language and IELTS/PTE Courses in Pokhara')
@section('meta_description', trim(strip_tags($settings['courses_subtitle'] ?? 'Explore practical computer, office and web-development courses, alongside language and IELTS/PTE classes. Compare learning areas, fees and durations.')))
@if(request()->filled('search') || request()->filled('category'))
    @section('robots', 'noindex, follow')
    @section('canonical_url', \App\Support\CanonicalUrl::route('courses-all'))
@endif

@section('content')
    @php
        $guidanceUrl = fn (string $sourceSection, string $selectedCourse = 'undecided') => route('join-now', [
            'course' => $selectedCourse,
            'selected_course' => $selectedCourse,
            'source_page' => 'courses-all',
            'source_section' => $sourceSection,
            'inquiry_intent' => 'course_guidance',
        ]);
    @endphp

    @if(!request()->filled('search') && !request()->filled('category') && $courses->total() >= 3)
        <script type="application/ld+json">
        {
          "@@context": "https://schema.org",
          "@@type": "ItemList",
          "itemListElement": [
            @foreach($courses as $index => $course)
            {
              "@@type": "ListItem",
              "position": {{ (($courses->currentPage() - 1) * $courses->perPage()) + $index + 1 }},
              "item": {
                "@@type": "Course",
                "url": "{{ \App\Support\CanonicalUrl::route('courses-detail', ['slug' => $course->slug]) }}",
                "name": @json($course->name),
                "description": @json(strip_tags($course->description)),
                "provider": {
                  "@@id": "{{ \App\Support\StructuredData::organizationId() }}"
                }
              }
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
          ]
        }
        </script>
    @endif

    <section class="py-5 bg-brand-dark text-white">
        <div class="container py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">{{ $settings['courses_all_tagline'] ?? 'Courses and current batches' }}</span>
                    <h1 class="display-5 fw-black mt-3 mb-3 text-white" style="letter-spacing: 0;">{{ $settings['courses_all_title'] ?? 'Explore practical classes by subject.' }}</h1>
                    <div class="text-white/75 mb-0" style="font-size: 15px; line-height: 1.8;">
                        @sanitize($settings['courses_subtitle'] ?? 'Explore active courses by subject, compare the learning focus and ask about the current batch that fits your goal.')
                    </div>
                </div>
                <div class="col-lg-5">
                    <form action="{{ route('courses-all') }}" method="GET" class="bg-white/10 border border-white/10 rounded-2xl p-3 shadow-2xl" data-track-event="course_filter_used" data-source-page="courses-all" data-source-section="courses-filter" data-cta-label="Apply Filters">
                        <div class="row g-2">
                            <div class="col-12">
                                <label for="course-search" class="visually-hidden">Search courses</label>
                                <input id="course-search" type="search" name="search" value="{{ $search }}" class="form-control border-0 rounded-xl py-3 px-4" placeholder="Search IELTS, Korean, web, office skills...">
                            </div>
                            <div class="col-md-7">
                                <label for="course-category" class="visually-hidden">Filter by category</label>
                                <select id="course-category" name="category" class="form-select border-0 rounded-xl py-3 px-4">
                                    <option value="">All categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->slug }}" @selected($categorySlug === $category->slug)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 d-grid">
                                <button type="submit" data-cta="courses-filter-submit" class="btn btn-primary rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">Apply Filters</button>
                            </div>
                        </div>
                    </form>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="{{ route('courses-all') }}" class="text-white/70 text-decoration-none font-bold" style="font-size: 12px;" data-track-event="course_filter_used" data-source-page="courses-all" data-source-section="courses-filter-reset" data-cta-label="Reset filters">Reset filters</a>
                        <span class="text-white/30">|</span>
                        <a href="{{ $guidanceUrl('courses-hero') }}" data-cta="courses-hero-course-guidance" class="text-brand-gold text-decoration-none font-black" style="font-size: 12px;">Ask for Course Help</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(isset($featuredCourses) && $featuredCourses->count() > 0 && blank($search) && blank($categorySlug))
        <section class="py-5 bg-white border-bottom border-zinc-100">
            <div class="container">
                <div class="row justify-content-between align-items-end mb-4 g-3">
                    <div class="col-lg-7">
                        <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Popular courses</span>
                        <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Courses students ask about first.</h2>
                        <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">Start here if you want to compare common classes before choosing a batch.</p>
                    </div>
                </div>
                <div class="row g-4">
                    @foreach($featuredCourses as $course)
                        <div class="col-lg-4">
                            <article class="premium-card h-100 overflow-hidden d-flex flex-column">
                                <div class="position-relative aspect-[16/9] overflow-hidden bg-zinc-100">
                                    <img class="w-100 h-100 object-cover" loading="lazy" decoding="async" width="640" height="360" src="{{ \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg') }}" onerror="this.src='{{ asset('site/img/cat-1.jpg') }}'" alt="{{ $course->name }}">
                                    <span class="position-absolute top-0 start-0 bg-brand-gold text-brand-dark px-2.5 py-1 m-3 rounded-full font-black uppercase tracking-[0.25em]" style="font-size: 8px;">{{ $course->badge_text ?? 'Hot Course' }}</span>
                                </div>
                                <div class="p-4 flex-grow-1 d-flex flex-column">
                                    <h3 class="h6 fw-black text-brand-dark mb-2">{{ $course->name }}</h3>
                                    <p class="text-zinc-600 mb-4" style="font-size: 12px; line-height: 1.65;">{{ Str::limit(strip_tags($course->description), 120) }}</p>
                                    <div class="d-flex flex-wrap gap-2 mb-4 mt-auto">
                                        <span class="bg-zinc-50 rounded-full px-3 py-1.5 border border-zinc-100 fw-black text-brand-dark uppercase tracking-widest" style="font-size: 8px;">{{ $course->duration }}</span>
                                        <span class="bg-zinc-50 rounded-full px-3 py-1.5 border border-zinc-100 fw-black text-brand-dark uppercase tracking-widest" style="font-size: 8px;">{{ $course->price }}</span>
                                    </div>
                                    <div class="d-grid">
                                        <a href="{{ route('courses-detail', $course->slug) }}" data-cta="popular-course-details" class="btn btn-primary py-2 rounded-xl font-black uppercase tracking-widest" style="font-size: 9px;">View Course Details</a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="course-category-shortcuts py-4 bg-zinc-50/50 border-bottom border-zinc-100" aria-label="Course category shortcuts">
        <div class="container">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
                <a href="{{ route('courses-all') }}" class="btn {{ blank($categorySlug) ? 'btn-brand-dark' : 'btn-outline-brand-dark' }} rounded-pill px-4 py-2 font-black uppercase tracking-widest" style="font-size: 9px;" data-track-event="course_filter_used" data-source-page="courses-all" data-source-section="category-filter" data-cta-label="All">All</a>
                @foreach($categories as $category)
                    <a href="{{ route('courses-all', ['category' => $category->slug, 'search' => $search ?: null]) }}" class="btn {{ $categorySlug === $category->slug ? 'btn-brand-dark' : 'btn-outline-brand-dark' }} rounded-pill px-4 py-2 font-black uppercase tracking-widest" style="font-size: 9px;" data-track-event="course_filter_used" data-source-page="courses-all" data-source-section="category-filter" data-cta-label="{{ $category->name }}">
                        {{ $category->name }} <span class="opacity-60">({{ $category->courses_count }})</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="container-xxl py-5 bg-white">
        <div class="container">
            <div class="row justify-content-between align-items-end mb-4 g-3">
                <div class="col-lg-8">
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Course list</span>
                    <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">
                        @if($search || $categorySlug)
                            Matching courses
                        @else
                            All available courses
                        @endif
                    </h2>
                    <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">Open the course details first, then ask for class and enrollment support with the course context attached.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <span class="text-zinc-500 font-bold" style="font-size: 12px;">{{ $courses->total() }} course{{ $courses->total() === 1 ? '' : 's' }} found</span>
                </div>
            </div>

            @if($courses->count() > 0)
                <div class="row g-4 justify-content-center">
                    @foreach($courses as $course)
                        <div class="col-lg-4 col-md-6">
                            <article class="premium-card p-0 overflow-hidden h-100 d-flex flex-column border border-zinc-100 shadow-lg rounded-xl">
                                <div class="position-relative overflow-hidden aspect-[16/9] bg-zinc-100">
                                    <img class="w-100 h-100 object-cover" loading="lazy" decoding="async" width="640" height="360" src="{{ \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg') }}" onerror="this.src='{{ asset('site/img/cat-1.jpg') }}'" alt="{{ $course->name }}">
                                    <span class="position-absolute top-0 start-0 bg-brand-gold text-brand-dark px-2 py-1 m-3 rounded-full font-black uppercase tracking-widest shadow-lg" style="font-size: 8px;">{{ $course->badge_text ?? 'Available' }}</span>
                                </div>
                                <div class="p-4 flex-grow-1 d-flex flex-column">
                                    <small class="text-brand-gold font-black uppercase tracking-[0.25em] mb-2" style="font-size: 8px;">{{ $course->courseCategory?->name ?? $course->category }}</small>
                                    <h3 class="h6 mb-2 font-black text-brand-dark" style="line-height: 1.35;">{{ $course->name }}</h3>
                                    <p class="text-zinc-600 mb-4" style="font-size: 12px; line-height: 1.65;">{{ Str::limit(strip_tags($course->description), 120) }}</p>
                                    <div class="d-flex flex-wrap gap-2 mb-4 mt-auto">
                                        <span class="bg-zinc-50 rounded-full px-3 py-1.5 border border-zinc-100 fw-black text-brand-dark uppercase tracking-widest" style="font-size: 8px;"><i class="fa fa-clock text-brand-gold me-1"></i>{{ $course->duration }}</span>
                                        <span class="bg-zinc-50 rounded-full px-3 py-1.5 border border-zinc-100 fw-black text-brand-dark uppercase tracking-widest" style="font-size: 8px;"><i class="fa fa-tag text-brand-gold me-1"></i>{{ $course->price }}</span>
                                    </div>
                                    <div class="d-grid">
                                        <a href="{{ route('courses-detail', $course->slug) }}" data-cta="course-card-details" class="btn btn-primary py-2 rounded-lg font-black uppercase tracking-widest shadow-lg" style="font-size: 9px;">View Course Details</a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $courses->links() }}
                </div>
            @else
                <div class="text-center py-5 bg-zinc-50 rounded-2xl border border-zinc-100">
                    <h3 class="h5 fw-black text-brand-dark mb-2">No course matched that search.</h3>
                    <p class="text-zinc-600 mb-4">Try one subject, language, exam, or category at a time, or ask the academy team to suggest the closest current option.</p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="{{ route('courses-all') }}" class="btn btn-outline-brand-dark rounded-xl px-5 py-3 font-black uppercase tracking-widest" style="font-size: 10px;">Clear Search</a>
                        <a href="{{ $guidanceUrl('courses-empty') }}" data-cta="courses-empty-course-guidance" class="btn btn-primary rounded-xl px-5 py-3 font-black uppercase tracking-widest" style="font-size: 10px;">Ask for Course Help</a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
