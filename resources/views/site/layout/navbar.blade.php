@php
    $navCourseRoutes = [
        [
            'label' => 'All Courses',
            'url' => route('courses-all'),
        ],
        [
            'label' => 'IELTS / PTE',
            'url' => route('courses-all', ['search' => 'IELTS PTE']),
        ],
        [
            'label' => 'Japanese / Korean',
            'url' => route('courses-all', ['search' => 'Japanese Korean']),
        ],
        [
            'label' => 'Computer Skills',
            'url' => route('courses-all', ['search' => 'Computer Office Skills']),
        ],
        [
            'label' => 'Web Development',
            'url' => route('courses-all', ['search' => 'Web Development']),
        ],
    ];

    $headerHelpUrl = route('join-now', [
        'course' => 'undecided',
        'selected_course' => 'undecided',
        'source_page' => 'navigation',
        'source_section' => 'header-cta',
        'inquiry_intent' => 'course_help',
    ]);
    $headerHelpLabel = trim((string) ($settings['hero_cta_1_text'] ?? $settings['hero_cta_text'] ?? 'Ask for Course Help')) ?: 'Ask for Course Help';
    $headerHelpLabel = strtolower($headerHelpLabel) === 'ask for course guidance' ? 'Ask for Course Help' : $headerHelpLabel;
    $navWhatsappNumber = $settings['whatsapp_number'] ?? '9779856058599';
    $navWhatsappCleanNumber = str_replace(['+', ' ', '-'], '', $navWhatsappNumber);
    $navWhatsappMessage = rawurlencode($settings['whatsapp_prefill_message'] ?? 'Hi GoldenEye Academy, I need course guidance.');
    $navWhatsappLabel = trim((string) ($settings['whatsapp_cta_text'] ?? $settings['whatsapp_button_text'] ?? 'Message on WhatsApp')) ?: 'Message on WhatsApp';
    $navWhatsappLabel = $navWhatsappLabel === 'Message us on WhatsApp' ? 'Message on WhatsApp' : $navWhatsappLabel;
@endphp

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top p-0 site-navbar">
    <a href="{{ route('home') }}" class="navbar-brand site-navbar-brand d-flex align-items-center px-3 px-lg-4 text-decoration-none">
        <img class="img-logo me-2 object-contain" src="{{ \App\Support\PublicAsset::url($settings['site_logo'] ?? null, 'site/img/logo.png') }}" onerror="this.src='{{ asset('site/img/logo.png') }}'" alt="{{ $settings['site_name'] ?? 'GoldenEye' }}" style="height: 55px; width: auto;">
        <h4 class="m-0 text-brand-gold font-black tracking-tighter d-flex align-items-center site-brand-wordmark">
            {{ $settings['site_name'] ?? 'GoldenEye' }} 
            <span class="site-brand-pill">{{ $settings['site_name_suffix'] ?? 'Academy' }}</span>
        </h4>
    </a>
    <button type="button" class="navbar-toggler me-4 d-flex align-items-center d-lg-none p-2 rounded-xl shadow-sm transition-all active:scale-95 border-brand-gold"
            data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation menu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav site-desktop-nav ms-auto p-4 p-lg-0 d-none d-lg-flex">
            <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>

            <div class="nav-item dropdown dropdown-hover">
                <a href="{{ route('courses-all') }}" class="nav-link dropdown-toggle {{ request()->routeIs('courses', 'courses-all', 'course-category', 'course-catagory', 'courses-detail', 'join-now') ? 'active' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Courses</a>
                <div class="dropdown-menu fade-down m-0 shadow-sm border-0">
                    @foreach($navCourseRoutes as $courseRoute)
                        <a href="{{ $courseRoute['url'] }}" class="dropdown-item">{{ $courseRoute['label'] }}</a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('about') }}" class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
            <a href="{{ route('blog') }}" class="nav-item nav-link {{ request()->routeIs('blog') ? 'active' : '' }}">Blog</a>
            <a href="{{ route('contact') }}" class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
        </div>

        <div class="navbar-nav site-mobile-nav p-4 d-lg-none">
            <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('courses-all') }}" class="nav-item nav-link {{ request()->routeIs('courses', 'courses-all', 'course-category', 'course-catagory', 'courses-detail') ? 'active' : '' }}">Courses</a>
            <a href="{{ route('courses-all', ['search' => 'study abroad IELTS PTE']) }}" class="nav-item nav-link">Study Abroad</a>
            <a href="{{ route('courses-all', ['search' => 'Computer Office Skills']) }}" class="nav-item nav-link">Computer Skills</a>
            <a href="{{ route('courses-all', ['search' => 'Japanese Korean Language']) }}" class="nav-item nav-link">Languages</a>
            <a href="{{ route('about') }}" class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
            <a href="{{ route('contact') }}" class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
            @if($navWhatsappNumber)
                <a href="https://wa.me/{{ $navWhatsappCleanNumber }}?text={{ $navWhatsappMessage }}" target="_blank" rel="noopener" class="nav-item nav-link site-mobile-whatsapp" data-cta="mobile-menu-whatsapp">
                    <i class="fab fa-whatsapp me-2"></i>{{ $navWhatsappLabel }}
                </a>
            @endif
        </div>

        <div class="site-navbar-actions p-3 p-lg-0 d-none d-lg-flex justify-content-center">
            <a href="{{ $headerHelpUrl }}" data-cta="navbar-course-help" data-cta-label="{{ $headerHelpLabel }}" class="btn btn-primary site-navbar-cta">
                {{ $headerHelpLabel }} <i class="fa fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</nav>
<!-- Navbar End -->
