@extends('layouts.app')

@section('content')
    @include('layouts.navigation')
    <main class="main-content  mt-0">
        <section class="my-10">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 my-auto">
                        <h1 class="display-1 text-bolder text-gradient text-danger">Error 404</h1>
                        <h2>Erm. Invoice not found</h2>
                        <p class="lead">{!! $message !!}</p>
                        <a href="mailto:support@saanapay.ng" role="button"class="btn bg-gradient-dark mt-4">Contact Support</a>
                    </div>
                    <div class="col-lg-6 my-auto position-relative">
                        <img class="w-100 position-relative" src="{{asset('assets/img/illustrations/error-404.png')}}" alt="404-error">
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
