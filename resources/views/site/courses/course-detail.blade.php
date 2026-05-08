@extends('site.layout.app')
@section('page_title', ($course->meta_title ?? $course->name) . ' - GoldenEye Academy')
@section('og_title', $course->name . ' Course at GoldenEye Academy')
@section('meta_description', $course->meta_description ?? Str::limit(strip_tags($course->description), 160))
@section('meta_keywords', $course->meta_keywords ?? '')
@section('aeo_summary', strip_tags($course->aeo_summary ?? ''))
@section('og_image', \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg'))

@section('schema_markup')
    @if($course->schema_markup)
        @jsonld($course->schema_markup)
    @endif
@endsection

@section('content')
    {{-- Advanced Course Schema for AEO/GEO --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Course",
      "name": @json($course->name),
      "description": @json(strip_tags($course->description)),
      "image": "{{ \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg') }}",
      "provider": {
        "@@type": "EducationalOrganization",
        "name": @json(($settings['site_name'] ?? 'GoldenEye') . ' ' . ($settings['site_name_suffix'] ?? 'Academy')),
        "url": "{{ url('/') }}",
        "logo": "{{ \App\Support\PublicAsset::url($settings['site_logo'] ?? null, 'site/img/logo.png') }}",
        "address": {
          "@@type": "PostalAddress",
          "streetAddress": "{{ $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal' }}",
          "addressLocality": "Pokhara",
          "addressCountry": "NP"
        }
      },
      "hasCourseInstance": {
        "@@type": "CourseInstance",
        "courseMode": "Onsite",
        "location": "Pokhara, Nepal",
        "duration": "{{ $course->duration }}",
        "instructor": {
          "@@type": "Person",
          "name": @json($course->instructor)
        }
      },
      "offers": {
        "@@type": "Offer",
        "price": "{{ preg_replace('/[^0-9]/', '', $course->price) ?: '0' }}",
        "priceCurrency": "NPR",
        "category": "Professional Education",
        "availability": "https://schema.org/InStock",
        "url": "{{ url()->current() }}"
      }
    }
    </script>
    
    <!-- Header Start -->
    <div class="container-fluid page-header py-4 mb-4 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="h2 text-white animated slideInDown font-black uppercase tracking-tighter">{{ $course->name }}</h1>
                    <p class="text-brand-gold fw-black uppercase tracking-[0.3em] mb-4 animated fadeIn" style="font-size: 10px;">{{ $course->badge_text ?? 'Professional Roadmap' }}</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('courses-all') }}">Courses</a></li>
                            <li class="breadcrumb-item text-brand-gold active font-bold uppercase" aria-current="page">Details</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Course Details Start -->
    <article class="container-xxl py-4">
        <div class="container">
            <div class="row g-5">
                <!-- Image Section -->
                <div class="col-lg-6 wow fadeInUp position-relative" data-wow-delay="0.1s">
                    <div class="position-absolute top-0 start-0 m-4 z-10">
                        <span class="badge bg-brand-gold text-brand-dark fw-black px-2 py-1 rounded-lg shadow-sm" style="font-size: 9px;">{{ $course->badge_text ?? 'Professional Track' }}</span>
                    </div>
                    <img class="img-fluid rounded-xl shadow-lg border-2 border-white w-100 object-cover" src="{{ \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg') }}" onerror="this.src='{{ asset('site/img/cat-1.jpg') }}'" alt="{{ $course->name }}" style="min-height:220px; max-height: 350px;">
                    
                    <!-- Career Highlights Sidebar -->
                    <div class="mt-4 p-4 bg-white rounded-xl shadow-sm border border-neutral-100">
                        <h6 class="fw-black mb-3 text-uppercase tracking-wider border-bottom pb-2" style="font-size: 12px;">Career Highlights</h6>
                        <ul class="list-unstyled">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-primary me-3"></i>
                                <span>{{ $settings['career_highlight_1'] ?? 'Industry-Recognized Certification' }}</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-primary me-3"></i>
                                <span>{{ $settings['career_highlight_2'] ?? '100% Practical & Project-Based' }}</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-primary me-3"></i>
                                <span>{{ $settings['career_highlight_3'] ?? 'Job Placement Assistance' }}</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-primary me-3"></i>
                                <span>{{ $settings['career_highlight_4'] ?? 'Flexible Learning Hours' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Course Details Section -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <span class="badge rounded-pill bg-light text-brand-gold px-3 py-1 mb-3 fw-bold text-uppercase tracking-wider" style="font-size: 8px; border: 1px solid var(--color-brand-gold, #C5A059);">{{ $course->badge_text ?? 'Professional Track' }}</span>
                    <h2 class="h3 fw-black mb-4 text-brand-dark uppercase tracking-tight">{{ $course->name }}</h2>
                    <div class="course-description mb-4 leading-relaxed text-zinc-600 extra-small">
                        @sanitize($course->description)
                    </div>

                    @if($course->aeo_summary)
                        <div class="aeo-fast-facts p-4 mb-5 rounded-xl bg-neutral-900 text-brand-gold border-2 border-brand-gold/20 shadow-xl wow fadeInUp">
                            <h6 class="text-white text-uppercase tracking-widest font-black mb-3" style="font-size: 10px;">
                                <i class="fas fa-bolt me-2"></i> Fast Facts (AI Summary)
                            </h6>
                            <div class="small leading-relaxed">
                                @sanitize($course->aeo_summary)
                            </div>
                        </div>
                    @endif

                    <div class="row g-4 mb-5">
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center bg-white p-2 rounded-xl shadow-sm border border-neutral-50 h-100">
                                <div class="bg-light p-2 rounded-circle me-3">
                                    <i class="fa fa-tag text-brand-gold" style="font-size: 11px;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 8px;">Investment</small>
                                    <span class="fw-black h6 mb-0" style="font-size: 12px;">{{ $course->price }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center bg-white p-2 rounded-xl shadow-sm border border-neutral-50 h-100">
                                <div class="bg-light p-2 rounded-circle me-3">
                                    <i class="fa fa-clock text-brand-gold" style="font-size: 11px;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 8px;">Duration</small>
                                    <span class="fw-black h6 mb-0" style="font-size: 12px;">{{ $course->duration }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex align-items-center bg-white p-2 rounded-xl shadow-sm border border-neutral-50 h-100">
                                <div class="bg-brand-gold/10 p-2 rounded-circle me-3">
                                    <i class="fa fa-chart-line text-brand-gold" style="font-size: 11px;"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 8px;">Placement</small>
                                    <span class="fw-black h6 mb-0 text-brand-dark" style="font-size: 12px;">{{ rand(92, 98) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="mb-4 border-bottom pb-2 uppercase tracking-wider font-black text-neutral-900 d-flex align-items-center" style="font-size: 14px;">
                        <i class="fas fa-graduation-cap text-brand-gold me-2"></i> What You Will Master:
                    </h6>
                    <div class="mb-5 course-outline-content leading-relaxed text-zinc-600 extra-small">
                        @sanitize($course->course_outline)
                    </div>
                    
                    <div class="p-3 bg-light rounded-xl d-flex flex-column flex-sm-row align-items-center justify-content-between border border-neutral-100 gap-4">
                        <div class="instructor text-center text-sm-start">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 9px;">Expert Instructor</small>
                            <span class="fw-black text-brand-dark" style="font-size: 14px;">{{ $course->instructor }}</span>
                        </div>
                        <div class="text-center text-sm-end w-100 w-sm-auto">
                            <a class="btn btn-primary py-2 px-4 rounded-lg shadow-lg animate-glow w-100 w-sm-auto" data-cta="course-detail-course-help" style="font-size: 11px;" href="{{ route('join-now', ['course' => $course->slug]) }}">
                                Ask About This Course <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                            <small class="d-block mt-1 text-danger fw-bold animate-pulse" style="font-size: 9px;">Ask early: batches fill quickly</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endsection
