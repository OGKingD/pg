@extends('layouts.app')
@include('layouts.navigation')


@section('content')
    <section>
        <div class="page-header min-vh-100">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                        <div class="card card-plain py-lg-3">
                            <div class="card-body text-center">
                                <h4 class="mb-0 font-weight-bolder">{{auth()->user()->first_name . " ". auth()->user()->last_name }}</h4>
                                <p class="mb-4">
                                    Thanks for signing up! Could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                                </p>

                                <div class="row">
                                    <div class="col">
                                        <form method="POST" action="{{ route('verification.send') }}">
                                            @csrf

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-lg w-100 bg-gradient-primary mb-0">Resend Verification Email</button>
                                            </div>
                                        </form>

                                    </div>
                                    <div class="col">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf

                                            <button type="submit" class="btn btn-lg w-100 bg-gradient-danger mb-0">Logout</button>

                                        </form>
                                    </div>
                                </div>



                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                        <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center">
                            <img src="{{asset('assets/img/shapes/pattern-lines.svg')}}" alt="pattern-lines" class="position-absolute opacity-4 start-0">
                            <div class="position-relative">
                                <img class="max-width-500 w-100 position-relative z-index-2" src="{{asset('assets/img/illustrations/dark-lock-ill.png')}}" alt="dark-lock">
                            </div>
                            <h4 class="mt-5 text-white font-weight-bolder">"{{$author}}"</h4>
                            <p class="text-white">{{$quote}}</p>
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection



