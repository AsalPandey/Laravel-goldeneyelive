@extends('site.layout.app')
@section('page_title', ($settings['privacy_header_title'] ?? 'Privacy Policy') . ' - ' . \App\Support\StructuredData::siteName($settings ?? []))
@section('meta_description', $settings['meta_description'] ?? 'Learn how ' . \App\Support\StructuredData::siteName($settings ?? []) . ' protects your privacy and personal data.')
@section('content')
    <!-- Header Start -->
    <div class="container-fluid page-header">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="h2 text-white animated slideInDown font-black uppercase tracking-tighter">{{ $settings['privacy_header_title'] ?? 'Privacy Policy' }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a class="text-white opacity-50" href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item text-brand-gold active font-black uppercase tracking-tighter" aria-current="page">{{ $settings['privacy_header_title'] ?? 'Privacy' }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Privacy Policy Start -->
    <div class="container my-4">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                @if(isset($settings['privacy_policy_content']) && !empty(trim($settings['privacy_policy_content'])))
                    <div class="bg-white p-4 rounded-xl border border-zinc-100 shadow-sm leading-relaxed text-zinc-600 extra-small">
                        @sanitize($settings['privacy_policy_content'])
                    </div>
                @else
                    <div class="accordion border-0" id="privacyAccordion">
                        <div class="accordion-item border border-zinc-100 mb-3 rounded-xl overflow-hidden">
                            <h2 class="accordion-header" id="heading1">
                                <button class="accordion-button text-brand-dark font-black uppercase tracking-tight" style="border: none; box-shadow: none; padding: 12px 20px; background-color: var(--zinc-50); font-size: 13px;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                    1. Information We Collect
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#privacyAccordion">
                                <div class="px-4 py-3 accordion-body text-zinc-600 extra-small">
                                    <strong>Personal Information:</strong> We collect personal details such as full name, email address, phone number, mailing address, and any other information you provide when using our services.
                                    <br>
                                    <strong>Non-Personal Information:</strong> We automatically collect data such as browser type, device specifications, IP address, cookies, and usage statistics to improve our website experience.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border border-zinc-100 mb-3 rounded-xl overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button text-brand-dark font-black uppercase tracking-tight collapsed" style="border: none; box-shadow: none; padding: 12px 20px; background-color: var(--zinc-50); font-size: 13px;" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                    2. How We Use Your Information
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#privacyAccordion">
                                <div class="px-4 py-3 accordion-body text-zinc-600 extra-small">
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
                                <div class="px-4 py-3 accordion-body text-zinc-600 extra-small">
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
                                <div class="px-4 py-3 accordion-body text-zinc-600 extra-small">
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
                                <div class="px-4 py-3 accordion-body text-zinc-600 extra-small">
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
                                <div class="px-4 py-3 accordion-body text-zinc-600 extra-small">
                                    Our services are primarily operated in Nepal. If you access our platform from outside Nepal, you are responsible for compliance with local laws regarding data privacy.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Privacy Policy End -->
@endsection

