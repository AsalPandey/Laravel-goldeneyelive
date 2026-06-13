@extends('site.layout.app')
@section('page_title', $post->meta_title ?? $post->title . ' - Golden Eye Academy')
@section('og_title', $post->title . ' | Golden Eye Academy Blog')
@section('meta_description', $post->meta_description ?? Str::limit(strip_tags($post->content), 160))
@section('meta_keywords', $post->meta_keywords ?? '')
@section('aeo_summary', $post->aeo_summary ?? '')
@section('og_image', \App\Support\PublicAsset::url($post->image ?? null, 'site/img/carousel-1.png'))

@section('schema_markup')
    @if($post->schema_markup)
        @jsonld($post->schema_markup)
    @endif
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
                            <li class="breadcrumb-item text-brand-gold active font-bold uppercase" aria-current="page">Details</li>
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
                    {{-- Default Article Schema for GEO --}}
                    <script type="application/ld+json">
                    {
                      "@@context": "https://schema.org",
                      "@@type": "Article",
                      "headline": @json($post->title),
                      "image": "{{ \App\Support\PublicAsset::url($post->image ?? null, 'site/img/carousel-1.png') }}",
                      "author": {
                        "@@type": "Person",
                        "name": @json($post->author ?? 'Golden Eye Academy')
                      },
                      "publisher": {
                        "@@type": "Organization",
                        "name": @json(\App\Support\StructuredData::siteName($settings ?? [])),
                        "logo": {
                          "@@type": "ImageObject",
                          "url": "{{ url('site/img/logo.png') }}"
                        }
                      },
                      "datePublished": "{{ $post->created_at->toIso8601String() }}",
                      "dateModified": "{{ $post->updated_at->toIso8601String() }}"
                    }
                    </script>

                    @if($post->image)
                    <img class="img-fluid w-100 rounded-xl shadow-lg mb-4" src="{{ \App\Support\PublicAsset::url($post->image ?? null, 'site/img/carousel-1.png') }}" onerror="this.src='{{ asset('site/img/carousel-1.png') }}'" alt="{{ $post->title }}" style="max-height: 350px; object-fit: cover;">
                    @endif

                    <div class="d-flex mb-3 border-bottom pb-2 text-muted" style="font-size: 10px;">
                        <div class="me-4"><i class="fa fa-user text-brand-gold me-2"></i>{{ $post->author ?? 'Admin' }}</div>
                        <div class="me-4"><i class="fa fa-calendar-alt text-brand-gold me-2"></i>{{ $post->created_at->format('M d, Y') }}</div>
                        <div><i class="fa fa-tag text-brand-gold me-2"></i>{{ $post->category ?? 'Insights' }}</div>
                    </div>

                    <div class="blog-content fs-6 lh-relaxed text-neutral-800">
                        @sanitize($post->content)
                    </div>

                    @if($post->schema_markup)
                        @jsonld($post->schema_markup)
                    @endif
                </article>

                <!-- Sidebar -->
                <aside class="col-lg-4">
                    <div class="bg-zinc-50 p-4 rounded-xl mb-5 border border-zinc-100">
                        <h6 class="mb-4 font-black uppercase tracking-tight text-brand-dark" style="font-size: 14px;">{{ $settings['recent_posts_title'] ?? 'Recent Posts' }}</h6>
                        @foreach($recentPosts as $rPost)
                        <div class="d-flex mb-3 align-items-center">
                            <img src="{{ \App\Support\PublicAsset::url($rPost->image ?? null, 'site/img/carousel-1.png') }}" class="rounded-lg" style="width: 50px; height: 50px; object-fit: cover;">
                            <div class="ps-3">
                                <h6 class="mb-1" style="font-size: 12px;"><a href="{{ route('blog-detail', $rPost->slug) }}" class="text-dark hover:text-brand-gold">{{ Str::limit($rPost->title, 40) }}</a></h6>
                                <small class="text-muted" style="font-size: 9px;">{{ $rPost->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="bg-brand-dark p-4 rounded-xl text-white text-center border border-brand-gold/20 shadow-lg">
                        <h6 class="text-brand-gold mb-2 font-black uppercase tracking-tight" style="font-size: 14px;">{{ $settings['blog_cta_title'] ?? 'Ready to Join?' }}</h6>
                        <p class="mb-3 text-white/60 extra-small">{{ $settings['blog_cta_desc'] ?? 'Take the next step in your career with our specialized courses.' }}</p>
                        <a href="{{ $blogDetailGuidanceUrl }}" data-cta="blog-detail-course-guidance" class="btn btn-primary px-4 py-2 rounded-lg font-black uppercase tracking-widest shadow-md" style="font-size: 9px;">Ask for Course Help</a>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection
