<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="{{ route('Home') }}" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <h4 class="m-0 text-primary">
            <img class="img-logo px-3" src="{{ asset('site/img/logo.png') }}" alt="">
            GoldenEye <span class="logo-text-2">Academy</span>
        </h4>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="{{ route('Home') }}" class="nav-item nav-link {{ Route::currentRouteName() == 'Home' ? 'active' : '' }}">Home</a>
            <a href="{{ route('About') }}" class="nav-item nav-link {{ str_contains(request()->url(), 'about') ? 'active' : '' }}">About</a>
            <a href="{{ route('Courses') }}" class="nav-item nav-link {{ str_contains(request()->url(), 'courses') ? 'active' : '' }}">Courses</a>
             {{-- <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                <div class="dropdown-menu fade-down m-0">
                    <a href="team.html" class="dropdown-item">Our Team</a>
                    <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                    <a href="404.html" class="dropdown-item">404 Page</a>
                </div>
            </div> --}}
            <a href="{{ route('Contact') }}" class="nav-item nav-link {{ str_contains(request()->url(), 'contact') ? 'active' : '' }}">Contact</a>
        </div>
        <a href="{{ route('JoinNow') }}" class="btn btn-primary py-4 px-lg-5 d-none d-lg-block">Join Now<i class="fa fa-arrow-right ms-3"></i></a>
    </div>
</nav>
<!-- Navbar End -->
