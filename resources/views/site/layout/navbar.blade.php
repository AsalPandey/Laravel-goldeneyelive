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
            data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            
            <div class="nav-item dropdown dropdown-hover">
                <a href="{{ route('courses-all') }}" class="nav-link dropdown-toggle {{ request()->is('courses*') ? 'active' : '' }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Courses</a>
                <div class="dropdown-menu fade-down m-0 shadow-sm border-0">
                    <a href="{{ route('courses-all') }}" class="dropdown-item">All Career Paths</a>
                    @foreach($categories as $category)
                        <a href="{{ route('courses-all', ['category' => $category->slug]) }}" class="dropdown-item">{{ $category->name }}</a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('about') }}" class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
            

            <a href="{{ route('blog') }}" class="nav-item nav-link {{ request()->routeIs('blog') ? 'active' : '' }}">Blog</a>
            <a href="{{ route('contact') }}" class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
        </div>
        <div class="p-3 p-lg-0 d-flex justify-content-center">
            <a href="{{ route('join-now') }}" data-cta="navbar-course-help" class="btn btn-primary site-navbar-cta">
                {{ $settings['hero_cta_1_text'] ?? 'Ask for Course Help' }} <i class="fa fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</nav>
<!-- Navbar End -->
