@extends('site.layout.app')

@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Terms and Conditions</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('Home') }}">Home</a></li>
                            <li class="breadcrumb-item text-primary" aria-current="page">Terms and Conditions</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Terms and Conditions Start -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="accordion border-0" id="termsAccordion">
                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button text-dark" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                1. Acceptance of Terms
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#termsAccordion">
                            <div class="px-5 accordion-body text-muted">
                                By accessing and using our website, you agree to comply with these Terms and Conditions. If you do not agree with any part of these terms, please refrain from using our services.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button text-dark collapsed" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                2. Use of Services
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#termsAccordion">
                            <div class="px-5 accordion-body text-muted">
                                Our services are provided for personal and non-commercial use. You agree not to misuse our website, interfere with its functionality, or attempt unauthorized access.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button text-dark collapsed" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                3. User Responsibilities
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#termsAccordion">
                            <div class="px-5 accordion-body text-muted">
                                Users must provide accurate and truthful information when interacting with our website. Any activity that violates applicable laws or infringes on intellectual property rights is strictly prohibited.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading4">
                            <button class="accordion-button text-dark collapsed" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                4. Limitation of Liability
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#termsAccordion">
                            <div class="px-5 accordion-body text-muted">
                                We are not responsible for any direct or indirect damages resulting from the use of our website. We do not guarantee uninterrupted service or error-free content.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading5">
                            <button class="accordion-button text-dark collapsed" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                5. Privacy Policy
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#termsAccordion">
                            <div class="px-5 accordion-body text-muted">
                                Your use of our services is also governed by our Privacy Policy, which details how we collect, use, and protect your personal information.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading6">
                            <button class="accordion-button text-dark collapsed" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                6. Modifications to Terms
                            </button>
                        </h2>
                        <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6" data-bs-parent="#termsAccordion">
                            <div class="px-5 accordion-body text-muted">
                                We reserve the right to modify these terms at any time. Any changes will be posted on this page, and continued use of our services indicates acceptance of the revised terms.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading7">
                            <button class="accordion-button text-dark collapsed" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                7. Governing Law
                            </button>
                        </h2>
                        <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="heading7" data-bs-parent="#termsAccordion">
                            <div class="px-5 accordion-body text-muted">
                                These Terms and Conditions are governed by the laws of Nepal. Any disputes arising from the use of our services shall be resolved in accordance with Nepalese legal proceedings.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Terms and Conditions End -->
@stop