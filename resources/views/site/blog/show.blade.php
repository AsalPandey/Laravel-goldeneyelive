@extends('site.layout.app')
@section('page_title', \App\Support\StructuredData::titleWithBrand($post->meta_title ?: $post->title))
@section('og_title', \App\Support\StructuredData::titleWithBrand($post->meta_title ?: $post->title))
@section('og_type', 'article')
@section('meta_description', filled($post->meta_description) ? $post->meta_description : Str::limit(strip_tags($post->content), 160))
@section('canonical_url', route('blog-detail', $post->slug))
@section('meta_keywords', $post->meta_keywords ?? '')
@section('aeo_summary', $post->aeo_summary ?? '')
@section('og_image', \App\Support\PublicAsset::canonicalUrl(
    $post->image ?? null,
    \App\Support\PublicAsset::path(
        $settings['homepage_social_image'] ?? null,
        \App\Support\PublicAsset::path($settings['hero_image'] ?? null, 'site/img/logo.png'),
    ),
))
@if($post->image)
    @section('preload_assets')
        <link rel="preload" as="image" href="{{ \App\Support\PublicAsset::url($post->image, 'site/img/carousel-1.png') }}" fetchpriority="high">
    @endsection
@endif
@if($isPreview ?? false)
    @section('robots', 'noindex, nofollow, noarchive')
    @section('canonical_url', route('blog-detail', $post->slug))
@endif

@section('schema_markup')
    @jsonld(json_encode(\App\Support\StructuredData::articleSchema($post, $settings ?? [])))
    @jsonld(json_encode(\App\Support\StructuredData::breadcrumbSchema([
        ['name' => 'Home', 'url' => route('home')],
        ['name' => $settings['blog_header_title'] ?? 'Blog', 'url' => route('blog')],
        ['name' => $post->title, 'url' => route('blog-detail', $post->slug)],
    ])))
