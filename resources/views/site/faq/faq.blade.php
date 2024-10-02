@extends('site.layout.app')

@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">FAQs</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('Home') }}">Home</a></li>
                            <li class="breadcrumb-item text-primary" aria-current="page">FAQs</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- FAQs Start -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="accordion border-0" id="faqAccordion">
                    @foreach ($faqs as $index => $faq)
                        @if ($index < 10)
                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header" id="heading{{ $index }}">
                                    <button class="accordion-button text-dark {{ $index != 0 ? 'collapsed' : '' }}" 
                                            style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse{{ $index }}" 
                                            aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" 
                                            aria-controls="collapse{{ $index }}">
                                        {{ $faq->question }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                                    <div class="px-5 accordion-body text-muted">
                                        {{ $faq->answer }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <!-- Check if FAQs count exceeds 10 -->
                @if (count($faqs) > 10)
                    <div id="additionalFAQs" style="display: none;">
                        @foreach ($faqs as $index => $faq)
                            @if ($index >= 10)
                                <div class="accordion-item border-0 mb-3">
                                    <h2 class="accordion-header" id="heading{{ $index }}">
                                        <button class="accordion-button text-dark {{ $index != 0 ? 'collapsed' : '' }}" 
                                                style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $index }}" 
                                                aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" 
                                                aria-controls="collapse{{ $index }}">
                                            {{ $faq->question }}
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                                        <div class="px-5 accordion-body text-muted">
                                            {{ $faq->answer }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <button id="readMoreBtn" class="btn btn-warning mt-3" onclick="toggleFAQs()" style="border-radius: 20px; padding: 10px 20px; transition: background-color 0.3s;">
                        Read More
                    </button>
                @endif
            </div>
        </div>
    </div>
    <!-- FAQs End -->

    <script>
        function toggleFAQs() {
            const additionalFAQs = document.getElementById('additionalFAQs');
            const readMoreBtn = document.getElementById('readMoreBtn');
            if (additionalFAQs.style.display === "none") {
                additionalFAQs.style.display = "block";
                readMoreBtn.textContent = "Show Less";
            } else {
                additionalFAQs.style.display = "none";
                readMoreBtn.textContent = "Read More";
            }
        }
    </script>
@stop
