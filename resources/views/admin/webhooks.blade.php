@extends('layouts.admin.admin_dashboardapp')

@section('content')

    <div class="container-fluid py-4 ">
        <div class="d-sm-flex justify-content-between">

            <div>
                <div class="page-header   position-relative m-3 border-radius-xl">
                    <img src="{{asset('assets/img/shapes/waves-white.svg')}}" alt="pattern-lines"
                         class="position-absolute opacity-6 start-0 top-0 w-100">

                </div>

                <h3> Webhooks </h3>

            </div>

        </div>
        <div class="container">
            <div class="card">
                <div class="card-body">

                    <div class="row ">
                        <div class="text-center col-md-6">
                            <h6 class="card-header">TOTAL TRANSACTIONS </h6>
                            <h2>{{$transactionCount}}</h2>
                        </div>

                    </div>

                </div>
            </div>


            <form action="" id="searchBox">
                <fieldset>
                    <h3 class="mb-0">Filter Webhooks By:</h3>

                    <div class="row">
                        <div class="col-lg-3 col-md-4 ">
                            <div class="form-group">
                                <label for="payment_provider_id" class="col-form-label text-md-right">
                                    {{__("Payment Ref")}}
                                </label>
                                <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                    <i class="fa fa-mobile-alt" style="font-size: 15px;"></i>
                                                </span>
                                    <input id="payment_provider_id" type="text"
                                           placeholder="Payment Ref"
                                           class="form-control @error('payment_provider_id') is-invalid @enderror"
                                           name="payment_provider_id"
                                           value="{{ old('payment_provider_id') }}"
                                    >

                                </div>
                            </div>
                        </div>


                        <div class="col-lg-3 col-md-4">
                            <div class="form-group">
                                <label for="created_at" class="col-form-label text-md-right">
                                    {{__("Start Date")}}
                                </label>
                                <div class="input-group input-group-merge input-group-alternative mb-3">
                                            <span class="input-group-text">
                                                    <i class="fa fa-calendar-alt" style="font-size: 15px;"></i>
                                                </span>
                                    <input id="created_at" type="date" placeholder="yyyy-mm-dd"
                                           class="datechk form-control @error('created_at') is-invalid @enderror"
                                           name="created_at" value=""
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4">
                            <div class="form-group">
                                <label for="created_at" class="col-form-label text-md-right">
                                    {{__("End Date")}}
                                </label>
                                <div class="input-group input-group-merge input-group-alternative mb-3">
                                            <span class="input-group-text">
                                                    <i class="fa fa-calendar-alt" style="font-size: 15px;"></i>
                                            </span>
                                    <input id="created_at" type="date" placeholder="yyyy-mm-dd"
                                           class="datechk form-control @error('created_at') is-invalid @enderror"
                                           name="end_date" value=""
                                    >
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col text-right">
                        <a href="#" class="btn btn-danger" onclick="clearSearchFields()">Reset</a>
                        <button type="submit" class="btn btn-success"> Search</button>
                    </div>
                </fieldset>
            </form>
            <hr>

        </div>




        <div class="row min-vh-90">
            <div class="card table-responsive">
                <div class="dataTable-wrapper dataTable-loading sortable  fixed-columns">
                    <div class="dataTable-container">
                        <table class="table table-flush " id="datatable-basic">

                            <thead class="table-info">

                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    data-sortable="">
                                    <a href="#" class="dataTable-sorter">Reference</a>
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    data-sortable="">
                                    <a href="#" class="dataTable-sorter">User Ref</a>
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    data-sortable="">
                                    <a href="#" class="dataTable-sorter">count</a>
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    data-sortable="">
                                    <a href="#" class="dataTable-sorter">Response</a>
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    data-sortable="">
                                    <a href="#" class="dataTable-sorter">Payload</a>
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    data-sortable="">
                                    <a href="#" class="dataTable-sorter">Created At</a>
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                    data-sortable="">
                                    <a href="#" class="dataTable-sorter">Updated At</a>
                                </th>
                            </tr>

                            </thead>
                            <tbody>
                            @forelse($webhooks as $webhook)
                                <tr>

                                    <td>{{$webhook->payment_provider_id}}</td>
                                    <td>{{$webhook->user_ref}}</td>
                                    <td>{{$webhook->count}}</td>
                                    <td>
                                        @foreach($webhook->response as $response)
                                            {!! json_encode($response)."<br>" !!}
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($webhook->payment_provider_message as $payload)
                                            {!! json_encode($payload)."<br>" !!}
                                        @endforeach
                                    </td>

                                    <td>{{$webhook->created_at}}</td>
                                    <td>{{$webhook->updated_at}}</td>

                                </tr>

                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="container p-3 my-3  ">
                                            <h1 class="display-4 text-info text-center ">No Webhooks(s)
                                                found
                                                !</h1>
                                        </div>
                                    </td>
                                </tr>

                            @endforelse

                            {{$webhooks->links()}}

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

        </div>


        @section('scripts')
            <style>
                .nav.nav-pills .nav-link.active {
                    background-color: #2ccae3;
                    color: white;
                }
            </style>

            @if(count($webhooks))
                <script>
                    $('#datatable-basic').DataTable({
                        paging: false,
                        ordering: true,
                        info: false,
                        fixedHeight: true,
                        searchable: true,

                    });
                </script>
            @endif

        @endsection


    </div>


@endsection





