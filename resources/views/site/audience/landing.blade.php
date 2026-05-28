@extends('site.layout.app')

@section('page_title', $landingPage['page_title'])
@section('meta_description', $landingPage['meta_description'])
@section('tracking_source_page', $landingPage['source_page'])
@section('tracking_audience_type', $landingPage['audience_type'])
@section('tracking_inquiry_intent', $landingPage['inquiry_intent'])
@section('tracking_selected_course', $landingPage['selected_course'])

@section('content')
    @php
        $courseHelpUrl = fn (string $sourceSection) => route('join-now', [
            'course' => 'undecided',
            'selected_course' => $landingPage['selected_course'],
            'source_page' => $landingPage['source_page'],
            'source_section' => $sourceSection,
            'audience_type' => $landingPage['audience_type'],
            'inquiry_intent' => $landingPage['inquiry_intent'],
        ]);

        $whatsappNumber = str_replace(['+', ' ', '-'], '', $settings['whatsapp_number'] ?? '9779856058599');
        $whatsappMessage = rawurlencode('Hi GoldenEye Academy, I need course help. Source: '.$landingPage['source_page']);
    @endphp

    <section class="container-fluid page-header mb-0" style="background: linear-gradient(rgba(5, 12, 28, 0.88), rgba(5, 12, 28, 0.82)), url('{{ \App\Support\PublicAsset::url($settings['hero_image'] ?? null, 'site/img/carousel-1.png') }}'); background-size: cover; background-position: center;">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-center">
                    <span class="badge rounded-pill bg-brand-gold px-4 py-2 text-brand-dark fw-black text-uppercase tracking-[0.3em] mb-4" style="font-size: 9px;">{{ $landingPage['badge'] }}</span>
                    <h1 class="font-black text-white mb-4" style="font-size: clamp(2rem, 4vw, 3.4rem); line-height: 1.05;">{{ $landingPage['headline'] }}</h1>
                    <p class="text-white-50 mx-auto mb-4" style="max-width: 760px; font-size: 16px; line-height: 1.7;">{{ $landingPage['subheadline'] }}</p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="{{ $courseHelpUrl('hero_cta') }}" data-cta="audience-landing-course-help" data-cta-label="Ask for Course Help" data-source-page="{{ $landingPage['source_page'] }}" data-source-section="hero_cta" data-selected-course="{{ $landingPage['selected_course'] }}" data-audience-type="{{ $landingPage['audience_type'] }}" data-inquiry-intent="{{ $landingPage['inquiry_intent'] }}" class="btn btn-primary py-3 px-5 rounded-pill fw-black text-uppercase tracking-widest" style="font-size: 10px;">Ask for Course Help</a>
                        @if($landingPage['secondary_cta'] === 'whatsapp')
                            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}" target="_blank" rel="noopener" data-cta="audience-landing-whatsapp" data-cta-label="Message on WhatsApp" class="btn btn-outline-light py-3 px-5 rounded-xl fw-black text-uppercase tracking-widest" style="font-size: 10px;">Message on WhatsApp</a>
                        @else
                            <a href="{{ route('courses-all') }}" data-cta="audience-landing-course-details" data-cta-label="View Course Details" class="btn btn-outline-light py-3 px-5 rounded-xl fw-black text-uppercase tracking-widest" style="font-size: 10px;">View Course Details</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-5">
                    <div class="minimal-card h-100 p-4 p-lg-5">
                        <span class="text-brand-gold font-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">What usually happens</span>
                        <h2 class="h4 fw-black text-brand-dark mt-3 mb-3">The choice needs context first</h2>
                        <p class="text-zinc-600 mb-0" style="line-height: 1.7;">{{ $landingPage['problem'] }}</p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="minimal-card h-100 p-4 p-lg-5">
                        <span class="text-brand-gold font-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Recommended paths</span>
                        <h2 class="h4 fw-black text-brand-dark mt-3 mb-4">Start with the path that fits your goal</h2>
                        <div class="row g-3">
                            @foreach($landingPage['paths'] as $path)
                                <div class="col-md-6">
                                    <div class="d-flex gap-3 h-100 p-3 rounded-xl bg-zinc-50 border border-zinc-100">
                                        <i class="fa fa-check-circle text-brand-gold mt-1"></i>
                                        <p class="mb-0 fw-bold text-brand-dark" style="font-size: 13px; line-height: 1.5;">{{ $path }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-zinc-50">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="h-100">
                        <span class="text-brand-gold font-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Why GoldenEye helps</span>
                        <h2 class="h3 fw-black text-brand-dark mt-3 mb-3">Guidance before enrollment</h2>
                        <p class="text-zinc-600 mb-4" style="line-height: 1.7;">{{ $landingPage['why'] }}</p>
                        <a href="{{ $courseHelpUrl('why_goldeneye') }}" data-cta="audience-why-course-help" data-cta-label="Ask for Course Help" data-source-page="{{ $landingPage['source_page'] }}" data-source-section="why_goldeneye" data-selected-course="{{ $landingPage['selected_course'] }}" data-audience-type="{{ $landingPage['audience_type'] }}" data-inquiry-intent="{{ $landingPage['inquiry_intent'] }}" class="btn btn-primary py-3 px-5 rounded-xl fw-black text-uppercase tracking-widest" style="font-size: 10px;">Ask for Course Help</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="minimal-card p-4 p-lg-5 h-100">
                        <span class="text-brand-gold font-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Trust markers</span>
                        <div class="d-grid gap-3 mt-4">
                            @foreach($landingPage['proof'] as $proof)
                                <div class="d-flex gap-3">
                                    <i class="fa fa-shield-alt text-brand-gold mt-1"></i>
                                    <p class="mb-0 fw-bold text-brand-dark" style="font-size: 13px;">{{ $proof }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 conversion-final">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <span class="text-brand-gold font-black text-uppercase tracking-[0.35em]" style="font-size: 9px;">Next step</span>
                    <h2 class="h3 fw-black text-white mt-3 mb-3">{{ $landingPage['final_headline'] }}</h2>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-4">
                        <a href="{{ $courseHelpUrl('final_cta') }}" data-cta="audience-final-course-help" data-cta-label="Ask for Course Help" data-source-page="{{ $landingPage['source_page'] }}" data-source-section="final_cta" data-selected-course="{{ $landingPage['selected_course'] }}" data-audience-type="{{ $landingPage['audience_type'] }}" data-inquiry-intent="{{ $landingPage['inquiry_intent'] }}" class="btn btn-primary py-3 px-5 rounded-xl fw-black text-uppercase tracking-widest" style="font-size: 10px;">Ask for Course Help</a>
                        @if($landingPage['secondary_cta'] === 'whatsapp')
                            <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}" target="_blank" rel="noopener" data-cta="audience-final-whatsapp" data-cta-label="Message on WhatsApp" class="btn btn-outline-light py-3 px-5 rounded-xl fw-black text-uppercase tracking-widest" style="font-size: 10px;">Message on WhatsApp</a>
                        @else
                            <a href="{{ route('courses-all') }}" data-cta="audience-final-course-details" data-cta-label="View Course Details" class="btn btn-outline-light py-3 px-5 rounded-xl fw-black text-uppercase tracking-widest" style="font-size: 10px;">View Course Details</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
