@extends('site.layout.app')
@section('page_title', 'Contact GoldenEye Academy - ' . ($settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal'))
@section('meta_description', 'Contact GoldenEye Academy for questions about courses, enrollment, and course guidance. Visit us at ' . ($settings['site_address'] ?? 'Srijana Chowk, Pokhara') . ' or call ' . ($settings['site_phone'] ?? '061-572599') . '.')
@section('content')
    <style>
        .contact-field > label {
            display: block;
            margin-bottom: .5rem;
            color: #64748b;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .12em;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .contact-field .form-control,
        .contact-field .form-select {
            border-color: #cbd5e1 !important;
            min-height: 56px;
        }
    </style>

    <!-- Header Start -->
    <div class="container-fluid page-header py-4 mb-4 wow fadeIn" data-wow-delay="0.1s" style="background: linear-gradient(rgba(5, 12, 28, 0.85), rgba(5, 12, 28, 0.85)), url('{{ \App\Support\PublicAsset::url($settings['hero_image'] ?? null, 'site/img/carousel-1.png') }}'); background-size: cover; background-position: center;">
        <div class="container py-4 text-center">
            <h1 class="font-black text-white animated slideInDown uppercase tracking-tighter" style="font-size: clamp(1.6rem, 3.5vw, 2.5rem); line-height: 1;">{{ $settings['contact_header_title'] ?? 'Message GoldenEye Academy' }}</h1>
            <p class="text-brand-gold fw-black uppercase tracking-[0.3em] mb-4 animated fadeIn" style="font-size: 10px;">Course, fee, and timing questions</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item text-brand-gold active font-bold uppercase" aria-current="page">Contact</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Header End -->

    <!-- Contact Start -->
    <div class="container-xxl py-4">
        <div class="container">
            <div class="text-center wow fadeInUp mb-10" data-wow-delay="0.1s">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                    <div style="width: 40px; height: 2px; background: var(--brand-gold);"></div>
                    <span class="text-brand-gold font-black uppercase tracking-[0.3em]" style="font-size: 11px;">Course questions</span>
                    <div style="width: 40px; height: 2px; background: var(--brand-gold);"></div>
                </div>
                <h2 class="h3 fw-black text-brand-dark uppercase tracking-tighter">Ask a Quick <span class="text-brand-gold">Course Question</span></h2>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-lg-5 col-md-12 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="premium-card p-5 p-lg-6 h-100 border border-zinc-100 shadow-lg rounded-xl relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 opacity-5">
                            <i class="fa fa-map-marked-alt fa-10x"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div style="width: 30px; height: 2px; background: var(--brand-gold);"></div>
                                <span class="text-brand-gold font-black uppercase tracking-[0.3em]" style="font-size: 10px;">Visit or call</span>
                            </div>
                            <h5 class="mb-4 font-black uppercase tracking-tight text-brand-dark">Visit Our Campus</h5>
                            <div class="mb-6 text-zinc-600 leading-relaxed italic border-start-4 border-brand-gold/20 ps-4 extra-small">
                                @if(isset($settings['contact_page_content']) && !empty($settings['contact_page_content']))
                                    @sanitize($settings['contact_page_content'])
                                @else
                                    Not sure which course fits your goal? Visit us or send a quick question. We will explain course fit, fees, timing, and next steps before enrollment.
                                @endif
                            </div>
                            
                            <div class="space-y-8">
                                <div class="d-flex align-items-center gap-3 group">
                                    <div class="w-12 h-12 rounded-xl bg-brand-dark text-brand-gold flex items-center justify-center flex-shrink-0 transition-all group-hover:scale-110 shadow-lg">
                                        <i class="fa fa-map-marker-alt fs-6"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-zinc-400 font-black uppercase tracking-widest mb-1" style="font-size: 9px;">Academy Headquarters</h6>
                                        <p class="mb-0 font-black text-brand-dark">{{ $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal' }}</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-3 group">
                                    <div class="w-14 h-14 rounded-2xl bg-brand-dark text-brand-gold flex items-center justify-center flex-shrink-0 transition-all group-hover:scale-110 shadow-lg">
                                        <i class="fa fa-phone-alt fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-zinc-400 font-black uppercase tracking-widest mb-1" style="font-size: 9px;">Phone</h6>
                                        @php
                                            $contactPhone = $settings['site_phone'] ?? '061-572599, 9856058599';
                                            $contactPhoneHref = preg_replace('/[^0-9+]/', '', explode(',', $contactPhone)[0]);
                                        @endphp
                                        <p class="mb-0 font-black text-brand-dark">
                                            <a href="tel:{{ $contactPhoneHref }}" class="text-brand-dark text-decoration-none" data-source-page="contact" data-source-section="contact-phone" data-cta-label="Phone">{{ $contactPhone }}</a>
                                        </p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-3 group">
                                    <div class="w-14 h-14 rounded-2xl bg-brand-dark text-brand-gold flex items-center justify-center flex-shrink-0 transition-all group-hover:scale-110 shadow-lg">
                                        <i class="fa fa-envelope fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-zinc-400 font-black uppercase tracking-widest mb-1" style="font-size: 9px;">Email</h6>
                                        <p class="mb-0 font-black text-brand-dark">{{ $settings['site_email'] ?? 'info@goldeneye.edu.np' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 col-md-12 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="premium-card p-5 p-lg-6 shadow-lg rounded-xl bg-white border border-zinc-100">
                        <h5 class="font-black uppercase tracking-tight mb-5 text-brand-dark">Send Your <span class="text-brand-gold">Course Question</span></h5>
                        <form action="{{ route('contact-submit') }}" method="POST" class="form-conversational">
                            @csrf
                            <input type="hidden" name="lead_source" value="contact_page">
                            <input type="hidden" name="landing_page" value="{{ url()->current() }}">
                            <input type="hidden" name="cta_id" value="contact-form">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="contact-field">
                                        <label for="name">Full Name</label>
                                        <input type="text" class="form-control bg-white border rounded-xl px-4 py-3 @error('name') is-invalid @enderror" name="name" id="name" placeholder="Your full name" value="{{ old('name') }}" required autocomplete="name" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}" @error('name') aria-describedby="nameError" @enderror>
                                        @error('name') <div id="nameError" class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="contact-field">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" class="form-control bg-white border rounded-xl px-4 py-3 @error('phone') is-invalid @enderror" name="phone" id="phone" placeholder="98XXXXXXXX" value="{{ old('phone') }}" required inputmode="tel" autocomplete="tel" pattern="(?:\+977(?:97|98)[0-9]{8}|(?:97|98)[0-9]{8}|0[0-9]{9})" title="Use a valid Nepal phone number, such as 98XXXXXXXX, 97XXXXXXXX, +97798XXXXXXXX, or 0XXXXXXXXX." aria-describedby="phoneHelp{{ $errors->has('phone') ? ' phoneError' : '' }}" aria-invalid="{{ $errors->has('phone') ? 'true' : 'false' }}">
                                        <div id="phoneHelp" class="form-text small">Use 98XXXXXXXX, 97XXXXXXXX, or +97798XXXXXXXX.</div>
                                        @error('phone') <div id="phoneError" class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="contact-field">
                                        <label for="subject">Interested Pathway</label>
                                        <select class="form-select bg-white border rounded-xl px-4 py-3 font-bold @error('subject') is-invalid @enderror" name="subject" id="subject" required aria-invalid="{{ $errors->has('subject') ? 'true' : 'false' }}" @error('subject') aria-describedby="subjectError" @enderror>
                                            <option value="" disabled selected>Interested Pathway...</option>
                                            @forelse($categories as $cat)
                                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                            @empty
                                                <option value="IELTS / PTE / Study Abroad">IELTS / PTE / Study Abroad</option>
                                                <option value="Computer Skills / Office Package">Computer Skills / Office Package</option>
                                                <option value="Web Development / IT Career">Web Development / IT Career</option>
                                                <option value="Language Classes">Language Classes</option>
                                            @endforelse
                                            <option value="General Inquiry">General Inquiry</option>
                                        </select>
                                        @error('subject') <div id="subjectError" class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="contact-field">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control bg-white border rounded-xl px-4 py-3 @error('email') is-invalid @enderror" name="email" id="email" placeholder="you@example.com" value="{{ old('email') }}" required autocomplete="email" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" @error('email') aria-describedby="emailError" @enderror>
                                        @error('email') <div id="emailError" class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="contact-field">
                                        <label for="message">What goal or course question can we help with?</label>
                                        <textarea class="form-control bg-white border rounded-xl px-4 py-3 @error('message') is-invalid @enderror" name="message" id="message" placeholder="Tell us your goal, preferred course, or timing question." style="height: 140px" required aria-invalid="{{ $errors->has('message') ? 'true' : 'false' }}" @error('message') aria-describedby="messageError" @enderror>{{ old('message') }}</textarea>
                                        @error('message') <div id="messageError" class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12 pt-2">
                                    <button class="btn btn-primary w-100 py-3 rounded-xl shadow-lg animate-glow font-black uppercase tracking-widest hover:scale-105 transition-all" style="font-size: 11px;" type="submit">
                                        Ask for Course Help <i class="fa fa-paper-plane ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Map Section -->
                <div class="col-12 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="premium-card p-2 h-100 shadow-lg rounded-xl overflow-hidden" style="min-height: 300px;">
                        @if(isset($settings['google_maps_embed']) && !empty($settings['google_maps_embed']))
                            <iframe class="w-100 h-100" src="{{ $settings['google_maps_embed'] }}" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
                        @else
                            <iframe class="w-100 h-100" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1687.7949561082255!2d83.98105790540313!3d28.212102922025796!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399595ac8d5df7a3%3A0x1971ef66310b97ae!2sGolden%20Eye%20Academy!5e0!3m2!1sen!2snp!4v1725470686886!5m2!1sen!2snp" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->
@endsection
