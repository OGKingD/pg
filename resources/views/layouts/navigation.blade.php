<nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 w-100 shadow-none pt-0 navbar-transparent ">
    <div class="container">
        <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 "
           href="/">
            <img class="max-width-100" src="{{asset('assets/img/saanapay.png')}}" alt="SAANAPAY BRAND IMAGE"
                 style="max-width: 120px!important;">
        </a>
        <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse"
                data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon mt-2">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
            </span>
        </button>
        <div class="collapse navbar-collapse w-100 pt-3 pb-2 py-lg-0" id="navigation">
            <ul class="navbar-nav navbar-nav-hover mx-auto">

            </ul>
{{--            @if(request()->path() === "register")--}}
{{--                <ul class="navbar-nav right d-sm-block mx-2 ">--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{route('login')}}" class="btn   bg-gradient-primary  btn-round me-1">--}}
{{--                            Log-in--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--            @endif--}}

{{--            @if(request()->path() === "login")--}}
{{--                <ul class="navbar-nav right d-sm-block mx-2 my-1">--}}
{{--                    <li class="nav-item">--}}
{{--                        <a href="{{route('register')}}" class="btn  bg-gradient-primary  btn-round  me-1">--}}
{{--                            Register--}}
{{--                        </a>--}}
{{--                    </li>--}}
{{--                </ul>--}}
{{--            @endif--}}
        </div>
    </div>
</nav>





