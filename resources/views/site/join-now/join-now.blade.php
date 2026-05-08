@extends('site.layout.app')
@section('page_title', ($settings['enroll_header_title'] ?? 'Ask for Course Help') . ' - ' . ($settings['site_name'] ?? 'GoldenEye Academy'))
@section('content')
    <!-- Header Start -->
    <div class="container-fluid page-header mb-5" style="background: linear-gradient(rgba(5, 12, 28, 0.85), rgba(5, 12, 28, 0.85)), url('{{ \App\Support\PublicAsset::url($settings['hero_image'] ?? null, 'site/img/carousel-1.png') }}'); background-size: cover; background-position: center;">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="font-black text-white animated slideInDown uppercase tracking-tighter" style="font-size: clamp(1.6rem, 3.5vw, 2.5rem); line-height: 1;">{{ $settings['enroll_header_title'] ?? 'Ask for Course Help' }}</h1>
                    <p class="text-brand-gold fw-black uppercase tracking-[0.3em] mb-4 animated fadeIn" style="font-size: 11px;">Find the right course before you enroll</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item text-brand-gold active font-bold uppercase" aria-current="page">Enrollment</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
    <!-- join now Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp mb-5" data-wow-delay="0.1s">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                    <div style="width: 40px; height: 2px; background: var(--brand-gold);"></div>
                    <span class="text-brand-gold font-black uppercase tracking-[0.3em]" style="font-size: 11px;">Quick Course Help</span>
                    <div style="width: 40px; height: 2px; background: var(--brand-gold);"></div>
                </div>
                <h2 class="h3 fw-black text-brand-dark uppercase tracking-tighter">{{ $settings['enroll_section_title'] ?? 'Tell Us Your Goal' }} <span class="text-brand-gold">We Will Reply With Options</span></h2>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-8 col-md-12 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="premium-card p-5 shadow-lg form-conversational">
                        <form action="{{ route('join-now-submit') }}" method="POST" id="joinNow">
                            @csrf
                            <input type="hidden" name="lead_source" value="join_now_page">
                            <input type="hidden" name="landing_page" value="{{ url()->current() }}">
                            <input type="hidden" name="cta_id" value="join-now-form">
                            <div class="row g-4">
                                <!-- The Hook: Core Lead Info -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control border-0 bg-light" id="firstName" name="firstName" placeholder="First Name" value="{{ old('firstName') }}" required>
                                        <label for="firstName">First Name</label>
                                        @error('firstName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-2">
                                        <input type="text" class="form-control border-0 bg-light" id="lastName" name="lastName" placeholder="Last Name" value="{{ old('lastName') }}" required>
                                        <label for="lastName">Last Name</label>
                                        @error('lastName') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control border-0 bg-light" id="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required>
                                        <label for="email">Where should we email you?</label>
                                        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control border-0 bg-light" id="phone" name="phone" placeholder="Phone Number" value="{{ old('phone') }}" required>
                                        <label for="phone">Your WhatsApp or Phone Number?</label>
                                        @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <select class="form-select border-0 bg-light" id="course" name="course" required>
                                            <option value="" disabled selected>Select Your Desired Career Path</option>
                                            <option value="undecided" {{ (old('course') ?? ($selectedCourse ?? '')) === 'undecided' ? 'selected' : '' }}>
                                                I need help choosing the right program
                                            </option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->slug }}" {{ (old('course') ?? ($selectedCourse ?? '')) == $course->slug ? 'selected' : '' }}>
                                                    {{ $course->name }}
                                                </option>
                                            @endforeach
                                        </select>                                    
                                        <label for="course">Select Your Professional Track</label>
                                        @error('course') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <!-- Secondary Info: Collapsible to reduce friction -->
                                <div class="col-12 mt-4">
                                    <a class="text-primary small fw-bold text-decoration-none" data-bs-toggle="collapse" href="#extraDetails" role="button" aria-expanded="{{ $errors->hasAny(['address', 'contactMethod', 'queries']) ? 'true' : 'false' }}">
                                        <i class="fa fa-plus-circle me-1"></i> Add more details (Optional)
                                    </a>
                                    <div class="collapse mt-3 {{ $errors->hasAny(['address', 'contactMethod', 'queries']) ? 'show' : '' }}" id="extraDetails">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select border-0 bg-light" id="contactMethod" name="contactMethod">
                                                        <option value="" disabled selected>Best Way to Contact You?</option>
                                                        <option value="Phone Call">Phone Call</option>
                                                        <option value="WhatsApp">WhatsApp</option>
                                                        <option value="Email">Email</option>
                                                    </select>
                                                    <label for="contactMethod">Preferred Contact Method</label>
                                                    @error('contactMethod') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control border-0 bg-light" id="address" name="address" placeholder="Address" value="{{ old('address') }}">
                                                    <label for="address">Where do you live? (City/Area)</label>
                                                    @error('address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control border-0 bg-light" name="queries" id="queries" placeholder="Your Queries" style="height: 100px">{{ old('queries') }}</textarea>
                                                    <label for="queries">Any specific career goals or questions?</label>
                                                    @error('queries') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if(isset($settings['recaptcha_site_key']) && !empty($settings['recaptcha_site_key']))
                                <div class="col-12">
                                    <div class="g-recaptcha" data-sitekey="{{ $settings['recaptcha_site_key'] }}"></div>
                                    @error('g-recaptcha-response') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                @endif

                                <div class="col-12">
                                    <button class="btn btn-primary w-100 py-3 rounded-pill shadow-xl animate-glow" type="submit" data-cta="join-now-form-submit" style="font-size: 1.1rem; font-weight: 800;">
                                        Ask for Course Help <i class="fa fa-check-circle ms-2"></i>
                                    </button>
                                    <p class="text-center mt-3 text-muted small" style="font-size: 0.75rem;">
                                        <i class="fa fa-lock me-1"></i> No spam. Our team will reply as soon as possible.
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- join now End -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const select = document.getElementById('course');
            // Only auto-select from URL if no old() value exists (select is at index 0 / disabled)
            if (select && (select.selectedIndex === 0 || select.value === "")) {
                const urlParams = new URLSearchParams(window.location.search);
                const courseSlug = urlParams.get('course') || urlParams.get('slug');
                if (courseSlug) {
                    select.value = courseSlug;
                }
            }
        });
    </script>
@endsection
