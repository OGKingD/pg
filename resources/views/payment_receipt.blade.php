<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76"
          href="https://demos.creative-tim.com/soft-ui-dashboard-pro/assets/img/apple-icon.png">
    <link rel="icon" type="image/png"
          href="https://demos.creative-tim.com/soft-ui-dashboard-pro/assets/img/favicon.png">
    <title>
        {{  $title ?? config('app.name', 'Laravel') }}
    </title>


    <link rel="canonical" href="https://www.creative-tim.com/product/soft-ui-dashboard-pro"/>

    <meta name="keywords"
          content="creative tim, html dashboard, html css dashboard, web dashboard, bootstrap 5 dashboard, bootstrap 5, css3 dashboard, bootstrap 5 admin, soft ui dashboard bootstrap 5 dashboard, frontend, responsive bootstrap 5 dashboard, soft design, soft dashboard bootstrap 5 dashboard">
    <meta name="description"
          content="Soft UI Dashboard PRO is a beautiful Bootstrap 5 admin dashboard with a large number of components, designed to look beautiful, clean and organized. If you are looking for a tool to manage dates about your business, this dashboard is the thing for you.">

    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@creativetim">
    <meta name="twitter:title" content="Soft UI Dashboard PRO by Creative Tim">
    <meta name="twitter:description"
          content="Soft UI Dashboard PRO is a beautiful Bootstrap 5 admin dashboard with a large number of components, designed to look beautiful, clean and organized. If you are looking for a tool to manage dates about your business, this dashboard is the thing for you.">
    <meta name="twitter:creator" content="@creativetim">
    <meta name="twitter:image"
          content="https://s3.amazonaws.com/creativetim_bucket/products/487/thumb/opt_sdp_thumbnail.jpg">

    <meta property="fb:app_id" content="655968634437471">
    <meta property="og:title" content="Soft UI Dashboard PRO by Creative Tim"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url"
          content="https://demos.creative-tim.com/soft-ui-dashboard-pro/pages/dashboards/default.html"/>
    <meta property="og:image"
          content="https://s3.amazonaws.com/creativetim_bucket/products/487/thumb/opt_sdp_thumbnail.jpg"/>
    <meta property="og:description"
          content="Soft UI Dashboard PRO is a beautiful Bootstrap 5 admin dashboard with a large number of components, designed to look beautiful, clean and organized. If you are looking for a tool to manage dates about your business, this dashboard is the thing for you."/>
    <meta property="og:site_name" content="Creative Tim"/>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet"/>

    <link href="{{asset('assets/css/nucleo-icons.css')}}" rel="stylesheet"/>
    <link href="{{asset('assets/css/nucleo-svg.css')}}" rel="stylesheet"/>


    <link id="pagestyle" href="{{asset('assets/css/datatables.css')}}" rel="stylesheet"/>
    <link id="pagestyle" href="{{asset('assets/css/soft-ui-dashboard.min.css')}}" rel="stylesheet"/>

    <style>
        .async-hide {
            opacity: 0 !important
        }
    </style>


    @livewireStyles


</head>

<body>
<!-- Navbar -->
<nav id="navbar-main" class="navbar navbar-main navbar-expand-lg  navbar-dark  py-2" >
    <div class="container">
        <a class=" min-vw-60" href="/">
            <img src="{{asset('assets/img/saanapay.png')}}" style="width: 55%" alt="">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar_global"
                aria-controls="navbar_global" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>
<!-- End Navbar -->

