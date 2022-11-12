@extends('layouts.admin.admin_dashboardapp')


@section('content')
    <div>
        <div class="row mt-4">
            <div class="col-xl-7 ">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-gradient-primary">
                            <div class="card-body p-3">
                                <div class="row">
                                    <div class="col-8 my-auto">
                                        <div class="numbers">
                                            <h5 class="text-white font-weight-bolder text-capitalize font-weight-bold  mb-0">
                                                {{$greeting}} - °
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        @if(strtoupper($time_of_day) === "NIGHT")
                                            <img class="w-50" src="{{asset("assets/img/logos/cloudy-night.png")}}"
                                                 alt="image Night">
                                        @else
                                            <img class="w-50" src="{{asset("assets/img/logos/icon-sun-cloud.png")}}"
                                                 alt="image sun">
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Transaction Breakdown</h6>
                            <button type="button"
                                    class="btn btn-icon-only btn-rounded btn-outline-secondary mb-0 ms-2 btn-sm d-flex align-items-center justify-content-center ms-auto"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="See the transactions with a bird's eye view">
                                <i class="fas fa-info"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-5 text-center">
                                <div class="chart">
                                    <canvas id="chart-consumption" class="chart-canvas" height="197"></canvas>
                                </div>
                                <h4 class="font-weight-bold mt-n8">
                                    <span>{{$transactions_count}}</span>
                                    <span class="d-block text-body text-sm">TRANSACTIONS</span>
                                </h4>
                            </div>
                            <div class="col-7">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-success me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Successful Transactions </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> {{$transactions_count ?? number_format((($successful_transactions_count/$transactions_count) * 100) ,1)}}% ({{$successful_transactions_count}})</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-warning me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Pending Transactions</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> {{$transactions_count ?? number_format((($pending_transactions_count/$transactions_count) * 100) ,1)}}% ({{$pending_transactions_count}})</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-danger me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Failed Transactions</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> {{$transactions_count ?? number_format((($failed_transactions_count/$transactions_count) * 100) ,1)}}% ({{$failed_transactions_count}})</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-gradient-success me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Successful Amount</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> &#{{nairaSymbol()}} {{number_format($successful_transactions ,1)}} </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-gradient-warning me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Pending Amount</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> &#{{nairaSymbol()}} {{number_format($pending_transactions ,1)}} </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-gradient-danger me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Failed Amount</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> &#{{nairaSymbol()}} {{number_format($failed_transactions ,1)}} </span>
                                            </td>
                                        </tr>

                                        </tbody>
                                        <tfoot>

                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-gradient-faded-info-vertical me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Expected Revenue </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> <b>&#{{nairaSymbol()}} {{number_format($total_fees_charge ,1)}}</b> </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-0">
                                                    <span class="badge bg-gradient-faded-info-vertical me-3"> </span>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">Revenue </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-xs font-weight-bold"> <b>&#{{nairaSymbol()}} {{number_format($total_successful_fees_charge,1 )}}</b> </span>
                                            </td>
                                        </tr>

                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5 ms-auto mt-xl-0 mt-4">
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Gateways Breakdown</h6>
                            <button type="button"
                                    class="btn btn-icon-only btn-rounded btn-outline-secondary mb-0 ms-2 btn-sm d-flex align-items-center justify-content-center ms-auto"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="See the gateways used to process transactions with a bird's eye view">
                                <i class="fas fa-info"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0 mb-4">
                        <div class="row ">
                            <div class="col-md-6 mb-md-2">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h1 class="text-gradient text-primary"><span id="status3" countto="{{$card_transactions_count}}">{{$card_transactions_count}}</span> <span
                                                class="text-lg ms-n2">°</span></h1>
                                        <h6 class="mb-0 font-weight-bolder">Card</h6>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-md-2 ">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h1 class="text-gradient text-primary"><span id="status4" countto="{{$bank_transfer_transactions_count}}">{{$bank_transfer_transactions_count}}</span> <span
                                                class="text-lg ms-n2">°</span></h1>
                                        <h6 class="mb-0 font-weight-bolder">Bank Transfer</h6>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-md-2 ">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h1 class="text-gradient text-primary"><span id="status4" countto="{{$remita_transactions_count}}">{{$remita_transactions_count}}</span> <span
                                                class="text-lg ms-n2">°</span></h1>
                                        <h6 class="mb-0 font-weight-bolder">Remita</h6>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-md-2 ">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h1 class="text-gradient text-primary"><span id="status4" countto="{{$google_pay_transactions_count}}">{{$google_pay_transactions_count}}</span> <span
                                                class="text-lg ms-n2">°</span></h1>
                                        <h6 class="mb-0 font-weight-bolder">GooglePay</h6>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-md-2 ">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h1 class="text-gradient text-primary"><span id="status4" countto="{{$apple_pay_transactions_count}}">{{$apple_pay_transactions_count}}</span> <span
                                                class="text-lg ms-n2">°</span></h1>
                                        <h6 class="mb-0 font-weight-bolder">Applepay</h6>

                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="row mt-4">
            <div class="col-sm-6">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-0">Transactions</h6>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end align-items-center">
                                <i class="far fa-calendar-alt me-2" aria-hidden="true"></i>
                                <small>23 - 30 March 2021</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group">

                            @forelse($latest_transactions as $trnx)
                                <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radiu s-lg">
                                    <div class="d-flex">
                                        <div class="d-flex align-items-center">
                                            <button
                                                class="btn btn-icon-only btn-rounded @if($trnx->flag === "debit") btn-outline-danger @else btn-outline-success @endif mb-0 me-3 p-3 btn-sm d-flex align-items-center justify-content-center">
                                                @if($trnx->flag === "debit")
                                                    <i class="fas fa-arrow-down" aria-hidden="true"></i>
                                                @else
                                                    <i class="fas fa-arrow-up" aria-hidden="true"></i>
                                                @endif

                                            </button>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-1 text-dark text-sm">{{$trnx->merchant_transaction_ref}}</h6>
                                                <span class="text-xs">{{\Illuminate\Support\Carbon::parse($trnx->updated_at)->toDayDateTimeString()}}</span>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex align-items-center @if($trnx->flag === "debit") text-danger @else text-success @endif text-gradient text-sm font-weight-bold ms-auto">
                                            @if($trnx->flag === "debit")
                                                -
                                            @else
                                                +
                                            @endif
                                                &#{{nairaSymbol()}} {{ number_format($trnx->total)}}
                                        </div>
                                    </div>
                                    <hr class="horizontal dark mt-3 mb-2">
                                </li>

                            @empty
                                <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                    <div class="d-flex">
                                        <div class="d-flex align-items-center">
                                            <button
                                                class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 p-3 btn-sm d-flex align-items-center justify-content-center">
                                                😢
                                            </button>
                                            <div class="d-flex flex-column">
                                                <h2 class="font-weight-bolder"> There are no transactions Yet!</h2>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="horizontal dark mt-3 mb-2">
                                </li>


                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mt-sm-0 mt-4">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="mb-0">Breakdown via Gateways </h6>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end align-items-center">
                                <i class="far fa-calendar-alt me-2" aria-hidden="true"></i>
                                <small>01 - 07 June 2021</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <ul class="list-group">
                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-up" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Successful via Card</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold ms-auto">
                                        + &#{{nairaSymbol()}} {{ number_format($successful_card_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-warning mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Pending via Card</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-warning text-gradient text-sm font-weight-bold ms-auto">
                                        + &#{{nairaSymbol()}} {{ number_format($pending_card_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-down" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Failed via Card</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold ms-auto">
                                        - &#{{nairaSymbol()}} {{ number_format($failed_card_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>

                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-up" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Successful via Bank Transfer</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold ms-auto">
                                        + &#{{nairaSymbol()}} {{ number_format($successful_bank_transfer_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-warning mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Pending via Bank Transfer</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-warning text-gradient text-sm font-weight-bold ms-auto">
                                        + &#{{nairaSymbol()}} {{ number_format($pending_bank_transfer_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-down" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Failed via Bank Transfer</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold ms-auto">
                                        - &#{{nairaSymbol()}} {{ number_format($failed_bank_transfer_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>

                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-up" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Successful via Remita</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold ms-auto">
                                        + &#{{nairaSymbol()}} {{ number_format($successful_remita_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-warning mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Pending via Remita</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-warning text-gradient text-sm font-weight-bold ms-auto">
                                        + &#{{nairaSymbol()}} {{ number_format($pending_remita_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-down" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Failed via Remita</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold ms-auto">
                                        - &#{{nairaSymbol()}} {{ number_format($failed_remita_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>

                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-up" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Successful via GooglePay</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold ms-auto">
                                        + &#{{nairaSymbol()}} {{ number_format($successful_google_pay_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-warning mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Pending via GooglePay</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-warning text-gradient text-sm font-weight-bold ms-auto">
                                        + &#{{nairaSymbol()}} {{ number_format($pending_google_pay_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-down" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Failed via GooglePay</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold ms-auto">
                                        - &#{{nairaSymbol()}} {{ number_format($failed_google_pay_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>

                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-up" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Successful via ApplePay</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold ms-auto">
                                        + &#{{nairaSymbol()}} {{ number_format($successful_apple_pay_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-warning mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Pending via ApplePay</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-warning text-gradient text-sm font-weight-bold ms-auto">
                                        + &#{{nairaSymbol()}} {{ number_format($pending_apple_pay_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                            <li class="list-group-item border-0 justify-content-between ps-0 pb-0 border-radius-lg">
                                <div class="d-flex">
                                    <div class="d-flex align-items-center">
                                        <button
                                            class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 p-2 btn-sm d-flex align-items-center justify-content-center">
                                            <i class="fas fa-arrow-down" aria-hidden="true"></i></button>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1 text-dark text-sm">Failed via ApplePay</h6>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold ms-auto">
                                        - &#{{nairaSymbol()}} {{ number_format($failed_apple_pay_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-6 ms-auto">
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <div class="d-flex align-items-center">
                            <h6 class="mb-0">Users Breakdown</h6>
                            <button type="button"
                                    class="btn btn-icon-only btn-rounded btn-outline-secondary mb-0 ms-2 btn-sm d-flex align-items-center justify-content-center ms-auto"
                                    data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="See the users ">
                                <i class="fas fa-info"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <h6 class="ms-2 mt-4 mb-0"> Active Users </h6>
                        <p class="text-sm ms-2"> (<span class="font-weight-bolder">+23%</span>) than last week </p>
                        <div class="container border-radius-lg">
                            <div class="row">
                                <div class="col-3 py-3 ps-0">
                                    <div class="d-flex mb-2">
                                        <div
                                            class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                                            <svg width="10px" height="10px" viewBox="0 0 40 44" version="1.1"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <title>document</title>
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <g transform="translate(-1870.000000, -591.000000)" fill="#FFFFFF"
                                                       fill-rule="nonzero">
                                                        <g transform="translate(1716.000000, 291.000000)">
                                                            <g transform="translate(154.000000, 300.000000)">
                                                                <path class="color-background"
                                                                      d="M40,40 L36.3636364,40 L36.3636364,3.63636364 L5.45454545,3.63636364 L5.45454545,0 L38.1818182,0 C39.1854545,0 40,0.814545455 40,1.81818182 L40,40 Z"
                                                                      opacity="0.603585379"></path>
                                                                <path class="color-background"
                                                                      d="M30.9090909,7.27272727 L1.81818182,7.27272727 C0.814545455,7.27272727 0,8.08727273 0,9.09090909 L0,41.8181818 C0,42.8218182 0.814545455,43.6363636 1.81818182,43.6363636 L30.9090909,43.6363636 C31.9127273,43.6363636 32.7272727,42.8218182 32.7272727,41.8181818 L32.7272727,9.09090909 C32.7272727,8.08727273 31.9127273,7.27272727 30.9090909,7.27272727 Z M18.1818182,34.5454545 L7.27272727,34.5454545 L7.27272727,30.9090909 L18.1818182,30.9090909 L18.1818182,34.5454545 Z M25.4545455,27.2727273 L7.27272727,27.2727273 L7.27272727,23.6363636 L25.4545455,23.6363636 L25.4545455,27.2727273 Z M25.4545455,20 L7.27272727,20 L7.27272727,16.3636364 L25.4545455,16.3636364 L25.4545455,20 Z"></path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg>
                                        </div>
                                        <p class="text-xs mt-1 mb-0 font-weight-bold">Total Merchants</p>
                                    </div>
                                    <h4 class="font-weight-bolder">{{number_format($total_merchants)}}</h4>
                                    <div class="progress w-75">
                                        <div class="progress-bar bg-dark w-100" role="progressbar" aria-valuenow="{{$total_merchants}}"
                                             aria-valuemin="0" aria-valuemax="{{$total_merchants}}"></div>
                                    </div>
                                </div>
                                <div class="col-3 py-3 ps-0">
                                    <div class="d-flex mb-2">
                                        <div
                                            class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-info text-center me-2 d-flex align-items-center justify-content-center">
                                            <svg width="10px" height="10px" viewBox="0 0 40 40" version="1.1"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <title>spaceship</title>
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <g transform="translate(-1720.000000, -592.000000)" fill="#FFFFFF"
                                                       fill-rule="nonzero">
                                                        <g transform="translate(1716.000000, 291.000000)">
                                                            <g transform="translate(4.000000, 301.000000)">
                                                                <path class="color-background"
                                                                      d="M39.3,0.706666667 C38.9660984,0.370464027 38.5048767,0.192278529 38.0316667,0.216666667 C14.6516667,1.43666667 6.015,22.2633333 5.93166667,22.4733333 C5.68236407,23.0926189 5.82664679,23.8009159 6.29833333,24.2733333 L15.7266667,33.7016667 C16.2013871,34.1756798 16.9140329,34.3188658 17.535,34.065 C17.7433333,33.98 38.4583333,25.2466667 39.7816667,1.97666667 C39.8087196,1.50414529 39.6335979,1.04240574 39.3,0.706666667 Z M25.69,19.0233333 C24.7367525,19.9768687 23.3029475,20.2622391 22.0572426,19.7463614 C20.8115377,19.2304837 19.9992882,18.0149658 19.9992882,16.6666667 C19.9992882,15.3183676 20.8115377,14.1028496 22.0572426,13.5869719 C23.3029475,13.0710943 24.7367525,13.3564646 25.69,14.31 C26.9912731,15.6116662 26.9912731,17.7216672 25.69,19.0233333 L25.69,19.0233333 Z"></path>
                                                                <path class="color-background"
                                                                      d="M1.855,31.4066667 C3.05106558,30.2024182 4.79973884,29.7296005 6.43969145,30.1670277 C8.07964407,30.6044549 9.36054508,31.8853559 9.7979723,33.5253085 C10.2353995,35.1652612 9.76258177,36.9139344 8.55833333,38.11 C6.70666667,39.9616667 0,40 0,40 C0,40 0,33.2566667 1.855,31.4066667 Z"></path>
                                                                <path class="color-background"
                                                                      d="M17.2616667,3.90166667 C12.4943643,3.07192755 7.62174065,4.61673894 4.20333333,8.04166667 C3.31200265,8.94126033 2.53706177,9.94913142 1.89666667,11.0416667 C1.5109569,11.6966059 1.61721591,12.5295394 2.155,13.0666667 L5.47,16.3833333 C8.55036617,11.4946947 12.5559074,7.25476565 17.2616667,3.90166667 L17.2616667,3.90166667 Z"
                                                                      opacity="0.598539807"></path>
                                                                <path class="color-background"
                                                                      d="M36.0983333,22.7383333 C36.9280725,27.5056357 35.3832611,32.3782594 31.9583333,35.7966667 C31.0587397,36.6879974 30.0508686,37.4629382 28.9583333,38.1033333 C28.3033941,38.4890431 27.4704606,38.3827841 26.9333333,37.845 L23.6166667,34.53 C28.5053053,31.4496338 32.7452344,27.4440926 36.0983333,22.7383333 L36.0983333,22.7383333 Z"
                                                                      opacity="0.598539807"></path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg>
                                        </div>
                                        <p class="text-xs mt-1 mb-0 font-weight-bold">Api Merchants</p>
                                    </div>
                                    <h4 class="font-weight-bolder">{{number_format($total_api_merchants)}}</h4>
                                    <div class="progress w-75">
                                        <div class="progress-bar bg-dark w-{{number_format(($total_api_merchants/$total_merchants) * 100,)}}" role="progressbar" aria-valuenow="{{$total_api_merchants}}"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-3 py-3 ps-0">
                                    <div class="d-flex mb-2">
                                        <div
                                            class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-warning text-center me-2 d-flex align-items-center justify-content-center">
                                            <svg width="10px" height="10px" viewBox="0 0 43 36" version="1.1"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <title>credit-card</title>
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF"
                                                       fill-rule="nonzero">
                                                        <g transform="translate(1716.000000, 291.000000)">
                                                            <g transform="translate(453.000000, 454.000000)">
                                                                <path class="color-background"
                                                                      d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z"
                                                                      opacity="0.593633743"></path>
                                                                <path class="color-background"
                                                                      d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg>
                                        </div>
                                        <p class="text-xs mt-1 mb-0 font-weight-bold">Active Merchants</p>
                                    </div>
                                    <h4 class="font-weight-bolder">{{$total_active_merchants}}</h4>
                                    <div class="progress w-75">
                                        <div class="progress-bar bg-dark w-{{number_format(($total_active_merchants/$total_merchants) * 100,)}}" role="progressbar" aria-valuenow="30"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-3 py-3 ps-0">
                                    <div class="d-flex mb-2">
                                        <div
                                            class="icon icon-shape icon-xxs shadow border-radius-sm bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                                            <svg width="10px" height="10px" viewBox="0 0 40 40" version="1.1"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <title>settings</title>
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <g transform="translate(-2020.000000, -442.000000)" fill="#FFFFFF"
                                                       fill-rule="nonzero">
                                                        <g transform="translate(1716.000000, 291.000000)">
                                                            <g transform="translate(304.000000, 151.000000)">
                                                                <polygon class="color-background" opacity="0.596981957"
                                                                         points="18.0883333 15.7316667 11.1783333 8.82166667 13.3333333 6.66666667 6.66666667 0 0 6.66666667 6.66666667 13.3333333 8.82166667 11.1783333 15.315 17.6716667"></polygon>
                                                                <path class="color-background"
                                                                      d="M31.5666667,23.2333333 C31.0516667,23.2933333 30.53,23.3333333 30,23.3333333 C29.4916667,23.3333333 28.9866667,23.3033333 28.48,23.245 L22.4116667,30.7433333 L29.9416667,38.2733333 C32.2433333,40.575 35.9733333,40.575 38.275,38.2733333 L38.275,38.2733333 C40.5766667,35.9716667 40.5766667,32.2416667 38.275,29.94 L31.5666667,23.2333333 Z"
                                                                      opacity="0.596981957"></path>
                                                                <path class="color-background"
                                                                      d="M33.785,11.285 L28.715,6.215 L34.0616667,0.868333333 C32.82,0.315 31.4483333,0 30,0 C24.4766667,0 20,4.47666667 20,10 C20,10.99 20.1483333,11.9433333 20.4166667,12.8466667 L2.435,27.3966667 C0.95,28.7083333 0.0633333333,30.595 0.00333333333,32.5733333 C-0.0583333333,34.5533333 0.71,36.4916667 2.11,37.89 C3.47,39.2516667 5.27833333,40 7.20166667,40 C9.26666667,40 11.2366667,39.1133333 12.6033333,37.565 L27.1533333,19.5833333 C28.0566667,19.8516667 29.01,20 30,20 C35.5233333,20 40,15.5233333 40,10 C40,8.55166667 39.685,7.18 39.1316667,5.93666667 L33.785,11.285 Z"></path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </g>
                                            </svg>
                                        </div>
                                        <p class="text-xs mt-1 mb-0 font-weight-bold">Inactive Merchants</p>
                                    </div>
                                    <h4 class="font-weight-bolder">{{number_format($total_inactive_merchants)}}</h4>
                                    <div class="progress w-75">
                                        <div class="progress-bar bg-dark w-{{number_format(($total_inactive_merchants/$total_merchants) * 100,)}}" role="progressbar" aria-valuenow="50"
                                             aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-lg-6 mt-lg-0 mt-4">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <h6>Consumption per day</h6>
                        <div class="chart pt-3">
                            <canvas id="chart-cons-week" class="chart-canvas" height="170"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <hr class="horizontal dark my-5">
    </div>
