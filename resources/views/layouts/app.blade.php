<!DOCTYPE html>
<html lang="en">
@include('partials.header')

<body>
<main class="main-content mt-0">

    @yield('content')
</main>


@yield('scripts')
@livewireScripts
<script src="{{asset('assets/js/core/popper.min.js')}}"></script>
<script src="{{asset('assets/js/core/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/js/core/axios.js')}}"></script>
<script src="{{asset('assets/js/plugins/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/smooth-scrollbar.min.js')}}"></script>

<script src="{{asset('assets/js/plugins/dragula/dragula.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/jkanban/jkanban.js')}}"></script>

<script>

    function salert(title, text, icon) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
        });
    }
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>


<script src="{{asset('assets/js/soft-ui-dashboard.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/sweetalert.min.js')}}"></script>
@if(session()->has('error'))
    <script>
        salert("Error!", "{{session('error')}}!", "error");
    </script>
@endif
@if(session()->has('info'))
    <script>
        salert("Info!", "{{session('info')}}!", "info");
    </script>
@endif
@if(session()->has('success'))
    <script>
        salert("Success!", "{{session('success')}}!", "success");
    </script>
@endif

</body>



</html>
