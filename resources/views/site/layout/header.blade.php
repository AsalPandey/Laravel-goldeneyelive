@php
    $brandName = \App\Support\StructuredData::siteName($settings ?? []);
    $defaultTitle = $settings['meta_title'] ?? 'Golden Eye Academy | Courses and Classes in Pokhara';
    $requestedTitle = trim($__env->yieldContent('page_title', $defaultTitle));
    $pageTitle = $requestedTitle !== '' ? $requestedTitle : $defaultTitle;
    $defaultDescription = $settings['meta_description'] ?? 'Golden Eye Academy offers IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes in Pokhara, Nepal.';
    $requestedDescription = trim($__env->yieldContent('meta_description', $defaultDescription));
    $pageDescription = $requestedDescription !== '' ? $requestedDescription : $defaultDescription;
    $requestedCanonical = trim($__env->yieldContent('canonical_url', ''));
    $canonicalUrl = $requestedCanonical !== ''
        ? \App\Support\CanonicalUrl::normalize($requestedCanonical)
        : \App\Support\CanonicalUrl::current();
    $robotsDirective = trim($__env->yieldContent('robots', ''));
    $ogTitle = trim($__env->yieldContent('og_title', $pageTitle)) ?: $pageTitle;
    $ogImage = trim($__env->yieldContent('og_image', \App\Support\PublicAsset::canonicalUrl($settings['hero_image'] ?? null, 'site/img/logo.png')));
@endphp
<head>
    <meta charset="utf-8">
    <title>{{ $pageTitle }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if($robotsDirective !== '')
        <meta name="robots" content="{{ $robotsDirective }}">
    @endif
    
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
    <meta content="@yield('meta_keywords', $settings['meta_keywords'] ?? 'Golden Eye Academy, IELTS Pokhara, PTE Pokhara, Computer Classes, Language Classes, IT Classes, Pokhara, Nepal')" name="keywords">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="author" content="Golden Eye Academy">
    <meta name="geo.placename" content="Pokhara" />
    @if(filled($settings['geo_latitude'] ?? null) && filled($settings['geo_longitude'] ?? null))
        <meta name="geo.position" content="{{ $settings['geo_latitude'].';'.$settings['geo_longitude'] }}" />
        <meta name="ICBM" content="{{ $settings['geo_latitude'].', '.$settings['geo_longitude'] }}" />
    @endif
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
    @jsonld(json_encode(\App\Support\StructuredData::siteGraph($settings ?? [], $pageTitle, $pageDescription, $canonicalUrl)))

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="{{ $brandName }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $canonicalUrl }}">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <link rel="canonical" href="{{ $canonicalUrl }}">

    <!-- Favicon -->
    <link href="{{ \App\Support\PublicAsset::url($settings['site_favicon'] ?? ($settings['site_logo'] ?? null), 'site/img/logo.png') }}" rel="icon">

    @yield('preload_assets')

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://code.jquery.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet" integrity="sha384-CK2SzKma4jA5H/MXDUU7i1TqZlCFaD4T01vtyDFvPlD97JQyS+IsSh1nI2EFbpyk" crossorigin="anonymous">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset("site/lib/animate/animate.min.css") }}" rel="stylesheet">
    <link href="{{ asset("site/lib/owlcarousel/assets/owl.carousel.min.css") }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset("site/css/bootstrap.min.css") }}" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <!-- Template Stylesheet -->
    <link href="{{ asset("site/css/style.css") }}" rel="stylesheet">
    @if(\App\Support\Recaptcha::enabled())
    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #fff; 
            color: #050C1C;
            overflow-x: clip;
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

</head>
