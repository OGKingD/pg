<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{asset('assets/img/saanapay.png')}}">
    <link rel="icon" type="image/png" href="{{asset('assets/img/saanapay.png')}}">
    <title>
        {{  $title ?? config('app.name', 'Laravel') }}
    </title>
    <!-- Extra details for Live View on GitHub Pages -->
    <!-- Canonical SEO -->
    <link rel="canonical" href="https://www.saanapay.ng"/>
    <!--  Social tags      -->
    <meta name="keywords"
          content="payment, gateway, simple">
    <meta name="description" content="Saanapay.">

    <!--  -->
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Nucleo Icons -->
    <link href="{{asset('assets/css/nucleo-icons.css')}}" rel="stylesheet"/>
    <!-- Font Awesome Icons -->
    <link href="{{asset('assets/css/nucleo-svg.css')}}" rel="stylesheet"/>
    <!-- CSS Files -->
    <link href="{{asset('assets/css/argonpro.css')}}" rel="stylesheet"/>

    <!-- End Google Tag Manager -->
</head>

<body class="presentation-page">

<!-- Navbar -->
<nav id="navbar-main" class="navbar navbar-main navbar-expand-lg  navbar-dark position-sticky top-0  py-2" style="background: rgb(255,255,255);background: linear-gradient(152deg, rgba(255,255,255,1) 36%, rgba(44,202,227,1) 43%, rgba(19,60,139,1) 99%);">
    <div class="container">
        <a class=" min-vw-60" href="/">
            <img src="{{asset('assets/img/saanapay.png')}}" style="width: 55%" alt="">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_global"
                aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-collapse collapse" id="navbar_global">
            <div class="navbar-collapse-header">
                <div class="row">
                    <div class="col-6 collapse-brand">
                        <a href="/">
                            <img src="{{asset('assets/img/saanapay.png')}}" alt="">
                        </a>
                    </div>
                    <div class="col-6 collapse-close">
                        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbar_global"
                                aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
            <ul class="navbar-nav navbar-nav-hover align-items-lg-center ml-lg-auto">


                <li class="nav-item">
                    <a href="{{route('login')}}" class="btn btn-outline-white" target="_blank">
                        <i class="ni ni-laptop d-lg-none"></i>
                        <span class="nav-link-inner--text">
                            <i class="fas fa-key"></i>
                            Login</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('register')}}" class="btn btn-white"
                       target="_blank">
                        <i class="ni ni-basket d-lg-none"></i>
                        <span class="nav-link-inner--text">
                            <i class="fas fa-door-open"></i>
                            Register
                        </span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->
