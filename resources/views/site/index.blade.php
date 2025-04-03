@extends('site.layout.app')
@section('content')
    <style>
       /* Modal Container - Full Width with 20px Margin */
.custom-modal-content {
    background-color: transparent;
    border: none;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%; /* Full width minus 20px margin on each side */
    max-width: 100%;
}

/* Modal Wrapper */
.custom-modal-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: calc(100% - 40px); /* Full width minus margin */
    height: auto;
    position: relative;
    padding: 20px;
}

/* Responsive Image - Fills Modal */
.custom-notice-image {
    width: 100%; /* Make the image fill the modal width */
    max-height: 80vh;
    object-fit: contain;
    border-radius: 10px;
    position: relative;
}

/* Close Button (X) - Top Right of Image */
.custom-close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: red;
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    cursor: pointer;
    transition: transform 0.2s ease-in-out;
    z-index: 1000;
}

.custom-close-btn i {
    font-size: 20px;
}

/* Close Button Hover Effect */
.custom-close-btn:hover {
    transform: scale(1.1);
}

/* Register Now Button - Centered on Image */
.custom-register-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #F4A701;
    color: #6C757D;
    font-size: 18px;
    font-weight: bold;
    padding: 14px 28px;
    border-radius: 30px;
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 1px;
    border: 2px solid white;
    box-shadow: 0 4px 10px rgba(244, 167, 1, 0.4);
    transition: all 0.3s ease-in-out;
    text-align: center;
    display: inline-block;
    animation: custom-pulseScale 2s infinite ease-in-out;
}

/* Register Button Hover */
.custom-register-btn:hover {
    background: white;
    color: #F4A701;
    border: 2px solid #F4A701;
    box-shadow: 0 6px 15px rgba(244, 167, 1, 0.6);
    transform: translate(-50%, -50%) scale(1.1);
}

/* Pulse & Scale Animation */
@keyframes custom-pulseScale {
    0% {
        transform: translate(-50%, -50%) scale(1);
        box-shadow: 0 0 10px rgba(244, 167, 1, 0.5);
    }

    50% {
        transform: translate(-50%, -50%) scale(1.08);
        box-shadow: 0 0 20px rgba(244, 167, 1, 0.8);
    }

    100% {
        transform: translate(-50%, -50%) scale(1);
        box-shadow: 0 0 10px rgba(244, 167, 1, 0.5);
    }
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .custom-modal-wrapper {
        padding: 10px;
        max-width: calc(100%);
    }

    .custom-notice-image {
    }

    .custom-register-btn {
        font-size: 16px;
        padding: 10px 20px;
    }
}

