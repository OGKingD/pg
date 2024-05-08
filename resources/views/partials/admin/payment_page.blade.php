 <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="apple-touch-icon" sizes="80x80"
              href="{{asset('assets/img/saanapay.png')}}">
        <link rel="icon" type="image/png"
              href="{{asset('assets/img/saanapay.png')}}">
        <title>
            {{  $title ?? config('app.name', 'Laravel') }}
        </title>


        <link id="pagestyle" href="{{asset('assets/css/soft-ui-dashboard.min.css')}}" rel="stylesheet" />

        <style>
            body{
                font-family: 'Open Sans', SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, Courier New, monospace;
                font-style: normal;
                font-weight: 600;
                font-stretch: 100%;
            }
            .async-hide {
                opacity: 0 !important
            }
        </style>


        @livewireStyles
        <script src="{{asset('assets/js/plugins/jquery.js')}}"></script>

        <script src="{{asset('assets/js/core/bootstrap.min.js')}}"></script>
        <!-- Latest BS-Select compiled and minified CSS/JS -->
        <link rel="stylesheet" href="{{asset('assets/css/bootstrap-select.min.css')}}">
        <script src="{{asset('assets/js/core/bootstrap-select.min.js')}}"></script>


        <script src="{{asset('assets/js/core/axios.js')}}"></script>

        <script src="{{asset('assets/js/plugins/sweetalert.min.js')}}"></script>
        <script src="{{asset('assets/js/fontawesome.js')}}"></script>

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




