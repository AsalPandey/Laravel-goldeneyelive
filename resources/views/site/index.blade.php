@extends('site.layout.app')
@section('page_title', $settings['meta_title'] ?? 'GoldenEye Academy - Web Development, Computer & Language Courses in Pokhara')
@section('meta_description', $settings['meta_description'] ?? 'GoldenEye Academy offers Web Development, Computer, IELTS, PTE, Japanese, Korean, and English courses in Pokhara since 2008. Message us for quick course help.')

@section('content')
    @php
        $heroImage = \App\Support\PublicAsset::url($settings['hero_image'] ?? null, 'site/img/carousel-1.png');
        $primaryCta = $settings['hero_cta_1_text'] ?? 'Ask for Course Help';
        $secondaryCta = $settings['hero_cta_2_text'] ?? 'See Courses';
        $audienceSegments = [
            [
                'icon' => 'fa fa-graduation-cap',
                'label' => 'Students',
                'title' => 'Unsure what to study next?',
                'body' => 'Tell us your grade, interest, and goal. We will suggest a practical next step.',
                'cta' => 'Ask as a Student',
                'source' => 'audience-students',
            ],
            [
                'icon' => 'fa fa-plane',
                'label' => 'Study Abroad',
                'title' => 'IELTS, PTE, Japanese, or Korean?',
                'body' => 'Compare test and language options before you spend money on the wrong prep.',
                'cta' => 'Ask About Abroad Prep',
                'source' => 'audience-study-abroad',
            ],
            [
                'icon' => 'fa fa-briefcase',
                'label' => 'Job Seekers',
                'title' => 'Need skills employers notice?',
                'body' => 'Ask which computer, office, language, or web skill fits your current level.',
                'cta' => 'Ask About Skills',
                'source' => 'audience-job-seekers',
            ],
            [
                'icon' => 'fa fa-users',
                'label' => 'Parents',
                'title' => 'Want a clear option for your child?',
                'body' => 'Ask about fees, timing, course fit, and realistic outcomes before enrollment.',
                'cta' => 'Ask the Team',
                'source' => 'audience-parents',
            ],
        ];
    @endphp

    <section class="container-fluid p-0 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(5, 12, 28, 0.98), rgba(15, 23, 42, 0.9)), url('{{ $heroImage }}'); background-size: cover; background-position: center; color: white;">
        <div class="container py-5">
            <div class="row align-items-center g-5" style="min-height: 74vh;">
                <div class="col-lg-8 py-5">
                    <span class="badge rounded-full bg-brand-gold px-4 py-2 text-[8px] fw-black text-brand-dark text-uppercase tracking-[4px] mb-4 shadow-2xl">
                        {{ $settings['hero_badge_text'] ?? 'GoldenEye Academy Launchpad' }}
                    </span>
                    <h1 class="hero-hook-title font-black mb-4 text-white tracking-tighter drop-shadow-lg" style="font-size: clamp(2.35rem, 6vw, 5rem); line-height: 0.96; letter-spacing: 0;">
                        {{ $settings['hero_hook_headline'] ?? "Don't just study. Build your competitive edge." }}
                    </h1>
                    <p class="hero-hook-body mb-5 text-white/90" style="font-weight: 500; max-width: 760px; line-height: 1.75; font-size: 15px; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
                        {{ $settings['hero_hook_body'] ?? "Confused about what to study next? Message GoldenEye Academy with your goal. We will help you compare the right course, test prep, language, or skill path before you commit." }}
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a href="{{ route('join-now', ['course' => 'undecided']) }}" data-cta="hero-course-help" class="btn btn-primary py-3 px-5 rounded-pill shadow-xl font-black uppercase tracking-widest hover:scale-105 transition-all" style="font-size: 10px;">
                            {{ $primaryCta }} <i class="fa fa-bolt ms-2"></i>
                        </a>
                        <a href="{{ route('courses-all') }}" data-cta="explore-programs" class="btn btn-outline-light py-3 px-5 rounded-xl font-black uppercase tracking-widest hover:bg-white hover:text-brand-dark transition-all" style="font-size: 10px;">
                            {{ $secondaryCta }}
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="bg-white/10 border border-white/15 rounded-2xl p-4 backdrop-blur shadow-2xl">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-xl bg-brand-gold text-brand-dark d-flex align-items-center justify-content-center">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <p class="mb-0 text-brand-gold font-black uppercase tracking-[0.25em]" style="font-size: 8px;">Instant Chat</p>
                                <h2 class="h6 mb-0 text-white fw-black">Send a quick message first.</h2>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <span class="rounded-xl bg-white/10 px-3 py-2 text-white fw-bold" style="font-size: 12px;">Ask which course fits you</span>
                            <span class="rounded-xl bg-white/10 px-3 py-2 text-white fw-bold" style="font-size: 12px;">Get fee and timing clarity</span>
                            <span class="rounded-xl bg-white/10 px-3 py-2 text-white fw-bold" style="font-size: 12px;">Reply by phone or WhatsApp</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container-fluid py-4 bg-brand-dark border-y border-brand-gold/5 shadow-2xl">
        <div class="container">
            <div class="row text-center align-items-center g-4">
                @foreach([
                    ['value' => $settings['stat_1_val'] ?? '15+', 'label' => $settings['stat_1_lab'] ?? 'Years of Trust'],
                    ['value' => $settings['stat_2_val'] ?? '5,000+', 'label' => $settings['stat_2_lab'] ?? 'Learners Supported'],
                    ['value' => $settings['stat_3_val'] ?? '4.9/5', 'label' => $settings['stat_3_lab'] ?? 'Student Rating'],
                    ['value' => $settings['stat_4_val'] ?? '2 hr', 'label' => $settings['stat_4_lab'] ?? 'Typical Response'],
                ] as $stat)
                    <div class="col-6 col-lg-3">
                        <div class="d-flex flex-column align-items-center px-3 {{ $loop->first ? '' : 'border-start border-white/10' }}">
                            <h3 class="text-brand-gold font-black mb-1" style="font-size: 1.2rem; letter-spacing: -1px;">{{ $stat['value'] }}</h3>
                            <p class="text-white font-black uppercase tracking-[0.2em] mb-0" style="font-size: 10px;">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-5 bg-white border-bottom border-zinc-100">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-4">
                    <article class="p-5 bg-brand-dark text-white rounded-2xl shadow-2xl">
                        <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Our DNA</span>
                        <h2 class="h3 fw-black mt-3 mb-4 text-white" style="letter-spacing: 0;">15+ Years of Trust</h2>
                        <p class="text-white/80 mb-4" style="font-size: 14px; line-height: 1.75;">
                            {{ $settings['about_text'] ?? 'GoldenEye Academy helps learners avoid random course decisions. Ask first, compare your options, then choose the course that fits your goal.' }}
                        </p>
                        <div class="d-grid gap-3">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="w-10 h-10 rounded-lg bg-brand-gold text-brand-dark d-flex align-items-center justify-content-center flex-shrink-0">
                                    <i class="fas fa-comment-dots" aria-hidden="true"></i>
                                </div>
                                <p class="mb-0 text-white/70" style="font-size: 12px; line-height: 1.6;">Message us before choosing a course.</p>
                            </div>
                            <div class="d-flex gap-3 align-items-center">
                                <div class="w-10 h-10 rounded-lg bg-white/10 text-brand-gold d-flex align-items-center justify-content-center flex-shrink-0">
                                    <i class="fas fa-clock" aria-hidden="true"></i>
                                </div>
                                <p class="mb-0 text-white/70" style="font-size: 12px; line-height: 1.6;">Quick reply on course fit, fees, and timing.</p>
                            </div>
                            <div class="d-flex gap-3 align-items-center">
                                <div class="w-10 h-10 rounded-lg bg-brand-gold text-brand-dark d-flex align-items-center justify-content-center flex-shrink-0">
                                    <i class="fas fa-compass" aria-hidden="true"></i>
                                </div>
                                <p class="mb-0 text-white/70" style="font-size: 12px; line-height: 1.6;">Guidance-first learning since 2008.</p>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="col-lg-8">
                    <div class="h-100 p-5 bg-zinc-50 border border-zinc-100 rounded-2xl">
                        <div class="d-flex align-items-center gap-2.5 mb-3">
                            <div style="width: 25px; height: 1.5px; background: var(--brand-gold); opacity: 0.4;"></div>
                            <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Start here</span>
                        </div>
                        <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">{{ $settings['pathway_title'] ?? 'Start with the path that sounds like you.' }}</h2>
                        <p class="text-zinc-600 mb-4" style="font-size: 14px; line-height: 1.7;">Choose your situation and send a quick question. We will reply with the next step that fits.</p>
                        <div class="row g-3">
                            @foreach($audienceSegments as $segment)
                                <div class="col-md-6">
                                    <article class="audience-card h-100 p-4 bg-white border border-zinc-100">
                                        <div class="audience-card-icon mb-3">
                                            <i class="{{ $segment['icon'] }}"></i>
                                        </div>
                                        <span class="audience-card-label">{{ $segment['label'] }}</span>
                                        <h3 class="h6 fw-black text-brand-dark mt-2 mb-2">{{ $segment['title'] }}</h3>
                                        <p class="text-zinc-600 mb-4" style="font-size: 12px; line-height: 1.65;">{{ $segment['body'] }}</p>
                                        <a href="{{ route('join-now', ['course' => 'undecided', 'audience' => $segment['source']]) }}" data-cta="{{ $segment['source'] }}" class="audience-card-link">{{ $segment['cta'] }} <i class="fa fa-arrow-right ms-1"></i></a>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(isset($servicePillars) && $servicePillars->count() > 0)
        <section class="py-5 bg-white">
            <div class="container">
                <div class="row justify-content-between align-items-end mb-4 g-3">
                    <div class="col-lg-7">
                        <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Need something specific?</span>
                        <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Choose one help area.</h2>
                        <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">Pick one help area and send us your goal. We will reply with the right course, timing, and next step.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('join-now', ['course' => 'undecided']) }}" data-cta="service-section-course-help" class="btn btn-brand-dark px-5 py-3 rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">Ask What Fits Me</a>
                    </div>
                </div>
                <div class="row g-3">
                    @foreach($servicePillars->take(3) as $pillar)
                        <div class="col-md-4">
                            <article class="h-100 p-4 bg-zinc-50 border border-zinc-100 rounded-2xl">
                                <div class="w-11 h-11 rounded-xl {{ $pillar->is_featured ? 'bg-brand-dark text-brand-gold' : 'bg-brand-gold/10 text-brand-gold' }} d-flex align-items-center justify-content-center mb-4">
                                    <i class="{{ $pillar->icon ?: 'fa fa-star' }}"></i>
                                </div>
                                <h3 class="h6 fw-black text-brand-dark mb-3" style="line-height: 1.35;">{{ $pillar->title }}</h3>
                                @if($pillar->summary)
                                    <p class="text-zinc-600 mb-4" style="font-size: 12px; line-height: 1.65;">{{ $pillar->summary }}</p>
                                @endif
                                <a href="{{ $pillar->cta_url ?: route('contact') }}" data-cta="service-pillar-{{ $pillar->slug }}" class="text-brand-dark font-black uppercase tracking-widest text-decoration-none hover:text-brand-gold transition-all" style="font-size: 9px;">
                                    {{ $pillar->cta_label ?: 'Ask More' }} <i class="fa fa-arrow-right ms-1"></i>
                                </a>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(isset($courses) && $courses->count() > 0)
        <section class="py-5 bg-zinc-50/50">
            <div class="container">
                <div class="row justify-content-between align-items-end mb-4 g-3">
                    <div class="col-lg-7">
                        <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Popular courses</span>
                        <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Courses students ask about most.</h2>
                        <p class="text-zinc-600 mb-0" style="font-size: 14px; line-height: 1.7;">Open a course that interests you, or ask us to compare options before you enroll.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('courses-all') }}" class="btn btn-outline-brand-dark px-5 py-3 rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">See All Courses</a>
                    </div>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach($courses->take(3) as $course)
                        <div class="col-lg-4 col-md-6">
                            <article class="premium-card p-0 group border border-zinc-100 shadow-xl rounded-2xl overflow-hidden h-100 d-flex flex-column bg-white">
                                <div class="relative overflow-hidden aspect-[16/9]">
                                    <span class="position-absolute top-0 start-0 bg-brand-gold text-brand-dark px-2.5 py-1 m-3 rounded-full text-[7.5px] font-black uppercase tracking-[0.3em] shadow-lg z-10">
                                        {{ $course->badge_text ?? 'Popular' }}
                                    </span>
                                    <img class="img-fluid w-100 h-100 object-cover transition-all duration-700 group-hover:scale-110" src="{{ \App\Support\PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg') }}" alt="{{ $course->name }}">
                                </div>
                                <div class="p-5 flex-grow-1 d-flex flex-column text-center">
                                    <h3 class="h6 mb-2 font-black text-brand-dark uppercase tracking-tight" style="font-size: 14px;">{{ $course->name }}</h3>
                                    <p class="text-zinc-500 mb-4" style="font-size: 11px; line-height: 1.65;">{{ Str::limit(strip_tags($course->description), 95) }}</p>
                                    <div class="d-flex justify-content-center gap-2 mb-4 mt-auto">
                                        <span class="bg-zinc-50 rounded-full px-3 py-1.5 border border-zinc-100 fw-black text-brand-dark uppercase tracking-widest" style="font-size: 8px;">{{ $course->duration }}</span>
                                        <span class="bg-zinc-50 rounded-full px-3 py-1.5 border border-zinc-100 fw-black text-brand-dark uppercase tracking-widest" style="font-size: 8px;">{{ $course->price }}</span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('courses-detail', $course->slug) }}" data-cta="homepage-course-roadmap" class="btn btn-outline-brand-dark py-2.5 rounded-xl font-black uppercase tracking-widest" style="font-size: 9px;">View Roadmap</a>
                                        <a href="{{ route('join-now', ['course' => $course->slug]) }}" data-cta="homepage-course-help" class="btn btn-primary py-2.5 rounded-xl font-black uppercase tracking-widest shadow-xl" style="font-size: 9px;">Ask About This Course</a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(isset($testimonials) && $testimonials->count() > 0)
        <section class="py-5 bg-white">
            <div class="container">
                <div class="text-center mb-4">
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Student proof</span>
                    <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Real students. Practical progress.</h2>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach($testimonials->take(3) as $testimonial)
                        <div class="col-lg-4 col-md-6">
                            <article class="premium-card p-5 h-100 border border-zinc-100 shadow-xl rounded-2xl bg-zinc-50/50">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <img src="{{ \App\Support\PublicAsset::url($testimonial->photo ?? null, 'site/img/testimonial-1.jpg') }}" onerror="this.src='{{ asset('site/img/testimonial-1.jpg') }}'" alt="{{ $testimonial->student_name ?? 'GoldenEye student' }}" class="rounded-circle object-cover border border-white shadow-sm" style="width: 58px; height: 58px;">
                                    <div>
                                        <h3 class="mb-1 font-black text-brand-dark text-xs uppercase tracking-wider">{{ $testimonial->student_name ?? 'Student' }}</h3>
                                        <small class="text-zinc-400 font-bold text-[9px] uppercase tracking-widest">{{ $testimonial->course_name ?? 'Alumni' }}</small>
                                    </div>
                                </div>
                                <div class="flex gap-1 text-brand-gold mb-4 text-xs">
                                    @for($i = 0; $i < 5; $i++)
                                        <i class="fa fa-star"></i>
                                    @endfor
                                </div>
                                <p class="text-zinc-600 text-sm italic mb-5 leading-relaxed">"{{ Str::limit(strip_tags($testimonial->content ?? 'Excellent institute!'), 145) }}"</p>
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
                    <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Keep it simple</span>
                    <h2 class="h3 fw-black text-white mt-3 mb-3">Tell us your goal. We will help you choose the next step.</h2>
                    <p class="text-white/70 mb-0" style="font-size: 14px; line-height: 1.7;">No pressure to enroll immediately. Send your details, ask a question, or message on WhatsApp.</p>
                </div>
                <div class="col-lg-4 d-flex flex-column gap-2">
                    <a href="{{ route('join-now', ['course' => 'undecided']) }}" data-cta="homepage-final-course-help" class="btn btn-primary py-3 rounded-xl font-black uppercase tracking-widest">Ask for Course Help</a>
                    <a href="{{ route('contact') }}" data-cta="homepage-final-contact" class="btn btn-outline-light py-3 rounded-xl font-black uppercase tracking-widest" style="font-size: 10px;">Ask a Question</a>
                </div>
            </div>
        </div>
    </section>
@endsection
