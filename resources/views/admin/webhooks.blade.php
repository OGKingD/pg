@extends('layouts.admin.admin_dashboardapp')

@section('content')
    <style>
        .successful {
            color: green;
        }

        .failed {
            color: red;
        }

        .pending {
            color: #e0d003;
        }

    </style>
    <div class="container">
        <div class="container mt--6">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col text-center">
                                    <h3 class="mb-0"><i class="fa fa-file-invoice-dollar"></i> Webhooks</h3>
                                </div>

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
                                                    <input id="payment_provider_id" type="text" placeholder="Payment Ref"
                                                           class="form-control @error('payment_provider_id') is-invalid @enderror"
                                                           name="payment_provider_id" value="{{ old('payment_provider_id') }}"
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

                    </div>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="d-flex ">

                            <div class="p-2 mr-3 ">
                                <label for=""></label><select name="perpage" id="" class=" form-control-sm"
                                                              onchange="changeLocation(this.value)">
                                    <option value=""></option>
                                    <option value="30" @if($perPage === 30) selected @endif> 30</option>
                                    <option value="50" @if($perPage === 50) selected @endif> 50</option>
                                    <option value="100" @if($perPage === 100) selected @endif> 100</option>
                                    <option value="150" @if($perPage === 150) selected @endif> 150</option>
                                    <option value="300" @if($perPage === 300) selected @endif> 300</option>
                                    <option value="1000" @if($perPage === 1000) selected @endif> 1000</option>
                                </select>
                            </div>


                        </div>
                        <div class="tab-pane  active" id="allTransactions">
                            {{--                        table for all Transactions--}}
                            <div class="table-responsive">
                                <div>
                                    {{$webhooks->withQueryString()->links()}}
                                </div>
                                <table id="allTransactionsTable" class="table table-borderless table-hover table-striped">
                                    <thead class="table-info">
                                    <tr>

                                        <th> Reference</th>
                                        <th> User Reference</th>
                                        <th>Count</th>
                                        <th>Payload</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($webhooks as $webhook)
                                        <tr>

                                            <td>{{$webhook->payment_provider_id}}</td>
                                            <td>{{$webhook->user_ref}}</td>
                                            <td>{{$webhook->count}}</td>
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
                                                    <h1 class="display-4 text-info text-center ">No Webhooks(s) found
                                                        !</h1>
                                                </div>
                                            </td>
                                        </tr>

                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                            {{$webhooks->withQueryString()->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection






