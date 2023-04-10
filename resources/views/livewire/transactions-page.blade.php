<div class="container-fluid py-4 ">
    <div class="d-sm-flex justify-content-between">

        <div>
            <div class="page-header   position-relative m-3 border-radius-xl">
                <img src="{{asset('assets/img/shapes/waves-white.svg')}}" alt="pattern-lines"
                     class="position-absolute opacity-6 start-0 top-0 w-100">

            </div>
            <!-- Nav pills -->
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="pill" href="#home">Filter Transactions</a>
                </li>

                @if($isAdmin)
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="pill" href="#menu1">Summary Report </a>
                    </li>
                @endif

            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
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
                <hr>
                <div class="tab-pane container active" id="home">
                    <form role="form" action="{{route('transactions')}}" id="transactionSearchBox" onsubmit="event.preventDefault(); searchTransactions(this); ">
                        @csrf
                        <fieldset class="py-md-4">
                            <div class="row" id="rawFilter">

                                <div class="col-lg-3 col-md-4 non_essential_summary_filter">
                                    <div class="form-group">
                                        <label for="merchant_transaction_ref" class="col-form-label text-md-right">
                                            {{__("Merchant Ref")}}
                                        </label>
                                        <div class="input-group input-group-merge input-group-alternative mb-3">
                                            <span class="input-group-text">
                                                    <i class="fa fa-mobile-alt" style="font-size: 15px;"></i>
                                                </span>
                                            <input id="merchant_transaction_ref" type="text" placeholder="Merchant Ref"
                                                   class="form-control @error('merchant_transaction_ref') is-invalid @enderror"
                                                   name="merchant_transaction_ref" value="{{$merchant_transaction_ref}}"
                                            >

                                        </div>
                                    </div>
                                </div>
                                @if($isAdmin)
                                    <div class="col-lg-3 col-md-4 non_essential_summary_filter">
                                        <div class="form-group">
                                            <label for="flutterwave_ref" class="col-form-label text-md-right">
                                                {{__("Flutterwave Ref")}}
                                            </label>
                                            <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                    <i class="fa fa-mobile-alt" style="font-size: 15px;"></i>
                                                </span>
                                                <input id="flutterwave_ref" type="text" placeholder="Card Ref"
                                                       class="form-control @error('flutterwave_ref') is-invalid @enderror"
                                                       name="flutterwave_ref" value="{{ old('flutterwave_ref') }}"
                                                >

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 non_essential_summary_filter">
                                        <div class="form-group">
                                            <label for="bank_transfer_ref" class="col-form-label text-md-right">
                                                {{__("Bank Transfer Ref")}}
                                            </label>
                                            <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                    <i class="fa fa-mobile-alt" style="font-size: 15px;"></i>
                                                </span>
                                                <input id="bank_transfer_ref" type="text" placeholder="SettlementID"
                                                       class="form-control @error('bank_transfer_ref') is-invalid @enderror"
                                                       name="bank_transfer_ref" value="{{ old('bank_transfer_ref') }}"
                                                >

                                            </div>
                                        </div>
                                    </div>

                                @endif
                                <div class="col-lg-3 col-md-4">
                                    <div class="form-group">
                                        <label for="status" class=" col-form-label text-md-right">
                                            {{ __('Payment Status') }}
                                        </label>

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-university py-1" style="font-size: 18px;"></i>
                                                </span>
                                            </div>


                                            <select id="status" title="Choose a Status"
                                                    data-style="btn border"
                                                    class=" form-control" name="status">

                                                <option value="">Choose Status</option>

                                                <option @if($payment_status === "pending" ) selected
                                                        @endif value="pending">PENDING
                                                </option>
                                                <option @if($payment_status === "failed" ) selected
                                                        @endif value="failed">FAILED
                                                </option>
                                                <option @if($payment_status === "successful" ) selected
                                                        @endif value="successful">SUCCESSFUL
                                                </option>
                                            </select>

                                        </div>


                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4">
                                    <div class="form-group">
                                        <label for="flag"
                                               class=" col-form-label text-md-right">{{ __('Payment Flag') }}</label>

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-flag py-1" style="font-size: 18px;"></i>
                                                </span>
                                            </div>
                                            <select id="flag" title="Credit / Debit"
                                                    data-style="btn border" class=" form-control " name="flag">
                                                <option value="">Choose Type</option>

                                                <option @if($payment_flag === "credit" ) selected @endif value="credit">
                                                    CREDIT
                                                </option>
                                                <option @if($payment_flag === "debit" ) selected @endif value="debit">
                                                    DEBIT
                                                </option>
                                            </select>

                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-4">
                                    <div class="form-group">
                                        <label for="flag"
                                               class=" col-form-label text-md-right">{{ __('Payment Channel') }}</label>

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-flag py-1" style="font-size: 18px;"></i>
                                                </span>
                                            </div>
                                            <select id="flag"
                                                    data-style="btn border" class=" form-control " name="gateway_id">
                                                <option value="">Choose Type</option>

                                                <option @if($payment_channel === 1 ) selected @endif value="1">
                                                    Card
                                                </option>
                                                <option @if($payment_channel === 2 ) selected @endif value="2">
                                                    Bank Transfer
                                                </option>
                                                <option @if($payment_channel === "3" ) selected @endif value="3">
                                                    Remita
                                                </option>
                                                <option @if($payment_channel === "4" ) selected @endif value="4">
                                                    GooglePay
                                                </option>
                                                <option @if($payment_channel === "5" ) selected @endif value="5">
                                                    ApplePay
                                                </option>

                                            </select>

                                        </div>

                                    </div>
                                </div>


                                <div class="col-lg-3 col-md-4 ">
                                    <div class="form-group">
                                        <label for="customer_email" class="col-form-label text-md-right">
                                            {{__("Merchant Email")}}
                                        </label>
                                        <livewire:email-search />
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
                                                   name="created_at" value="{{$payment_created_at}}"
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
                                                   name="end_date" value="{{$payment_end_date}}"
                                            >
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="col text-right">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success mx-3"> Search</button>
                                    <button class="btn btn-danger mx-3" onclick="document.getElementById('transactionSearchBox').reset(); event.preventDefault()"> Reset</button>
                                </div>
                            </div>
                        </fieldset>



                    </form>
                </div>

                <div class="tab-pane container fade" id="menu1">
                    <form role="form" action="{{route('transactions')}}" id="summaryTransactionForm" onsubmit="event.preventDefault(); searchTransactions(this); ">
                        @csrf
                        <fieldset class="py-md-4">
                            <div class="row" id="rawFilter">


                                <div class="col-lg-4 col-md-4">
                                    <div class="form-group">
                                        <label for="status" class=" col-form-label text-md-right">
                                            {{ __('Payment Status') }}
                                        </label>

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-university py-1" style="font-size: 18px;"></i>
                                                </span>
                                            </div>



                                            <select id="status" title="Choose a Status"
                                                    data-style="btn border"
                                                    class=" form-control" name="status">

                                                <option value="">Choose Status</option>

                                                <option @if($payment_status === "pending" ) selected
                                                        @endif value="pending">PENDING
                                                </option>
                                                <option @if($payment_status === "failed" ) selected
                                                        @endif value="failed">FAILED
                                                </option>
                                                <option @if($payment_status === "successful" ) selected
                                                        @endif value="successful">SUCCESSFUL
                                                </option>
                                            </select>

                                        </div>


                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4">
                                    <div class="form-group">
                                        <label for="flag"
                                               class=" col-form-label text-md-right">{{ __('Payment Flag') }}</label>

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-flag py-1" style="font-size: 18px;"></i>
                                                </span>
                                            </div>
                                            <select id="flag" title="Credit / Debit"
                                                    data-style="btn border" class=" form-control " name="flag">
                                                <option value="">Choose Type</option>

                                                <option @if($payment_flag === "credit" ) selected @endif value="credit">
                                                    CREDIT
                                                </option>
                                                <option @if($payment_flag === "debit" ) selected @endif value="debit">
                                                    DEBIT
                                                </option>
                                            </select>

                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4">
                                    <div class="form-group">
                                        <label for="flag"
                                               class=" col-form-label text-md-right">{{ __('Payment Channel') }}</label>

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-flag py-1" style="font-size: 18px;"></i>
                                                </span>
                                            </div>
                                            <select id="flag"
                                                    data-style="btn border" class=" form-control " name="gateway_id">
                                                <option value="">Choose Type</option>

                                                <option @if($payment_channel === 1 ) selected @endif value="1">
                                                    Card
                                                </option>
                                                <option @if($payment_channel === 2 ) selected @endif value="2">
                                                    Bank Transfer
                                                </option>
                                                <option @if($payment_channel === "3" ) selected @endif value="3">
                                                    Remita
                                                </option>
                                                <option @if($payment_channel === "4" ) selected @endif value="4">
                                                    GooglePay
                                                </option>
                                                <option @if($payment_channel === "5" ) selected @endif value="5">
                                                    ApplePay
                                                </option>

                                            </select>

                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4">
                                    <div class="form-group">
                                        <label for="group_by"
                                               class=" col-form-label text-md-right">{{ __('Group By') }}</label>

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-flag py-1" style="font-size: 18px;"></i>
                                                </span>
                                            </div>
                                            <select id="group_by" title="Group By"
                                                    data-style="btn border" class="form-control " name="group_by">
                                                <option value="">Choose Type</option>

                                                <option @if($payment_flag === "user_id" ) selected @endif value="user_id">
                                                    Merchant
                                                </option>
                                                <option @if($payment_flag === "gateway_id" ) selected @endif value="gateway_id">
                                                    Payment Channel
                                                </option>
                                                <option @if($payment_flag === "status" ) selected @endif value="status">
                                                    Status
                                                </option>
                                                <option @if($payment_flag === "flag" ) selected @endif value="flag">
                                                    Flag (Credit/Debit)
                                                </option>
                                            </select>

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
                                                   name="created_at" value="{{$payment_created_at}}"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-md-4">
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
                                                   name="end_date" value="{{$payment_end_date}}"
                                            >
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="col text-right">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success mx-3"> Search</button>
                                    <button class="btn btn-danger mx-3" onclick="document.getElementById('summaryTransactionForm').reset(); event.preventDefault()"> Reset</button>
                                </div>
                            </div>
                        </fieldset>



                    </form>
                </div>

            </div>

        </div>

    </div>
    <div class="d-block">
        <button class="btn btn-icon btn-outline-dark ms-2 export" data-type="csv" type="button"
                onclick="generateCsvReport()">
            <span class="btn-inner--icon"><i class="ni ni-archive-2"></i></span>
            <span class="btn-inner--text">Export CSV</span>
        </button>
    </div>
    @if(isset($reportExists))
        <a href="#">
            <div class="alert alert-info alert-dismissible fade show" role="alert" style="font-size: 25px"
                 onclick=" downloadReport('reportGeneratedAlert')" id="reportGeneratedAlert">
                        <span class="alert-text">
                            <span class="alert-icon text-white"><i class="ni ni-like-2"></i></span>
                            <strong>Info! </strong>Report Generated! click me to download
                        </span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

        </a>
    @endif
    <div class="row min-vh-90">
        <div class="card table-responsive">
            <div class="dataTable-wrapper dataTable-loading sortable  fixed-columns">
                <div class="dataTable-container">
                    <table class="table table-flush " id="datatable-basic">
                        <thead class="thead-light">
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
                                <a href="#" class="dataTable-sorter"> Transaction Ref</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Gateway</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Amount</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Fee</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Total</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Email</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Status</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Flag</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">Date</a>
                            </th>
                            <th>

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
                            @endif
                            <tr>
                                <td class="text-sm font-weight-normal">{{++$k}}</td>
                                <td class="text-sm font-weight-normal">{{$val->merchant_transaction_ref}}</td>
                                <td class="text-sm font-weight-normal">{{ $trnRef }}</td>
                                <td class="text-sm font-weight-normal">{{ $val->gateway->name??  "N/A"}}</td>
                                <td> &#{{nairaSymbol()}} {{number_format($val->amount,'2','.','')}}</td>
                                <td>&#{{nairaSymbol()}} {{number_format($val->fee,'2','.','')}}</td>
                                <td>&#{{nairaSymbol()}} {{number_format($val->total,'2','.','')}}</td>
                                <td class="text-sm font-weight-normal">{{$val->details["email"] ?? "N/A"}}</td>
                                <td class="text-sm font-weight-normal">{{$val->status}}</td>
                                <td class="text-sm font-weight-normal">{{$val->flag}}</td>

                                <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                                <td>
                                    @if( in_array(strtoupper($val->status),["SUCCESSFUL","FAILED"]))
                                        <a target="_blank" href="{{route('receipt',['id'=>"INV$val->merchant_transaction_ref"])}}" class="btn btn-success"> Receipt »</a>
                                    @endif
                                </td>
                            </tr>
                        @empty

                            <tr>
                                <td colspan="10"  class="text-sm font-weight-normal">
                                    <div class="justify-content-center card card-plain">
                                        <div class="card-header text-center pb-0 text-start">
                                            <h2 class="font-weight-bolder">😢 No transaction Found!</h2>
                                        </div>

                                    </div>

                                </td>
                            </tr>

                        @endforelse

                        {{ $transactionsCollection->links() }}
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

        <script>
            function searchTransactions(element) {
                event.preventDefault();
                const formData = new FormData(element);
                let formValues = {};
                formData.forEach(function (value, key) {
                    formValues[key] = value;
                });
                //bind value;
            @this.searchParameters
                = JSON.stringify(formValues);

            sprocessing("Please Wait!");

            Livewire.emit("searchTransactions");
            }

            addEventListener("searchComplete",function () {
                Swal.close();
            })

            function downloadReport(element, filename) {
                var alertNode = document.querySelector('#' + element);
                console.log(alertNode);
                alertNode.style.display = "none";
                location.assign("{{$reportDownloadLink}}")
            }

            function generateCsvReport() {

                sprocessing("Generating Report");
                Livewire.emit("exportCsv", @this.searchQuery);
            }

            addEventListener("generatingReport", event => {
                let response = event.detail;

                if (response.status !== true) {
                    salert("Generating Report", "Cannot Generate Report! No data Available", "warning")
                }
                if (response.status === true) {
                    salert("Generating Report", "You'll be notified once Report is Generated", "info")
                }

            });
        </script>
    @endsection


</div>


