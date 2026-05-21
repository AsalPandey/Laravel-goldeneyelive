    <!-- Footer Start -->
    <div class="container-fluid bg-brand-dark text-light footer pt-4 mt-0 border-t-2 border-brand-gold">
        <div class="container py-3">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white text-uppercase tracking-widest font-black mb-3" style="font-size: 11px;">{{ $settings['footer_quick_link_title'] ?? 'Quick Link' }}</h6>
                    <a class="btn btn-link extra-small" href="{{ route('about') }}">About Us</a>
                    <a class="btn btn-link extra-small" href="{{ route('courses-all') }}">Courses</a>
                    <a class="btn btn-link extra-small" href="{{ route('contact') }}">Contact</a>
                    <a class="btn btn-link extra-small" href="{{ route('privacy-policy') }}">Privacy Policy</a>
                    <a class="btn btn-link extra-small" href="{{ route('terms-and-conditions') }}">Terms & Conditions</a>
                    <a class="btn btn-link extra-small" href="{{ route('faq') }}">FAQs & Help</a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white text-uppercase tracking-widest font-black mb-3" style="font-size: 11px;">{{ $settings['footer_contact_title'] ?? 'Contact' }}</h6>
                    <p class="mb-2 extra-small"><i class="fa fa-map-marker-alt me-3 text-brand-gold"></i>{{ $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal' }}</p>
                    @php
                        $footerPhone = $settings['site_phone'] ?? '061-572599, 9856058599';
                        $footerPhoneHref = preg_replace('/[^0-9+]/', '', explode(',', $footerPhone)[0]);
                    @endphp
                    <p class="mb-2 extra-small"><i class="fa fa-phone-alt me-3 text-brand-gold"></i><a href="tel:{{ $footerPhoneHref }}" class="text-light text-decoration-none" data-source-page="footer" data-source-section="footer-phone" data-cta-label="Phone">{{ $footerPhone }}</a></p>
                    <p class="mb-2 extra-small"><i class="fa fa-envelope me-3 text-brand-gold"></i>{{ $settings['site_email'] ?? 'contact@goldeneye.edu.np' }}</p>
                    <div class="d-flex pt-2">
                        @if(isset($settings['facebook_url']) && $settings['facebook_url'])
                        <a class="btn btn-outline-light btn-social" href="{{ $settings['facebook_url'] }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(isset($settings['instagram_url']) && $settings['instagram_url'])
                        <a class="btn btn-outline-light btn-social" href="{{ $settings['instagram_url'] }}" target="_blank"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(isset($settings['linkedin_url']) && $settings['linkedin_url'])
                        <a class="btn btn-outline-light btn-social" href="{{ $settings['linkedin_url'] }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        @endif
                        @if(isset($settings['youtube_url']) && $settings['youtube_url'])
                        <a class="btn btn-outline-light btn-social" href="{{ $settings['youtube_url'] }}" target="_blank"><i class="fab fa-youtube"></i></a>
                        @endif
                        @if(isset($settings['whatsapp_number']) && $settings['whatsapp_number'])
                        <a class="btn btn-outline-light btn-social" href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $settings['whatsapp_number']) }}" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h6 class="text-white text-uppercase tracking-widest font-black mb-3" style="font-size: 11px;">{{ $settings['footer_faq_title'] ?? 'Academic Guide' }}</h6>
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
                    <h6 class="text-white text-uppercase tracking-widest font-black mb-3" style="font-size: 11px;">{{ $settings['footer_social_title'] ?? 'Stay Connected' }}</h6>
                    <p class="extra-small mb-3">{{ $settings['footer_newsletter_desc'] ?? 'Sign up for career insights & class updates.' }}</p>
                    <form action="{{ route('newsletter') }}" method="POST" id="newsletterForm">
                        @csrf
                        <div class="d-flex flex-column flex-sm-row gap-2 mx-auto mb-3" style="max-width: 420px;">
                            <input class="form-control border-0 flex-grow-1 py-3 px-3 bg-white text-brand-dark rounded-lg extra-small" type="email" name="email" id="newsletter_email" placeholder="Your Email Address" required>
                            <button type="submit" class="btn btn-primary py-3 px-4 rounded-lg font-black uppercase tracking-widest shadow-lg flex-shrink-0" style="font-size: 9px;">Join</button>
                        </div>
                        @if(isset($settings['recaptcha_site_key']) && !empty($settings['recaptcha_site_key']))
                        <div class="g-recaptcha" data-sitekey="{{ $settings['recaptcha_site_key'] }}" data-size="compact" data-theme="dark"></div>
                        @error('g-recaptcha-response') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        @endif
                    </form>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="copyright">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        &copy; <a class="border-bottom" href="{{ url('/') }}">{{ $settings['site_name'] ?? 'GoldenEye' }} {{ $settings['site_name_suffix'] ?? 'Academy' }}</a> (Est. 2008), All Rights Reserved.
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
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('site/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('site/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('site/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('site/lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('site/js/main.js') }}"></script>
