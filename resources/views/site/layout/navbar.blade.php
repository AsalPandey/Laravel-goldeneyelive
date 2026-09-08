@php
    $navCourseRoutes = collect($categories ?? [])
        ->filter(fn ($category) => filled(data_get($category, 'slug'))
            && (int) data_get($category, 'courses_count', 1) > 0)
        ->map(fn ($category) => [
            'label' => data_get($category, 'name'),
            'url' => route('courses-all', ['category' => data_get($category, 'slug')]),
        ])
        ->values();

    $primaryNavItems = [
        [
            'label' => 'Home',
            'url' => route('home'),
            'active_routes' => ['home'],
        ],
        [
            'label' => 'Courses',
            'url' => route('courses-all'),
            'active_routes' => ['courses', 'courses-all', 'course-category', 'course-catagory', 'courses-detail', 'join-now'],
            'course_menu' => true,
        ],
        [
            'label' => 'About',
            'url' => route('about'),
            'active_routes' => ['about', 'about-detail'],
        ],
        [
            'label' => 'Blog',
            'url' => route('blog'),
            'active_routes' => ['blog', 'blog-detail'],
        ],
        [
            'label' => 'FAQ',
            'url' => route('faq'),
            'active_routes' => ['faq'],
        ],
        [
            'label' => 'Contact',
            'url' => route('contact'),
            'active_routes' => ['contact'],
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
    $displaySiteName = \App\Support\StructuredData::siteName($settings ?? []);
    $displaySiteNameParts = preg_split('/\s+/', $displaySiteName) ?: [];
    $displaySiteSuffix = array_pop($displaySiteNameParts) ?: '';
    $displaySitePrefix = implode(' ', $displaySiteNameParts) ?: $displaySiteName;
    $navWhatsappMessage = rawurlencode($settings['whatsapp_prefill_message'] ?? 'Hi Golden Eye Academy, I have a question about classes and enrollment.');
    $navWhatsappLabel = trim((string) ($settings['whatsapp_cta_text'] ?? $settings['whatsapp_button_text'] ?? 'Message on WhatsApp')) ?: 'Message on WhatsApp';
    $navWhatsappLabel = $navWhatsappLabel === 'Message us on WhatsApp' ? 'Message on WhatsApp' : $navWhatsappLabel;
@endphp

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top p-0 site-navbar" aria-label="Primary navigation">
    <a href="{{ route('home') }}" class="navbar-brand site-navbar-brand d-flex align-items-center px-3 px-lg-4 text-decoration-none">
        <img class="img-logo me-2 object-contain" src="{{ \App\Support\PublicAsset::url($settings['site_logo'] ?? null, 'site/img/logo.png') }}" onerror="this.src='{{ asset('site/img/logo.png') }}'" alt="{{ $displaySiteName }}" decoding="async" width="55" height="55" style="height: 55px; width: auto;">
        <span class="m-0 text-brand-gold font-black tracking-tighter d-flex align-items-center site-brand-wordmark">
            {{ $displaySitePrefix }}
            @if($displaySiteSuffix !== '')
                <span class="site-brand-pill">{{ $displaySiteSuffix }}</span>
            @endif
        </span>
    </a>
    <button id="primaryNavigationToggle" type="button" class="navbar-toggler me-4 d-flex align-items-center d-lg-none p-2 rounded-xl shadow-sm transition-all active:scale-95 border-brand-gold"
            data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Open navigation menu">
        <span class="navbar-toggler-icon" aria-hidden="true"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav site-desktop-nav ms-auto p-4 p-lg-0 d-none d-lg-flex">
            @foreach($primaryNavItems as $navItem)
                @if($navItem['course_menu'] ?? false)
                    <div class="nav-item dropdown dropdown-hover">
                        <a href="{{ $navItem['url'] }}" class="nav-link dropdown-toggle {{ request()->routeIs(...$navItem['active_routes']) ? 'active' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">{{ $navItem['label'] }}</a>
                        <div class="dropdown-menu fade-down m-0 shadow-sm border-0">
                            <a href="{{ route('courses-all') }}" class="dropdown-item">All Courses</a>
                            @foreach($navCourseRoutes as $courseRoute)
                                <a href="{{ $courseRoute['url'] }}" class="dropdown-item">{{ $courseRoute['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $navItem['url'] }}" class="nav-item nav-link {{ request()->routeIs(...$navItem['active_routes']) ? 'active' : '' }}">{{ $navItem['label'] }}</a>
                @endif
            @endforeach
        </div>

        <div class="navbar-nav site-mobile-nav p-4 d-lg-none" data-mobile-navigation>
            @foreach($primaryNavItems as $navItem)
                @if($navItem['course_menu'] ?? false)
                    <details class="site-mobile-course-menu" {{ request()->routeIs(...$navItem['active_routes']) ? 'open' : '' }}>
                        <summary class="nav-item nav-link {{ request()->routeIs(...$navItem['active_routes']) ? 'active' : '' }}">
                            <span>{{ $navItem['label'] }}</span>
                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </summary>
                        <div class="site-mobile-course-options">
                            <a href="{{ $navItem['url'] }}" class="nav-item nav-link site-mobile-course-link">All Courses</a>
                            @foreach($navCourseRoutes as $courseRoute)
                                <a href="{{ $courseRoute['url'] }}" class="nav-item nav-link site-mobile-course-link">{{ $courseRoute['label'] }}</a>
                            @endforeach
                        </div>
                    </details>
                @else
                    <a href="{{ $navItem['url'] }}" class="nav-item nav-link {{ request()->routeIs(...$navItem['active_routes']) ? 'active' : '' }}">{{ $navItem['label'] }}</a>
                @endif
            @endforeach
            <a href="{{ $headerHelpUrl }}" class="nav-item nav-link site-mobile-primary" data-cta="mobile-menu-course-help">
                {{ $headerHelpLabel }}
            </a>
            @if($navWhatsappNumber)
                <a href="https://wa.me/{{ $navWhatsappCleanNumber }}?text={{ $navWhatsappMessage }}" target="_blank" rel="noopener" class="nav-item nav-link site-mobile-whatsapp" data-cta="mobile-menu-whatsapp">
                    <i class="fab fa-whatsapp me-2" aria-hidden="true"></i>{{ $navWhatsappLabel }}
                </a>
            @endif
        </div>

        <div class="site-navbar-actions p-3 p-lg-0 d-none d-lg-flex justify-content-center">
            <a href="{{ $headerHelpUrl }}" data-cta="navbar-course-help" data-cta-label="{{ $headerHelpLabel }}" class="btn btn-primary site-navbar-cta">
                {{ $headerHelpLabel }} <i class="fa fa-arrow-right ms-2" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</nav>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navigation = document.getElementById('navbarCollapse');
        const toggle = document.getElementById('primaryNavigationToggle');

        if (!navigation || !toggle) {
            return;
        }

        const updateNavigationState = function (isOpen, shouldMoveFocus = true) {
            toggle.setAttribute('aria-expanded', String(isOpen));
            toggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');

            if (isOpen && shouldMoveFocus) {
                navigation.querySelector('[data-mobile-navigation] a')?.focus();
            } else if (!isOpen && navigation.contains(document.activeElement)) {
                toggle.focus();
            }
        };

        navigation.addEventListener('shown.bs.collapse', function () {
            updateNavigationState(true);
        });

        navigation.addEventListener('hidden.bs.collapse', function () {
            updateNavigationState(false);
        });

        toggle.addEventListener('click', function (event) {
            if (window.bootstrap) {
                return;
            }

            event.preventDefault();
            const isOpen = navigation.classList.toggle('show');
            updateNavigationState(isOpen);
        });

        document.addEventListener('pointerdown', function (event) {
            if (!window.matchMedia('(max-width: 991.98px)').matches
                || !navigation.classList.contains('show')
                || navigation.contains(event.target)
                || toggle.contains(event.target)) {
                return;
            }

            if (window.bootstrap?.Collapse) {
                bootstrap.Collapse.getOrCreateInstance(navigation, { toggle: false }).hide();
            } else {
                navigation.classList.remove('show');
                updateNavigationState(false);
            }
        });

        navigation.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            event.preventDefault();

            if (window.bootstrap) {
                bootstrap.Collapse.getOrCreateInstance(navigation).hide();
            } else {
                navigation.classList.remove('show');
                updateNavigationState(false);
            }

            toggle.focus();
        });
    });
</script>
<!-- Navbar End -->
