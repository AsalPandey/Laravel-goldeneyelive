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
                    <img class="img-fluid" src="{{ asset("site/img/course-1.jpg")}}" alt="Basic Computer Classes" style="height:450px; object-fit: cover;">
                </div>
                
                <!-- Course Details Section -->
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                    <h1 class="mb-4">Basic Computer Classes</h1>
                    <p>
                        Our Basic Computer Classes are perfect for beginners or anyone looking to brush up on their computer skills. The course covers everything from basic hardware and software knowledge to navigating the internet and using essential software tools like Microsoft Office.
                    </p>
                    <p>
                        You'll also get a brief introduction to basic programming concepts to give you a well-rounded understanding of today's digital world. Whether you're a student, job seeker, or just looking to improve your technical skills, this course is designed to meet your needs.
                    </p>
                    <h5 class="mb-4">Course Outline:</h5>
                    <p><i class="fa fa-check text-primary me-3"></i>Introduction to Computers and Operating Systems</p>
                    <p><i class="fa fa-check text-primary me-3"></i>File Management and Basic Applications</p>
                    <p><i class="fa fa-check text-primary me-3"></i>Internet Browsing and Email Management</p>
                    <p><i class="fa fa-check text-primary me-3"></i>Introduction to Microsoft Office (Word, Excel, PowerPoint)</p>
                    <p><i class="fa fa-check text-primary me-3"></i>Basic Troubleshooting and Maintenance</p>
                    <p><i class="fa fa-check text-primary me-3"></i>Intro to Basic Programming Concepts</p>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <h5 class="text-primary">Price: Rs. 3,000</h5>
                        <h5 class="text-primary">Duration: 1.49 Hours</h5>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <h5 class="text-primary">Instructor: Surendra Bhattarai</h5>
                        <a class="btn btn-primary py-3 px-5" href="{{ route('JoinNow') }}">Enroll Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Course Details End -->
@stop