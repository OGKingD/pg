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
                                <h4 class="font-weight-bolder">Password Update</h4>
                            </div>
                            <div class="card-body">

                                <form role="form" action="{{route('password.update')}}" method="post">

                                    @csrf
                                    <div class="d-none">
                                        <label>Email</label>
                                        @error('email')
                                        <div class="alert alert-danger alert-dismissible text-white" role="alert">
                                            {{$message}}
                                            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        @enderror
                                        <div class="mb-3">
                                            <input type="text" required name="token" class="form-control" value="{{old('token', $request->token)}}" placeholder="Email" aria-label="Email" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAjZJREFUWAntV7vKIlEMPt5dOy/NID9WIoKKCAsiWIqFFmLra4j2IoKPsL24YKWFD7Ai+ASClY2yNioWgjckfzLrCeN46XSKNXDmJPkyyWdOZnCE+CdfuP3G9RcXvHhRDapFNVUhZYXr1YX1+anmlwUvv3D9xPVu+YEFFRNeqCXKu6tf6i2IALXGMDEbVvlS+EPg/+tAuVwWmUyGR+/tHSgUCiKdThtHgCs/egosFouIRCIiEAjoY9n2+XwikUgIp9PJPq1itVpFLBYTfr9f636o8zs6GAzCeDwGKb1eD1wuF+OYARqNBpzPZzVkuVxCNpu9wuPxOEynU5kCWq0W2Gw2jhkOh1Cr1dgmVmyMRiO+USrNZpNxPD/p5n29XoPb7VZjTCYTTCYTxqRSqVQ4h54AD6HX6xXJZPKmTblcjn35fJ51qWBxkUqlVBM7KEKhkIR41+Zg50VhAofDQSBjPS72+z37tDo7Udntdqopdy1G+qP7CGMC2+1WdLtd8l0JniHb7Xb7hiSet8CjU2Nms5kYDAYcLxVtDunT7nw+Ho8HOp0OYDdgs9lAvV4Hs9nMON4EpVIJ5vO5Oog0M+Fw+ApXFAX6/T4cj0dYrVZQrVavcP0MEJGrALJpammg7mHS53A4nuJ2u/0uridgxYQ3cjqdbnx6B83MM8EOPIMZ4xlgjwHK3VYhj5f4i8UiRKNRzv35S2b4DHwIUAcWBjx5suSCCPyRlgG7WtvQj1P5o4kEfTK//fP8G/4x2UymA12DAAAAAElFTkSuQmCC&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;">
                                        </div>

                                    </div>

                                    <label>Email</label>
                                    @error('email')
                                    <div class="alert alert-danger alert-dismissible text-white" role="alert">
                                        {{$message}}
                                        <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    @enderror
                                    <div class="mb-3">
                                        <input type="email" required name="email" class="form-control" value="{{old('email', $request->email)}}" placeholder="Email" aria-label="Email" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAjZJREFUWAntV7vKIlEMPt5dOy/NID9WIoKKCAsiWIqFFmLra4j2IoKPsL24YKWFD7Ai+ASClY2yNioWgjckfzLrCeN46XSKNXDmJPkyyWdOZnCE+CdfuP3G9RcXvHhRDapFNVUhZYXr1YX1+anmlwUvv3D9xPVu+YEFFRNeqCXKu6tf6i2IALXGMDEbVvlS+EPg/+tAuVwWmUyGR+/tHSgUCiKdThtHgCs/egosFouIRCIiEAjoY9n2+XwikUgIp9PJPq1itVpFLBYTfr9f636o8zs6GAzCeDwGKb1eD1wuF+OYARqNBpzPZzVkuVxCNpu9wuPxOEynU5kCWq0W2Gw2jhkOh1Cr1dgmVmyMRiO+USrNZpNxPD/p5n29XoPb7VZjTCYTTCYTxqRSqVQ4h54AD6HX6xXJZPKmTblcjn35fJ51qWBxkUqlVBM7KEKhkIR41+Zg50VhAofDQSBjPS72+z37tDo7Udntdqopdy1G+qP7CGMC2+1WdLtd8l0JniHb7Xb7hiSet8CjU2Nms5kYDAYcLxVtDunT7nw+Ho8HOp0OYDdgs9lAvV4Hs9nMON4EpVIJ5vO5Oog0M+Fw+ApXFAX6/T4cj0dYrVZQrVavcP0MEJGrALJpammg7mHS53A4nuJ2u/0uridgxYQ3cjqdbnx6B83MM8EOPIMZ4xlgjwHK3VYhj5f4i8UiRKNRzv35S2b4DHwIUAcWBjx5suSCCPyRlgG7WtvQj1P5o4kEfTK//fP8G/4x2UymA12DAAAAAElFTkSuQmCC&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;">
                                    </div>

                                    <label>Password</label>
                                    @error('password')
                                    <div class="alert alert-danger alert-dismissible text-white" role="alert">
                                        {{$message}}
                                        <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    @enderror

                                    <div class="mb-3">
                                        <input type="password" required name="password" class="form-control" placeholder="Password" aria-label="Password" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKRJREFUWAnNl0tsVGUUxzvTTlslZUaCloZHY6BRFkp9sDBuqgINpaBp02dIDImwKDG6ICQ8jBYlhg0rxUBYEALTpulMgBlqOqHRDSikJkZdGG0CRqAGUuwDovQ1/s7NPTffnTu3zMxGvuT2vP7n8Z3vu+dOi4r+5xUoJH8sFquamZmpTqfTVeIfCARGQ6HQH83NzaP5xsu5gL6+vuVzc3NdJN1Kkhd8Ev1MMYni4uJjra2tt3wwLvUjCxgYGFg8Pj7+MV5dPOUub3/hX0zHIpFId0NDw6Q/jO4tZOzv76+Znp6+AOb5TBw7/YduWC2Hr4J/IhOD/GswGHy7vb39tyw2S+VbAC1/ZXZ29hKoiOE8RrIvaPE5WvyjoS8CX8sRvYPufYpZYtjGS0pKNoD/wdA5bNYCCLaMYMMEWq5IEn8ZDof3P6ql9pF9jp8cma6bFLGeIv5ShdISZUzKzqPIVnISp3l20caTJsaPtwvc3dPTIx06ziZkkyvY0FnoW5l+ng7guAWnpAI5w4MkP6yy0GQy+dTU1JToGm19sqKi4kBjY+PftmwRYn1ErEOq4+i2tLW1DagsNGgKNv+p6tj595nJxUbyOIF38AwipoSfnJyMqZ9SfD8jxlWV5+fnu5VX6iqgt7d3NcFeUiN0n8FbLEOoGkwdgY90dnbu7OjoeE94jG9wd1aZePRp5AOqw+9VMM+qLNRVABXKkLEWzn8S/FtbdAhnuVQE7LdVafBPq04pMYawO0OJ+6XHZkFcBQA0J1xKgyhlB0EChEWGX8RulsgjvOjEBu+5V+icWOSoFawuVwEordluG28oSCmXSs55SGSCHiXhmDzC25ghMHGbdwhJr6sAdpnyQl0FYIyoEX5CeYOuNHg/NhvGiUUxVgfV2VUAxjtqgPecp9oKoE4sNnbX9HcVgMH8nD5nAoWnKM/5ZmKyySRdq3pCmDncR4DxOwVC64eHh0OGLOcur1Vey46xUZ3IcVl5oa4OlJaWXgQwJwZyhUdGRjqE14VtSnk/mokhxnawiwUvsZmsX5u+rgKamprGMDoA5sKhRCLxpDowSpsJ8vpCj2AUPzg4uIiNfKIyNMkH6Z4hF3k+RgTYz6vVAEiKq2bsniZIC0nTtvMVMwBzoBT9tKkTHp8Ak1V8dTrOE+NgJs7VATESTH5WnVAgfHUqlXK6oHpJEI1G9zEZH/Du16leqHyS0UXBNKmeOMf5NvyislJPB8RAFz4g8IuwofLy8k319fUP1EEouw7L7mC3kUTO1nn3sb02MTFxFpsz87FfJuaH4pu5fF+reDz+DEfxkI44Q0ScSbyOpDGe1RqMBN08o+ha0L0JdeKi/6msrGwj98uZMeon1AGaSj+elr9LwK9IkO33n8cN7Hl2vp1N3PcYbUXOBbDz9bwV1/wCmXoS3+B128OPD/l2LLg8l9APXVlZKZfzfDY7ehlQv0PPQDez6zW5JJdYOXdAwHK2dGIv7GH4YtHJIvEOvvunLCHPPzl3QOLKTkl0hPbKaDUvlTU988xtwfMqQBPQ3m/4mf0yBVlDCSr/CRW0CipAMnGzb9XU1NSRvIX7kSgo++Pg9B8wltxxbHKPZgAAAABJRU5ErkJggg==&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;">
                                    </div>

                                    <label>Password Confirmation</label>
                                    @error('password_confirmation')
                                    <div class="alert alert-danger alert-dismissible text-white" role="alert">
                                        {{$message}}
                                        <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    @enderror
                                    <div class="mb-3">
                                        <input type="password" required name="password_confirmation" class="form-control" placeholder="Password" aria-label="Password" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAABKRJREFUWAnNl0tsVGUUxzvTTlslZUaCloZHY6BRFkp9sDBuqgINpaBp02dIDImwKDG6ICQ8jBYlhg0rxUBYEALTpulMgBlqOqHRDSikJkZdGG0CRqAGUuwDovQ1/s7NPTffnTu3zMxGvuT2vP7n8Z3vu+dOi4r+5xUoJH8sFquamZmpTqfTVeIfCARGQ6HQH83NzaP5xsu5gL6+vuVzc3NdJN1Kkhd8Ev1MMYni4uJjra2tt3wwLvUjCxgYGFg8Pj7+MV5dPOUub3/hX0zHIpFId0NDw6Q/jO4tZOzv76+Znp6+AOb5TBw7/YduWC2Hr4J/IhOD/GswGHy7vb39tyw2S+VbAC1/ZXZ29hKoiOE8RrIvaPE5WvyjoS8CX8sRvYPufYpZYtjGS0pKNoD/wdA5bNYCCLaMYMMEWq5IEn8ZDof3P6ql9pF9jp8cma6bFLGeIv5ShdISZUzKzqPIVnISp3l20caTJsaPtwvc3dPTIx06ziZkkyvY0FnoW5l+ng7guAWnpAI5w4MkP6yy0GQy+dTU1JToGm19sqKi4kBjY+PftmwRYn1ErEOq4+i2tLW1DagsNGgKNv+p6tj595nJxUbyOIF38AwipoSfnJyMqZ9SfD8jxlWV5+fnu5VX6iqgt7d3NcFeUiN0n8FbLEOoGkwdgY90dnbu7OjoeE94jG9wd1aZePRp5AOqw+9VMM+qLNRVABXKkLEWzn8S/FtbdAhnuVQE7LdVafBPq04pMYawO0OJ+6XHZkFcBQA0J1xKgyhlB0EChEWGX8RulsgjvOjEBu+5V+icWOSoFawuVwEordluG28oSCmXSs55SGSCHiXhmDzC25ghMHGbdwhJr6sAdpnyQl0FYIyoEX5CeYOuNHg/NhvGiUUxVgfV2VUAxjtqgPecp9oKoE4sNnbX9HcVgMH8nD5nAoWnKM/5ZmKyySRdq3pCmDncR4DxOwVC64eHh0OGLOcur1Vey46xUZ3IcVl5oa4OlJaWXgQwJwZyhUdGRjqE14VtSnk/mokhxnawiwUvsZmsX5u+rgKamprGMDoA5sKhRCLxpDowSpsJ8vpCj2AUPzg4uIiNfKIyNMkH6Z4hF3k+RgTYz6vVAEiKq2bsniZIC0nTtvMVMwBzoBT9tKkTHp8Ak1V8dTrOE+NgJs7VATESTH5WnVAgfHUqlXK6oHpJEI1G9zEZH/Du16leqHyS0UXBNKmeOMf5NvyislJPB8RAFz4g8IuwofLy8k319fUP1EEouw7L7mC3kUTO1nn3sb02MTFxFpsz87FfJuaH4pu5fF+reDz+DEfxkI44Q0ScSbyOpDGe1RqMBN08o+ha0L0JdeKi/6msrGwj98uZMeon1AGaSj+elr9LwK9IkO33n8cN7Hl2vp1N3PcYbUXOBbDz9bwV1/wCmXoS3+B128OPD/l2LLg8l9APXVlZKZfzfDY7ehlQv0PPQDez6zW5JJdYOXdAwHK2dGIv7GH4YtHJIvEOvvunLCHPPzl3QOLKTkl0hPbKaDUvlTU988xtwfMqQBPQ3m/4mf0yBVlDCSr/CRW0CipAMnGzb9XU1NSRvIX7kSgo++Pg9B8wltxxbHKPZgAAAABJRU5ErkJggg==&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;">
                                    </div>


                                    <div class="text-center">
                                        <button type="submit" class="btn btn-lg bg-gradient-primary btn-lg w-100 mt-4 mb-0">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 d-lg-flex d-none  my-auto pe-0 position-absolute end-0 text-center justify-content-center flex-column">
                        <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center">
                            <img src="{{asset('assets/img/shapes/pattern-lines.svg')}}" alt="pattern-lines" class="position-absolute opacity-4 start-0">
                            <div class="position-relative">
                                <img class="max-width-500 w-100 position-relative z-index-2" src="{{asset('assets/img/illustrations/dark-lock-ill.png')}}" alt="chat-img">
                            </div>
                            <h6 class=" mt-2 text-dark">{{$quote}}</h6>
                            <h4 class="text-dark font-weight-bolder">"{{$author}}"</h4>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

@endsection

