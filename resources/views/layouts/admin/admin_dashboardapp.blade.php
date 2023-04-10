@include('partials.admin.admin_header')

<body class="g-sidenav-show  bg-gray-100">

@include('partials.admin.admin_sidebar')

{{--//main content here --}}
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

    @include('partials.admin.admin_navbar')

    <div class="container-fluid py-4">

        @yield('content')

    </div>


    {{--            //footer goes here--}}
    @include('partials.admin.admin_footer')

</main>




@yield('scripts')

</body>





