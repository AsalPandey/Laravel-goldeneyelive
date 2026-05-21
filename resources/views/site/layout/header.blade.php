<head>
    <meta charset="utf-8">
    <title>@yield('page_title', $settings['meta_title'] ?? 'GoldenEye Academy - Preparing for Global Opportunities Since 2008')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @if(isset($settings['google_analytics_id']) && $settings['google_analytics_id'])
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings['google_analytics_id'] }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ $settings['google_analytics_id'] }}');
    </script>
    @endif

    @include('site.layout.analytics')
    
    {{-- SEO / GEO / AEO Meta Tags --}}
    <meta content="@yield('meta_keywords', $settings['meta_keywords'] ?? 'Web Development, Computer Classes, Language Classes, IELTS, TOEFL, GoldenEye Academy, Pokhara, Nepal')" name="keywords">
    <meta name="description" content="@yield('meta_description', $settings['meta_description'] ?? 'GoldenEye Academy offers expert-led Web Development, Computer, and Language courses in Pokhara, Nepal. IELTS, TOEFL, Korean preparation since 2008.')">
    <meta name="author" content="GoldenEye Academy">
    <meta name="geo.region" content="NP-DH" />
    <meta name="geo.placename" content="Pokhara" />
    <meta name="geo.position" content="{{ ($settings['geo_latitude'] ?? '28.2172') . ';' . ($settings['geo_longitude'] ?? '83.9825') }}" />
    <meta name="ICBM" content="{{ ($settings['geo_latitude'] ?? '28.2172') . ', ' . ($settings['geo_longitude'] ?? '83.9825') }}" />
    <meta name="aeo-summary" content="@yield('aeo_summary', $settings['aeo_summary'] ?? '')">

    @if(!empty($settings['google_search_console_id']))
        <meta name="google-site-verification" content="{{ $settings['google_search_console_id'] }}">
    @endif
    @if(!empty($settings['bing_webmaster_id']))
        <meta name="msvalidate.01" content="{{ $settings['bing_webmaster_id'] }}">
    @endif

    {{-- Individual Page Schema Injection --}}
    @yield('schema_markup')

    {{-- Global Site Schema Injection --}}
    @if(isset($settings['schema_markup']) && $settings['schema_markup'])
        @jsonld($settings['schema_markup'])
    @endif

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', $__env->yieldContent('page_title', $settings['meta_title'] ?? 'GoldenEye Academy - Preparing for Global Opportunities Since 2008'))">
    <meta property="og:description" content="@yield('meta_description', $settings['meta_description'] ?? 'Expert-led Web Development and Language courses in Pokhara.')">
    <meta property="og:image" content="@yield('og_image', \App\Support\PublicAsset::url($settings['hero_image'] ?? null, 'site/img/logo.png'))">

    {{-- Twitter --}}
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('og_title', $__env->yieldContent('page_title', $settings['meta_title'] ?? 'GoldenEye Academy - Preparing for Global Opportunities Since 2008'))">
    <meta property="twitter:description" content="@yield('meta_description', $settings['meta_description'] ?? 'Expert-led Web Development and Language courses in Pokhara.')">
    <meta property="twitter:image" content="@yield('og_image', \App\Support\PublicAsset::url($settings['hero_image'] ?? null, 'site/img/logo.png'))">

    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Favicon -->
    <link href="{{ \App\Support\PublicAsset::url($settings['site_favicon'] ?? ($settings['site_logo'] ?? null), 'site/img/logo.png') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset("site/lib/animate/animate.min.css") }}" rel="stylesheet">
    <link href="{{ asset("site/lib/owlcarousel/assets/owl.carousel.min.css") }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset("site/css/bootstrap.min.css") }}" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <!-- Template Stylesheet -->
    <link href="{{ asset("site/css/style.css") }}" rel="stylesheet">
    @if(isset($settings['recaptcha_site_key']) && !empty($settings['recaptcha_site_key']))
    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #fff; 
            color: #050C1C;
            overflow-x: hidden;
        }

        h1, h2, h3, .font-heading { 
            font-family: 'Outfit', sans-serif; 
            font-weight: 700;
        }

        /* Hardened CTA Psychology */
        .btn-primary {
            background-color: var(--primary-solid) !important;
            border: none !important;
            color: #050C1C !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover) !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
        }
    </style>

    @php
        $speakableSelectors = array_values(array_filter(array_map('trim', explode(',', $settings['speakable_selectors'] ?? ''))));
    @endphp

    {{-- JSON-LD Organization Schema (Global) --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "EducationalOrganization",
        "name": "{{ $settings['site_name'] ?? 'GoldenEye' }} {{ $settings['site_name_suffix'] ?? 'Academy' }}",
        "url": "{{ url('/') }}",
        "logo": "{{ \App\Support\PublicAsset::url($settings['site_logo'] ?? null, 'site/img/logo.png') }}",
        "foundingDate": "2008",
        "description": "{{ $settings['meta_description'] ?? 'GoldenEye Academy helps learners in Pokhara choose study abroad, language, computer, office, and web development courses with guidance before enrollment.' }}",
        "address": {
            "@@type": "PostalAddress",
            "streetAddress": "{{ $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal' }}",
            "addressLocality": "Pokhara",
            "addressRegion": "Gandaki",
            "postalCode": "33700",
            "addressCountry": "NP"
        },
        "geo": {
            "@@type": "GeoCoordinates",
            "latitude": "{{ $settings['geo_latitude'] ?? '28.2172' }}",
            "longitude": "{{ $settings['geo_longitude'] ?? '83.9825' }}"
        },
        "contactPoint": {
            "@@type": "ContactPoint",
            "telephone": "{{ $settings['site_phone'] ?? '+977-61-572599' }}",
            "contactType": "customer service",
            "areaServed": "NP",
            "availableLanguage": ["English", "Nepali"]
        },
        "sameAs": [
            "{{ $settings['facebook_url'] ?? 'https://www.facebook.com/goldeneyeacademy' }}",
            "{{ $settings['instagram_url'] ?? 'https://www.instagram.com/goldeneye.academy/' }}",
            "{{ $settings['linkedin_url'] ?? 'https://www.linkedin.com/company/golden-eye-academy/' }}"
        ]
        @if(!empty($speakableSelectors))
        ,
        "speakable": {
            "@@type": "SpeakableSpecification",
            "cssSelector": @json($speakableSelectors)
        }
        @endif
    }
    </script>
</head>
