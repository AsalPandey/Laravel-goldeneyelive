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
                            <li class="breadcrumb-item text-primary" aria-current="page">other Classes</li>
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
                <h1 class="mb-5">other Classes</h1>
            </div>
            <div class="row g-4 justify-content-center">
                <h1 class="text-secondary text-center">No course available right now!</h1>
            </div>
            <!-- <div class="text-center mt-4 wow fadeInUp" data-wow-delay="0.7s">
                <a href="coursesAll.html" class="btn btn-primary">View All</a>
            </div> -->
        </div>
    </div>
    <!-- Courses End -->
@stop