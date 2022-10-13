<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76"
          href="https://demos.creative-tim.com/soft-ui-dashboard-pro/assets/img/apple-icon.png">
    <link rel="icon" type="image/png"
          href="https://demos.creative-tim.com/soft-ui-dashboard-pro/assets/img/favicon.png">
    <title>
        {{  $title ?? config('app.name', 'Laravel') }}
    </title>


    <link rel="canonical" href="https://www.creative-tim.com/product/soft-ui-dashboard-pro" />

    <meta name="keywords" content="creative tim, html dashboard, html css dashboard, web dashboard, bootstrap 5 dashboard, bootstrap 5, css3 dashboard, bootstrap 5 admin, soft ui dashboard bootstrap 5 dashboard, frontend, responsive bootstrap 5 dashboard, soft design, soft dashboard bootstrap 5 dashboard">
    <meta name="description" content="Soft UI Dashboard PRO is a beautiful Bootstrap 5 admin dashboard with a large number of components, designed to look beautiful, clean and organized. If you are looking for a tool to manage dates about your business, this dashboard is the thing for you.">

    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@creativetim">
    <meta name="twitter:title" content="Soft UI Dashboard PRO by Creative Tim">
    <meta name="twitter:description" content="Soft UI Dashboard PRO is a beautiful Bootstrap 5 admin dashboard with a large number of components, designed to look beautiful, clean and organized. If you are looking for a tool to manage dates about your business, this dashboard is the thing for you.">
    <meta name="twitter:creator" content="@creativetim">
    <meta name="twitter:image" content="https://s3.amazonaws.com/creativetim_bucket/products/487/thumb/opt_sdp_thumbnail.jpg">

    <meta property="fb:app_id" content="655968634437471">
    <meta property="og:title" content="Soft UI Dashboard PRO by Creative Tim" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="https://demos.creative-tim.com/soft-ui-dashboard-pro/pages/dashboards/default.html" />
    <meta property="og:image" content="https://s3.amazonaws.com/creativetim_bucket/products/487/thumb/opt_sdp_thumbnail.jpg" />
    <meta property="og:description" content="Soft UI Dashboard PRO is a beautiful Bootstrap 5 admin dashboard with a large number of components, designed to look beautiful, clean and organized. If you are looking for a tool to manage dates about your business, this dashboard is the thing for you." />
    <meta property="og:site_name" content="Creative Tim" />

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

    <link href="{{asset('assets/css/nucleo-icons.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/css/nucleo-svg.css')}}" rel="stylesheet" />


    <link id="pagestyle" href="{{asset('assets/css/datatables.css')}}" rel="stylesheet" />
    <link id="pagestyle" href="{{asset('assets/css/soft-ui-dashboard.min.css')}}" rel="stylesheet" />

    <style>
        .async-hide {
            opacity: 0 !important
        }
    </style>


    @livewireStyles




</head>

