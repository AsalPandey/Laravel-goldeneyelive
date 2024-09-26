@extends('site.layout.app')
@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Categories</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('Home') }}">Home</a></li>
                            <li class="breadcrumb-item text-primary" aria-current="page"><a class="text-white" href="{{ route('Courses') }}">Courses</a></li>
                            <li class="breadcrumb-item text-primary" aria-current="page">Computer Classes</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
    <!-- Courses Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Courses</h6>
                <h1 class="mb-5">Computer Classes</h1>
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
            <!-- <div class="text-center mt-4 wow fadeInUp" data-wow-delay="0.7s">
                <a href="coursesAll.html" class="btn btn-primary">View All</a>
            </div> -->
        </div>
    </div>
    <!-- Courses End -->
@stop