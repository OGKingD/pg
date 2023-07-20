<div class="container-fluid py-4 ">
    <div>

        <div class="page-header   position-relative m-3 border-radius-xl">
            <img src="{{asset('assets/img/shapes/waves-white.svg')}}" alt="pattern-lines"
                 class="position-absolute opacity-6 start-0 top-0 w-100">

        </div>



        <!-- Tab panes -->
        <div class="card">
            <div class="card-body" id="transactionsCountDashboard">

                <div class="row ">
                    <div class="text-center col-md-6">
                        <h6 class="card-header">TOTAL TRANSACTIONS </h6>
                        <h2>{{$transactionCount}}</h2>
{{--                        <div class="col text-right">--}}
{{--                            <div class="d-flex justify-content-center">--}}
{{--                                <button class="btn btn-info mx-3" onclick="summarizeTransaction()"> Live Refresh</button>--}}
{{--                            </div>--}}
{{--                        </div>--}}

                    </div>
                    <div class="col-md-6">
                        <form role="form"  id="transactionSearchBox" onsubmit="event.preventDefault(); summarizeTransaction(); ">
                            @csrf
                            <fieldset class="py-md-4">
                                <div class="">
                                    <div class="form-group">
                                        <label for="created_at" class="col-form-label text-md-right">
                                            {{__("Start Date")}}
                                        </label>
                                        <div class="input-group input-group-merge input-group-alternative mb-3">
                                            <span class="input-group-text">
                                                    <i class="fa fa-calendar-alt" style="font-size: 15px;"></i>
                                                </span>
                                            <input id="updated_at" type="date" placeholder="yyyy-mm-dd" wire:model.defer="startDate"
                                                   class="datechk form-control @error('updated_at') is-invalid @enderror"
                                                   name="updated_at"
                                            >
                                        </div>
                                    </div>
                                </div>


                                <div class="col text-right">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success mx-3" wire:change="summarizeTransactions"> Search</button>
                                        <button class="btn btn-danger mx-3" onclick="resetPage()"> Reset</button>
                                    </div>
                                </div>
                            </fieldset>



                        </form>

                    </div>

                </div>



            </div>
        </div>
        <hr>


    </div>



    <div class="row min-vh-90">
        <div class="card table-responsive" wire:poll.keep-alive.{{$pollingTime}}="summarizeTransactions">
            <div class="dataTable-wrapper dataTable-loading sortable  fixed-columns">
                <div class="dataTable-container">
                    <table class="table table-flush " id="datatable-basic">
                        <thead class="thead-light">
                        <tr>


                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Merchant Name</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter"> Service Name </a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Gateway</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Total Transactions</a>
                            </th>
                            <th class="text-uppercase  bg-gradient-success  "
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Successful</a>
                            </th>
                            <th class="text-uppercase  bg-gradient-warning  "
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Initiated</a>
                            </th>
                            <th class="text-uppercase  bg-gradient-danger  "
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Failed</a>
                            </th>


                        </tr>
                        </thead>
                        <tbody>
                        @php
                              $tTotal = 0;
                                $sumtTotal = 0;
                                $sumSuccess = 0;
                                $sumFailed = 0;
                                $sumPending = 0;
                        @endphp
                        @if(count($transactions))
                            @foreach($transactions as $k=>$val)
                                @php

                                    $channel = strtolower(str_replace(" ", "_", $val->gateway->name));
                                    $tTotal = $val->{$channel."_successful_bills"} + $val->{$channel."_pending_bills"} + $val->{$channel."_failed_bills"};
                                    $sumtTotal += $tTotal;
                                    $sumSuccess += $val->{$channel."_successful_bills"};
                                    $sumFailed += $val->{$channel."_failed_bills"};
                                    $sumPending += $val->{$channel."_pending_bills"};
                                @endphp

                                @if( isset( $val->gateway) )
                                    @if($val->gateway->name === "Bank Transfer")
                                        @php
                                            $trnRef = $val->bank_transfer_ref
                                        @endphp
                                    @endif
                                    @if($val->gateway->name === "Card")
                                        @php
                                            $trnRef = $val->flutterwave_ref
                                        @endphp
                                    @endif
                                    @if($val->gateway->name === "Remita")
                                        @php
                                            $trnRef = $val->remita_ref
                                        @endphp
                                    @endif
                                @endif


                                <tr>
                                    <td class="text-sm font-weight-normal">{{$val->user->first_name." ".$val->user->last_name }}</td>
                                    <td class="text-sm font-weight-normal">{{ $val->type }}</td>
                                    <td class="text-sm font-weight-normal">{{ $val->gateway->name??  "N/A"}}</td>
                                    <td class="text-sm font-weight-normal">{{ $tTotal}}</td>
                                    <td class="bg-success text-white">  {{number_format($val->{$channel."_successful_bills"})}}</td>
                                    <td class="bg-warning text-white">  {{number_format($val->{$channel."_pending_bills"})}}</td>
                                    <td class="bg-danger text-white">  {{number_format($val->{$channel."_failed_bills"})}}</td>


                                </tr>
                            @endforeach
                            {{ $transactions->withQueryString()->links() }}

                        @else
                            <tr>
                                <td colspan="7"  class="text-sm font-weight-normal">
                                    <div class="justify-content-center card card-plain">
                                        <div class="card-header text-center pb-0 text-start">
                                            <h2 class="font-weight-bolder">😢 No transaction Found!</h2>
                                        </div>

                                    </div>

                                </td>
                            </tr>

                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th class=" font-weight-bolder">Total</th>
                            <th class=" font-weight-bolder">  {{number_format($sumtTotal)}}</th>
                            <th class=" font-weight-bolder">  {{number_format($sumSuccess)}}</th>
                            <th class=" font-weight-bolder">  {{number_format($sumPending)}}</th>
                            <th class=" font-weight-bolder">  {{number_format($sumFailed)}}</th>
                        </tr>
                        </tfoot>

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


        <script>
            function resetPage() {
                event.preventDefault();
                $(':input','#transactionSearchBox')
                    .not(':button, :submit, :reset, :hidden')
                    .val('');
            }

            function summarizeTransaction() {
                sprocessing("Summarizing Report!");

                Livewire.emit("summarizeTransactions");
            }

            addEventListener("searchComplete",function () {
                Swal.close();
            })



            addEventListener("generatingReport", event => {
                sprocessing("Summarizing Report!");
            });



        </script>
        @if(count($transactions))
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
