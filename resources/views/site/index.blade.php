@extends('site.layout.app')
@section('content')
    <!-- Carousel Start -->
    <div class="container-fluid p-0 mb-5">
        <div class="owl-carousel header-carousel position-relative" style="height: 100vh;">
            <!-- Carousel 1 -->
            <div class="owl-carousel-item position-relative" style="height: 100vh;">
                <img class="img-fluid position-absolute w-100 h-100" src="{{ asset("site/img/carousel-1.jpg") }}" alt="" style="object-fit: cover;">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">Expert-Led Courses</h5>
                                <h1 class="display-3 text-white animated slideInDown">Learn In-Person with Industry Professionals</h1>
                                <p class="fs-5 text-white mb-4 pb-2">At Golden Eye Academy, gain hands-on experience in web development, computer studies, language classes, and more. Our expert instructors are committed to helping you achieve excellence in your field.</p>
                                <a href="{{ route('About') }}" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Discover More</a>
                                <a href="{{ route('JoinNow') }}" class="btn btn-light py-md-3 px-md-5 animated slideInRight">Enroll Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- Carousel 2 -->
            <div class="owl-carousel-item position-relative" style="height: 100vh;">
                <img class="img-fluid position-absolute w-100 h-100" src="{{ asset("site/img/carousel-2.jpg") }}" alt="" style="object-fit: cover;">
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center" style="background: rgba(24, 29, 56, .7);">
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-sm-10 col-lg-8">
                                <h5 class="text-primary text-uppercase mb-3 animated slideInDown">Build Your Future</h5>
                                <h1 class="display-3 text-white animated slideInDown">Prepare for Global Opportunities</h1>
                                <p class="fs-5 text-white mb-4 pb-2">Whether you're pursuing web development, language proficiency, or preparing for international studies, Golden Eye Academy offers the resources and guidance you need to succeed locally and abroad.</p>
                                <a href="{{ route('About') }}" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Learn More</a>
                                <a href="{{ route('JoinNow') }}" class="btn btn-light py-md-3 px-md-5 animated slideInRight">Join Us Today</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>         
    </div>
    <!-- Carousel End -->


    <!-- Service Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-graduation-cap text-primary mb-4"></i>
                            <h5 class="mb-3">Skilled Instructors</h5>
                            <p>Our instructors are industry experts, bringing real-world experience to the classroom, ensuring you receive practical and effective education.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-globe text-primary mb-4"></i>
                            <h5 class="mb-3">Web Development Classes</h5>
                            <p>Master the fundamentals of web development with hands-on training in HTML, CSS, and modern frameworks to build your career in web development.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-book-open text-primary mb-4"></i>
                            <h5 class="mb-3">Language Classes</h5>
                            <p>Enhance your communication skills with comprehensive language courses designed to improve fluency and confidence for both personal and professional success.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-laptop text-primary mb-4"></i>
                            <h5 class="mb-3">Computer Classes</h5>
                            <p>Develop essential computer skills with our wide range of courses, from basic computer literacy to advanced programming and software applications.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->

    <!-- Message For Chairperson -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <!-- Round Image of Chairperson -->
                <div class="col-lg-3 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="position-relative rounded-circle overflow-hidden shadow" style="width: 300px; height: 300px;">
                            <img class="img-fluid position-absolute w-100 h-100" src="{{ asset('site/img/message-chairperson.jpg') }}" alt="Chairperson Message" style="object-fit: cover;">
                        </div>
                    </div>                    
                    <div class="mt-3 text-center">
                        <h4 class="mb-1 text-primary">Shankar Pokharel</h4> <!-- Name of Chairperson -->
                        <p class="text-muted">Founder</p> <!-- Position -->
                    </div>
                </div>
                <!-- Message from Chairperson -->
                <div class="col-lg-9 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="section-title bg-white text-start text-primary pe-3">Message</h6>
                    <h1 class="mb-4">A Message <br>From Our Founder</h1>
                    <p class="mb-4">
                        A dedicate educational organization, GoldenEye Academy was established with the motto "Complete Solution for preparation." It provides you various options for building your golden future. <br>
                        We welcome you in the ocean of knowledge to dive into it and achieve the best. We facilitate you to identify yourself with the best through motivational tips, inner engineering skills, memory power as well as career counselling. You will be enriched with knowledge and capacity and be able to glow your cherished aim as pearls which you always dreamt for. We help you to determine your destination.
                        <br>I heartly welcome all dear students, participats as well institutions at Golden Eye Academy and assure golden opportunities with quality services.
                        <br><strong class="text-primary"> "GoldenEye Academy" Prepares you to build a sound academic foundation with clear concepts for your higher studies.</strong>
                    </p>
                    <!-- <a href="#more" class="btn btn-primary rounded-pill py-3 px-5">Read More</a> -->
                </div>
            </div>
        </div>
    </div>    

    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100" src="{{ asset("site/img/about.jpg") }}" alt="" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="section-title bg-white text-start text-primary pe-3">About Us</h6>
                    <h1 class="mb-4">Welcome to <br>Golden Eye Academy</h1>
                    <p class="mb-4">GoldenEye Academy is an independent organization, established in 2008 with the aim of promoting qualitative education with special emphasis on carrer counseling and personality development.
                        GoldenEye Academy provides formal and authorized training, preparation classes, language classes, computer classes and various motivational sessions.
                        In order to achieve institutional goal, it operates full time education counselling service. Focusing on the demand and valuable time. it provides morning, day and evening shifts.
                        </p>
                        <div class="row gy-2 gx-4 mb-4">
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Quality Preparation Classes</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Experienced Teachers</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Affordable Fees</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Weekly Tests</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Educational Environment</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Friendly Staff</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Sufficient Teaching Materials</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Classes on Memory Power</p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Career Counseling & Motivational Sessions</p>
                            </div>
                        </div>                        
                    <a class="btn btn-primary py-3 px-5 mt-2" href="{{ route('AboutDetail') }}">Read More</a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


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



    <!-- Team Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Instructors</h6>
                <h1 class="mb-5">Expert Instructors</h1>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="team-item bg-light">
                        <div class="overflow-hidden">
                            <img class="img-fluid" style="height:300px;width:300px" src="{{ asset("site/img/team-1.jpg") }}" alt="">
                        </div>
                        {{-- <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                            <div class="bg-light d-flex justify-content-center pt-2 px-1">
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div> --}}
                        <div class="text-center p-4">
                            <h5 class="mb-0">Shankar Pokhrel</h5>
                            <small>Founder</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="team-item bg-light">
                        <div class="overflow-hidden">
                            <img class="img-fluid" style="height:300px;width:300px" src="{{ asset("site/img/team-2.jpg") }}" alt="">
                        </div>
                        {{-- <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                            <div class="bg-light d-flex justify-content-center pt-2 px-1">
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div> --}}
                        <div class="text-center p-4">
                            <h5 class="mb-0">Pradeep Paudel</h5>
                            <small>Korean Teacher</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="team-item bg-light">
                        <div class="overflow-hidden">
                            <img class="img-fluid" style="height:300px;width:300px" src="{{ asset("site/img/team-3.jpg") }}" alt="">
                        </div>
                        {{-- <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                            <div class="bg-light d-flex justify-content-center pt-2 px-1">
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div> --}}
                        <div class="text-center p-4">
                            <h5 class="mb-0">Surendra Bhattarai</h5>
                            <small>Computer Teacher</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="team-item bg-light">
                        <div class="overflow-hidden">
                            <img class="img-fluid" style="height:300px;width:300px" src="{{ asset("site/img/user.png") }}" alt="">
                        </div>
                        {{-- <div class="position-relative d-flex justify-content-center" style="margin-top: -23px;">
                            <div class="bg-light d-flex justify-content-center pt-2 px-1">
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-twitter"></i></a>
                                <a class="btn btn-sm-square btn-primary mx-1" href=""><i class="fab fa-instagram"></i></a>
                            </div>
                        </div> --}}
                        <div class="text-center p-4">
                            <h5 class="mb-0">Ajay B.k</h5>
                            <small>English Teacher</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Team End -->


    <!-- Testimonial Start -->
    <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="text-center">
                <h6 class="section-title bg-white text-center text-primary px-3">Testimonial</h6>
                <h1 class="mb-5">Our Students Say!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel position-relative">
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="{{ asset("site/img/testimonial-1.jpg") }}" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Sandesh Mahat</h5>
                    <p>Student</p>
                    <div class="testimonial-text bg-light text-center p-4">
                    <p class="mb-0">Joining Golden Eye Academy's computer classes was the best decision I made this year. The instructors are patient and skilled, making complex topics easy to grasp. I now feel confident and ready to take on new tech challenges!</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="{{ asset("site/img/testimonial-2.jpg") }}" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Sharap dorje Gurung </h5>
                    <p>Student</p>
                    <div class="testimonial-text bg-light text-center p-4">
                    <p class="mb-0">The Computer Class at Golden Eye Academy exceeded my expectations. The hands-on approach let me explore my creativity while mastering industry-standard tools. I now have a portfolio I'm proud to showcase.</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="{{ asset("site/img/testimonial-3.jpg") }}" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Apekshya Chhetri</h5>
                    <p>Student</p>
                    <div class="testimonial-text bg-light text-center p-4">
                    <p class="mb-0">I also never imagined learning Korean so quickly! The language classes are perfectly structured, blending conversation practice with grammar. The supportive environment has made a huge difference in my progress.</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="{{ asset("site/img/testimonial-4.jpg") }}" style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Nirmala Thapa</h5>
                    <p>Student</p>
                    <div class="testimonial-text bg-light text-center p-4">
                    <p class="mb-0">GoldenEye Academy offered excellent IELTS preparation. The instructors were knowledgeable and supportive, helping me build confidence. I highly recommend them for anyone aiming for a high IELTS score!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->
@stop