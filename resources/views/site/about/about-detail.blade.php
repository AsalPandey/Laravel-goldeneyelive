@extends('site.layout.app')
@section('page_title', $settings['meta_title'] ?? 'Our Story - GoldenEye Academy')
@section('meta_description', $settings['meta_description'] ?? 'Learn about the history and mission of GoldenEye Academy in Pokhara.')
@section('content')
    <!-- Header Start -->
    <div class="container-fluid page-header py-4 mb-4 wow fadeIn" data-wow-delay="0.1s">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="h2 text-white animated slideInDown font-black uppercase tracking-tighter">About Us</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('about') }}">About Us</a></li>
                            <li class="breadcrumb-item text-brand-gold active font-black uppercase tracking-tighter" aria-current="page">Our Story</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
    <!-- About Details Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp position-relative" data-wow-delay="0.1s" style="min-height: 300px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100 rounded-xl shadow-lg border-2 border-white" src="{{ \App\Support\PublicAsset::url($settings['about_image'] ?? null, 'site/img/about-details.jpg') }}" alt="About GoldenEye" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    @if(isset($settings['about_page_content']) && !empty(trim($settings['about_page_content'])))
                        <h6 class="section-title bg-white text-start text-primary pe-3">About Us</h6>
                        <div class="cms-dynamic-content text-zinc-600 mb-5 extra-small leading-relaxed">
                            @sanitize($settings['about_page_content'])
                        </div>
                    @else
                        <h6 class="section-title bg-white text-start text-primary pe-3">About Us</h6>
                        <h2 class="h3 mb-4 font-black text-brand-dark uppercase tracking-tight">Our Journey at <br> Golden Eye Academy</h2>
                        <p class="mb-4 extra-small text-zinc-600 leading-relaxed">Since its founding in 2008, Golden Eye Academy has been committed to delivering practical educational experiences. Through a blend of expert guidance, focused programs, and personal course direction, we help students build skills they can use in study, work, and daily communication.</p>

                        <h3 class="h4 mb-3 font-black text-brand-dark uppercase tracking-tight">Our Mission</h3>
                        <p class="mb-4 extra-small text-zinc-600 leading-relaxed">Our mission is to help students and parents choose practical courses with clear information. We explain course fit, fees, timing, support, and realistic next steps before enrollment.</p>

                        <h3 class="h4 mb-3 font-black text-brand-dark uppercase tracking-tight">Our Vision</h3>
                        <p class="mb-4 extra-small text-zinc-600 leading-relaxed">At Golden Eye Academy, we want learners in Pokhara to get practical preparation for study, work, language, and computer needs without pressure or confusion.</p>

                        <h3 class="h4 mb-3 font-black text-brand-dark uppercase tracking-tight">Our History</h3>
                        <p class="mb-4 extra-small text-zinc-600 leading-relaxed">Golden Eye Academy was established in 2008 in Pokhara. Over the years, students and parents have visited us for course guidance, language preparation, computer skills, office skills, and study-abroad test preparation.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- About Details End -->
@endsection
