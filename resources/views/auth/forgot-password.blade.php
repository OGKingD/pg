@extends('layouts.app')

@section('content')
    @include('layouts.navigation')

    <section class="mt-1">
        <div class="page-header min-vh-100">
            <div class="container">

                <div class="row">
                    <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                        <div class="card card-plain">
                            <div class="card-header pb-0 text-start">
                                <h4 class="font-weight-bolder">Password Reset</h4>
                                <p class="mb-0">Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                                </p>
                            </div>
                            <div class="card-body">

                                <form role="form" action="{{route('password.email')}}" method="post">

                                    @csrf

                                    <div class="mb-3">
                                        @error('email')
                                        <div class="alert alert-danger alert-dismissible text-white" role="alert">
                                            {{$message}}
                                            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        @enderror
                                        <input type="email" name="email" class="form-control form-control-lg" placeholder="Email" aria-label="Email">
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                        <div class="position-relative  h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center">
                            <img src="{{asset('assets/img/shapes/pattern-lines.svg')}}" alt="pattern-lines" class="position-absolute opacity-4 start-0">
                            <div class="position-relative">
                                <img class="max-width-500 w-100 position-relative z-index-2" src="{{asset('assets/img/illustrations/forgot-password.png')}}" alt="chat-img">
                            </div>
                            <h4 class="mt-5 text-white font-weight-bolder">"{{$author}}"</h4>
                            <p class="text-white">{{$quote}}</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

@endsection

