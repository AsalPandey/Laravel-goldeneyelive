@extends('site.layout.app')
@section('page_title', \App\Support\StructuredData::titleWithBrand($settings['about_header_title'] ?? 'About Golden Eye Academy', \App\Support\StructuredData::siteName($settings ?? [])))
@section('meta_description', $settings['meta_description'] ?? 'Learn about Golden Eye Academy and its language, test preparation, computer, office, web development, and IT classes in Pokhara.')

@section('schema_markup')
    {{-- Person Schema for Teachers (AEO/GEO) --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@graph": [
        @foreach($teachers as $teacher)
        @if(is_object($teacher))
        {
          "@@type": "Person",
          "name": @json($teacher->name ?? ''),
          "jobTitle": @json($teacher->designation ?? ''),
          "image": "{{ \App\Support\PublicAsset::canonicalUrl($teacher->photo ?? null, 'site/img/team-1.jpg') }}",
          "description": @json($teacher->bio ?? ''),
          "worksFor": {
            "@@id": "{{ \App\Support\StructuredData::organizationId() }}"
          },
          "sameAs": @json(array_values(array_filter([$teacher->facebook_url ?? null, $teacher->linkedin_url ?? null], 'filled')))
        }{{ !$loop->last ? ',' : '' }}
        @endif
        @endforeach
      ]
    }
    </script>
@endsection

