@extends('site.layout.app')
@section('content')
    <!-- Header Start -->
    <div class="container-fluid bg-primary py-5 mb-5 page-header">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="display-3 text-white animated slideInDown">Course Details</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a class="text-white" href="{{ route('Home') }}">Home</a></li>
                            <li class="breadcrumb-item text-primary" aria-current="page"><a class="text-white" href="{{ route('Courses') }}">Courses</a></li>
                            <li class="breadcrumb-item text-primary" aria-current="page">Basic Computer Class</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->
    <!-- Course Details Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <!-- Image Section -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <img class="img-fluid" src="{{ asset('site/img/' . $course->photo) }}" alt="{{ $course->name }}" style="height:450px; object-fit: cover;">
                </div>
                
                <!-- Course Details Section -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <h1 class="mb-4">{{ $course->name }}</h1>
                    <p>{{ $course->description }}</p>
                    <h5 class="mb-4">Course Outline:</h5>
                    @foreach(explode('/', $course->course_outline) as $outline)
                        <p><i class="fa fa-check text-primary me-3"></i>{{ $outline }}</p>
                    @endforeach
    
                    <div class="d-flex justify-content-between mt-4">
                        <h5 class="text-primary">Price: Rs. {{ $course->price }}</h5>
                        <h5 class="text-primary">Duration: {{ $course->duration }}</h5>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <h5 class="text-primary">Instructor: {{ $course->instructor }}</h5>
                        <h5 class="text-primary">
                            <!-- Dynamic star rating -->
                            @for ($i = 0; $i < 5; $i++)
                                <small class="fa {{ $i < floor($course->rating_star) ? 'fa-star text-primary' : ($i == floor($course->rating_star) && ($course->rating_star - floor($course->rating_star)) >= 0.5 ? 'fa-star-half-alt text-primary' : 'fa-star text-secondary') }}"></small>
                            @endfor
                            <small>({{ $course->rating_count }})</small>
                        </h5>
                        <a class="btn btn-primary py-3 px-5" href="{{ route('JoinNow', $course->slug) }}">Enroll Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Course Details End -->
@stop