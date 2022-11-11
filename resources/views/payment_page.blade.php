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


    <div class="container-fluid py-3 col-lg-8 mx-auto">
        <div class="card">
            @livewire('payment-page', ['invoice' => $invoice,])
        </div>

    </div>


    {{--            //footer goes here--}}
    @include('partials.admin.admin_footer')
    @include('partials.card_gateway')

</main>

</body>