@media (max-width: 480px) {
    .custom-notice-image {
        max-width: 100%;
    }

    .custom-register-btn {
        font-size: 14px;
        padding: 8px 16px;
    }

    .custom-close-btn {
        width: 30px;
        height: 30px;
    }

    .custom-close-btn i {
        font-size: 16px;
    }
}

    </style>

    <!-- Notice Popup -->
    @foreach ($notices as $noticeData)
        <!-- Modal Markup for Each Row -->
        <div class="modal fade" id="modal{{ $noticeData->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $noticeData->id }}"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="custom-modal-content">
                    <div class="custom-modal-wrapper">
                        <!-- Close Button -->
                        <button type="button" class="custom-close-btn" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                        <div class="modal-body">
                            <!-- Notice Image -->
                            <img class="custom-notice-image" src="{{ asset('site/uploads/notices/' . $noticeData->image) }}"
                                alt="">
                            <!-- Register Now Button -->
                        </div>
                    </div>
                </div>
            </div>
            <a href="https://docs.google.com/forms/d/e/1FAIpQLSfBL1Ii__eZokGm28E4M5dBLPct0jlZiX-wKJuWUvF9a9UMTg/viewform" class="custom-register-btn" target="_blank">Register Now</a>
        </div>
    @endforeach

    <!-- Notice Popup Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @foreach ($notices as $noticeData)
                // if (!sessionStorage.getItem('customModalShown{{ $noticeData->id }}')) {
                var customModal{{ $noticeData->id }} = new bootstrap.Modal(document.getElementById(
                    'modal{{ $noticeData->id }}'));
                customModal{{ $noticeData->id }}.show();
                sessionStorage.setItem('customModalShown{{ $noticeData->id }}', 'true');
                // }
            @endforeach
        });
    </script>
    

    <!-- Carousel Start -->
    <div class="container py-5 mx-auto">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <!-- Left content section -->
                <div class="academy-content mx-5">
                    <h1>
                        <div style="color: #F2A900; font-size: 2.5rem;">Preparing for</div>
                        <div style="color: #5A3D24; font-size: 2.5rem;">Global</div>
                        <div style="color: #F2A900; font-size: 2.5rem;">Opportunities</div>
                    </h1>
                    <style>
                        @media (min-width: 480px) {
                            .since-2008 {
                                margin: 1rem 0 1.5rem 16rem;
                                font-size: 3.5rem;
                            }
                        }
                        @media (max-width: 480px) {
                            .since-2008 {
                                margin: 1rem 0 1.5rem 0;
                                font-size: 2.5rem;
                            }
                        }
                    </style>
                    <h2 class="since-2008" style="
                    background: linear-gradient(  #FFAA00,#E53935);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent; ">Since 2008</h2>
                   <blockquote class="text-center fw-bold font-italic" style="
                        background: linear-gradient(80deg, #FFAA00,#E53935);
                        -webkit-background-clip: text !important;
                        -webkit-text-fill-color: transparent !important;

                   "> 
                        <i class="fas fa-quote-left"></i>  
                            <span>
                                GoldenEye, Golden Future
                            </span>
                        <i class="fas fa-quote-right"></i>               
                    </blockquote>
                    <p style="color: #6c757d; line-height: 1.6;">
                        At Golden Eye Academy, gain hands-on experience in Web development, Computer studies,
                        Skill advancement trainings, Professional workshops, career consultancy, and more.
                        Our expert instructors are committed to helping you achieve
                        excellence in your field.
                    </p>
                    
                    <div class="d-flex justify-content-center mt-4">
                        <a href="{{ route('About') }}"
                       class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Learn More</a>
                        <a href="{{ route('JoinNow') }}"
                        class="btn btn-light py-md-3 px-md-5 animated slideInRight">Join Us Today</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 col-sm-12 position-relative my-5 d-flex justify-content-end">
                <style>
                    .first, .second {
                        height: 300px;
                        width: 200px;
                        border-radius: 100px;
                        transition: all 0.3s ease-in-out;
                    }
            
                    .first {
                        background: #000;
                        position: absolute;
                        top: 125px;
                        left: 15px;
                        z-index: 1;
                    }
            
                    .second {
                        background: #ff0000;
                        text-align: center;
                        z-index: 2;
                    }
            
                    /* Responsive Design */
                    @media (max-width: 1200px) { /* Large screens */
                        .first, .second {
                            height: 250px;
                            width: 180px;
                            border-radius: 90px;
                        }
                        .first {
                            top: 100px;
                            left: 10px;
                        }
                    }
    
                    @media (max-width: 992px) { /* Tablets */
                        .first, .second {
                            height: 230px;
                            width: 160px;
                            border-radius: 80px;
                        }
                        .first {
                            top: 90px;
                            left: 8px;
                        }
                    }
    
                    @media (max-width: 768px) { /* Mobile Devices */
                        .first, .second {
                            height: 200px;
                            width: 140px;
                            border-radius: 70px;
                        }
                        .first {
                            top: 80px;
                            left: 5px;
                        }
                    }
                </style>
            
                <!-- Wrapper for small image -->
                <div class="first animated slideInLeft overflow-hidden">
                    <img src="{{ asset('site/img/carousel-1.jpg') }}" alt="Academy Logo" class="img-fluid"
                        style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            
                <!-- Wrapper for large image -->
                <div class="second animated slideInRight overflow-hidden">
                    <img src="{{ asset('site/img/carousel-2.jpg') }}" alt="Academy Logo" class="img-fluid"
                        style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            
                <!-- Notification Box -->
                <div class="position-absolute text-white p-3 text-center"
                    style="bottom: -10%; left: 50%; transform: translateX(-50%);
                        background: linear-gradient(to right, #F2A900, #E53935);
                        box-shadow: 0px 4px 8px rgba(0,0,0,0.2);
                        width: 80%; max-width: 265px;
                        z-index: 3; border-radius: 20px;">
                    <p class="mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="currentColor"
                            class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a 8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        Exclusive Discount Available
                    </p>
                    <p class="mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="currentColor"
                            class="bi bi-check-circle-fill me-2" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a 8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        Expert Teachers Await You.
                    </p>
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
                            <p>Our instructors are industry experts, bringing real-world experience to the classroom,
                                ensuring you receive practical and effective education.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-globe text-primary mb-4"></i>
                            <h5 class="mb-3">Web Development Classes</h5>
                            <p>Master the fundamentals of web development with hands-on training in HTML, CSS, and modern
                                frameworks to build your career in web development.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-book-open text-primary mb-4"></i>
                            <h5 class="mb-3">Language Classes</h5>
                            <p>Enhance your communication skills with comprehensive language courses designed to improve
                                fluency and confidence for both personal and professional success.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                    <div class="service-item text-center pt-3">
                        <div class="p-4">
                            <i class="fa fa-3x fa-laptop text-primary mb-4"></i>
                            <h5 class="mb-3">Computer Classes</h5>
                            <p>Develop essential computer skills with our wide range of courses, from basic computer
                                literacy to advanced programming and software applications.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Service End -->

    <!-- About Start -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s" style="min-height: 400px;">
                    <div class="position-relative h-100">
                        <img class="img-fluid position-absolute w-100 h-100" src="{{ asset('site/img/about.jpg') }}"
                            alt="" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.3s">
                    <h6 class="section-title bg-white text-start text-primary pe-3">About Us</h6>
                    <h1 class="mb-4">Welcome to <br>Golden Eye Academy</h1>
                    <p class="mb-4">GoldenEye Academy is an independent organization, established in 2008 with the aim of
                        promoting qualitative education with special emphasis on carrer counseling and personality
                        development.
                        GoldenEye Academy provides formal and authorized training, preparation classes, language classes,
                        computer classes and various motivational sessions.
                        In order to achieve institutional goal, it operates full time education counselling service.
                        Focusing on the demand and valuable time. it provides morning, day and evening shifts.
                    </p>
                    <div class="row gy-2 gx-4 mb-4">
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Quality Preparation
                                Classes</p>
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
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Educational Environment
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Friendly Staff</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Sufficient Teaching
                                Materials</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Classes on Memory Power
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-0"><i class="fa fa-arrow-right text-primary me-2"></i>Career Counseling &
                                Motivational Sessions</p>
                        </div>
                    </div>
                    <a class="btn btn-primary py-3 px-5 mt-2" href="{{ route('AboutDetail') }}">Read More</a>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->

    <!-- Message For Chairperson -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <!-- Round Image of Chairperson -->
                <div class="col-lg-3 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="position-relative rounded-circle overflow-hidden shadow"
                            style="width: 300px; height: 300px;">
                            <img class="img-fluid position-absolute w-100 h-100"
                                src="{{ asset('site/img/message-chairperson.jpg') }}" alt="Chairperson Message"
                                style="object-fit: cover;">
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
                        A dedicate educational organization, GoldenEye Academy was established with the motto "Complete
                        Solution for preparation." It provides you various options for building your golden future. <br>
                        We welcome you in the ocean of knowledge to dive into it and achieve the best. We facilitate you to
                        identify yourself with the best through motivational tips, inner engineering skills, memory power as
                        well as career counselling. You will be enriched with knowledge and capacity and be able to glow
                        your cherished aim as pearls which you always dreamt for. We help you to determine your destination.
                        <br>I heartly welcome all dear students, participats as well institutions at Golden Eye Academy and
                        assure golden opportunities with quality services.
                        <br><strong class="text-primary"> "GoldenEye Academy" Prepares you to build a sound academic
                            foundation with clear concepts for your higher studies.</strong>
                    </p>
                    <!-- <a href="#more" class="btn btn-primary rounded-pill py-3 px-5">Read More</a> -->
                </div>
            </div>
        </div>
    </div>


    {{-- <!-- Categories Start -->
    <div class="container-xxl py-5 category">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Categories</h6>
                <h1 class="mb-5">Courses Categories</h1>
            </div>
            <style>
                .course-cat {
                    object-fit: cover;
                }
            </style>
            @php
                $count = App\Models\Courses::all();
                $data = [
                    'computer-classes' => 0,
                    'language-classes' => 0,
                    'other-classes' => 0,
                ];
                foreach ($count as $course) {
                    if ($course->category_slug == 'computer-classes') {
                        $data['computer-classes']++;
                    } elseif ($course->category_slug == 'language-classes') {
                        $data['language-classes']++;
                    } elseif ($course->category_slug == 'other-classes') {
                        $data['other-classes']++;
                    }
                }
            @endphp
            <div class="row g-3 justify-content-center">
                <div class="col-lg-9 col-md-9">
                    <div class="row g-3">
                        <div class="col-lg-12 col-md-12 wow zoomIn" data-wow-delay="0.1s">
                            <a class="position-relative d-block overflow-hidden"
                                href="{{ route('courseCatagory', 'computer-classes') }}">
                                <img class="img-fluid course-cat" src="{{ asset('site/img/cat-1.jpg') }}"
                                    alt="">
                                <div class="bg-white text-center position-absolute bottom-0 end-0 py-2 px-3"
                                    style="margin: 1px;">
                                    <h5 class="m-0">Computer Classes</h5>
                                    <small class="text-primary">{{ $data['computer-classes'] }} Courses</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-6 col-md-12 wow zoomIn" data-wow-delay="0.3s">
                            <a class="position-relative d-block overflow-hidden"
                                href="{{ route('courseCatagory', 'language-classes') }}">
                                <img class="img-fluid course-cat" src="{{ asset('site/img/cat-2.jpg') }}"
                                    alt="">
                                <div class="bg-white text-center position-absolute bottom-0 end-0 py-2 px-3"
                                    style="margin: 1px;">
                                    <h5 class="m-0">Language Classes</h5>
                                    <small class="text-primary">{{ $data['language-classes'] }} Courses</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-6 col-md-12 wow zoomIn" data-wow-delay="0.5s">
                            <a class="position-relative d-block overflow-hidden"
                                href="{{ route('courseCatagory', 'other-classes') }}">
                                <img class="img-fluid course-cat" src="{{ asset('site/img/cat-3.jpg') }}"
                                    alt="">
                                <div class="bg-white text-center position-absolute bottom-0 end-0 py-2 px-3"
                                    style="margin: 1px;">
                                    <h5 class="m-0">Other Courses</h5>
                                    <small class="text-primary">{{ $data['other-classes'] }} Courses</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Categories End --> --}}
    <!-- Categories Start -->
    <div class="container-xxl py-5 category">
        <div class="container">
            <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
                <h6 class="section-title bg-white text-center text-primary px-3">Categories</h6>
                <h1 class="mb-5">Courses Categories</h1>
            </div>
            <style>
                .category-container {
                    display: flex;
                    gap: 5px;
                    justify-content: center;
                }

                .category-item {
                    position: relative;
                    overflow: hidden;
                    height: 410px;
                    flex: 1;
                    transition: flex 0.3s ease, transform 0.3s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                /* Scale horizontally and compress others */
                .category-item:hover {
                    flex: 1.4; /* Expand the hovered item */
                }

                .category-item:not(:hover) {
                    flex: 0.8; /* Compress other items */
                }

                /* Dark Mask over image */
                .category-mask {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5); /* 50% Black overlay */
                    transition: opacity 0.3s ease;
                    z-index: 2;
                }

                /* Hide dark mask on hover */
                .category-item:hover .category-mask {
                    opacity: 0;
                }

                .category-item img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.3s ease;
                }

                /* White Mask for Category Text - Always Visible */
                .category-text {
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    text-align: center;
                    padding: 15px;
                    background: rgba(255, 255, 255, 0.8);
                    z-index: 3;
                    transition: transform 0.3s ease;
                }

                /* Scale up text div when hovered */
                .category-item:hover .category-text {
                    transform: scale(1.1);
                }

                .category-text h5 {
                    margin: 0;
                    font-weight: bold;
                }

                .category-text small {
                    color: #007bff;
                }
            </style>

            @php
                $count = App\Models\Courses::all();
                $data = [
                    'computer-classes' => 0,
                    'language-classes' => 0,
                    'other-classes' => 0,
                ];
                foreach ($count as $course) {
                    if ($course->category_slug == 'computer-classes') {
                        $data['computer-classes']++;
                    } elseif ($course->category_slug == 'language-classes') {
                        $data['language-classes']++;
                    } elseif ($course->category_slug == 'other-classes') {
                        $data['other-classes']++;
                    }
                }
            @endphp

            <div class="category-container">
                <!-- Computer Classes Category -->
                <a href="{{ route('courseCatagory', 'computer-classes') }}" class="position-relative d-block category-item">
                    <div class="category-mask"></div> <!-- Dark Mask Layer -->
                    <img class="img-fluid" src="{{ asset('site/img/cat-1.jpg') }}" alt="Computer Classes">
                    <div class="category-text">
                        <h5>Computer Classes</h5>
                        <small>{{ $data['computer-classes'] }} Courses</small>
                    </div>
                </a>

                <!-- Language Classes Category -->
                <a href="{{ route('courseCatagory', 'language-classes') }}" class="position-relative d-block category-item">
                    <div class="category-mask"></div> <!-- Dark Mask Layer -->
                    <img class="img-fluid" src="{{ asset('site/img/cat-2.jpg') }}" alt="Language Classes">
                    <div class="category-text">
                        <h5>Language Classes</h5>
                        <small>{{ $data['language-classes'] }} Courses</small>
                    </div>
                </a>

                <!-- Other Classes Category -->
                <a href="{{ route('courseCatagory', 'other-classes') }}" class="position-relative d-block category-item">
                    <div class="category-mask"></div> <!-- Dark Mask Layer -->
                    <img class="img-fluid" src="{{ asset('site/img/cat-3.jpg') }}" alt="Other Courses">
                    <div class="category-text">
                        <h5>Other Courses</h5>
                        <small>{{ $data['other-classes'] }} Courses</small>
                    </div>
                </a>
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
                        <div class="course-item bg-light position-relative">
                            @if ($course->slug === 'basic-web-development')
                                <img src="{{ asset('site/img/premium.png') }}" alt="Premium Badge" 
                                    class="position-absolute top-50 start-0 m-3" style="width: 100px; z-index: 1000;">
                            @endif
                            <div class="position-relative overflow-hidden">
                                <img class="img-fluid popular-course-img" src="{{ asset("site/img/$course->photo") }}"
                                    alt="">
                                <div class="w-100 d-flex justify-content-center position-absolute bottom-0 start-0 mb-4">
                                    <a href="{{ route('CoursesDetail', $course->slug) }}"
                                        class="flex-shrink-0 btn btn-sm btn-primary px-3 border-end"
                                        style="border-radius: 30px 0 0 30px;">Read More</a>
                                    <a href="{{ route('JoinNow') }}" class="flex-shrink-0 btn btn-sm btn-primary px-3"
                                        style="border-radius: 0 30px 30px 0;">Join Now</a>
                                </div>
                            </div>
                            <div class="text-center p-4 pb-0 popular-course">
                                <h3 class="mb-0">{{ $course->price }}</h3>
                                <div class="mb-3">
                                    @php
                                        $rating_star = $course->rating_star;
                                        $rating_count = $course->rating_count;
                                        $fullStars = floor($rating_star);
                                        $hasHalfStar = $rating_star - $fullStars >= 0.5;
                                    @endphp
                                    @for ($i = 0; $i < 5; $i++)
                                        <small
                                            class="fa {{ $i < $fullStars ? 'fa-star text-primary' : ($i == $fullStars && $hasHalfStar ? 'fa-star-half-alt text-primary' : 'fa-star text-secondary') }}"></small>
                                    @endfor
                                    <small>({{ $rating_count }})</small>
                                </div>
                                <h5 class="mb-4">{{ $course->name }}</h5>
                            </div>
                            <div class="d-flex border-top">
                                <small class="flex-fill text-center border-end py-2"><i
                                        class="fa fa-user-tie text-primary me-2"></i>{{ $course->instructor }}</small>
                                <small class="flex-fill text-center border-end py-2"><i
                                        class="fa fa-clock text-primary me-2"></i>{{ $course->duration }}</small>
                                <small class="flex-fill text-center py-2"><i
                                        class="fa fa-user text-primary me-2"></i>{{ $course->capacity }}</small>
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
            <div class="row g-4 justify-content-center">
                <!-- Instructor 1 -->
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 d-flex justify-content-center wow fadeInUp"
                    data-wow-delay="0.1s">
                    <div class="team-item bg-light">
                        <div class="overflow-hidden">
                            <img class="img-fluid" style="height:300px;width:100%; object-fit: cover;"
                                src="{{ asset('site/img/team_4.jpg') }}" alt="Instructor 1">
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-0">Aakash Subedi</h5>
                            <small>IELTS/PTE/TOFEL Instructor</small>
                        </div>
                    </div>
                </div>
                <!-- Instructor 2 -->
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 d-flex justify-content-center wow fadeInUp"
                    data-wow-delay="0.3s">
                    <div class="team-item bg-light">
                        <div class="overflow-hidden">
                            <img class="img-fluid" style="height:300px;width:100%; object-fit: cover;"
                                src="{{ asset('site/img/team-2.jpg') }}" alt="Instructor 2">
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-0">Pradeep Paudel</h5>
                            <small>Korean Teacher</small>
                        </div>
                    </div>
                </div>
                <!-- Instructor 3 -->
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 d-flex justify-content-center wow fadeInUp"
                    data-wow-delay="0.5s">
                    <div class="team-item bg-light">
                        <div class="overflow-hidden">
                            <img class="img-fluid" style="height:300px;width:100%; object-fit: cover;"
                                src="{{ asset('site/img/team-3.jpg') }}" alt="Instructor 3">
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-0">Surendra Bhattarai</h5>
                            <small>Computer Teacher</small>
                        </div>
                    </div>
                </div>
                <!-- Instructor 4 -->
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 d-flex justify-content-center wow fadeInUp"
                    data-wow-delay="0.7s">
                    <div class="team-item bg-light">
                        <div class="overflow-hidden">
                            <img class="img-fluid" style="height:300px;width:100%; object-fit: cover;"
                                src="{{ asset('site/img/team_1.jpg') }}" alt="Instructor 4">
                        </div>
                        <div class="text-center p-4">
                            <h5 class="mb-0">Ajay B.K</h5>
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
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="{{ asset('site/img/testimonial-1.jpg') }}"
                        style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Sandesh Mahat</h5>
                    <p>Student</p>
                    <div class="testimonial-text bg-light text-center p-4">
                        <p class="mb-0">Joining Golden Eye Academy's computer classes was the best decision I made this
                            year. The instructors are patient and skilled, making complex topics easy to grasp. I now feel
                            confident and ready to take on new tech challenges!</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="{{ asset('site/img/testimonial-2.jpg') }}"
                        style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Sharap dorje Gurung </h5>
                    <p>Student</p>
                    <div class="testimonial-text bg-light text-center p-4">
                        <p class="mb-0">The Computer Class at Golden Eye Academy exceeded my expectations. The hands-on
                            approach let me explore my creativity while mastering industry-standard tools. I now have a
                            portfolio I'm proud to showcase.</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="{{ asset('site/img/testimonial-3.jpg') }}"
                        style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Apekshya Chhetri</h5>
                    <p>Student</p>
                    <div class="testimonial-text bg-light text-center p-4">
                        <p class="mb-0">I also never imagined learning Korean so quickly! The language classes are
                            perfectly structured, blending conversation practice with grammar. The supportive environment
                            has made a huge difference in my progress.</p>
                    </div>
                </div>
                <div class="testimonial-item text-center">
                    <img class="border rounded-circle p-2 mx-auto mb-3" src="{{ asset('site/img/testimonial-4.jpg') }}"
                        style="width: 80px; height: 80px;">
                    <h5 class="mb-0">Nirmala Thapa</h5>
                    <p>Student</p>
                    <div class="testimonial-text bg-light text-center p-4">
                        <p class="mb-0">GoldenEye Academy offered excellent IELTS preparation. The instructors were
                            knowledgeable and supportive, helping me build confidence. I highly recommend them for anyone
                            aiming for a high IELTS score!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial End -->
@stop
