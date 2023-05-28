@extends('layouts.merchant_dashboardapp')


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
                                                        <h6 class="mb-0 text-sm">Successful  Transactions</h6>
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
                                                <span class="text-xs font-weight-bold"> <b>&#{{nairaSymbol()}} {{number_format($transactions_expected_revenue ,1)}}</b> </span>
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
                    <div class="card-body p-0">
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
                                        <h1 class="text-gradient text-primary"><span id="status4" countto="{{$googlepay_transactions_count}}">{{$$googlepay_transactions_count}}</span> <span
                                                class="text-lg ms-n2">°</span></h1>
                                        <h6 class="mb-0 font-weight-bolder">GooglePay</h6>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-md-2 ">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h1 class="text-gradient text-primary"><span id="status4" countto="{{$applepay_transactions_count}}">{{$applepay_transactions_count}}</span> <span
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
            <div class="col-12">
                <div class="card bg-gradient-secondary">
                    <img src="{{asset('assets/img/shapes/waves-white.svg')}}" alt="pattern-lines" class="position-absolute opacity-4 start-0 top-0 w-100">
                    <div class="card-body px-5 z-index-1 bg-cover">
                        <div class="row">
                            <div class="col-lg-3 col-12 my-auto">
                                <h4 class="text-white opacity-9">Since Last Charge</h4>
                                <hr class="horizontal light mt-1 mb-3">
                                <div class="d-flex">
                                    <div>
                                        <h6 class="mb-0 text-white opacity-7">Distance</h6>
                                        <h3 class="text-white">145 <small class="text-sm align-top">Km</small></h3>
                                    </div>
                                    <div class="ms-lg-6 ms-4">
                                        <h6 class="mb-0 text-white opacity-7">Average Energy</h6>
                                        <h3 class="text-white">300 <small class="text-sm align-top">Kw</small></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12 text-center">
                                <div class="d-flex align-items-center">
                                    <h4 class="text-white opacity-7 ms-0 ms-md-auto">Available Range</h4>
                                    <h2 class="text-white ms-2 me-auto">70<small class="text-sm align-top"> %</small></h2>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12 my-auto">
                                <h4 class="text-white opacity-9">Nearest Charger</h4>
                                <hr class="horizontal light mt-1 mb-3">
                                <div class="d-flex">
                                    <div>
                                        <h6 class="mb-0 text-white">Miclan, DW</h6>
                                        <h6 class="mb-0 text-white">891 Limarenda road</h6>
                                    </div>
                                    <div class="ms-lg-6 ms-4">
                                        <button class="btn btn-icon-only btn-rounded btn-outline-white mb-0">
                                            <i class="ni ni-map-big" aria-hidden="true"></i>
                                        </button>
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
    </script>
@endsection

