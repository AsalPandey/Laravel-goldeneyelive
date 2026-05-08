@extends('site.layout.app')

@section('page_title', $settings['courses_title'] ?? 'Courses - GoldenEye Academy')
@section('meta_description', $settings['courses_subtitle'] ?? 'Browse GoldenEye Academy courses by category, search by goal, and ask the team before choosing your next course.')

@section('content')
    @if(count($courses) > 0)
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
                "url": "{{ route('courses-detail', $course->slug) }}",
                "name": @json($course->name),
                "description": @json(strip_tags($course->description))
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
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Find your next step</span>
                    <h1 class="display-5 fw-black mt-3 mb-3 text-white" style="letter-spacing: 0;">Choose a course by goal, not guesswork.</h1>
                    <p class="text-white/75 mb-0" style="font-size: 15px; line-height: 1.8;">Search by course, skill, language, exam, or job goal. The most relevant and seasonal options stay easy to find.</p>
                </div>
                <div class="col-lg-5">
                    <form action="{{ route('courses-all') }}" method="GET" class="bg-white/10 border border-white/10 rounded-2xl p-3 shadow-2xl">
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
                                <button type="submit" data-cta="courses-filter-submit" class="btn btn-primary rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">Find Courses</button>
                            </div>
                        </div>
                    </form>
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <a href="{{ route('courses-all') }}" class="text-white/70 text-decoration-none font-bold" style="font-size: 12px;">Reset filters</a>
                        <span class="text-white/30">|</span>
                        <a href="{{ route('join-now', ['course' => 'undecided']) }}" data-cta="courses-hero-course-help" class="text-brand-gold text-decoration-none font-black" style="font-size: 12px;">Not sure? Ask what fits you</a>
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
                        <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Popular right now</span>
                        <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Hot courses students ask about first.</h2>
                        <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">Start here if you want the courses students are asking about most right now.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('join-now', ['course' => 'undecided']) }}" data-cta="popular-course-help" class="btn btn-brand-dark px-5 py-3 rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">Ask What Fits Me</a>
                    </div>
                </div>
                <div class="row g-4">
                    @foreach($featuredCourses as $course)
                        <div class="col-lg-4">
                            <article class="premium-card h-100 overflow-hidden d-flex flex-column">
                                <div class="position-relative aspect-[16/9] overflow-hidden bg-zinc-100">
                                    <img class="w-100 h-100 object-cover" loading="lazy" src="{{ \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg') }}" onerror="this.src='{{ asset('site/img/cat-1.jpg') }}'" alt="{{ $course->name }}">
                                    <span class="position-absolute top-0 start-0 bg-brand-gold text-brand-dark px-2.5 py-1 m-3 rounded-full font-black uppercase tracking-[0.25em]" style="font-size: 8px;">{{ $course->badge_text ?? 'Hot Course' }}</span>
                                </div>
                                <div class="p-4 flex-grow-1 d-flex flex-column">
                                    <h3 class="h6 fw-black text-brand-dark mb-2">{{ $course->name }}</h3>
                                    <p class="text-zinc-600 mb-4" style="font-size: 12px; line-height: 1.65;">{{ Str::limit(strip_tags($course->description), 120) }}</p>
                                    <div class="d-flex flex-wrap gap-2 mb-4 mt-auto">
                                        <span class="bg-zinc-50 rounded-full px-3 py-1.5 border border-zinc-100 fw-black text-brand-dark uppercase tracking-widest" style="font-size: 8px;">{{ $course->duration }}</span>
                                        <span class="bg-zinc-50 rounded-full px-3 py-1.5 border border-zinc-100 fw-black text-brand-dark uppercase tracking-widest" style="font-size: 8px;">{{ $course->price }}</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('courses-detail', $course->slug) }}" data-cta="popular-course-roadmap" class="btn btn-outline-brand-dark py-2 rounded-xl font-black uppercase tracking-widest" style="font-size: 9px;">View Roadmap</a>
                                        <a href="{{ route('join-now', ['course' => $course->slug]) }}" data-cta="popular-course-inquiry" class="btn btn-primary py-2 rounded-xl font-black uppercase tracking-widest" style="font-size: 9px;">Ask About This Course</a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="py-4 bg-zinc-50/50 border-bottom border-zinc-100">
        <div class="container">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-center">
                <a href="{{ route('courses-all') }}" class="btn {{ blank($categorySlug) ? 'btn-brand-dark' : 'btn-outline-brand-dark' }} rounded-pill px-4 py-2 font-black uppercase tracking-widest" style="font-size: 9px;">All</a>
                @foreach($categories as $category)
                    <a href="{{ route('courses-all', ['category' => $category->slug, 'search' => $search ?: null]) }}" class="btn {{ $categorySlug === $category->slug ? 'btn-brand-dark' : 'btn-outline-brand-dark' }} rounded-pill px-4 py-2 font-black uppercase tracking-widest" style="font-size: 9px;">
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
                    <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">Open the roadmap for details, or send an inquiry directly from the course card.</p>
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
                                    <img class="w-100 h-100 object-cover" loading="lazy" src="{{ \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg') }}" onerror="this.src='{{ asset('site/img/cat-1.jpg') }}'" alt="{{ $course->name }}">
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
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('courses-detail', $course->slug) }}" data-cta="course-card-roadmap" class="btn btn-outline-brand-dark py-2 rounded-lg font-black uppercase tracking-widest" style="font-size: 9px;">View Roadmap</a>
                                        <a href="{{ route('join-now', ['course' => $course->slug]) }}" data-cta="course-card-course-help" class="btn btn-primary py-2 rounded-lg font-black uppercase tracking-widest shadow-lg" style="font-size: 9px;">Ask About This Course</a>
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
                    <p class="text-zinc-600 mb-4">Send us your goal and we will point you to the closest option.</p>
                    <a href="{{ route('join-now', ['course' => 'undecided']) }}" data-cta="courses-empty-course-help" class="btn btn-primary rounded-xl px-5 py-3 font-black uppercase tracking-widest" style="font-size: 10px;">Ask What Fits Me</a>
                </div>
            @endif
        </div>
    </section>
@endsection