@section('content')
    <!-- Header Start -->
    <div class="container-fluid page-header mb-4" style="background: linear-gradient(rgba(5, 12, 28, 0.85), rgba(5, 12, 28, 0.85)), url('{{ \App\Support\PublicAsset::url($settings['hero_image'] ?? null, 'site/img/carousel-1.png') }}'); background-size: cover; background-position: center;">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="font-black text-white animated slideInDown uppercase tracking-tighter mb-4" style="font-size: clamp(1.6rem, 3.5vw, 2.5rem); line-height: 1;">{{ $settings['about_header_title'] ?? 'About Golden Eye Academy' }}</h1>
                    <p class="text-brand-gold fw-black uppercase tracking-[0.3em] mb-4 animated fadeIn" style="font-size: 11px;">{{ $settings['logo_subtitle'] ?? 'Pokhara, Nepal' }}</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item text-brand-gold active font-bold uppercase" aria-current="page">About</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Feature Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="premium-card text-center p-4 border-zinc-100 hover:border-brand-gold/30 transition-all">
                        <div class="bg-brand-dark text-brand-gold w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                            <i class="fa fa-graduation-cap fs-6"></i>
                        </div>
                        <h6 class="mb-2 font-black text-brand-dark uppercase tracking-tight" style="font-size: 13px;">{{ $settings['about_feat_1_title'] ?? 'Enrollment Support' }}</h6>
                        <p class="extra-small text-zinc-500 mb-0">{{ $settings['about_feat_1_desc'] ?? 'Ask about course fit, current timing, fees, and support before enrollment.' }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="premium-card text-center p-4 border-zinc-100 hover:border-brand-gold/30 transition-all">
                        <div class="bg-brand-dark text-brand-gold w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                            <i class="fa fa-globe fa-lg"></i>
                        </div>
                        <h6 class="mb-2 font-black text-brand-dark uppercase tracking-tight" style="font-size: 13px;">{{ $settings['about_feat_2_title'] ?? 'Practical Learning' }}</h6>
                        <p class="small text-zinc-500 mb-0">{{ $settings['about_feat_2_desc'] ?? 'Review each course description and outline to understand its listed learning areas.' }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="premium-card text-center p-4 border-zinc-100 hover:border-brand-gold/30 transition-all">
                        <div class="bg-brand-dark text-brand-gold w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                            <i class="fa fa-laptop-code fa-lg"></i>
                        </div>
                        <h6 class="mb-2 font-black text-brand-dark uppercase tracking-tight" style="font-size: 13px;">{{ $settings['about_feat_3_title'] ?? 'Parent-Friendly Decisions' }}</h6>
                        <p class="small text-zinc-500 mb-0">{{ $settings['about_feat_3_desc'] ?? 'Parents can ask about fees, timing, safety, and realistic next steps.' }}</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="premium-card text-center p-4 border-zinc-100 hover:border-brand-gold/30 transition-all">
                        <div class="bg-brand-dark text-brand-gold w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                            <i class="fa fa-award fa-lg"></i>
                        </div>
                        <h6 class="mb-2 font-black text-brand-dark uppercase tracking-tight" style="font-size: 13px;">{{ $settings['about_feat_4_title'] ?? 'Current Information' }}</h6>
                        <p class="small text-zinc-500 mb-0">{{ $settings['about_feat_4_desc'] ?? 'Confirm current batches, availability, and instructor details before enrollment.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Feature End -->

    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 350px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100 rounded-xl shadow-xl object-cover" src="{{ \App\Support\PublicAsset::url($settings['about_image'] ?? null, 'site/img/about.jpg') }}" alt="Golden Eye Academy Building">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div style="width: 30px; height: 2px; background: var(--brand-gold);"></div>
                        <span class="text-brand-gold font-black uppercase tracking-[0.3em]" style="font-size: 11px;">Our Story</span>
                    </div>
                    <h2 class="h3 fw-black text-brand-dark mb-4 uppercase tracking-tighter">{{ $settings['about_content_title'] ?? 'Welcome to Golden Eye Academy' }}</h2>
                    <div class="mb-5 text-zinc-600 leading-relaxed fs-6 border-start-4 border-brand-gold/20 ps-4 italic">
                        @sanitize(filled($settings['about_page_content'] ?? null)
                            ? $settings['about_page_content']
                            : ($settings['about_content'] ?? 'Review the course focus, fee, duration, outline, and available instructor information, then ask the academy to confirm the current schedule and support.'))
                    </div>
                    <div class="row gy-3 gx-4 mb-5">
                        <div class="col-sm-6"><p class="mb-0 fw-bold text-brand-dark small uppercase tracking-wide"><i class="fa fa-check-circle text-brand-gold me-2"></i>{{ $settings['about_point_1'] ?? 'Academic support before enrollment' }}</p></div>
                        <div class="col-sm-6"><p class="mb-0 fw-bold text-brand-dark small uppercase tracking-wide"><i class="fa fa-check-circle text-brand-gold me-2"></i>{{ $settings['about_point_2'] ?? 'Language, test prep, IT, and office skills' }}</p></div>
                        <div class="col-sm-6"><p class="mb-0 fw-bold text-brand-dark small uppercase tracking-wide"><i class="fa fa-check-circle text-brand-gold me-2"></i>{{ $settings['about_point_3'] ?? 'Support for students, parents, and learners' }}</p></div>
                        <div class="col-sm-6"><p class="mb-0 fw-bold text-brand-dark small uppercase tracking-wide"><i class="fa fa-check-circle text-brand-gold me-2"></i>{{ $settings['about_point_4'] ?? 'Current details confirmed before enrollment' }}</p></div>
                    </div>
                    <a class="btn btn-primary py-3 px-6 rounded-xl shadow-xl font-black uppercase tracking-widest hover:scale-105 transition-all" style="font-size: 11px;" href="{{ route('courses-all') }}">
                        View Course Details <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Why Golden Eye Comparison Start -->
    <div class="container-xxl py-5 bg-zinc-50">
        <div class="container">
            <div class="text-center wow fadeInUp mb-5" data-wow-delay="0.1s">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                    <div style="width: 40px; height: 2px; background: var(--brand-gold);"></div>
                    <span class="text-brand-gold font-black uppercase tracking-[0.3em]" style="font-size: 11px;">Before enrollment</span>
                    <div style="width: 40px; height: 2px; background: var(--brand-gold);"></div>
                </div>
                <h2 class="h3 fw-black text-brand-dark uppercase tracking-tighter">Questions we help you answer</h2>
            </div>
            
            <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.3s">
                <div class="col-lg-10">
                    <div class="premium-card bg-white rounded-2xl shadow-xl overflow-hidden border border-zinc-100">
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead class="bg-brand-dark text-white text-center border-b border-brand-gold/30">
                                    <tr>
                                        <th class="py-4 font-black uppercase tracking-widest text-xs border-0 text-start ps-5 w-1/3">Decision point</th>
                                        <th class="py-4 font-black uppercase tracking-widest text-xs border-0 w-1/3 bg-brand-gold/10 text-brand-gold">What we explain</th>
                                        <th class="py-4 font-black uppercase tracking-widest text-xs border-0 text-zinc-400 w-1/3">What to ask anywhere</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-zinc-100 transition-all hover:bg-zinc-50">
                                        <td class="py-4 ps-5 fw-bold text-zinc-700 small">What students learn</td>
                                        <td class="py-4 text-center fw-black text-brand-dark small"><i class="fa fa-check text-brand-gold me-2"></i> Published course description and outline</td>
                                        <td class="py-4 text-center text-zinc-500 small">Ask for the course outline</td>
                                    </tr>
                                    <tr class="border-b border-zinc-100 transition-all hover:bg-zinc-50 bg-zinc-50/50">
                                        <td class="py-4 ps-5 fw-bold text-zinc-700 small">Batch timing and seat fit</td>
                                        <td class="py-4 text-center fw-black text-brand-dark small"><i class="fa fa-check text-brand-gold me-2"></i> {{ $settings['course_confirmation_note'] ?? 'Confirm current batch timing, seat availability, and instructor details with the academy before enrollment.' }}</td>
                                        <td class="py-4 text-center text-zinc-500 small">Ask current batch size and timing</td>
                                    </tr>
                                    <tr class="border-b border-zinc-100 transition-all hover:bg-zinc-50">
                                        <td class="py-4 ps-5 fw-bold text-zinc-700 small">Who teaches it</td>
                                        <td class="py-4 text-center fw-black text-brand-dark small"><i class="fa fa-check text-brand-gold me-2"></i> Stored instructor name and any matched active profile</td>
                                        <td class="py-4 text-center text-zinc-500 small">Ask who will teach your batch</td>
                                    </tr>
                                    <tr class="transition-all hover:bg-zinc-50 bg-zinc-50/50">
                                        <td class="py-4 ps-5 fw-bold text-zinc-700 small">What is not guaranteed</td>
                                        <td class="py-4 text-center fw-black text-brand-dark small"><i class="fa fa-check text-brand-gold me-2"></i> Limits and details to confirm</td>
                                        <td class="py-4 text-center text-zinc-500 small">Ask what depends on student practice</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Why Golden Eye Comparison End -->

    <!-- Team Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp mb-12" data-wow-delay="0.1s">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                    <div style="width: 40px; height: 2px; background: var(--brand-gold);"></div>
                    <span class="text-brand-gold font-black uppercase tracking-[0.3em]" style="font-size: 11px;">Instructor profiles</span>
                    <div style="width: 40px; height: 2px; background: var(--brand-gold);"></div>
                </div>
                <h2 class="h2 fw-black text-brand-dark uppercase tracking-tighter">Teachers and Academic Support Team</h2>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach($teachers as $teacher)
                @if(is_object($teacher))
                <div class="col-xl-3 col-lg-4 col-md-6 wow zoomIn">
                    <div class="team-item bg-white w-100 shadow-lg rounded-2xl overflow-hidden position-relative group border border-zinc-100 hover:border-brand-gold/30 transition-all duration-500">
                        <div class="overflow-hidden position-relative" style="height:300px;">
                            <img class="img-fluid w-100 h-100 object-cover transition-all duration-700 group-hover:scale-110" src="{{ \App\Support\PublicAsset::url($teacher->photo ?? null, 'site/img/team-1.jpg') }}" alt="{{ $teacher->name }}">
                        </div>
                        <div class="text-center p-4 bg-white relative">
                            <h3 class="h6 mt-2 mb-1 font-black text-brand-dark" style="font-size: 15px;">{{ $teacher->name }}</h3>
                            <small class="text-brand-gold font-black uppercase tracking-[0.2em]" style="font-size: 8px;">{{ $teacher->designation }}</small>
                            @if(!empty($teacher->bio))
                                <p class="small text-zinc-600 mt-3 mb-0 leading-relaxed">{{ Str::limit($teacher->bio, 200) }}</p>
                            @endif
                            <div class="d-flex justify-content-center mt-4 gap-3">
                                @if(!empty($teacher->facebook_url))
                                    <a class="w-10 h-10 rounded-full border border-zinc-200 d-flex align-items-center justify-content-center text-zinc-400 hover:bg-brand-dark hover:text-brand-gold transition-all" href="{{ $teacher->facebook_url }}" target="_blank" rel="noopener" aria-label="{{ $teacher->name }} on Facebook"><i class="fab fa-facebook-f text-xs" aria-hidden="true"></i></a>
                                @endif
                                @if(!empty($teacher->linkedin_url))
                                    <a class="w-10 h-10 rounded-full border border-zinc-200 d-flex align-items-center justify-content-center text-zinc-400 hover:bg-brand-dark hover:text-brand-gold transition-all" href="{{ $teacher->linkedin_url }}" target="_blank" rel="noopener" aria-label="{{ $teacher->name }} on LinkedIn"><i class="fab fa-linkedin-in text-xs" aria-hidden="true"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
    <!-- Team End -->
@endsection
