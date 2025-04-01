@extends('site.layout.app')

@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Privacy Policy</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('Home') }}">Home</a></li>
                            <li class="breadcrumb-item text-primary" aria-current="page">Privacy Policy</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Privacy Policy Start -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="accordion border-0" id="privacyAccordion">
                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button text-dark" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                1. Information We Collect
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#privacyAccordion">
                            <div class="px-5 accordion-body text-muted">
                                <strong>Personal Information:</strong> We collect personal details such as full name, email address, phone number, mailing address, and any other information you provide when using our services.
                                <br>
                                <strong>Non-Personal Information:</strong> We automatically collect data such as browser type, device specifications, IP address, cookies, and usage statistics to improve our website experience.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button text-dark collapsed" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                2. How We Use Your Information
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#privacyAccordion">
                            <div class="px-5 accordion-body text-muted">
                                We use the collected information to:
                                <ul>
                                    <li>Provide, operate, and maintain our services.</li>
                                    <li>Respond to inquiries and provide customer support.</li>
                                    <li>Send updates, newsletters, and promotional content if you opt-in.</li>
                                    <li>Analyze user interactions to improve website functionality and security.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button text-dark collapsed" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                3. How We Share Your Information
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#privacyAccordion">
                            <div class="px-5 accordion-body text-muted">
                                We do not sell or rent your personal data. However, we may share your information with:
                                <ul>
                                    <li>Service providers who assist in website operations and maintenance.</li>
                                    <li>Legal authorities when required by law.</li>
                                    <li>Business partners with whom we collaborate to offer services.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading5">
                            <button class="accordion-button text-dark collapsed" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                4. Data Security
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#privacyAccordion">
                            <div class="px-5 accordion-body text-muted">
                                We implement security measures to protect your data from unauthorized access. However, we advise users to take their own precautions, such as avoiding sharing sensitive details online.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading6">
                            <button class="accordion-button text-dark collapsed" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                5. Changes to Privacy Policy
                            </button>
                        </h2>
                        <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6" data-bs-parent="#privacyAccordion">
                            <div class="px-5 accordion-body text-muted">
                                We reserve the right to update this policy at any time. Any changes will be posted on this page, and continued use of our services indicates acceptance of the revised terms.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 mb-3">
                        <h2 class="accordion-header" id="heading7">
                            <button class="accordion-button text-dark collapsed" style="border: none; box-shadow: none; padding: 15px; background-color: transparent;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                6. Location
                            </button>
                        </h2>
                        <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="heading7" data-bs-parent="#privacyAccordion">
                            <div class="px-5 accordion-body text-muted">
                                Our services are primarily operated in Nepal. If you access our platform from outside Nepal, you are responsible for compliance with local laws regarding data privacy.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Privacy Policy End -->
@stop

