<script src="{{asset('assets/js/plugins/jquery.js')}}"></script>
<script src="{{asset('assets/js/jquery.datatables.js')}}"></script>


<script src="{{asset('assets/js/core/popper.min.js')}}"></script>
<script src="{{asset('assets/js/core/bootstrap.min.js')}}"></script>
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
