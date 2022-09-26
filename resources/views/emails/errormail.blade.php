@extends('layouts.app')

@section('content')
    @include('layouts.navigation')
    <section class="my-10">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 my-auto">
                    <h1 class="display-1 text-bolder text-gradient text-danger">{!!$subject!!}</h1>
                    <h2>Erm 😢 !. Sorry to disturb you  {{$name}} !</h2>
                    <p class="lead">{!!$content!!}</p>
                    <a href="{{config('app.url')}}" role="button" class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0">Go to HomePage</a>
                </div>
                <div class="col-lg-6 my-auto position-relative">
                    <img class="w-100 position-relative" src="{{asset('assets/img/illustrations/error-404.png')}}" alt="404-error">
                </div>
            </div>
        </div>
    </section>
@endsection

