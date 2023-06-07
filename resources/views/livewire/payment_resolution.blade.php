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
                                            <h3 class="mb-0"><i class="fa fa-recycle"></i> Resolution Tool</h3>
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
                                                        <label for="channel" class=" col-form-label text-md-right">
                                                            {{ __('Payment Channel') }}
                                                        </label>

                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa fa-university py-1" style="font-size: 18px;"></i>
                                                </span>
                                                            </div>


                                                            <select id="channel" title="Choose a Status" wire:model="channel"
                                                                    data-style="btn border" class=" form-control"
                                                                    name="channel" required>

                                                                <option value="">Choose Provider</option>

                                                                <option value="1">FLUTTERWAVE
                                                                </option>
                                                                <option value="2">PROVIDUS
                                                                </option>
                                                                <option value="3">9PSB
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

                            @if(strtoupper($messageType) === "WARNING")
                                <div class="alert alert-warning alert-dismissible text-black " role="alert">
                                    {!! $message !!}
                                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            @endif
                            @if(strtoupper($messageType) === "DANGER")
                                <div class="alert alert-danger alert-dismissible text-white " role="alert">
                                    {!! $message !!}
                                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            @endif
                            @if(strtoupper($messageType) === "SUCCESS")
                                <div class="alert alert-success alert-dismissible text-white " role="alert">
                                    {!! $message !!}
                                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            @endif
                            @if(strtoupper($messageType) === "INFO")
                                <div class="alert alert-info alert-dismissible text-white " role="alert">
                                    {!! $message !!}
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
                    //check to make sure ref and channel is selected
                    if ( $("#channel").val() === ""){
                        salert("Warning", "Please Choose A Channel",'warning');
                        return;
                    }
                    if ($("#transaction_ref").val() === ""){
                        salert("Warning", "Please Input Transaction Ref",'warning')
                        return;
                    }
                }

                sprocessing(message ??"Fetching Transaction" + "!")
            }

        </script>
    @endsection


</div>


