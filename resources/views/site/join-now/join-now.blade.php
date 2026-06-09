@extends('site.layout.app')
@section('page_title', 'Ask for Course Help - ' . ($settings['site_name'] ?? 'GoldenEye Academy'))
@section('content')
    @php
        $sourcePage = request('source_page', url()->previous() ?: url()->current());
        $sourceSection = request('source_section', 'join-now-page');
        $inquiryIntent = request('inquiry_intent', 'course_guidance');
        $audienceType = request('audience_type', '');
        $selectedCourseContext = request('selected_course', $selectedCourse ?? 'undecided');
        $selectedCourseValue = old('course', $selectedCourseContext);
        $helpTopics = $helpTopics ?? \App\Http\Requests\Site\JoinNowRequest::helpTopics();
        $selectedHelpTopic = old('help_topic', match (true) {
            str_contains(strtolower($sourceSection), 'parent') => 'Parent inquiry',
            str_contains(strtolower($sourceSection), 'study') => 'IELTS / PTE',
            str_contains(strtolower($sourceSection), 'job'), str_contains(strtolower($sourceSection), 'computer') => 'Computer skills',
            default => 'Choosing a course',
        });
        $optionalHasErrors = $errors->hasAny(['email', 'current_education_level', 'course', 'preferred_batch_time', 'goal', 'queries', 'contactMethod', 'address']);
    @endphp

    <div class="container-fluid page-header course-help-page-header mb-4" style="background: linear-gradient(rgba(5, 12, 28, 0.85), rgba(5, 12, 28, 0.85)), url('{{ \App\Support\PublicAsset::url($settings['hero_image'] ?? null, 'site/img/carousel-1.png') }}'); background-size: cover; background-position: center;">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="font-black text-white animated slideInDown uppercase tracking-tighter" style="font-size: clamp(1.6rem, 3.5vw, 2.5rem); line-height: 1;">Ask for Course Help</h1>
                    <p class="text-brand-gold fw-black uppercase tracking-[0.3em] mb-4 animated fadeIn" style="font-size: 11px;">Find the right course before you enroll</p>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item text-brand-gold active font-bold uppercase" aria-current="page">Course Guidance</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-xxl course-help-section py-4">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-xl-7 col-lg-8 col-md-12 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="text-center mb-3 course-help-intro">
                        <span class="text-brand-gold font-black uppercase tracking-[0.3em]" style="font-size: 11px;">Course Guidance</span>
                        <h2 class="h3 fw-black text-brand-dark mt-2 mb-2">Send your goal. We will guide you before enrollment.</h2>
                        <p class="text-zinc-600 mb-0" style="font-size: 14px;">Start with three required details. Add more only if you want a more specific reply.</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success rounded-xl border-0 shadow-sm mb-4" role="status">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="premium-card p-4 p-lg-5 shadow-lg form-conversational course-help-card">
                        <form action="{{ route('join-now-submit') }}" method="POST" id="joinNow" novalidate data-analytics-form="course-help" data-source-page="{{ $sourcePage }}" data-source-section="{{ $sourceSection }}" data-selected-course="{{ $selectedCourseContext }}" data-audience-type="{{ $audienceType }}" data-inquiry-intent="{{ $inquiryIntent }}">
                            @csrf
                            <input type="hidden" name="lead_source" value="{{ old('lead_source', $sourceSection) }}">
                            <input type="hidden" name="landing_page" value="{{ old('landing_page', $sourcePage) }}">
                            <input type="hidden" name="cta_id" value="{{ old('cta_id', $inquiryIntent) }}">
                            <input type="hidden" name="selected_course" value="{{ old('selected_course', $selectedCourseContext) }}">
                            <input type="hidden" name="source_page" value="{{ old('source_page', $sourcePage) }}">
                            <input type="hidden" name="source_section" value="{{ old('source_section', $sourceSection) }}">
                            <input type="hidden" name="audience_type" value="{{ old('audience_type', $audienceType) }}">
                            <input type="hidden" name="inquiry_intent" value="{{ old('inquiry_intent', $inquiryIntent) }}">

                            <div class="course-help-step mb-4">
                                <span>Step 1</span>
                                <strong>Your main request</strong>
                            </div>

                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control border-0 bg-light @error('full_name') is-invalid @enderror @error('firstName') is-invalid @enderror" id="full_name" name="full_name" placeholder="Your Name" value="{{ old('full_name', trim(old('firstName').' '.old('lastName'))) }}" autocomplete="name" required aria-invalid="{{ $errors->hasAny(['full_name', 'firstName']) ? 'true' : 'false' }}" @if($errors->hasAny(['full_name', 'firstName'])) aria-describedby="fullNameError" @endif>
                                        <label for="full_name">Name</label>
                                        @if($errors->hasAny(['full_name', 'firstName']))
                                            <div id="fullNameError" class="text-danger small mt-1">{{ $errors->first('full_name') ?: $errors->first('firstName') }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="tel" class="form-control border-0 bg-light @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Phone / WhatsApp" value="{{ old('phone') }}" required inputmode="tel" autocomplete="tel" pattern="(?:\+977(?:97|98)[0-9]{8}|(?:97|98)[0-9]{8}|0[0-9]{9})" title="Use a valid Nepal phone number, such as 98XXXXXXXX, 97XXXXXXXX, +97798XXXXXXXX, or 0XXXXXXXXX." aria-describedby="phoneHelp phoneClientError{{ $errors->has('phone') ? ' phoneServerError' : '' }}" aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}">
                                        <label for="phone">Phone / WhatsApp</label>
                                        <div id="phoneHelp" class="form-text small">Use 98XXXXXXXX, 97XXXXXXXX, or +97798XXXXXXXX.</div>
                                        <div id="phoneClientError" class="text-danger small mt-1" aria-live="polite"></div>
                                        @error('phone') <div id="phoneServerError" class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select border-0 bg-light @error('help_topic') is-invalid @enderror" id="help_topic" name="help_topic" required aria-invalid="{{ $errors->has('help_topic') ? 'true' : 'false' }}" @error('help_topic') aria-describedby="helpTopicError" @enderror>
                                            @foreach($helpTopics as $topic)
                                                <option value="{{ $topic }}" {{ old('help_topic', $selectedHelpTopic) === $topic ? 'selected' : '' }}>{{ $topic }}</option>
                                            @endforeach
                                        </select>
                                        <label for="help_topic">What do you need help with?</label>
                                        @error('help_topic') <div id="helpTopicError" class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button class="btn btn-link text-primary small fw-bold text-decoration-none p-0 optional-details-toggle" type="button" aria-controls="extraDetails" aria-expanded="{{ $optionalHasErrors ? 'true' : 'false' }}" data-track-event="add_more_details_click" data-source-page="{{ $sourcePage }}" data-source-section="{{ $sourceSection }}" data-selected-course="{{ $selectedCourseContext }}" data-audience-type="{{ $audienceType }}" data-inquiry-intent="{{ $inquiryIntent }}" data-cta-label="Add more details">
                                        <i class="fa {{ $optionalHasErrors ? 'fa-minus-circle' : 'fa-plus-circle' }} me-1" aria-hidden="true"></i>
                                        <span>{{ $optionalHasErrors ? 'Hide optional details' : 'Add more details' }}</span>
                                    </button>
                                </div>

                                <div class="col-12">
                                    <div id="extraDetails" class="optional-details-panel mt-1 {{ $optionalHasErrors ? '' : 'd-none' }}">
                                        <div class="course-help-step mb-4">
                                            <span>Step 2</span>
                                            <strong>Optional details for better guidance</strong>
                                        </div>

                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control border-0 bg-light" id="email" name="email" placeholder="Email Address" value="{{ old('email') }}" autocomplete="email">
                                                    <label for="email">Email</label>
                                                    @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control border-0 bg-light" id="current_education_level" name="current_education_level" placeholder="Current education level" value="{{ old('current_education_level') }}">
                                                    <label for="current_education_level">Current education level</label>
                                                    @error('current_education_level') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select border-0 bg-light" id="course" name="course">
                                                        <option value="undecided" {{ $selectedCourseValue === 'undecided' || $selectedCourseValue === '' ? 'selected' : '' }}>I need help choosing the right program</option>
                                                        @foreach($courses as $course)
                                                            <option value="{{ $course->slug }}" {{ $selectedCourseValue === $course->slug ? 'selected' : '' }}>{{ $course->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="course">Preferred course</label>
                                                    @error('course') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select border-0 bg-light" id="preferred_batch_time" name="preferred_batch_time">
                                                        <option value="" {{ old('preferred_batch_time') ? '' : 'selected' }}>Not sure yet</option>
                                                        @foreach(['Morning', 'Day', 'Evening', 'Weekend', 'Need advice'] as $time)
                                                            <option value="{{ $time }}" {{ old('preferred_batch_time') === $time ? 'selected' : '' }}>{{ $time }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="preferred_batch_time">Preferred batch time</label>
                                                    @error('preferred_batch_time') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select class="form-select border-0 bg-light" id="contactMethod" name="contactMethod">
                                                        @foreach(['Phone Call', 'WhatsApp', 'Email'] as $method)
                                                            <option value="{{ $method }}" {{ old('contactMethod', 'Phone Call') === $method ? 'selected' : '' }}>{{ $method }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="contactMethod">Preferred contact method</label>
                                                    @error('contactMethod') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control border-0 bg-light" id="address" name="address" placeholder="City/Area" value="{{ old('address') }}">
                                                    <label for="address">City / Area</label>
                                                    @error('address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control border-0 bg-light" name="goal" id="goal" placeholder="Goal" style="height: 90px">{{ old('goal') }}</textarea>
                                                    <label for="goal">Goal</label>
                                                    @error('goal') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control border-0 bg-light" name="queries" id="queries" placeholder="Message" style="height: 100px">{{ old('queries') }}</textarea>
                                                    <label for="queries">Message</label>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggle = document.querySelector('.optional-details-toggle');
            const details = document.getElementById('extraDetails');
            if (toggle && details) {
                const label = toggle.querySelector('span');
                const icon = toggle.querySelector('i');

                const setExpanded = (expanded) => {
                    toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                    details.classList.toggle('d-none', !expanded);

                    if (label) {
                        label.textContent = expanded ? 'Hide optional details' : 'Add more details';
                    }

                    if (icon) {
                        icon.classList.toggle('fa-plus-circle', !expanded);
                        icon.classList.toggle('fa-minus-circle', expanded);
                    }
                };

                toggle.addEventListener('click', () => {
                    setExpanded(details.classList.contains('d-none'));
                });
            }

            const phone = document.getElementById('phone');
            const phoneError = document.getElementById('phoneClientError');
            const phoneRegex = /^(?:\+977(?:97|98)\d{8}|(?:97|98)\d{8}|0\d{9})$/;
            const fakeNumbers = ['9800000000', '9812345678', '1234567890', '0123456789'];

            if (phone) {
                const validatePhone = () => {
                    const value = phone.value.trim();
                    const digits = value.replace(/\D+/g, '');
                    const localDigits = digits.startsWith('977') ? digits.slice(3) : digits;
                    let message = '';

                    if (value !== '' && !phoneRegex.test(value)) {
                        message = 'Enter a valid Nepal number, such as 98XXXXXXXX or +97798XXXXXXXX.';
                    } else if (value !== '' && (/^(\d)\1+$/.test(localDigits) || fakeNumbers.includes(localDigits))) {
                        message = 'Enter a real phone or WhatsApp number.';
                    }

                    phone.setCustomValidity(message);
                    phone.setAttribute('aria-invalid', message ? 'true' : 'false');
                    phone.classList.toggle('is-invalid', message !== '');
                    if (phoneError) {
                        phoneError.textContent = message;
                    }
                };

                phone.addEventListener('input', validatePhone);
                phone.addEventListener('blur', validatePhone);
                phone.form?.addEventListener('submit', validatePhone);
            }
        });
    </script>
@endsection
