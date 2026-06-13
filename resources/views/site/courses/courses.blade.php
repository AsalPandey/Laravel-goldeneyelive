@extends('site.layout.app')
@section('page_title', 'Courses - Computer, Web Development & Language Classes at Golden Eye Academy')
@section('meta_description', 'Browse computer classes, language courses, IELTS/TOEFL preparation, and web development classes at Golden Eye Academy, Pokhara.')
@section('content')
    @php
        $guidanceUrl = fn (string $sourceSection, string $selectedCourse = 'undecided') => route('join-now', [
            'course' => $selectedCourse,
            'selected_course' => $selectedCourse,
            'source_page' => 'courses',
            'source_section' => $sourceSection,
            'inquiry_intent' => 'course_guidance',
        ]);
    @endphp

    {{-- ItemList Schema for GEO --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "ItemList",
      "itemListElement": [
        @foreach($courses as $index => $course)
        {
          "@@type": "ListItem",
          "position": {{ $index + 1 }},
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
    
    <!-- Header Start -->
    <div class="container-fluid page-header py-4 mb-4" style="background: linear-gradient(rgba(5, 12, 28, 0.85), rgba(5, 12, 28, 0.85)), url('{{ \App\Support\PublicAsset::url($settings['hero_image'] ?? null, 'site/img/carousel-1.png') }}'); background-size: cover; background-position: center;">
        <div class="container py-4 text-center">
            <h1 class="h2 font-black text-white animated slideInDown uppercase tracking-tighter">{{ $settings['courses_header_title'] ?? 'Academic Pathways' }}</h1>
            <p class="text-brand-gold fw-black uppercase tracking-[0.3em] mb-4 animated fadeIn" style="font-size: 10px;">Compare course options before enrollment</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item text-brand-gold active font-bold uppercase" aria-current="page">Courses</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Header End -->

    <!-- Categories Start -->
    <div class="container-xxl py-4 category">
        <div class="container">
            <div class="text-center wow fadeInUp mb-8" data-wow-delay="0.1s">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
                    <div style="width: 30px; height: 1.5px; background: var(--brand-gold);"></div>
                    <span class="text-brand-gold font-black uppercase tracking-[0.3em]" style="font-size: 10px;">{{ $settings['pathway_tagline'] ?? 'Course categories' }}</span>
                    <div style="width: 30px; height: 1.5px; background: var(--brand-gold);"></div>
                </div>
                <h2 class="h3 fw-black text-brand-dark uppercase tracking-tighter">Browse By <span class="text-brand-gold">Goal</span></h2>
            </div>

            <div class="row g-4 justify-content-center">
                @foreach($categories as $category)
                    <div class="col-lg-4 col-md-6 wow zoomIn">
                        <a class="premium-card p-0 group block overflow-hidden rounded-xl shadow-lg hover:shadow-brand-gold/10 transition-all border border-zinc-100" href="{{ route('course-category', $category->slug) }}">
                            <div class="relative overflow-hidden h-64">
                                <img class="img-fluid w-100 h-100 object-cover transition-all duration-700 group-hover:scale-110" src="{{ \App\Support\PublicAsset::url($category->image ?? null, 'site/img/cat-1.jpg') }}" alt="{{ $category->name }}">
                                <div class="absolute inset-0 bg-gradient-to-t from-brand-dark/90 via-brand-dark/30 to-transparent flex flex-column justify-end p-5 text-start">
                                    <h5 class="text-white fw-black mb-1 uppercase tracking-tight" style="font-size: 16px;">{{ $category->name }}</h5>
                                    <div class="flex items-center justify-between">
                                        <span class="text-brand-gold font-black text-[9px] uppercase tracking-[0.2em]">{{ $category->courses_count }} Specializations</span>
                                        <div class="w-8 h-8 rounded-lg bg-brand-gold flex items-center justify-center transform group-hover:scale-110 transition-all shadow-lg">
                                            <i class="fas fa-arrow-right text-brand-dark" style="font-size: 12px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Categories End -->

    <!-- Courses Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="text-center wow fadeInUp mb-8" data-wow-delay="0.1s">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
                    <div style="width: 30px; height: 1.5px; background: var(--brand-gold);"></div>
                    <span class="text-brand-gold font-black uppercase tracking-[0.3em]" style="font-size: 10px;">Common choices</span>
                    <div style="width: 30px; height: 1.5px; background: var(--brand-gold);"></div>
                </div>
                <h2 class="h3 fw-black text-brand-dark uppercase tracking-tighter">Our Most <span class="text-brand-gold">Popular Paths</span></h2>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach ($courses as $course)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="premium-card p-0 overflow-hidden h-full flex flex-col border border-zinc-100 shadow-lg rounded-xl">
                            <div class="relative overflow-hidden h-52">
                                <img class="w-full h-full object-cover" loading="lazy" src="{{ \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg') }}" onerror="this.src='{{ asset('site/img/cat-1.jpg') }}'" alt="{{ $course->name }}">
                                <span class="position-absolute top-0 start-0 bg-brand-gold text-brand-dark px-2 py-1 m-3 rounded-full text-[8px] font-black uppercase tracking-widest shadow-lg">{{ $course->badge_text ?? 'Course' }}</span>
                            </div>
                            
                            <div class="p-5 text-center flex flex-col flex-grow">
                                <div class="mb-4 d-inline-flex align-items-center justify-content-center gap-2 text-brand-dark fw-black text-uppercase tracking-widest" style="font-size: 8px;">
                                    <i class="fa fa-check-circle text-brand-gold" aria-hidden="true"></i>
                                    Practical class support available
                                </div>
                                <h6 class="mb-2 font-black text-brand-dark uppercase tracking-tight" style="font-size: 15px;">{{ $course->name }}</h6>
                                <p class="text-zinc-500 extra-small mb-4 line-clamp-2 italic" style="font-size: 11px;">{{ Str::limit(strip_tags($course->description), 100) }}</p>
                                
                                <div class="mt-auto pt-4 border-top border-zinc-100 d-flex justify-content-center gap-3 mb-2">
                                    <small class="text-brand-dark text-uppercase font-black tracking-widest" style="font-size: 8px;"><i class="fa fa-clock text-brand-gold me-2"></i>{{ $course->duration }}</small>
                                    <small class="text-brand-dark text-uppercase font-black tracking-widest" style="font-size: 8px;"><i class="fa fa-tag text-brand-gold me-2"></i>{{ $course->price }}</small>
                                </div>
                                <div class="d-flex align-items-center justify-content-center gap-2 mb-6 flex-wrap">
                                    <div class="text-center bg-brand-gold/10 rounded-full px-3 py-1 border border-brand-gold/20">
                                        <span class="fw-black text-brand-gold uppercase tracking-widest" style="font-size: 7.5px;">Ask for current batch</span>
                                    </div>
                                    <div class="text-center bg-zinc-50 rounded-full px-3 py-1 border border-zinc-100">
                                        <i class="fas fa-comment-dots text-brand-dark me-1" style="font-size: 8px;" aria-hidden="true"></i>
                                        <span class="fw-black text-brand-dark uppercase tracking-widest" style="font-size: 7.5px;">Guidance Available</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2">
                                    <a href="{{ $course->slug ? route('courses-detail', $course->slug) : route('courses-all') }}" class="btn btn-primary py-2 rounded-lg shadow-lg animate-glow font-black uppercase tracking-widest" style="font-size: 9px;">View Course Details</a>
                                    <a href="{{ $guidanceUrl('featured-course-card', $course->slug) }}" data-cta="featured-course-guidance" class="btn btn-outline-brand-dark py-2 rounded-lg font-black uppercase tracking-widest" style="font-size: 9px;">Ask for Course Help</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-8 wow fadeInUp" data-wow-delay="0.5s">
                <a href="{{ route('courses-all') }}" class="btn btn-brand-dark px-5 py-3 rounded-xl font-black uppercase tracking-widest shadow-xl hover:bg-brand-gold hover:text-brand-dark transition-all" style="font-size: 11px;">View Course Details <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>
    </div>
    <!-- Courses End -->
@endsection
