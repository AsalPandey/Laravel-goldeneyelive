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
@stop