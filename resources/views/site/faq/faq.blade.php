@extends('site.layout.app')

@section('page_title', 'Career Guide & FAQs - GoldenEye Academy')

@section('content')
    @php
        $faqGuidanceUrl = route('join-now', [
            'course' => 'undecided',
            'selected_course' => 'undecided',
            'source_page' => 'faq',
            'source_section' => 'faq-lead-block',
            'inquiry_intent' => 'course_guidance',
        ]);
    @endphp

    {{-- Advanced FAQ Schema for AEO --}}
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "FAQPage",
      "mainEntity": [
        @foreach($faqs as $faq)
        @if(is_object($faq))
        {
          "@@type": "Question",
          "name": @json(strip_tags($faq->question)),
          "acceptedAnswer": {
            "@@type": "Answer",
            "text": @json(strip_tags($faq->answer))
          }
        }{{ !$loop->last ? ',' : '' }}
        @endif
        @endforeach
      ]
    }
    </script>
    
    <!-- Header Start -->
    <div class="container-fluid page-header py-4 mb-4 wow fadeIn" data-wow-delay="0.1s" style="background: linear-gradient(rgba(5, 12, 28, 0.85), rgba(5, 12, 28, 0.85)), url('{{ \App\Support\PublicAsset::url($settings['hero_image'] ?? null, 'site/img/carousel-1.png') }}'); background-size: cover; background-position: center;">
        <div class="container py-4 text-center">
            <h1 class="font-black text-white animated slideInDown uppercase tracking-tighter" style="font-size: clamp(1.6rem, 3.5vw, 2.5rem); line-height: 1;">{{ $settings['faq_header_title'] ?? 'Career Guide' }}</h1>
            <p class="text-brand-gold fw-black uppercase tracking-[0.3em] mb-4 animated fadeIn" style="font-size: 10px;">Common Questions</p>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-center mb-0">
                    <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item text-brand-gold active font-bold uppercase" aria-current="page">FAQ</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- Header End -->

    <!-- FAQs Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp mb-12" data-wow-delay="0.1s">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                    <div style="width: 40px; height: 2px; background: var(--brand-gold);"></div>
                    <span class="text-brand-gold font-black uppercase tracking-[0.3em]" style="font-size: 11px;">Help & Support</span>
                    <div style="width: 40px; height: 2px; background: var(--brand-gold);"></div>
                </div>
                <h2 class="h3 fw-black text-brand-dark uppercase tracking-tighter">Frequently Asked <span class="text-brand-gold">Questions</span></h2>
            </div>

            @if(isset($settings['faq_page_content']) && !empty($settings['faq_page_content']))
                <div class="row justify-content-center mb-12">
                    <div class="col-lg-10">
                        <div class="premium-card p-4 p-lg-6 border border-zinc-100 bg-zinc-50/50 italic text-zinc-600 leading-relaxed border-start-4 border-l-brand-gold rounded-r-2xl shadow-sm" style="font-size: 14px;">
                            @sanitize($settings['faq_page_content'])
                        </div>
                    </div>
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="accordion" id="faqAccordion">
                        @foreach ($faqs as $index => $faq)
                            @if(is_object($faq))
                            <div class="faq-premium-item {{ $index >= 10 ? 'hidden-faq d-none' : '' }} mb-4" id="faq-{{ $faq->id }}">
                                <h2 class="accordion-header">
                                    <button class="faq-premium-btn {{ $index != 0 ? 'collapsed' : '' }} rounded-xl shadow-sm hover:shadow-md transition-all py-3 px-4" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse{{ $index }}" 
                                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                            style="font-size: 14px;">
                                        <span class="font-black tracking-tight">{{ $faq->question }}</span>
                                        <i class="fas fa-plus text-[9px] transition-transform duration-300"></i>
                                    </button>
                                </h2>
                                <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                    <div class="faq-premium-body bg-zinc-50/50 p-4 rounded-b-xl border-x border-b border-zinc-100">
                                        @sanitize($faq->answer)
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    @if (is_countable($faqs) && count($faqs) > 10)
                        <div class="flex justify-center mt-10">
                            <button id="readMoreBtn" onclick="toggleFAQs()" class="btn btn-primary px-5 py-3 rounded-xl font-black uppercase tracking-widest shadow-xl hover:scale-105 transition-all" style="font-size: 11px;">
                                <i class="fa fa-chevron-down me-2"></i> 
                                <span>{{ $settings['faq_btn_text'] ?? 'Explore More FAQs' }}</span>
                            </button>
                        </div>
                    @endif
                    
                    <!-- Lead Capture Block -->
                    <div class="mt-8 p-4 p-lg-6 bg-brand-dark text-white rounded-xl shadow-lg text-center wow fadeInUp relative overflow-hidden border border-brand-gold/20">
                        <div class="absolute -top-10 -right-10 opacity-10">
                            <i class="fas fa-question-circle fa-6x text-brand-gold"></i>
                        </div>
                        <div class="relative z-10">
                            <h4 class="font-black mb-2 uppercase tracking-tighter text-brand-gold">{{ $settings['faq_lead_title'] ?? 'Still Have Questions?' }}</h4>
                            <p class="mb-4 text-white/60 extra-small leading-relaxed">Send a quick question and our team will help you choose the right next step.</p>
                            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                                <a href="{{ $faqGuidanceUrl }}" data-cta="faq-course-guidance" class="btn btn-primary py-2.5 px-5 rounded-lg shadow-lg font-black uppercase tracking-widest hover:scale-105 transition-all" style="font-size: 10px;">Ask for Course Help</a>
                                <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $settings['whatsapp_number'] ?? '9779856058599') }}?text={{ rawurlencode('Hi GoldenEye Academy, I have a quick question about courses.') }}" target="_blank" rel="noopener" data-cta="faq-whatsapp" class="btn btn-outline-brand-gold py-2.5 px-5 rounded-lg font-black uppercase tracking-widest hover:bg-brand-gold hover:text-brand-dark transition-all" style="font-size: 10px;">Message on WhatsApp</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- FAQs End -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Handle Direct Anchor Links (e.g. #faq-5)
            const hash = window.location.hash;
            if (hash && hash.startsWith('#faq-')) {
                const targetFaq = document.querySelector(hash);
                if (targetFaq) {
                    // Show all hidden FAQs first
                    const hiddenFaqs = document.querySelectorAll('.hidden-faq');
                    hiddenFaqs.forEach(faq => faq.classList.remove('d-none'));
                    
                    // Update button state
                    const btn = document.getElementById('readMoreBtn');
                    if (btn) {
                        btn.querySelector('span').textContent = "Show Less";
                        btn.classList.add('active');
                    }

                    // Open the specific accordion item
                    const accordionBtn = targetFaq.querySelector('.faq-premium-btn');
                    const accordionCollapse = targetFaq.querySelector('.accordion-collapse');
                    if (accordionBtn && accordionCollapse) {
                        const bsCollapse = new bootstrap.Collapse(accordionCollapse, { toggle: false });
                        bsCollapse.show();
                        accordionBtn.classList.remove('collapsed');
                    }
                    
                    // Scroll into view
                    targetFaq.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        function toggleFAQs() {
            const hiddenFaqs = document.querySelectorAll('.hidden-faq');
            const btn = document.getElementById('readMoreBtn');
            const btnSpan = btn.querySelector('span');
            
            if (hiddenFaqs.length > 0 && hiddenFaqs[0].classList.contains('d-none')) {
                hiddenFaqs.forEach(faq => {
                    faq.classList.remove('d-none');
                });
                btnSpan.textContent = "Show Less";
                btn.classList.add('active');
            } else {
                hiddenFaqs.forEach(faq => {
                    faq.classList.add('d-none');
                });
                btnSpan.textContent = "Explore More FAQs";
                btn.classList.remove('active');
                document.getElementById('faqAccordion').scrollIntoView({ behavior: 'smooth' });
            }
        }
    </script>
@endsection
