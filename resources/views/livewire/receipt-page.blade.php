<div class="min-vh-90">

    <section>
        <div class="container">
            <div class="row">
                <div class="nonPrintable col-lg-8 col-md-7 mx-auto">
                    <form action="" id="searchForm">
                        <div class="card z-index-0 mt-2 mb-4 ">
                            <div class="card-header text-center pt-4 pb-1">
                                <h4 class="font-weight-bolder mb-1">Search Transactions </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label for="Ref" class=" col-form-label text-md-right">
                                            {{ __('Choose Search Via') }}
                                        </label>
                                        <div class="input-group mb-3">

                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-search py-1" style="font-size: 18px;"></i>
                                                </span>
                                            </div>

                                            <select name="searchVia" id="searchVia" title="Choose a Status"
                                                    data-style="btn border" class=" form-control"
                                                    wire:model.defer="searchVia" required>
                                                <option value=""></option>
                                                <option value="email">Email</option>
                                                <option value="merchant_transaction_ref">Tranx No</option>
                                            </select>

                                        </div>


                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label for="Ref" class=" col-form-label text-md-right" id="searchTitle">
                                            {{  empty(ucfirst($searchVia)) ? 'Ref' : ucfirst($searchVia) }}
                                        </label>
                                        <input type="text" id="searchTitleValue" wire:model.defer="searchViavalue"
                                               class="form-control" placeholder="" aria-label="">

                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn bg-gradient-dark btn-lg w-100 my-4 mb-2"
                                                onclick="searchTransactions()"><i class="fa fa fa-search py-1"
                                                                                  style="font-size: 18px;"> Search </i>
                                        </button>
                                    </div>
                                </div>


                            </div>
                        </div>

                    </form>
                </div>
                @if($hasTransactions)
                    <div class="col-lg-11 mx-auto">
                        <div class="card table-responsive">
                            <div class="dataTable-wrapper dataTable-loading sortable  fixed-columns">
                                <div class="dataTable-container">
                                    <table class="table table-flush " id="datatable-basic">
                                        <thead class="nonPrintable thead-light">
                                        {{ $transactions->withQueryString()->links() }}
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                data-sortable="">
                                                <a href="#" class="dataTable-sorter">#</a>
                                            </th>

                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                data-sortable="">
                                                <a href="#" class="dataTable-sorter">Merchant Ref</a>
                                            </th>

                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                data-sortable="">
                                                <a href="#" class="dataTable-sorter">Gateway</a>
                                            </th>

                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                data-sortable="">
                                                <a href="#" class="dataTable-sorter">Total</a>
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                data-sortable="">
                                                <a href="#" class="dataTable-sorter">Name</a>
                                            </th>

                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                data-sortable="">
                                                <a href="#" class="dataTable-sorter">Date</a>
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                                data-sortable="">
                                                <a href="#" class="dataTable-sorter"></a>
                                            </th>


                                        </tr>
                                        </thead>

                                        <tbody>

                                        @forelse($transactions as $k=>$val)
                                            @php
                                                $trnRef = ""
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
                                            <tr class="nonPrintable">
                                                <td class="text-sm font-weight-normal">{{++$k}}</td>
                                                <td class="text-sm font-weight-normal">{{$val->merchant_transaction_ref}}</td>
                                                <td class="text-sm font-weight-normal"
                                                    style="text-wrap: unset">{{ $val->gateway->name??  "N/A"}}</td>
                                                <td>&#{{nairaSymbol()}} {{number_format($val->total,'2','.',',')}}</td>
                                                <td class="text-sm font-weight-normal">{{$val->invoice->customer_name ?? "N/A"}}</td>

                                                <td>{{date("Y/m/d h:i:A", strtotime($val->updated_at))}}</td>
                                                <td>
                                                    <button type="button" class="btn btn-icon bg-gradient-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modal-open-receipt{{$val->merchant_transaction_ref}}">
                                                        Receipt
                                                    </button>
                                                </td>
                                            </tr>
                                            <div class="modal fade"
                                                 id="modal-open-receipt{{$val->merchant_transaction_ref}}" tabindex="-1"
                                                 aria-labelledby="modal-new-gateway"
                                                 aria-hidden="true" style="display: none;">
                                                <div class="modal-dialog modal-dialog-centered modal-md" role="document"
                                                     id="printPage">
                                                    <div class="modal-content">
                                                        <button type="button" class="btn-close text-dark"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>

                                                        <div class="modal-body p-0">
                                                            <div class="row container">
                                                                <div class="col-lg-4">
                                                                    <img class="max-width-200"
                                                                         src="{{asset('assets/img/saanapay.png')}} "
                                                                         alt="SAANAPAY BRAND IMAGE"
                                                                         style="max-width: 120px!important;">
                                                                </div>


                                                            </div>

                                                            <div class="card card-plain justify-content-center">


                                                                <div class="card-body">
                                                                    <div class="row text-center">
                                                                        <h3 class="text-uppercase text-center mt-3"
                                                                            style="font-size: 30px;">Payment Slip</h3>
                                                                    </div>
                                                                    <div class="row container">
                                                                        <ul class="list-group">
                                                                            <li class="list-group-item">Customer Name
                                                                                <span
                                                                                    class="float-end text-bolder "> {{$val->invoice->customer_name ?? "N/A"}} </span>
                                                                            </li>
                                                                            <li class="list-group-item">Customer Email
                                                                                <span
                                                                                    class="float-end text-bolder"> {{$val->invoice->customer_email  ?? "N/A"}} </span>
                                                                            </li>
                                                                            <li class="list-group-item">Transaction
                                                                                Number <span
                                                                                    class="float-end text-bolder"> {{$val->merchant_transaction_ref ?? "N/A"}} </span>
                                                                            </li>
                                                                            <li class="list-group-item">Item
                                                                                Description: <span
                                                                                    class="float-end text-bolder"> {{$val->type ?? "N/A"}} </span>
                                                                            </li>
                                                                            {{--                                                                                <li class="list-group-item">Item Amount:  <span class="float-end text-bolder"> &#{{nairaSymbol()}} {{number_format($val->amount,'2','.',',')}}</span> </li>--}}
                                                                            <li class="list-group-item">Payment Status:
                                                                                <span
                                                                                    class="float-end text-bolder"> {{ucfirst($val->status )?? "N/A"}} </span>
                                                                            </li>
                                                                            <li class="list-group-item">Payment Date:
                                                                                <span
                                                                                    class="float-end text-bolder"> {{date("Y/m/d h:i:A", strtotime($val->updated_at))}} </span>
                                                                            </li>

                                                                        </ul>

                                                                        <div class="col-xl-8">
                                                                            <ul class="list-unstyled float-end me-0">

                                                                                <li><span class="me-5">Fee:</span>
                                                                                    <b>
                                                                                        &#{{nairaSymbol()}} {{number_format($val->fee,'2','.',',')}} </b>
                                                                                </li>
                                                                                <li>
                                                                                    <span class="float-start"
                                                                                          style="margin-right: 35px;">Amount: </span>
                                                                                    <b>
                                                                                        &#{{nairaSymbol()}} {{number_format($val->amount,'2','.',',')}} </b>
                                                                                </li>

                                                                            </ul>
                                                                            <div class="col-xl-8"
                                                                                 style="margin-left:10px">
                                                                                <p class="float-end"
                                                                                   style="font-size: 30px; color: red; font-weight: 400;font-family: Arial, Helvetica, sans-serif;">
                                                                                    Total:
                                                                                    <span>
                                                                                        <b id="invoiceTotal"> &#{{nairaSymbol()}} {{number_format($val->total,'2','.',',')}} </b>
                                                                                    </span>
                                                                                </p>
                                                                            </div>

                                                                        </div>


                                                                    </div>


                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <div class="text-center">
                                                                <button class="btn btn-sm btn-info"
                                                                        onclick="printMe('modal-open-receipt{{$val->merchant_transaction_ref}}')">
                                                                    <i class="fa fa-thumbs-up"> Print Me!</i></button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        @empty

                                            <tr>
                                                <td colspan="10" class="text-sm font-weight-normal">
                                                    <div class="justify-content-center card card-plain">
                                                        <div class="card-header text-center pb-0 text-start">
                                                            <h2 class="font-weight-bolder">😢 No transaction Found!</h2>
                                                        </div>

                                                    </div>

                                                </td>
                                            </tr>

                                        @endforelse

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>


        </div>
    </section>

    @section('scripts')
        <style>
            @media print {

                .nonPrintable, div > nav {
                    display: none !important
                }
            }

        </style>
        <script>
            function searchTransactions() {
                event.preventDefault();
                //validation;
                let searchVia = $("#searchVia");
                let searchTitle = $("#searchTitleValue");
                if (searchTitle.val() === "") {
                    salert("Warning", "Search Value cannot be Empty!", 'warning');
                    return;
                }
                if (searchVia.val() === "") {
                    salert("Warning", "Choose a Search Criteria!", 'warning');
                }
                sprocessing("Fetching Transaction(S)! Please Wait")
                @this.resetPage();

                Livewire.emit('searchTransactions');


            }

            function printMe(el) {
                window.print();
            }
        </script>
    @endsection
</div>
