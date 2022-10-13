@extends('layouts.merchant_dashboardapp')


@section('content')

    <div class=" mt-4">
        <div class="container card min-vh-70">
            <div class="card-header header-elements-inline">
                <h3 class="mb-0">Invoice(s)</h3>
            </div>
            @livewire('invoice-page')

        </div>
    </div>

@endsection



