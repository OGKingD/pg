@extends('layouts.admin.admin_dashboardapp')

@section('content')


    <div class="card min-vh-100">

        <div class="table-responsive py-4">

            @livewire('show-users')

        </div>
    </div>


@endsection






