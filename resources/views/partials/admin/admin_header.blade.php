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
        <script src="{{asset('assets/js/plugins/jquery.js')}}"></script>

        <script src="{{asset('assets/js/core/popper.min.js')}}"></script>
        <script src="{{asset('assets/js/core/bootstrap.min.js')}}"></script>
        <!-- Latest BS-Select compiled and minified CSS/JS -->
        <link rel="stylesheet" href="{{asset('assets/css/bootstrap-select.min.css')}}">
        <script src="{{asset('assets/js/core/bootstrap-select.min.js')}}"></script>

        <script src="{{asset('assets/js/jquery.datatables.js')}}"></script>



        <script src="{{asset('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
        <script src="{{asset('assets/js/plugins/smooth-scrollbar.min.js')}}"></script>
        <script src="{{asset('assets/js/core/axios.js')}}"></script>


        <script src="{{asset('assets/js/plugins/dragula/dragula.min.js')}}"></script>
        <script src="{{asset('assets/js/plugins/chartjs.min.js')}}"></script>
        <script src="{{asset('assets/js/plugins/countup.min.js')}}"></script>
        <script src="{{asset('assets/js/plugins/sweetalert.min.js')}}"></script>
        <script src="{{asset('assets/js/plugins/flatpickr.min.js')}}"></script>

        <script src="{{asset('assets/js/fontawesome.js')}}"></script>
        <script src="{{asset('assets/js/soft-ui-dashboard.min.js')}}"></script>
        <script src="{{asset('assets/js/app.js')}}"></script>



        @livewireScripts


        @if(session()->has('status'))
            <script>
                salert('Info!', "{{session('status')}} !", 'info')
            </script>
        @endif
        @if(session()->has('success'))
            <script>
                salert("Success", "{{session()->get('success')}}", "success");
            </script>
        @endif

        <script>
            if (document.querySelector('.datepicker')) {
                flatpickr('.datepicker');
            }
        </script>



    </head>




