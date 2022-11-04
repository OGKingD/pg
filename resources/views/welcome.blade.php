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
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

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
    <section class="section-info">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="info text-left">
                        <div class="icon icon-lg icon-shape icon-shape-primary shadow rounded-circle">
                            <i class="ni ni-user-run"></i>
                        </div>
                        <h6 class="info-title text-uppercase text-primary pl-0">Huge number of components</h6>
                        <p class="description opacity-8">Every element that you need in a product comes built in as a
                            component. All components fit perfectly with each other and can take variations in
                            colour.</p>
                        <a href="/" class="text-primary">More about us
                            <i class="ni ni-bold-right text-primary"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="info text-left" style="margin-top:-50px;">
                        <div class="icon icon-lg icon-shape icon-shape-success shadow rounded-circle">
                            <i class="ni ni-atom"></i>
                        </div>
                        <h6 class="info-title text-uppercase text-success pl-0">Multi-Purpose Sections</h6>
                        <p class="description opacity-8">Putting together a page has never been easier than matching
                            together sections. From team presentation to pricing options, you can easily customise and
                            built your pages.</p>
                        <a href="/" class="text-primary">Learn about our products
                            <i class="ni ni-bold-right text-primary"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="info text-left">
                        <div class="icon icon-lg icon-shape icon-shape-warning shadow rounded-circle">
                            <i class="ni ni-istanbul"></i>
                        </div>
                        <h6 class="info-title text-uppercase text-warning pl-0">Multiple Example Pages</h6>
                        <p class="description opacity-8">If you want to get inspiration or just show something directly
                            to your clients, you can jump start your development with our pre-built example pages.</p>
                        <a href="/" class="text-primary">Check our documentation
                            <i class="ni ni-bold-right text-primary"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-basic-components">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-10 mb-md-5">
                    <h1 class="display-3">Basic Elements<span
                            class="text-primary"> The core elements of your website</span></h1>
                    <p class="lead">We re-styled every Bootstrap 4 element to match the Argon Design System style. All
                        the Bootstrap 4 components that you need in a development have been re-design with the new look.
                        Besides the numerous basic elements, we have also created additional classes. All these items
                        will help you take your project to the next level.</p>
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
    <section class="section-cards mb-5">
        <div class="content-center">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-5 col-md-10 col-12 mx-auto text-center">
                        <h1 class="display-3">Unconventional cards<span
                                class="text-primary"> One card for every problem</span></h1>
                        <p class="lead">
                            We love cards and everybody on the web seems to.
                            We have gone above and beyond with options for you to organise your information.
                            From cards designed for blog posts, to product cards or user profiles,
                            you will have many options to choose from. All the cards follow the material
                            principles and have a design that stands out.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mt-md-5 order-md-2 order-lg-1">
                    <div class="image-container">
                        <img class="img shadow rounded img-comments w-100"
                             src="{{asset('assets/img/presentation-page/content-2.png')}}" alt="">
                        <img class="img shadow rounded img-blog mt-5 w-100"
                             src="{{asset('assets/img/presentation-page/content-1.png')}}" alt="">
                    </div>
                </div>
                <div class="col-lg-6 mx-auto order-md-1">
                    <div class="section-description">
                        <h1 class="display-3">Content Areas<span
                                class="text-danger"> For Areas that Need More Space</span></h1>
                        <p class="lead">We took into consideration multiple use cases and came up with some specific
                            areas for you. If you need elements such as tables, comments, description areas, tabs and
                            many others, we've got you covered. They're beautiful and easy to use for the end user
                            navigating your website. </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-sections bg-secondary">
        <div class="container">
            <div class="col-md-8 mx-auto">
                <div class="section-description text-center">
                    <h2 class="display-2">Sections you will love</h2>
                    <p class="lead"> Build pages in no time using pre-made sections! From headers to footers, you will
                        be able to choose the best combination for your project. We have created multiple sections for
                        you to put together and customise into pixel perfect example pages.</p>
                    <a href="/" target="_blank" class="btn btn-primary btn-round">View All Sections</a>
                </div>
            </div>
        </div>
    </section>
    <section class="section-patterns">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-md-10 col-12 mx-auto text-center align">
                    <h1 class="display-3">Wonderful Patterns<span class="text-primary"> Different styles, colors and emotions</span>
                    </h1>
                    <p class="lead">
                        Devices mockups, Branding mockups, UI/UX Tools, Illustrations and much more. Free and premium.
                        Use Paaatterns together with powerful design system and speed up your workflow
                    </p>
                    <a href="https://www.ls.graphics/?status=accepted&expires=1574163072&seller=15046&affiliate=102023&link=1681&p_tok=05cf15f3-a34b-4dd4-aa6b-a4e8652ed45b"
                       target="_blank" rel="nofollow" class="btn btn-primary">View more</a>
                </div>
                <div class="col-lg-6 col-md-12">
                    <img class="w-50 pattern-1 shadow" src="{{asset('assets/img/presentation-page/layer-1.jpg')}}" alt="">
                    <img class="w-50 pattern-2 shadow" src="{{asset('assets/img/presentation-page/layer-2.jpg')}}" alt="">
                    <img class="w-50 pattern-3 shadow" src="{{asset('assets/img/presentation-page/layer-3.jpg')}}" alt="">
                    <img class="w-50 pattern-4 shadow" src="{{asset('assets/img/presentation-page/layer-4.jpg')}}" alt="">
                </div>
            </div>
        </div>
    </section>
    <section class="section-free-demo bg-secondary skew-separator">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-12">
                    <div class="section-description">
                        <h3 class="display-3">Free Demo</h3>
                        <p class="lead mb-4">Do you want to test and see the benefits of this Design System before
                            purchasing it? You can give the demo version a try. It features enough basic components for
                            you to get a feel of the design and also test the quality of the code. Get it free on
                            GitHub!</p>
                        <div class="github-buttons">
                            <a href="https://github.com/creativetimofficial/argon-design-system" target="_blank"
                               rel="nofollow" class="btn btn-primary btn-round">View Demo on Github</a>
                            <div class="github-button">
                                <a class="github-button"
                                   href="https://github.com/creativetimofficial/ct-argon-design-system-pro"
                                   rel="nofollow" data-icon="octicon-star" data-size="large"
                                   data-show-count="true">Star</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-12">
                    <div class="github-background-container">
                        <i class="fab fa-github"></i>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 pt-5">
                    <div class="card card-pricing card-background">
                        <div class="card-body">
                            <h2 class="card-title text-primary text-left ml-2">Free Demo</h2>
                            <ul>
                                <li class="text-left"><strong>70</strong> Components</li>
                                <li class="text-left"><strong>3</strong> Example Pages</li>
                                <li class="text-left">
                                    <div class="badge badge-circle badge-danger">
                                        <i class="fas fa-times text-white"></i>
                                    </div>
                                    Uncoventional cards
                                </li>
                                <li class="text-left">
                                    <div class="badge badge-circle badge-danger">
                                        <i class="fas fa-times text-white"></i>
                                    </div>
                                    Sections
                                </li>
                                <li class="text-left">
                                    <div class="badge badge-circle badge-danger">
                                        <i class="fas fa-times text-white"></i>
                                    </div>
                                    Photoshop for Prototype
                                </li>
                                <li class="text-left">
                                    <div class="badge badge-circle badge-danger">
                                        <i class="fas fa-times text-white"></i>
                                    </div>
                                    Premium Support
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 pt-5">
                    <div class="card card-pricing card-background">
                        <div class="card-body">
                            <h2 class="card-title text-primary text-left ml-2">PRO Version</h2>
                            <ul>
                                <li class="text-left"><strong>1100+</strong> Components</li>
                                <li class="text-left"><strong>17</strong> Example Pages</li>
                                <li class="text-left">
                                    <div class="badge badge-circle badge-success">
                                        <i class="ni ni-check-bold text-white"></i>
                                    </div>
                                    Uncoventional cards
                                </li>
                                <li class="text-left">
                                    <div class="badge badge-circle badge-success">
                                        <i class="ni ni-check-bold text-white"></i>
                                    </div>
                                    Sections
                                </li>
                                <li class="text-left">
                                    <div class="badge badge-circle badge-success">
                                        <i class="ni ni-check-bold text-white"></i>
                                    </div>
                                    Photoshop for Prototype
                                </li>
                                <li class="text-left">
                                    <div class="badge badge-circle badge-success">
                                        <i class="ni ni-check-bold text-white"></i>
                                    </div>
                                    Premium Support
                                </li>
                            </ul>
                        </div>
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
                    <h2 class="display-2">Custom Icons</h2>
                    <p class="lead">
                        Argon Design System PRO comes with 100 custom demo icons made by our friends from <a
                            href="https://nucleoapp.com/?ref=1712" target="_blank" rel="nofollow">NucleoApp</a>. The
                        official package contains over 20.729 icons which are looking great in combination with Argon
                        Design System PRO. Make sure you check all of them and use those that you like the most.
                    </p>
                    <br>
                    <a href="#" class="btn btn-primary btn-round" target="_blank">View Demo
                        Icons</a>
                    <a href="https://nucleoapp.com/?ref=1712" class="btn btn-outline-primary btn-round" rel="nofollow"
                       target="_blank">View All Icons</a>
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
    <section class="section-features bg-secondary">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 col-md-12 pt-5">
                    <div class="info info-horizontal">
                        <div class="icon icon-shape icon-shape-warning rounded-circle text-white">
                            <i class="ni ni-money-coins text-warning"></i>
                        </div>
                        <div class="description pl-4 pt-2">
                            <h5 class="title">Save Time & Money</h5>
                            <p>Creating your design from scratch with dedicated designers can be very expensive.Using
                                Argon Design System PRO you don't have to worry about customising the basic Bootstrap 4
                                design or its components.</p>
                        </div>
                    </div>
                    <div class="info info-horizontal">
                        <div class="icon icon-shape icon-shape-info rounded-circle text-white">
                            <i class="ni ni-bold text-info"></i>
                        </div>
                        <div class="description pl-4 pt-2">
                            <h5 class="title">Bootstrap 4 & Flexbox</h5>
                            <p>It is built over Bootstrap 4, it's fully responsive and has all the benefits of the
                                flexbox for the layout, grid system and components. This is a huge advantage when you
                                work with columns.</p>
                        </div>
                    </div>
                    <div class="info info-horizontal">
                        <div class="icon icon-shape icon-shape-danger rounded-circle text-white">
                            <i class="ni ni-paper-diploma text-danger"></i>
                        </div>
                        <div class="description pl-4 pt-2">
                            <h5 class="title">Fast Prototype</h5>
                            <p>Using Argon Design System PRO you can create hundreds of components combinations within
                                seconds and present them to your client. You just need to change some classes and
                                colors.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="image-container">
                        <img class="w-100" src="{{asset('assets/img/presentation-page/ipad.png')}}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-testimonials mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8 ml-auto mr-auto text-center">
                    <h2 class="display-2 mb-5">Trusted by 880,000+ People</h2>
                    <p class="lead">The UI Kits, Templates and Dashboards that we've created are used by <strong
                            class="text-primary">880,000+ web developers</strong> in over <strong class="text-primary">1,500,000
                            Web Projects</strong>. This is what some of them think:</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-8 mx-auto">
                    <div id="carouselExampleIndicatoru" class="carousel slide">
                        <ol class="carousel-indicators">
                            <li data-target="#carouselExampleIndicatoru" data-slide-to="0" class="active"></li>
                            <li data-target="#carouselExampleIndicatoru" data-slide-to="1"></li>
                            <li data-target="#carouselExampleIndicatoru" data-slide-to="2"></li>
                        </ol>
                        <div class="carousel-inner" role="listbox">
                            <div class="carousel-item active justify-content-center">
                                <div class="card card-testimonial card-plain">
                                    <div class="card-avatar">
                                        <a href="#pablo">
                                            <img class="img img-raised rounded"
                                                 src="https://s3.amazonaws.com/creativetim_bucket/photos/154001/thumb.JPG?1501184024" alt=""/>
                                        </a>
                                    </div>
                                    <div class="card-body text-center">
                                        <p class="card-description">"Awesome Design and very well organized code
                                            structure! Also, it contains numerous elements using which achieving the
                                            perfect or required template can be done with ease. Great job!"
                                        </p>
                                        <h4 class="card-title">Stefan</h4>
                                        <h6 class="category text-muted">Web Designer</h6>
                                        <div class="card-footer">
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item justify-content-center">
                                <div class="card card-testimonial card-plain">
                                    <div class="card-avatar">
                                        <a href="#pablo">
                                            <img class="img img-raised rounded"
                                                 src="https://s3.amazonaws.com/uifaces/faces/twitter/chadengle/128.jpg" alt=""/>
                                        </a>
                                    </div>
                                    <div class="card-body text-center">
                                        <p class="card-description">"It looks great and its somewhat futuristics cant
                                            wait to use it on a project here in nigeria i'm sure it would put me ahead..
                                            I cant wait to hv enough money to buy ur product."
                                        </p>
                                        <h4 class="card-title">Mr. Bones</h4>
                                        <h6 class="category text-muted">Web Designer</h6>
                                        <div class="card-footer">
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item justify-content-center">
                                <div class="card card-testimonial card-plain">
                                    <div class="card-avatar">
                                        <a href="#pablo">
                                            <img class="img img-raised rounded"
                                                 src="https://s3.amazonaws.com/creativetim_bucket/photos/123124/thumb.?1480480048"/>
                                        </a>
                                    </div>
                                    <div class="card-body text-center">
                                        <p class="card-description">"Everything is perfect. Codes are really organized.
                                            It's easy to edit for my own purposes. It's great that it is built on top of
                                            Bootstrap 4."<br><br>
                                        </p>
                                        <h4 class="card-title">DOĞA</h4>
                                        <h6 class="category text-muted">Web Developer</h6>
                                        <div class="card-footer">
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                            <i class="fa fa-star text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicatoru" role="button"
                   data-slide="prev">
                    <i class="ni ni-bold-left"></i>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicatoru" role="button"
                   data-slide="next">
                    <i class="ni ni-bold-right"></i>
                </a>
            </div>
        </div>
    </section>
    <div class="section section-pricing" id="sectionBuy">
        <div class="container">
            <div class="row our-clients">
                <div class="col-lg-3 col-md-6 col-6">
                    <img class="w-50" src="assets/img/presentation-page/harvard.jpg" alt=""/>
                </div>
                <div class="col-lg-3 col-md-6 col-6">
                    <img class="w-50" src="assets/img/presentation-page/microsoft.jpg" alt=""/>
                </div>
                <div class="col-lg-3 col-md-6 col-6">
                    <img class="w-50" src="assets/img/presentation-page/vodafone.jpg" alt=""/>
                </div>
                <div class="col-lg-3 col-md-6 col-6">
                    <img class="w-50" src="assets/img/presentation-page/stanford.jpg" alt=""/>
                </div>
            </div>
        </div>
    </div>
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
                        &copy; 2020 <a href="#" target="_blank">Creative Tim</a>.
                    </div>
                </div>
                <div class="col-md-6">
                    <ul class="nav nav-footer justify-content-end">
                        <li class="nav-item">
                            <a href="#" class="nav-link" target="_blank">Creative Tim</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" target="_blank">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a href="https://www.creative-tim.com/blog" class="nav-link" target="_blank">Blog</a>
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
    // Carousel
    new Glide('.presentation-cards', {
        type: 'carousel',
        startAt: 0,
        focusAt: 2,
        perTouch: 1,
        perView: 5
    }).mount();
    $('#twitter').sharrre({
        share: {
            twitter: true
        },
        enableHover: false,
        enableTracking: false,
        buttons: {
            twitter: {}
        },
        click: function (api, options) {
            api.simulateClick();
            api.openPopup('twitter');
        },
        template: '<i class="fab fa-twitter"></i>',
        url: 'http://demos.creative-tim.com/blk-design-system-pro/index.html'
    });

    $('#facebook').sharrre({
        share: {
            facebook: true
        },
        buttons: {
            facebook: {}
        },

        enableHover: false,
        enableTracking: false,
        click: function (api, options) {
            api.simulateClick();
            api.openPopup('facebook');
        },
        template: '<i class="fab fa-facebook-square"></i>',
        url: ' http://demos.creative-tim.com/blk-design-system-pro/index.html'
    });
</script>
<script src="{{asset('assets/js/core/popper.min.js')}}"></script>
<script src="{{asset('assets/js/core/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/js/core/axios.js')}}"></script>
<script src="{{asset('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/smooth-scrollbar.min.js')}}"></script>

<script src="{{asset('assets/js/plugins/dragula/dragula.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/jkanban/jkanban.js')}}"></script>
</body>

</html>