<div class="wrapper">
    <!-- Hero for PRO version -->
    <div class="section section-hero section-shaped pt-0">
        <div class="page-header mt-n5">
            <div class="page-header-image"
                 style="background-image: url('{{asset('assets/img/ill/presentation_bg.png')}}');">
            </div>
            <div class="container-fluid shape-container d-flex align-items-center py-lg">
                <div class="col px-0">
                    <div class="row">
                        <div class="col-lg-4 ml-5">
                            <img src="{{asset('assets/img/saanapay.png')}}" style="width: 200px;" class="img-fluid" alt="">
                            <span class="badge badge-danger">PRO</span>
                            <p class="lead">Get Started<br/> <b>Payments Made Easy.</b></p>
                            <div class="btn-wrapper mt-5">
                                <a href="{{route('login')}}"
                                   class="btn btn-icon mb-3 mb-sm-0 text-white" style="background-color: #2ccae3">
                                    <span class="btn-inner--icon"><i class="fas fa-sign-in-alt"></i></span>
                                    <span >Login</span>
                                </a>
                                <a href="{{route('register')}}"
                                   class="btn btn-outline-primary btn-icon mb-3 mb-sm-0" target="_blank">
                                    <span class="btn-inner--icon"><i class="fas fa-door-open"></i></span>
                                    <span class="btn-inner--text">Register</span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <section class="section-basic-components">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-10 mb-md-5">
                    <h1 class="display-3">Instant Payments<span
                            class="text-primary"> Infinite Possibilities </span></h1>
                    <p class="lead">Our payment gateway allows your business to accept fast, secure, and reliable payments through multiple channels—web, mobile, and API. Whether you're selling online, collecting bills, or building a digital product, we have the tools you need to succeed.</p>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="image-container">
                        <img class="table-img" src="{{asset('assets/img/presentation-page/table.png')}}" alt="">
                        <img class="coloured-card-btn-img" src="{{asset('assets/img/presentation-page/card-btn.png')}}" alt="">
                        <img class="coloured-card-img" src="{{asset('assets/img/presentation-page/card-orange.png')}}" alt="">
                        <img class="linkedin-btn-img" src="{{asset('assets/img/presentation-page/slack-btn.png')}}" alt="">
                        <img class="w-100" src="{{asset('assets/img/ill/example-3.svg')}}">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-icons mb-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-sm-2">
                    <div class="icons-nucleo">
                        <i class="first-left-icon ni ni-send text-primary"></i>
                        <i class="second-left-icon ni ni-alert-circle-exc text-warning"></i>
                        <i class="third-left-icon ni ni-cart text-info"></i>
                        <i class="fourth-left-icon ni ni-bold text-default"></i>
                        <i class="fifth-left-icon ni ni-headphones text-danger"></i>
                        <i class="sixth-left-icon ni ni-satisfied text-success"></i>
                        <i class="seventh-left-icon ni ni-cart text-pink"></i>
                        <i class="eighth-left-icon ni ni-spaceship text-info"></i>
                        <i class="ninth-left-icon ni ni-sound-wave text-warning"></i>
                        <i class="tenth-left-icon ni ni-heart-2 text-danger"></i>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-8 text-center">
                    <h2 class="display-2">Fast. Secure. Reliable.</h2>
                    <p class="lead">
                        In a world where convenience is king, your payment solution should be seamless, secure, and always available. Whether your customers pay on desktop, mobile, or any device, Our Payment Gateway delivers a smooth experience that keeps them coming back.</p>
                    <br>
                    <a href="#" class="btn btn-primary btn-round" target="_blank">View Demo
                    </a>
                    <a href="https://nucleoapp.com/?ref=1712" class="btn btn-outline-primary btn-round" rel="nofollow"
                       target="_blank">Get Started</a>
                </div>
                <div class="col-lg-3 col-sm-2">
                    <div class="icons-nucleo icons-nucleo-right text-success">
                        <i class="first-right-icon ni ni-palette text-warning"></i>
                        <i class="second-right-icon ni ni-tie-bow text-primary"></i>
                        <i class="third-right-icon ni ni-pin text-info"></i>
                        <i class="fourth-right-icon ni ni-key-25 text-purple"></i>
                        <i class="fifth-right-icon ni ni-istanbul text-danger"></i>
                        <i class="sixth-right-icon ni ni-bus-front-12 text-warning"></i>
                        <i class="seventh-right-icon ni ni-image-02 text-success"></i>
                        <i class="eighth-right-icon ni ni-world text-info"></i>
                        <i class="ninth-right-icon ni ni-puzzle-10 text-primary"></i>
                        <i class="tenth-right-icon ni ni-atom text-default"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <footer class="footer">
        <div class="container">
            <div class="row row-grid align-items-center mb-5">
                <div class="col-lg-6">
                    <h3 class="text-primary font-weight-light mb-2">Thank you for supporting us!</h3>
                    <h4 class="mb-0 font-weight-light">Let's get in touch on any of these platforms.</h4>
                </div>
                <div class="col-lg-6 text-lg-center btn-wrapper">
                    <button target="_blank" href="https://twitter.com/creativetim" rel="nofollow"
                            class="btn btn-icon-only btn-twitter rounded-circle" data-toggle="tooltip"
                            data-original-title="Follow us">
                        <span class="btn-inner--icon"><i class="fa fa-twitter"></i></span>
                    </button>
                    <button target="_blank" href="https://www.facebook.com/CreativeTim/" rel="nofollow"
                            class="btn-icon-only rounded-circle btn btn-facebook" data-toggle="tooltip"
                            data-original-title="Like us">
                        <span class="btn-inner--icon"><i class="fab fa-facebook"></i></span>
                    </button>
                    <button target="_blank" href="https://dribbble.com/creativetim" rel="nofollow"
                            class="btn btn-icon-only btn-dribbble rounded-circle" data-toggle="tooltip"
                            data-original-title="Follow us">
                        <span class="btn-inner--icon"><i class="fa fa-dribbble"></i></span>
                    </button>
                    <button target="_blank" href="https://github.com/creativetimofficial" rel="nofollow"
                            class="btn btn-icon-only btn-github rounded-circle" data-toggle="tooltip"
                            data-original-title="Star on Github">
                        <span class="btn-inner--icon"><i class="fa fa-github"></i></span>
                    </button>
                </div>
            </div>
            <hr>
            <div class="row align-items-center justify-content-md-between">
                <div class="col-md-6">
                    <div class="copyright">
                        &copy; <span id="year"></span> <a href="#" target="_blank"> {{config('app.name')}}</a>.
                    </div>
                </div>
                <div class="col-md-6">
                    <ul class="nav nav-footer justify-content-end">
                        <li class="nav-item">
                            <a href="https://saanapay.ng" class="nav-link" target="_blank">Saanapay</a>
                        </li>
                        <li class="nav-item">
                            <a href="https://saanapay.ng/#about_us" class="nav-link" target="_blank">About Us</a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link" target="_blank">License</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</div>
<!--   Core JS Files   -->


<script>
    document.getElementById('year').textContent = new Date().getFullYear();
</body>

</html>
