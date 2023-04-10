@include('partials.admin.admin_header')

<body class="g-sidenav-show  bg-gray-100">

@include('partials.merchant_sidebar')

{{--//main content here --}}
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

    @include('partials.merchant_navbar')

    <div class="container-fluid py-4">

        @yield('content')

    </div>


    {{--            //footer goes here--}}
    @include('partials.admin.admin_footer')

</main>


@include('partials.scripts')

@yield('scripts')

</body>
