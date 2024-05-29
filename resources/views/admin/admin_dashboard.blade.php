@extends('layouts.admin.admin_dashboardapp')


@section('content')
    <div>
        <div class="mb-3">
            <div class="card bg-gradient-primary">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8 my-auto">
                            <div class="numbers">
                                <h3 class="text-white font-weight-bolder text-capitalize font-weight-bold  mb-0">
                                    {{$greeting}} - °
                                </h3>
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
        <div class="row mt-4">
            <div class="col-lg-6">
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
                                    <span>{{number_format($transactions_count)}}</span>
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
                                                <span class="text-xs font-weight-bold">  {{number_format($successful_transactions_count)}} == {{empty($transactions_count) ?0 : number_format((($successful_transactions_count/$transactions_count) * 100) ,1). "%" }}</span>
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
                                                <span class="text-xs font-weight-bold"> {{number_format($pending_transactions_count)}} == {{empty($transactions_count) ?0 : number_format((($pending_transactions_count/$transactions_count) * 100) ,1). "%" }}</span>
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
                                                <span class="text-xs font-weight-bold"> {{number_format($failed_transactions_count)}} == {{empty($transactions_count) ?0 : number_format((($failed_transactions_count/$transactions_count) * 100) ,1). "%" }}</span>
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


                <div class="card mt-2">
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
                            @foreach($gateways as $gateway)
                                @php
                                    $channel = strtolower(str_replace(" ", "_", $gateway->name));
                                @endphp


                                <div class="col-md-6 mb-md-2">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <h1 class="text-gradient text-primary"><span id="status3" countto="{{(${$channel."_transactions_count"})}}">{{number_format((${$channel."_transactions_count"}))}}</span> <span
                                                    class="text-lg ms-n2">°</span></h1>
                                            <h6 class="mb-0 font-weight-bolder">{{$gateway->name}}</h6>

                                        </div>
                                    </div>
                                </div>
                            @endforeach



                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-6 ">
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
                                        + &#{{nairaSymbol()}} {{ number_format($successful_googlepay_transactions_total)}}
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
                                        + &#{{nairaSymbol()}} {{ number_format($pending_googlepay_transactions_total)}}
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
                                        - &#{{nairaSymbol()}} {{ number_format($failed_googlepay_transactions_total)}}
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
                                        + &#{{nairaSymbol()}} {{ number_format($successful_applepay_transactions_total)}}
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
                                        + &#{{nairaSymbol()}} {{ number_format($pending_applepay_transactions_total)}}
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
                                        - &#{{nairaSymbol()}} {{ number_format($failed_applepay_transactions_total)}}
                                    </div>
                                </div>
                                <hr class="horizontal dark mt-3 mb-2">
                            </li>
                        </ul>
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
