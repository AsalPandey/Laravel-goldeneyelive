@extends('site.layout.app')
@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Courses</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('Home') }}">Home</a></li>
                            <li class="breadcrumb-item text-primary active" aria-current="page">Courses</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- Categories Start -->
    <div class="container-xxl py-5 category">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Categories</h6>
                <h1 class="mb-5">Courses Categories</h1>
            </div>
            <style>
                .course-cat{
                    object-fit: cover;
                }
            </style>
            <div class="row g-3 justify-content-center">
                <div class="col-lg-9 col-md-9">
                    <div class="row g-3">
                        <div class="col-lg-12 col-md-12 wow zoomIn" data-wow-delay="0.1s">
                            <a class="position-relative d-block overflow-hidden" href="{{ route('courseCatagory','computer-classes') }}">
                                <img class="img-fluid course-cat" src="{{ asset("site/img/cat-1.jpg")}}" alt="">
                                <div class="bg-white text-center position-absolute bottom-0 end-0 py-2 px-3" style="margin: 1px;">
                                    <h5 class="m-0">Computer Classes</h5>
                                    <small class="text-primary">3 Courses</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-6 col-md-12 wow zoomIn" data-wow-delay="0.3s">
                            <a class="position-relative d-block overflow-hidden" href="{{ route('courseCatagory','language-classes') }}">
                                <img class="img-fluid course-cat" src="{{ asset("site/img/cat-2.jpg")}}" alt="">
                                <div class="bg-white text-center position-absolute bottom-0 end-0 py-2 px-3" style="margin: 1px;">
                                    <h5 class="m-0">Language Classes</h5>
                                    <small class="text-primary"> Courses</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-6 col-md-12 wow zoomIn" data-wow-delay="0.5s">
                            <a class="position-relative d-block overflow-hidden" href="{{ route('courseCatagory','other-classes') }}">
                                <img class="img-fluid course-cat" src="{{ asset("site/img/cat-3.jpg")}}" alt="">
                                <div class="bg-white text-center position-absolute bottom-0 end-0 py-2 px-3" style="margin: 1px;">
                                    <h5 class="m-0">Other Courses</h5>
                                    <small class="text-primary"> Courses</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Categories End -->

    <!-- Courses Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Courses</h6>
                <h1 class="mb-5">Popular Courses</h1>
            </div>
            <div class="row g-4 justify-content-center">
                @foreach ($courses as $course)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="course-item bg-light">
                            <div class="position-relative overflow-hidden">
                                <img class="img-fluid popular-course-img" src="{{ asset("site/img/$course->photo")}}" alt="">
                                <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                    <a href="{{ route('CoursesDetail',$course->slug) }}" class="flex-shrink-0 btn btn-sm btn-primary px-3 border-end" style="border-radius: 30px 0 0 30px;">Read More</a>
                                    <a href="{{ route('JoinNow') }}" class="flex-shrink-0 btn btn-sm btn-primary px-3" style="border-radius: 0 30px 30px 0;">Join Now</a>    
                                </div>
                            </div>
                            <div class="text-center p-4 pb-0 popular-course">
                                <h3 class="mb-0">{{ $course->price }}</h3>
                                <div class="mb-3">
                                    @php
                                        $rating_star = $course->rating_star; // e.g., 4.2
                                        $rating_count = $course->rating_count; // e.g., 75
                                        $fullStars = floor($rating_star);
                                        $hasHalfStar = ($rating_star - $fullStars) >= 0.5;
                                    @endphp
                                    <!-- filling stars -->
                                    @for ($i = 0; $i < 5; $i++)
                                        <small class="fa {{ $i < $fullStars ? 'fa-star text-primary' : ($i == $fullStars && $hasHalfStar ? 'fa-star-half-alt text-primary' : 'fa-star text-secondary') }}"></small>
                                    @endfor
                                
                                    <small>({{ $rating_count }})</small>
                                </div>                         
                                <h5 class="mb-4">{{ $course->name }}</h5>
                            </div>
                            <div class="d-flex border-top">
                                <small class="flex-fill text-center border-end py-2"><i class="fa fa-user-tie text-primary me-2"></i>{{ $course->instructor }}</small>
                                <small class="flex-fill text-center border-end py-2"><i class="fa fa-clock text-primary me-2"></i>{{ $course->duration }}</small>
                                <small class="flex-fill text-center py-2"><i class="fa fa-user text-primary me-2"></i>{{ $course->capacity }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4 wow fadeInUp" data-wow-delay="0.7s">
                <a href="{{ route('CoursesAll') }}" class="btn btn-primary">View All</a>
            </div>
        </div>
    </div>
    <!-- Courses End -->
@stop