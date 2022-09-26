@extends('layouts.app')

@section('content')
    @include('layouts.navigation')

    <section class="">
        <div class="page-header min-vh-100">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                        <div class="card card-plain">
                            <div class="card-body px-lg-5 py-lg-5 text-center">
                                <div class="text-center text-muted mb-4">
                                    <h2>2-Step Verification</h2>
                                </div>
                                <form action="{{route('2fa.verify')}}" method="POST">
                                    @csrf
                                    <div class="row gx-2 gx-sm-3">
                                        <div class="col">
                                            <div class="form-group">
                                                <input type="text" name="otp[]" class="form-control form-control-lg"
                                                       maxlength="1" autocomplete="off" autocapitalize="off">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>
                                                    <input type="text" name="otp[]" class="form-control form-control-lg"
                                                           maxlength="1" autocomplete="off" autocapitalize="off">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>
                                                    <input type="text" name="otp[]" class="form-control form-control-lg"
                                                           maxlength="1" autocomplete="off" autocapitalize="off">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>
                                                    <input type="text" name="otp[]" class="form-control form-control-lg"
                                                           maxlength="1" autocomplete="off" autocapitalize="off">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>
                                                    <input type="text" name="otp[]" class="form-control form-control-lg"
                                                           maxlength="1" autocomplete="off" autocapitalize="off">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label>
                                                    <input type="text" name="otp[]" class="form-control form-control-lg"
                                                           maxlength="1" autocomplete="off" autocapitalize="off">
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn bg-gradient-primary w-100 text-white">Verify
                                            code
                                        </button>
                                        <span class="text-white text-sm">Haven't received it?<a class="text-info" href="javascript:resend2fa() ;"> Resend a new code</a>.</span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                        <div
                            class="position-relative  h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center"
                            style="background-color: #2ccae3">
                            <img src="{{asset('assets/img/shapes/pattern-lines.svg')}}" alt="pattern-lines"
                                 class="position-absolute opacity-4 start-0">
                            <div class="position-relative">
                                <img class="max-width-500 w-100 position-relative z-index-2"
                                     src="{{asset('assets/img/illustrations/danger-chat-ill.png')}}" alt="chart-ill">
                            </div>
                            <h4 class="mt-5 text-white font-weight-bolder">"Attention is the new currency"</h4>
                            <p class="text-white">The more effortless the writing looks, the more effort the writer
                                actually put into the process.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('scripts')
<script>
    function resend2fa() {
        Swal.fire({
            title: 'Processing Please Wait!',
            html: '<span class="spinner-border text-primary"></span>',
            allowEscapeKey: false,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEnterKey: false,
        });
        axios.post('{{route('2fa.resend')}}')
            .then(function (response) {
                console.log(response.data);
                if (response.data === 1){
                    salert("Code Sent", "A verification code 📧️  has been sent! Please check your mail 📧 and if not in your mail, kindly check your spam folder!",'success')
                }
            })
            .catch(function (error) {
                console.log(error);
                salert("Error!", "Something Unexpected Occurred! Please contact support!", "error")

            });
        Swal.close();

    }
</script>
@endsection



