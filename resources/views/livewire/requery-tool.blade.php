<div class="container-fluid py-4 ">

    <div class="row min-vh-90">
        <div class="card table-responsive">
            <div class="container">
                <div class="container mt--6">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header border-0">
                                    <div class="row align-items-center">
                                        <div class="col text-center">
                                            <h3 class="mb-0"><i class="fa fa-recycle"></i> Requery Tool</h3>
                                        </div>

                                    </div>
                                </div>
                                <div class="container">
                                    <form action="" wire:submit.prevent="getTransactionDetails" >
                                        @csrf
                                        <fieldset>


                                            <div class="row">
                                                <div class="col-lg-4 col-md-4">
                                                    <div class="form-group">
                                                        <label for="provider" class=" col-form-label text-md-right">
                                                            {{ __('Payment Provider') }}
                                                        </label>

                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-university py-1" style="font-size: 18px;"></i>
                                                </span>
                                                            </div>


                                                            <select id="provider" title="Choose a Status" wire:model="provider"
                                                                    data-style="btn border" class=" form-control"
                                                                    name="provider" required>

                                                                <option value="">Choose Provider</option>

                                                                <option value="providus">PROVIDUS
                                                                </option>
                                                                <option value="flutterwave">FLUTTERWAVE
                                                                </option>

                                                            </select>

                                                        </div>


                                                    </div>
                                                </div>

                                                <div class="col-lg-4 col-md-4">
                                                    <div class="form-group">
                                                        <label for="transaction_ref" class=" col-form-label text-md-right">
                                                            {{ __('Transaction Number') }}
                                                        </label>

                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-keyboard py-1" style="font-size: 18px;"></i>
                                                </span>
                                                            </div>

                                                            <input type="text" name="transaction_ref" id="transaction_ref" class="form-control" wire:model="transaction_ref" required>
                                                        </div>


                                                    </div>
                                                </div>


                                            </div>

                                            <div class="col text-right">
                                                <button type="submit" class="btn btn-success" onclick="showProcessing(null,this)" id="getTransaction"> Get Transaction</button>
                                            </div>

                                        </fieldset>
                                    </form>

                                    <hr>

                                </div>



                            </div>



                            @if($transactionDetails)
                                <table class="table table-hover" style=" white-space: break-spaces !important;">
                                    <thead>
                                    <tr class="table-info">
                                        <th>Transaction Ref</th>
                                        <th>Amount</th>
                                        <th style="white-space: break-spaces !important;">Remarks</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{$transactionDetails['transaction_ref']}}</td>
                                        <td>{{$transactionDetails['amount']}}</td>
                                        <td>{{$transactionDetails['remarks']}}</td>
                                        <td>{{$transactionDetails['date']}}</td>
                                        <td>

                                            <button  class="btn btn-success btn-sm" onclick="showProcessing('Initializing {{$transaction_ref}}',this)" wire:click="requery"> Requery </button>
                                        </td>

                                    </tr>

                                    </tbody>
                                </table>

                            @endif

                            @if(strtoupper($messageType) === "DANGER")
                                <div class="alert alert-danger alert-dismissible text-white " role="alert">
                                    {{$message}}
                                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            @endif
                            @if(strtoupper($messageType) === "SUCCESS")
                                <div class="alert alert-success alert-dismissible text-white " role="alert">
                                    {{$message}}
                                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            @endif
                            @if(strtoupper($messageType) === "INFO")
                                <div class="alert alert-info alert-dismissible text-white " role="alert">
                                    {{$message}}
                                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            @endif
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


            function showProcessing(message,element) {
                if ($(element).attr('id') === "getTransaction"){
                    //check to make sure ref and provider is selected
                    if ( $("#provider").val() === ""){
                        salert("Warning", "Please Choose A Provider",'warning');
                        return;
                    }
                    if ($("#transaction_ref").val() === ""){
                        salert("Warning", "Please Input Transaction Ref",'warning')
                        return;
                    }
                }

                sprocessing(message ??"Fetching Transaction" + "!")
            }

            addEventListener('alertBox',function () {
                let type = event.detail.type;
                let message = event.detail.message;
                if (type === "processing"){
                    sprocessing("Fetching Transaction")
                }
                if (type === "success"){
                    salert("Success", message ?? "Transaction Fetched!",'success')
                }
                if (type === "info"){
                    salert("Info",message,'success')
                }
            })
            addEventListener("closeAlert",function () {
                Swal.close();
            })
        </script>
    @endsection


</div>
