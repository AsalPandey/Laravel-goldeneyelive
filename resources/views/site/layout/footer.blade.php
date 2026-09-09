    <!-- Footer Start -->
    <div class="container-fluid bg-brand-dark text-light footer pt-4 mt-0 border-t-2 border-brand-gold">
        <div class="container py-3">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <h2 class="text-white text-uppercase tracking-widest font-black mb-3" style="font-size: 11px;">{{ $settings['footer_quick_link_title'] ?? 'Quick Link' }}</h2>
                    <div class="extra-small text-muted mb-3">
                        @sanitize($settings['footer_about_text'] ?? 'Golden Eye Academy provides practical courses, classes, workshops and academic programs for students, professionals, schools and organizations.')
                    </div>
                    <a class="btn btn-link extra-small" href="{{ route('about') }}">About Us</a>
                    <a class="btn btn-link extra-small" href="{{ route('courses-all') }}">Courses</a>
                    <a class="btn btn-link extra-small" href="{{ route('contact') }}">Contact</a>
                    <a class="btn btn-link extra-small" href="{{ route('privacy-policy') }}">Privacy Policy</a>
                    <a class="btn btn-link extra-small" href="{{ route('terms-and-conditions') }}">Terms & Conditions</a>
                    <a class="btn btn-link extra-small" href="{{ route('faq') }}">FAQs & Help</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h2 class="text-white text-uppercase tracking-widest font-black mb-3" style="font-size: 11px;">{{ $settings['footer_contact_title'] ?? 'Contact' }}</h2>
                    <p class="mb-2 extra-small"><i class="fa fa-map-marker-alt me-3 text-brand-gold"></i>{{ $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal' }}</p>
                    @php
                        $footerPhones = \App\Support\ContactPhones::parse($settings['site_phone'] ?? '061-572599, 9856058599');
                    @endphp
                    <div class="mb-2 extra-small d-flex align-items-start">
                        <i class="fa fa-phone-alt me-3 mt-1 text-brand-gold" aria-hidden="true"></i>
                        <div>
                            @foreach($footerPhones as $footerPhone)
                                <a href="tel:{{ $footerPhone['href'] }}" class="d-block text-light text-decoration-none mb-1" data-source-page="footer" data-source-section="footer-phone" data-cta-label="Phone">{{ $footerPhone['display'] }}</a>
                            @endforeach
                        </div>
                    </div>
                    <p class="mb-2 extra-small"><i class="fa fa-envelope me-3 text-brand-gold"></i>{{ config('goldeneye.official_email', 'contact@goldeneye.edu.np') }}</p>
                    <div class="d-flex pt-2">
                        @if(isset($settings['facebook_url']) && $settings['facebook_url'])
                        <a class="btn btn-outline-light btn-social" href="{{ $settings['facebook_url'] }}" target="_blank" rel="noopener" aria-label="Golden Eye Academy on Facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
                        @endif
                        @if(isset($settings['instagram_url']) && $settings['instagram_url'])
                        <a class="btn btn-outline-light btn-social" href="{{ $settings['instagram_url'] }}" target="_blank" rel="noopener" aria-label="Golden Eye Academy on Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                        @endif
                        @if(isset($settings['linkedin_url']) && $settings['linkedin_url'])
                        <a class="btn btn-outline-light btn-social" href="{{ $settings['linkedin_url'] }}" target="_blank" rel="noopener" aria-label="Golden Eye Academy on LinkedIn"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a>
                        @endif
                        @if(isset($settings['youtube_url']) && $settings['youtube_url'])
                        <a class="btn btn-outline-light btn-social" href="{{ $settings['youtube_url'] }}" target="_blank" rel="noopener" aria-label="Golden Eye Academy on YouTube"><i class="fab fa-youtube" aria-hidden="true"></i></a>
                        @endif
                        @if(isset($settings['tiktok_url']) && $settings['tiktok_url'])
                        <a class="btn btn-outline-light btn-social" href="{{ $settings['tiktok_url'] }}" target="_blank" rel="noopener" aria-label="Golden Eye Academy on TikTok"><i class="fab fa-tiktok" aria-hidden="true"></i></a>
                        @endif
                        @if(isset($settings['whatsapp_number']) && $settings['whatsapp_number'])
                        <a class="btn btn-outline-light btn-social" href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $settings['whatsapp_number']) }}" target="_blank" rel="noopener" aria-label="Message Golden Eye Academy on WhatsApp"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h2 class="text-white text-uppercase tracking-widest font-black mb-3" style="font-size: 11px;">{{ $settings['footer_faq_title'] ?? 'Academic Guide' }}</h2>
                    <p class="extra-small text-muted mb-3">Quick answers about course fit, fees, timing, and next steps.</p>
                    @if(isset($footerFaqs) && is_iterable($footerFaqs) && count($footerFaqs) > 0)
                        @foreach($footerFaqs as $footerFaq)
                            <a class="btn btn-link py-1 text-truncate w-100 extra-small" href="{{ route('faq') }}#faq-{{ $footerFaq->id }}" title="{{ $footerFaq->question }}">
                                {{ $footerFaq->question }}
                            </a>
                        @endforeach
                    @else
                        <a class="btn btn-link py-1 extra-small" href="{{ route('faq') }}">General FAQs</a>
                        <a class="btn btn-link py-1 extra-small" href="{{ route('faq') }}">Admission Guide</a>
                    @endif
                </div>
                <div class="col-lg-3 col-md-6">
                    <h2 class="text-white text-uppercase tracking-widest font-black mb-3" style="font-size: 11px;">{{ $settings['footer_social_title'] ?? 'Stay Connected' }}</h2>
                    <p class="extra-small mb-3">{{ $settings['footer_newsletter_desc'] ?? 'Sign up for career insights & class updates.' }}</p>
                    @php
                        $newsletterValidationErrors = session('newsletter_validation_errors', []);
                        $newsletterEmailError = data_get($newsletterValidationErrors, 'email.0');
                    @endphp
                    <form action="{{ route('newsletter') }}" method="POST" id="newsletterForm">
                        @csrf
                        <div class="d-flex flex-column flex-sm-row gap-2 mx-auto mb-3" style="max-width: 420px;">
                            <label class="visually-hidden" for="newsletter_email">Email address for academy updates</label>
                            <input class="form-control border-0 flex-grow-1 py-3 px-3 bg-white text-brand-dark rounded-lg extra-small {{ $newsletterEmailError ? 'is-invalid' : '' }}" type="email" name="email" id="newsletter_email" placeholder="Your Email Address" value="{{ old('email') }}" required aria-invalid="{{ $newsletterEmailError ? 'true' : 'false' }}" @if($newsletterEmailError) aria-describedby="newsletterEmailError" @endif>
                            <button type="submit" class="btn btn-primary py-3 px-4 rounded-lg font-black uppercase tracking-widest shadow-lg flex-shrink-0" style="font-size: 9px;">Join</button>
                        </div>
                        @if($newsletterEmailError)
                            <div id="newsletterEmailError" class="text-danger small mt-2">{{ $newsletterEmailError }}</div>
                        @endif
                        <x-recaptcha error-bag="newsletter" size="compact" theme="dark" />
                    </form>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="{{ url('/') }}">{{ \App\Support\StructuredData::siteName($settings ?? []) }}</a>, All Rights Reserved.
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-menu">
                            <a href="{{ route('home') }}">Home</a>
                            <a href="{{ route('contact') }}">Help</a>
                            <a href="{{ route('faq') }}">FAQ Center</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top" aria-label="Back to top"><i class="bi bi-arrow-up" aria-hidden="true"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous" defer></script>
    <script src="{{ asset('site/lib/wow/wow.min.js') }}" defer></script>
    <script src="{{ asset('site/lib/easing/easing.min.js') }}" defer></script>
    <script src="{{ asset('site/lib/waypoints/waypoints.min.js') }}" defer></script>
    <script src="{{ asset('site/lib/owlcarousel/owl.carousel.min.js') }}" defer></script>

    <!-- Template Javascript -->
    <script src="{{ asset('site/js/main.js') }}" defer></script>
