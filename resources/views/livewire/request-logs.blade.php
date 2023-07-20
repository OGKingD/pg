<div class="container-fluid py-4 ">

    <div class="row min-vh-90">
        <div class="card ">
            <div class="container">
                <div class="container mt--6">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header border-0">
                                    <div class="row align-items-center">
                                        <div class="col text-center">
                                            <h3 class="mb-0"><i class="fa fa-file-invoice-dollar"></i> RequestLogs</h3>
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


                                    <form action="" id="apiRequestSearchBox">
                                        <fieldset>
                                            <h3 class="mb-0">Filter API REQUESTS By:</h3>

                                            <div class="row">

                                                <div class="col-lg-4 col-md-4">
                                                    <div class="form-group">
                                                        <label for="request_id"
                                                               class=" col-form-label text-md-right">{{ __('Request ID') }}</label>

                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-keyboard py-1" style="font-size: 18px;"></i>
                                                </span>
                                                            </div>
                                                            <input id ="request_id" type="text" class="form-control" name="request_id" wire:model="request_id">
                                                        </div>

                                                    </div>
                                                </div>


                                                <div class="col-lg-4 col-md-4">
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
                                                                   name="startdate" wire:model.lazy="startdate" >
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-4">
                                                    <div class="form-group">
                                                        <label for="enddate" class="col-form-label text-md-right">
                                                            {{__("End Date")}}
                                                        </label>
                                                        <div class="input-group input-group-merge input-group-alternative mb-3">
                                            <span class="input-group-text">
                                                    <i class="fa fa-calendar-alt" style="font-size: 15px;"></i>
                                            </span>
                                                            <input id="enddate" type="date" placeholder="yyyy-mm-dd"
                                                                   class="datechk form-control @error('created_at') is-invalid @enderror"
                                                                   name="end_date" wire:model.lazy="enddate"
                                                            >
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col text-right">
                                                <a href="#" class="btn btn-danger"
                                                   onclick="document.getElementById('apiRequestSearchBox').reset(); event.preventDefault()">Reset</a>
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
                                            <option value="30" @if($perPage == 30) selected @endif> 30</option>
                                            <option value="50" @if($perPage == 50) selected @endif> 50</option>
                                            <option value="100" @if($perPage == 100) selected @endif> 100</option>
                                            <option value="150" @if($perPage == 150) selected @endif> 150</option>
                                            <option value="300" @if($perPage == 300) selected @endif> 300</option>
                                            <option value="1000" @if($perPage == 1000) selected @endif> 1000</option>
                                        </select>
                                    </div>


                                </div>
                                <div class="tab-pane  active" id="allTransactions">
                                    {{--                        table for all Transactions--}}
                                    <div class="table-responsive">

                                        <table id="allTransactionsTable"
                                               class="table table-borderless table-hover table-striped">
                                            <thead class="table-info">
                                            <tr>

                                                <th> Reference</th>
                                                <th> User</th>
                                                <th>URL</th>
                                                <th>Payload</th>
                                                <th>Response</th>
                                                <th>Created At</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($requestlogs  as $requestlog)

                                                <tr>

                                                    <td>{{$requestlog['request_id']}}</td>
                                                    <td>{{$requestlog['user_id']}}</td>
                                                    <td>{{$requestlog['url']}}</td>
                                                    <td>
                                                        @foreach($requestlog['payload'] as $key => $resp)
                                                            {{ ++$key . ": ".json_encode($resp, JSON_THROW_ON_ERROR)}}
                                                            <br>

                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        @foreach($requestlog['response'] as $key => $resp)

                                                            {{++$key . ": ".json_encode($resp, JSON_THROW_ON_ERROR)}}
                                                            <br>
                                                        @endforeach
                                                    </td>
                                                    <td>{{$requestlog['created_at']}}</td>

                                                </tr>

                                            @empty
                                                <tr>
                                                    <td colspan="7">
                                                        <div class="container p-3 my-3  ">
                                                            <h1 class="display-4 text-info text-center ">No API Request(s) found !</h1>
                                                        </div>
                                                    </td>
                                                </tr>

                                            @endforelse
                                            </tbody>
                                        </table>
                                        {{$requestlogsCollection->withQueryString()->links()}}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    @section('scripts')
        <style>
            .table td, .table th {
                white-space: normal !important;
            }
        </style>
        <script>


            addEventListener('alertBox', function () {
                let type = event.detail.type;
                let message = event.detail.message;
                if (type === "processing") {
                    sprocessing("Fetching Transaction")
                }
                if (type === "success") {
                    salert("Success", message ?? "Transaction Fetched!", 'success')
                }
                if (type === "info") {
                    salert("Info", message, 'success')
                }
            })
            addEventListener("closeAlert", function () {
                Swal.close();
            })
        </script>
    @endsection


</div>
