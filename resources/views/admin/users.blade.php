@extends('layouts.admin.admin_dashboardapp')

@section('content')
    <div class="card card-plain">

        <div class="card-body pb-3">
            @livewire('search-users')

        </div>
    </div>


    <div class="card min-vh-100">
        <div class="card-header header-elements-inline">
            <h3 class="mb-0">{{__('Users')}}</h3>
        </div>
        <div class="table-responsive py-4">

                @livewire('show-users')

        </div>
    </div>


@endsection






