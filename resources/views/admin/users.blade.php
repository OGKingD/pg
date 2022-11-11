@extends('layouts.admin.admin_dashboardapp')

@section('content')
    <div class="card card-plain">

        <div class="card-body pb-3">
            <div>
                <div class="page-header   position-relative m-3 border-radius-xl">
                    <img src="{{asset('assets/img/shapes/waves-white.svg')}}" alt="pattern-lines"
                         class="position-absolute opacity-6 start-0 top-0 w-100">

                </div>
                <form role="form" action="#" wire:submit.prevent="searchUsers">
                    @csrf

                    <div class="pb-lg-3 pb-3 pt-2 postion-relative z-index-2">
                        <h3 class="text">Search</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_email" class="col-form-label text-md-right">
                                        {{__("Merchant Email")}}
                                    </label>
                                    <livewire:email-search />
                                </div>

                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success mx-3"> Search</button>
                                <button type="submit" class="btn btn-danger mx-3"> Reset</button>
                            </div>

                        </div>
                    </div>

                </form>


            </div>

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