<body>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">


    <div class="container-fluid py-4">
        <div class="card">

            <div class="row">
                <div class="col-md-2">
                    <div class="border-right mr-3" >
                        <div class="text-center pt-5 pb-3"><h4>PAY WITH</h4></div>
                        <hr>

                        <!-- Nav pills -->
                        <ul class="nav nav-pills flex-column " role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active mt-5 " data-bs-toggle="pill" href="#card">
                                    <i class="fa-duoton fa-credit-card ">
                                        &nbsp; Card
                                    </i>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link mt-5 " data-bs-toggle="pill" href="#remita">
                                    <i class="fa-solid fa-building ">
                                        &nbsp; Remita
                                    </i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link mt-5 " data-bs-toggle="pill" href="#banktransfer">
                                    <i class="fa-solid fa-school ">
                                        &nbsp; Bank Transfer
                                    </i>
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>

                <div class="col-md-10">
                    <!-- Tab panes -->
                    <div class="container">
                        <ul class="nav mt-4 pb-3 border-bottom">
                            <li class="nav-item">
                                <a href="/">
                                    <img class="max-width-200" src="{{asset('assets/img/saanapay.png')}} "
                                         alt="SAANAPAY BRAND IMAGE" style="max-width: 120px!important;">
                                </a>
                            </li>
                            <li class="mx-auto">

                            </li>
                            <li class="nav-item mr-3 ">
                            <span>
                                email here
                            </span>
                                <br>
                                <span class="text-primary">
                                total here
                            </span>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab panes -->

                    <div class="tab-content min-vh-55">
                        <div id="card" class="container tab-pane active"><br>
                            <h3>HOME</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                incididunt ut labore et dolore magna aliqua.</p>
                            <div id="genRRRstep1">
                                <div class="col-sm-3 mx-auto mt-4 ">
                                    <input type="button" class="btn-check" id="generateAcc">
                                    <label class="btn btn-lg btn-outline-secondary border-2 px-6 py-5" for="btncheck2"
                                           onclick="generateRRR()" >
                                        <i class="fa-brands fa-google-pay">

                                        </i>
                                    </label>
                                    <h6>Generate RRR </h6>
                                </div>
                            </div>

                        </div>
                        <div id="remita" class="container tab-pane fade"><br>
                            <h1 class="text-center"> Pay With Remita </h1>
                            <div class="row">
                                <div class="col-xl-5 col-lg-7 col-md-6 d-flex flex-column mx-lg-0 mx-auto">
                                    <div class="card card-plain">
                                        <div class="card-header pb-0 text-start">
                                            <h4 class="font-weight-bolder">INSTRUCTIONS</h4>
                                        </div>
                                        <div class="card-body">
                                            <ol >
                                                <li class="my-5 " id="step1">Click the Button to Generate RRR </li>
                                                <li class="step2">
                                                    <button data-toggle="collapse" data-target="#demo" class="btn btn-primary">Follow these Steps for Copy RRR to Clipboard » </button>
                                                </li>
                                                <template id="demo" class="collapse step2">
                                                    <ul>
                                                        <li class="my-5">Copy RRR and proceed to  <a href="  " role="button" class="btn btn-outline-success"> Remita website </a></li>
                                                        <li class="my-5"> Click Bills & Purchases </li>
                                                        <li class="my-5"> Click Pay RRR Invoice</li>
                                                        <li class="my-5"> Input the RRR you copied and proceed to make payment</li>
                                                    </ul>
                                                </template>

                                                <template class="text-center step2 my-4 "> OR </template>

                                                <li class="step2">
                                                    <a class="btn btn-success" id="rrrLink"  href="" >

                                                        <i class="fa fa-plane " style="font-size: 16px"> <b> Click me to take you to  Remita </b></i>
                                                    </a>
                                                </li>

                                            </ol>
                                        </div>
                                        <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                            <p class="mb-4 text-sm mx-auto">
                                                Don't have an account?
                                                <a href="{{route('register')}}" class="text-primary text-gradient font-weight-bold">Sign up</a> |

                                                <a href="{{route('password.request')}}" class="text-primary text-gradient font-weight-bold">Forgot Password</a>

                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-5 col-md-6 d-flex flex-column mx-lg-0 mx-auto">
                                    <div class="card card-plain">

                                        <div class="card-body">

                                            <hr class="mb-3">
                                            <button class="btn btn-success btn-lg btn-block" id="generateRRRButton"
                                                    onclick="generateRRR('')">Generate RRR</button>
                                            <div class=" mb-3 field-container" id="rrrBox">
                                                <h3 class="text-center">YOUR RRR IS</h3>
                                                <label for="rrr"></label><input class="form-control" id="rrr" type="text" readonly>


                                                <div class="row mt-2">
                                                    <div class="col-md-12 mb-2">
                                                        <button  onclick="copyTextToClipboard('rrr')" class="btn btn-info"><i class="fa fa-clipboard"> Copy RRR to ClipBoard</i></button>
                                                    </div>

                                                    <div class="col-md-12 mt-2">
                                                    </div>

                                                </div>
                                            </div>
                                            <hr class="mb-3">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div id="banktransfer" class="container tab-pane fade"><br>
                            <h3>Menu 2</h3>
                            <div id="genRRRstep1">
                                <div class="col-sm-3 mx-auto mt-4 ">
                                    <input type="button" class="btn-check" id="generateAcc">
                                    <label class="btn btn-lg btn-outline-secondary border-2 px-6 py-5" for="btncheck2"
                                           onclick="generateRRR()" >
                                        <i class="fas fa-google-pay">

                                        </i>
                                    </label>
                                    <h6>Generate RRR </h6>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="justify-content-center">
                    <hr>
                    <p class="mb-2 text-center"><i class="fa fa-lock"></i> Secured by <b>Saanapay</b>
                    </p>
                </div>
            </div>
        </div>

    </div>



    {{--            //footer goes here--}}
    @include('partials.admin.admin_footer')

</main>

</body>

@include('partials.card_gateway')