<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header p-3 pb-0">
                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <h6>Order Details</h6>
                            <p class="text-sm mb-0">
                                Order no. <b>{{$invoice->invoice_no}}</b>
                            </p>
                            <p class="text-sm">
                                Code: <b>{{$invoice->transaction->transaction_ref}}</b>
                            </p>
                        </div>
                        <a href="javascript:;" class="btn bg-gradient-secondary ms-auto mb-0">Invoice</a>
                    </div>
                </div>
                <div class="card-body p-3 pt-0">
                    <hr class="horizontal dark mt-0 mb-4">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="d-flex">
                                <div
                                    class="bg-gradient-primary shadow text-center border-radius-lg avatar avatar-xxl me-3">
                                    <i class="fas fa-file-invoice-dollar fa-3x " aria-hidden="true"></i>
                                </div>

                                <div>
                                    <h6 class="text-lg mb-0 mt-2">{{$invoice->name}}</h6>
                                    <p class="text-sm mb-3"> Order was completed
                                        {{\Carbon\Carbon::now()->longAbsoluteDiffForHumans($invoice->updated_at)}} ago.
                                    </p>
                                    <span class="badge badge-sm
                                        @if($invoice->status === "successful") bg-gradient-success @endif
                                        @if($invoice->status === "failed") bg-gradient-danger @endif
                                        ">{{$invoice->status}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-12 my-auto text-end">
                            <a href="javascript:;" class="btn bg-gradient-info mb-0">Contact Us</a>
                            <p class="text-sm mt-2 mb-0">Do you like the product? Leave us a review <a
                                    href="javascript:;">here</a>.</p>
                        </div>
                    </div>
                    <hr class="horizontal dark mt-4 mb-4">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12">
                            <h6 class="mb-3">Track order</h6>
                            <div class="timeline timeline-one-side">
                                <div class="timeline-block mb-3">
<span class="timeline-step">
<i class="ni ni-bell-55 text-secondary"></i>
</span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Order received</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">22 DEC 7:20
                                            AM</p>
                                    </div>
                                </div>
                                <div class="timeline-block mb-3">
<span class="timeline-step">
<i class="ni ni-html5 text-secondary"></i>
</span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Generate order id
                                            #1832412</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">22 DEC 7:21
                                            AM</p>
                                    </div>
                                </div>
                                <div class="timeline-block mb-3">
<span class="timeline-step">
<i class="ni ni-cart text-secondary"></i>
</span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Order transmited to
                                            courier</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">22 DEC 8:10
                                            AM</p>
                                    </div>
                                </div>
                                <div class="timeline-block mb-3">
<span class="timeline-step">
<i class="ni ni-check-bold text-success text-gradient"></i>
</span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">Order delivered</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">22 DEC 4:54
                                            PM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-6 col-12">
                            <h6 class="mb-3">Payment details</h6>
                            <div
                                class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                                <img class="w-10 me-3 mb-0" src="../../../assets/img/logos/mastercard.png"
                                     alt="logo">
                                <h6 class="mb-0">
                                    ****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;7852</h6>
                                <button type="button"
                                        class="btn btn-icon-only btn-rounded btn-outline-secondary mb-0 ms-2 btn-sm d-flex align-items-center justify-content-center ms-auto"
                                        data-bs-toggle="tooltip" data-bs-placement="bottom" title=""
                                        data-bs-original-title="We do not store card details">
                                    <i class="fas fa-info" aria-hidden="true"></i>
                                </button>
                            </div>
                            <h6 class="mb-3 mt-4">Billing Information</h6>
                            <ul class="list-group">
                                <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-3 text-sm">Oliver Liam</h6>
                                        <span class="mb-2 text-xs">Company Name: <span
                                                class="text-dark font-weight-bold ms-2">Viking Burrito</span></span>
                                        <span class="mb-2 text-xs">Email Address: <span
                                                class="text-dark ms-2 font-weight-bold">oliver@burrito.com</span></span>
                                        <span class="text-xs">VAT Number: <span
                                                class="text-dark ms-2 font-weight-bold">FRB1235476</span></span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-3 col-12 ms-auto">
                            <h6 class="mb-3">Order Summary</h6>
                            <div class="d-flex justify-content-between">
<span class="mb-2 text-sm">
 Product Price:
</span>
                                <span class="text-dark font-weight-bold ms-2">$90</span>
                            </div>
                            <div class="d-flex justify-content-between">
<span class="mb-2 text-sm">
Delivery:
</span>
                                <span class="text-dark ms-2 font-weight-bold">$14</span>
                            </div>
                            <div class="d-flex justify-content-between">
<span class="text-sm">
Taxes:
</span>
                                <span class="text-dark ms-2 font-weight-bold">$1.95</span>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
<span class="mb-2 text-lg">
Total:
</span>
                                <span class="text-dark text-lg ms-2 font-weight-bold">$105.95</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--            //footer goes here--}}
    @include('partials.admin.admin_footer')
</div>
@include('partials.scripts')
</body>
</html>