@endsection


@section('scripts')
    <script>
        // Chart Doughnut Consumption by room
        var ctx1 = document.getElementById("chart-consumption").getContext("2d");

        var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

        gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
        gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

        new Chart(ctx1, {
            type: "doughnut",
            data: {
                labels: ["Successful", "Pending", "Failed"],
                datasets: [{
                    label: "Consumption",
                    weight: 9,
                    cutout: 90,
                    tension: 0.9,
                    pointRadius: 2,
                    borderWidth: 2,
                    backgroundColor: ['#98ec2d', '#ffb500', '#f60303'],
                    data: [{{$successful_transactions_count}}, {{$pending_transactions_count}}, {{$failed_transactions_count}}],
                    fill: false
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                        },
                        ticks: {
                            display: false,
                        }
                    },
                },
            },
        });



        // Chart Consumption by day
        var ctx = document.getElementById("chart-cons-week").getContext("2d");

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                datasets: [{
                    label: "Watts",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "#3A416F",
                    data: [150, 230, 380, 220, 420, 200, 70],
                    maxBarThickness: 6
                }, ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {

                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false
                        },
                        ticks: {
                            beginAtZero: true,
                            font: {
                                size: 12,
                                family: "Open Sans",
                                style: 'normal',
                            },
                            display: true,
                            padding: 10,
                            color: '#9ca2b7'
                        },
                    },
                    y: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#9ca2b7'
                        }
                    },
                },
            },
        });
    </script>
@endsection
