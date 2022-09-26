@extends('layouts.app')

@section('content')
    @include('layouts.navigation')
    <section class="my-10">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 my-auto">
                    <h1 class="display-1 text-bolder text-gradient text-info">{!!$subject!!}</h1>
                    <h2>Hi 👋  {{$name}} !</h2>
                    <p class="lead">{!!$content!!}</p>
                    @if($url)
                        <a href="{{$url}}" role="button" class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0"> {{$buttonMessage}}</a>
                    @endif
                </div>
                <div class="col-lg-6 my-auto position-relative">
                    <img class="w-100 position-relative" src="{{asset('assets/img/illustrations/chat.png')}}" alt="404-error">
                </div>
            </div>
        </div>
    </section>
@endsection

