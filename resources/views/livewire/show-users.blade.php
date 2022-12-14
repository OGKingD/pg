<div>

    <table class="container table table-flush" id="datatable-buttons">
        <thead>
        <tr>
            <th>{{__('S/N')}}</th>
            <th class="scope"></th>
            <th>{{__('Name')}}</th>
            <th>{{__('Business name')}}</th>
            <th>{{__('Email')}}</th>
            <th>{{__('Status')}}</th>
            <th>{{__('Balance')}}</th>
            <th>{{__('Created')}}</th>
            <th>{{__('Updated')}}</th>
        </tr>
        </thead>
        <tbody>
        {{-- Nothing in the world is as soft and yielding as water. --}}
        @foreach($users as $k=>$val)

            <tr>
                <td>{{++$k}}.</td>
                <td class="text-right">
                    <div class="dropstart">
                        <a href="javascript:;" class="text-success" id="dropdownUserOptions" data-bs-toggle="dropdown"
                           aria-expanded="false">
                            <i class="fas fa-ellipsis-v " aria-hidden="true"> </i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-start px-2 py-3 bg-light-vertical"
                            aria-labelledby="dropdownUserOptions">
                            <li>
                                <a class="dropdown-item border-radius-md text-info" href="javascript:"
                                   onclick="openEditPaymentGatewayModal({{$val->Gateways}},'{{$val->id}}')">
                                    Edit {{$val->first_name}} Gateway(s)
                                </a>
                            </li>
                            <hr class="dropdown-divider">

                            <li>
                                <a class="dropdown-item border-radius-md" href="javascript:;">See Details</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                @if($val->status==0)

                                    <a class="dropdown-item border-radius-md text-danger" href="javascript:"
                                       wire:click="blockUser('{{$val->email}}','block')"
                                       onclick="salert('SUCCESS','Done','success')">
                                        <i class="fas fa-stop-circle"> </i>
                                        Disable {{$val->first_name}}
                                    </a>

                                @elseif($val->status==1)
                                    <a class="dropdown-item border-radius-md text-success" href="javascript:"
                                       wire:click="blockUser('{{$val->email}}','activate')"
                                       onclick="salert('SUCCESS','Done','success')">
                                        <i class="fas fa-check-circle"> </i>
                                        Activate {{$val->first_name}}
                                    </a>

                                @endif


                            </li>
                        </ul>
                    </div>
                </td>

                <td>{{$val->first_name.' '.$val->last_name}}</td>
                <td>{{$val->business_name}}</td>
                <td>{{$val->email}}</td>
                <td>
                    @if($val->status==0)
                        <span class="badge badge-pill badge-success" id="{{$val->email}}+status">{{__('Active')}}</span>
                    @elseif($val->status==1)
                        <span class="badge badge-pill badge-danger" id="{{$val->email}}+status">{{__('Blocked')}}</span>
                    @endif
                </td>
                <td>{{number_format($val->balance,'2','.','')}}</td>
                <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                <td>{{date("Y/m/d h:i:A", strtotime($val->updated_at))}}</td>
            </tr>
        @endforeach
        {{ $usersCollection->links() }}

        </tbody>

    </table>

    {{--    //modal to edit gateway comes here;--}}
    <div class="modal fade" id="modal-edit-user-gateway" tabindex="-1" aria-labelledby="modal-edit-user-gateway"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <form role="form" action="{{route('paymentgateway.edit')}}" method="post"
                      onsubmit="editUserPaymentGateways(this)">
                    <div class="modal-header ">
                        <h3 class="modal-title font-weight-bolder text-info text-gradient justify-content-center">
                            Edit {{$selectedUserName}} Gateway(s) </h3>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="card card-plain justify-content-center">

                            <div class="card-body">
                                @if(!is_null($gateways))

                                    @foreach($gateways as $key => $gway)

                                        <fieldset>
                                            <legend> {{$gway['name']}}</legend>

                                            <div class="row">
                                                <div class="col-5">
                                                    <label class="form-label " for="charge_factor"> Charge Type
                                                        :</label>
                                                    <select class="form-control form-select " id="charge_factor"
                                                            title="Percentage / Flat Rate" name="charge_type+{{$key}}">
                                                        <option value="0" @if(!$gway['charge_factor']) selected @endif>
                                                            Flat
                                                            Rate
                                                        </option>
                                                        <option value="1" @if($gway['charge_factor']) selected @endif>
                                                            Percentage(%)
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-6">
                                                    <label class="form-label" for="charge_factor_{{$key}}}}"> Charge
                                                        Factor
                                                        Value:</label>
                                                    <div class=" input-group mb-3">
                                                        <input type="number" class="form-control"
                                                               id="charge_factor_{{$key}}" placeholder="Value..."
                                                               value="{{$gway['charge']}}"
                                                               aria-label="Charge_Factor_value" onfocus="focused(this)"
                                                               onfocusout="defocused(this)" name="charge+{{$key}}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col">
                                                <label class="form-check-label" for="status">Gateway Status:</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="status"
                                                           name="status+{{$key}}"
                                                           checked="checked"> Active

                                                </div>
                                            </div>

                                            <div class="col d-none">
                                                <label>
                                                    <input type="text" name="name+{{$key}}" value="{{$gway['name']}}" >
                                                </label>
                                            </div>

                                        </fieldset>

                                        <hr>

                                    @endforeach

                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-center">
                            <button type="submit" class="btn btn-round bg-gradient-info btn-lg w-100 mt-4 mb-0">
                                Update
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>


    @section('scripts')
        <script>
            function openEditPaymentGatewayModal(payload, userId) {

                @this.selectedUser= userId;
                @this.gateways = payload.config_details;


                Swal.fire({
                    title: 'Fetching ' + name + ' Gateways!',
                    html: '<span class="spinner-border text-primary"></span>',
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEnterKey: false,
                })


            }


            function gatewaysFetched() {
                Swal.close();
                $('#modal-edit-user-gateway').modal('show');
            }

            window.addEventListener('gatewaysFetched', event => {
                gatewaysFetched();
            });

            function editUserPaymentGateways(element) {
                event.preventDefault();
                const formData = new FormData(element);
                let formValues = {};
                formData.forEach(function (value, key) {
                    formValues[key] = value;
                });
                //bind value;
                @this.editedUsersGateways= JSON.stringify(formValues);
                //trigger method;
                Livewire.emit('editUserPaymentGateways');
                Swal.fire({
                    title: 'Updating  Gateway Details!',
                    html: '<span class="spinner-border text-primary"></span>',
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEnterKey: false,
                });

            }


            function gatewaysUpdated() {
                salert("Merchant Gateway Updated", "Success", 'success');
                toggleModal('#modal-edit-user-gateway');
            }

            window.addEventListener('merchantGatewayUpdated', event => {
                gatewaysUpdated();
            });

        </script>
    @endsection

</div>


