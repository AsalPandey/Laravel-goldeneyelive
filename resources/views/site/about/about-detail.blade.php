@extends('site.layout.app')
@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">About Us</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('Home') }}">Home</a></li>
                            <li class="breadcrumb-item text-primary active" aria-current="page">About</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
    <!-- About Details Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100" src="{{ asset("site/img/about-details.jpg")}}" alt="" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="section-title bg-white text-start text-primary pe-3">About Us</h6>
                    <h1 class="mb-4">Our Journey at <br> Golden Eye Academy</h1>
                    <p class="mb-4">Since its founding in 2008, Golden Eye Academy has been committed to delivering top-tier educational experiences. Through a blend of expert guidance, cutting-edge programs, and personalized career counseling, we strive to equip our students with the skills they need to excel in their personal and professional lives.</p>

                    <h2 class="mb-3">Our Mission</h2>
                    <p class="mb-4">Our mission is to offer a complete solution for academic preparation, personal growth, and skill development. From language and computer classes to motivational coaching, we provide comprehensive resources for success. We aim to foster a community of learners who are driven to achieve excellence and explore new horizons.</p>

                    <h2 class="mb-3">Our Vision</h2>
                    <p class="mb-4">At Golden Eye Academy, we envision a future where education empowers individuals to overcome barriers, realize their potential, and contribute to society. We are committed to setting new standards in education by embracing innovation and providing exceptional training in language, computer skills, and beyond.</p>

                    <h2 class="mb-3">Our History</h2>
                    <p class="mb-4">Golden Eye Academy was established in 2008 with a vision to redefine education and preparation. Guided by our motto, "Complete Solution for Preparation," we have consistently delivered educational services that meet the evolving needs of students. Over the years, we have built a reputation for excellence, adaptability, and forward-thinking in our approach to education.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- About Details End -->
@stop