@extends('layouts.merchant_dashboardapp')


@section('content')
    <div class="container-fluid py-4">
        <div class="card">

            <div class="row">
                <div class="col-md-2">
                    <div class="border-right">
                        <div class="text-center pt-5 pb-3"><h4>PAY WITH</h4></div>
                        <hr>

                        <!-- Nav pills -->
                        <ul class="nav nav-pills flex-column " role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="pill" href="#home">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#menu1">Menu 1</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" href="#menu2">Menu 2</a>
                            </li>
                        </ul>
                    </div>

                </div>

                <div class="col-md-10">
                    <!-- Tab panes -->
                    <div class="container">
                        <ul class="nav mt-4 pb-3 border-bottom">
                            <li class="nav-item">
                                <a href="/">
                                    <img class="max-width-200" src="{{asset('assets/img/saanapay.png')}} "
                                         alt="SAANAPAY BRAND IMAGE" style="max-width: 120px!important;">
                                </a>
                            </li>
                            <li class="mx-auto">

                            </li>
                            <li class="nav-item mr-3 ">
                            <span>
                                email here
                            </span>
                                <br>
                                <span class="text-primary">
                                total here
                            </span>
                            </li>
                        </ul>

                    </div>

                    <!-- Tab panes -->

                    <div class="tab-content min-vh-55">
                        <div id="home" class="container tab-pane active"><br>
                            <h3>HOME</h3>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                incididunt ut labore et dolore magna aliqua.</p>
                        </div>
                        <div id="menu1" class="container tab-pane fade"><br>
                            <h3>Menu 1</h3>
                            <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex
                                ea commodo consequat.</p>
                        </div>
                        <div id="menu2" class="container tab-pane fade"><br>
                            <h3>Menu 2</h3>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                                laudantium, totam rem aperiam.</p>
                        </div>
                    </div>
                </div>
                <div class="justify-content-center">
                    <hr>
                    <p class="mb-2 text-center"><i class="fa fa-lock"></i> Secured by <b>Saanapay</b>
                    </p>
                </div>
            </div>
        </div>

    </div>



@endsection