@endsection
@section('content')
    @php
        $blogDetailGuidanceUrl = route('join-now', [
            'course' => 'undecided',
            'selected_course' => 'undecided',
            'source_page' => 'blog-detail',
            'source_section' => 'blog-sidebar',
            'inquiry_intent' => 'course_guidance',
        ]);
    @endphp

    <!-- Header Start -->
    <div class="container-fluid py-4 mb-4 page-header">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="h2 text-white animated slideInDown font-black uppercase tracking-tighter">{{ $post->title }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('blog') }}">{{ $settings['blog_header_title'] ?? 'Blog' }}</a></li>
                            <li class="breadcrumb-item text-brand-gold active font-bold" aria-current="page">{{ $post->title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Post Content Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <article class="col-lg-8">
                    @if($post->image)
                    <img class="img-fluid w-100 rounded-xl shadow-lg mb-4" src="{{ \App\Support\PublicAsset::url($post->image ?? null, 'site/img/carousel-1.png') }}" onerror="this.src='{{ asset('site/img/carousel-1.png') }}'" alt="{{ $post->title }}" loading="eager" decoding="async" fetchpriority="high" width="1200" height="675" style="max-height: 350px; object-fit: cover;">
                    @endif

                    <div class="d-flex mb-3 border-bottom pb-2 text-muted" style="font-size: 10px;">
                        <div class="me-4"><i class="fa fa-user text-brand-gold me-2"></i>{{ $post->author ?? 'Admin' }}</div>
                        <div class="me-4"><i class="fa fa-calendar-alt text-brand-gold me-2"></i>{{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</div>
                        <div><i class="fa fa-tag text-brand-gold me-2"></i>{{ $post->category ?? 'Insights' }}</div>
                    </div>

                    <div class="blog-content fs-6 lh-relaxed text-neutral-800">
                        @sanitize($post->content)
                    </div>

                    @if($relatedCourses->isNotEmpty())
                        <section class="mt-5 border-top pt-5" aria-labelledby="related-courses-heading">
                            <span class="text-brand-gold font-black uppercase tracking-[0.35em]" style="font-size: 9px;">Continue with current course information</span>
                            <h2 id="related-courses-heading" class="h3 fw-black text-brand-dark mt-2 mb-4">Related Courses</h2>
                            <div class="row g-4">
                                @foreach($relatedCourses as $relatedCourse)
                                    <div class="col-md-6">
                                        <article class="premium-card h-100 overflow-hidden d-flex flex-column">
                                            <img src="{{ \App\Support\PublicAsset::url($relatedCourse->photo ?? null, 'site/img/cat-1.jpg') }}" alt="{{ $relatedCourse->name }}" class="w-100 object-cover" loading="lazy" decoding="async" width="640" height="360" style="aspect-ratio: 16 / 9;">
                                            <div class="p-4 d-flex flex-column flex-grow-1">
                                                <small class="text-brand-gold font-black uppercase tracking-widest mb-2" style="font-size: 8px;">{{ $relatedCourse->courseCategory?->name ?? $relatedCourse->category }}</small>
                                                <h3 class="h6 fw-black text-brand-dark mb-3">{{ $relatedCourse->name }}</h3>
                                                <dl class="row g-2 mb-4 text-zinc-600" style="font-size: 11px;">
                                                    @if(filled($relatedCourse->duration))
                                                        <dt class="col-4">Duration</dt><dd class="col-8 mb-0">{{ $relatedCourse->duration }}</dd>
                                                    @endif
                                                    @if(filled($relatedCourse->price))
                                                        <dt class="col-4">Fee</dt><dd class="col-8 mb-0">{{ $relatedCourse->price }}</dd>
                                                    @endif
                                                    @if(filled($relatedCourse->instructor))
                                                        <dt class="col-4">Instructor</dt><dd class="col-8 mb-0">{{ $relatedCourse->instructor }}</dd>
                                                    @endif
                                                </dl>
                                                <a href="{{ route('courses-detail', $relatedCourse->slug) }}" data-cta="blog-related-course" data-track-event="blog_related_course_click" data-source-page="blog-detail" data-source-section="related-courses" data-selected-course="{{ $relatedCourse->slug }}" class="btn btn-primary py-2 rounded-xl font-black uppercase tracking-widest mt-auto" style="font-size: 9px;">View Course Details</a>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                </article>

                <!-- Sidebar -->
                <aside class="col-lg-4">
                    <div class="bg-zinc-50 p-4 rounded-xl mb-5 border border-zinc-100">
                        <h2 class="mb-4 font-black uppercase tracking-tight text-brand-dark" style="font-size: 14px;">{{ $settings['recent_posts_title'] ?? 'Recent Posts' }}</h2>
                        @foreach($recentPosts as $rPost)
                        <div class="d-flex mb-3 align-items-center">
                            <img src="{{ \App\Support\PublicAsset::url($rPost->image ?? null, 'site/img/carousel-1.png') }}" class="rounded-lg" width="50" height="50" alt="{{ $rPost->title }}" loading="lazy" decoding="async" style="width: 50px; height: 50px; object-fit: cover;">
                            <div class="ps-3">
                                <h3 class="mb-1" style="font-size: 12px;"><a href="{{ route('blog-detail', $rPost->slug) }}" class="text-dark hover:text-brand-gold">{{ Str::limit($rPost->title, 40) }}</a></h3>
                                <small class="text-muted" style="font-size: 9px;">{{ $rPost->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="bg-brand-dark p-4 rounded-xl text-white text-center border border-brand-gold/20 shadow-lg">
                        <h2 class="text-brand-gold mb-2 font-black uppercase tracking-tight" style="font-size: 14px;">{{ $settings['blog_cta_title'] ?? 'Ready to Join?' }}</h2>
                        <p class="mb-3 text-white/60 extra-small">{{ $settings['blog_cta_desc'] ?? 'Take the next step in your career with our specialized courses.' }}</p>
                        <a href="{{ $blogDetailGuidanceUrl }}" data-cta="blog-detail-course-guidance" class="btn btn-primary px-4 py-2 rounded-lg font-black uppercase tracking-widest shadow-md" style="font-size: 9px;">Ask for Course Help</a>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection
