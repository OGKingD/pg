@include('partials.admin.admin_header')

<body class="g-sidenav-show  bg-gray-100">


{{--//main content here --}}
<main class="main-content mt-0 max-height-vh-100 h-100 border-radius-lg ">

    <div class="container-fluid py-4">

        @yield('content')

    </div>

</main>


@yield('scripts')
@include('partials.admin.admin_footer')

</body>
