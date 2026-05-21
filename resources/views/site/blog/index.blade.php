@extends('site.layout.app')
@section('page_title', 'Academy Blog - GoldenEye Academy Pokhara')
@section('content')
    @php
        $blogGuidanceUrl = route('join-now', [
            'course' => 'undecided',
            'selected_course' => 'undecided',
            'source_page' => 'blog',
            'source_section' => 'blog-header',
            'inquiry_intent' => 'course_guidance',
        ]);
    @endphp

    <!-- Header Start -->
    <div class="container-fluid page-header py-4 mb-4 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="h2 text-white animated slideInDown font-black uppercase tracking-tighter">{{ $settings['blog_title'] ?? 'Academy Blog' }}</h1>
                    <p class="text-white-50 mb-3 small font-bold">{{ $settings['blog_subtitle'] ?? 'Guides, updates, and decisions that help you move faster.' }}</p>
                    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-center gap-2 mb-4">
                        <a href="{{ $blogGuidanceUrl }}" data-cta="blog-header-course-guidance" class="btn btn-primary rounded-pill px-4 py-2 font-black uppercase tracking-widest" style="font-size: 10px;">
                            Ask for Course Help
                        </a>
                        <a href="{{ route('courses-all') }}" data-cta="blog-header-programs" class="btn btn-outline-light rounded-pill px-4 py-2 font-black uppercase tracking-widest" style="font-size: 10px;">
                            View Course Details
                        </a>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item text-brand-gold active font-bold uppercase" aria-current="page">Blog</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Blog Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <span class="section-title">{{ $settings['blog_tagline'] ?? 'Academy Insights' }}</span>
                <h2 class="h3 mb-5 font-black uppercase tracking-tight">{{ $settings['blog_section_title'] ?? 'Latest From GoldenEye' }}</h2>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach($posts as $post)
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="premium-card p-0 h-100 d-flex flex-column group">
                        <div class="position-relative overflow-hidden" style="height: 180px;">
                            <img class="img-fluid w-100 h-100 object-cover authentic-vibe" src="{{ \App\Support\PublicAsset::url($post->image ?? null, 'site/img/carousel-1.png') }}" onerror="this.src='{{ asset('site/img/carousel-1.png') }}'" alt="{{ $post->title }}">
                            <div class="position-absolute top-2 left-2 z-10">
                                <span class="bg-brand-gold text-brand-dark text-[8px] font-black uppercase px-2 py-1 rounded-full shadow-lg tracking-widest">
                                    {{ $post->category ?? 'Insights' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-5 flex-grow-1">
                            <div class="d-flex align-items-center gap-3 mb-3 text-muted small uppercase font-black tracking-widest" style="font-size: 9px;">
                                <span><i class="fa fa-calendar-alt text-brand-gold me-1"></i>{{ $post->created_at->format('M d, Y') }}</span>
                                <span><i class="fa fa-user text-brand-gold me-1"></i>{{ $post->author ?? 'Admin' }}</span>
                            </div>
                            <h6 class="mb-2 font-black text-brand-dark lh-base" style="font-size: 14px;">
                                <a href="{{ route('blog-detail', $post->slug) }}" class="text-dark hover:text-brand-gold transition-colors">{{ Str::limit($post->title, 60) }}</a>
                            </h6>
                            <p class="text-zinc-500 extra-small leading-relaxed mb-0" style="font-size: 11px;">{{ Str::limit(strip_tags($post->content), 100) }}</p>
                        </div>
                        <div class="px-4 pb-4 mt-auto">
                            <a href="{{ route('blog-detail', $post->slug) }}" class="btn btn-outline-dark w-100 rounded-lg py-2 font-black uppercase tracking-widest" style="font-size: 9px;">
                                Read Full Article <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-5 d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection
