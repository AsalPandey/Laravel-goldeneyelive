@extends('site.layout.app')

@section('page_title', ($category->meta_title ?? $category->name . ' - GoldenEye Academy'))
@section('meta_description', ($category->meta_description ?? 'Browse our specialized ' . $category->name . ' courses at GoldenEye Academy Pokhara.'))
@section('meta_keywords', ($category->meta_keywords ?? $category->name . ', courses, academy, pokhara'))

@section('content')
    @php
        $guidanceUrl = fn (string $sourceSection, string $selectedCourse = 'undecided') => route('join-now', [
            'course' => $selectedCourse,
            'selected_course' => $selectedCourse,
            'source_page' => 'course-category-'.$category->slug,
            'source_section' => $sourceSection,
            'inquiry_intent' => 'course_guidance',
        ]);
    @endphp

    {{-- ItemList Schema for GEO --}}
    @if(count($courses) > 0)
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
    @endif

    <!-- Header Start -->
    <div class="container-fluid page-header py-4 mb-4 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="h2 text-white animated slideInDown font-black uppercase tracking-tighter">{{ $category->name }}</h1>
                    <p class="text-brand-gold fw-black uppercase tracking-[0.3em] mb-4 animated fadeIn" style="font-size: 10px;">{{ $settings['category_header_badge'] ?? 'Course category' }}</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('courses') }}">Courses</a></li>
                            <li class="breadcrumb-item text-brand-gold active font-bold uppercase" aria-current="page">{{ $category->name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Courses Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">{{ $settings['category_tagline'] ?? 'Course options' }}</h6>
                <h2 class="h2 mb-5 font-black uppercase tracking-tight">{{ $settings['category_title_prefix'] ?? 'Available' }} <span class="text-primary">{{ $settings['category_title_span'] ?? 'Programs' }}</span></h2>
            </div>
            
            <div class="row g-4 justify-content-center">
                @forelse ($courses as $course)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="premium-card p-0 overflow-hidden h-full flex flex-col rounded-xl shadow-lg border border-zinc-100">
                            <div class="relative overflow-hidden h-52">
                                <img class="w-full h-full object-cover" loading="lazy" src="{{ \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg') }}" onerror="this.src='{{ asset('site/img/cat-1.jpg') }}'" alt="{{ $course->name }}">
                                <span class="position-absolute top-0 start-0 bg-brand-gold text-brand-dark px-2 py-1 m-3 rounded-full text-[8px] font-black uppercase tracking-widest shadow-lg">{{ $course->badge_text ?? 'Course' }}</span>
                            </div>
                            
                            <div class="p-5 text-center flex flex-col flex-grow">
                                <div class="mb-3 d-inline-flex align-items-center justify-content-center gap-2 text-brand-dark fw-black text-uppercase tracking-widest" style="font-size: 8px;">
                                    <i class="fa fa-check-circle text-brand-gold" aria-hidden="true"></i>
                                    Practical guidance available
                                </div>
                                <h6 class="mb-2 font-black text-brand-dark uppercase tracking-tight" style="font-size: 15px;">{{ $course->name }}</h6>
                                <p class="text-zinc-500 extra-small mb-4 line-clamp-2" style="font-size: 11px;">{{ Str::limit(strip_tags($course->description), 100) }}</p>
                                
                                <div class="mt-auto pt-4 border-top border-zinc-100 d-flex justify-content-center gap-4 mb-6">
                                    <small class="text-muted text-uppercase font-black tracking-widest" style="font-size: 9px;"><i class="fa fa-clock text-brand-gold me-1"></i>{{ $course->duration }}</small>
                                    <small class="text-muted text-uppercase font-black tracking-widest" style="font-size: 9px;"><i class="fa fa-tag text-brand-gold me-1"></i>{{ $course->price }}</small>
                                    <small class="text-muted text-uppercase font-black tracking-widest" style="font-size: 9px;"><i class="fa fa-user text-brand-gold me-1"></i>{{ $course->capacity }} Seats</small>
                                </div>

                                <div class="d-flex flex-column gap-2">
                                    <a href="{{ $course->slug ? route('courses-detail', $course->slug) : route('courses-all') }}" class="btn btn-primary rounded-lg font-black text-uppercase tracking-widest py-2 shadow-lg" style="font-size: 9px;">View Course Details</a>
                                    <a href="{{ $guidanceUrl('category-course-card', $course->slug) }}" data-cta="category-course-guidance" class="btn btn-outline-dark rounded-lg py-2 font-black text-uppercase tracking-widest" style="font-size: 9px;">Ask for Course Help</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-lg-12 text-center py-5">
                        <div class="mb-4">
                            <i class="fa fa-info-circle fa-4x text-neutral-200"></i>
                        </div>
                        <h3 class="text-neutral-400 italic">No classes are currently available for this category.</h3>
                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 mt-4">
                            <a href="{{ $guidanceUrl('category-empty') }}" data-cta="category-empty-course-guidance" class="btn btn-primary px-5 rounded-pill">Ask for Course Help</a>
                            <a href="{{ route('courses') }}" class="btn btn-outline-brand-dark px-5 rounded-pill">View Course Details</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
    <!-- Courses End -->
@endsection
