@extends('site.layout.app')
@section('page_title', 'Page Not Found - Golden Eye Academy')
@section('content')
    @php
        $errorWhatsappNumber = str_replace(['+', ' ', '-'], '', $settings['whatsapp_number'] ?? '9779856058599');
        $errorWhatsappMessage = rawurlencode('Hi Golden Eye Academy, I could not find the page I needed. Can you help me find the right course or contact information?');
    @endphp

    <!-- 404 Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <i class="bi bi-exclamation-triangle display-1 text-primary" aria-hidden="true"></i>
                    <p class="display-1 fw-black text-brand-dark mb-2" aria-hidden="true">404</p>
                    <h1 class="mb-4">Page Not Found</h1>
                    <p class="mb-4">That page is unavailable or may have moved. Use the links below to continue finding a course or contact the academy team.</p>
                    <div class="d-flex flex-column flex-sm-row flex-wrap justify-content-center gap-3">
                        <a class="btn btn-primary rounded-pill py-3 px-5" href="{{ route('courses-all') }}">View Courses</a>
                        <a class="btn btn-outline-brand-dark rounded-pill py-3 px-5" href="{{ route('contact') }}">Contact the Academy</a>
                        <a class="btn btn-outline-brand-dark rounded-pill py-3 px-5" href="https://wa.me/{{ $errorWhatsappNumber }}?text={{ $errorWhatsappMessage }}" target="_blank" rel="noopener">Message on WhatsApp</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 404 End -->
@endsection
