
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

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet"/>

    <link href="{{asset('assets/css/nucleo-icons.css')}}" rel="stylesheet"/>
    <link href="{{asset('assets/css/nucleo-svg.css')}}" rel="stylesheet"/>

    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <link id="pagestyle" href="{{asset('assets/css/soft-ui-dashboard.min.css')}}" rel="stylesheet"/>

    <style>
        .async-hide {
            opacity: 0 !important
        }
    </style>


    <!-- Styles -->
    @livewireStyles


</head>




