<div class="container-fluid py-4 ">
    <div class="d-sm-flex justify-content-between">
        <div class="me-2">
            <button type="button" class="btn btn-icon bg-gradient-primary" data-bs-toggle="modal"
                    data-bs-target="#modal-new-gateway">New Gateway
            </button>
            <div class="modal fade" id="modal-new-gateway" tabindex="-1" aria-labelledby="modal-new-gateway"
                 aria-hidden="true" style="display: none;">
                <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                    <div class="modal-content">
                        <form role="form" id="addPaymentGatewayForm" action="{{route('paymentgateway.add')}}" method="post" wire:submit.prevent="addPaymentGateway">
                            @csrf

                            <div class="modal-header ">
                                <h3 class="modal-title font-weight-bolder text-info text-gradient justify-content-center">
                                    Create New Gateway </h3>
                                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"
                                        aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body p-0">
                                <div class="card card-plain justify-content-center">
                                    <div class="card-header pb-0 ">
                                        <p class="mb-0">Enter Gateway Name and Details</p>
                                    </div>

                                    <div class="card-body">
                                        <label>Gateway Name</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Name..." aria-label="Name"
                                                   aria-describedby="name-addon"
                                                   onfocus="focused(this)" onfocusout="defocused(this)" id="edit-gateway-name"
                                                   wire:model.defer="gatewayName">
                                        </div>

                                        <label class="form-check-label" for="edit-gateway-status">Gateway Status:</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="edit-gateway-status"
                                                   name="status" wire:model.defer="gatewayStatus"> Active
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-round bg-gradient-info btn-lg w-100 mt-4 mb-0">
                                        Create
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="d-flex">
            <div class="dropdown d-inline">
                <a href="javascript:;" class="btn btn-outline-dark dropdown-toggle " data-bs-toggle="dropdown"
                   id="navbarDropdownMenuLink2">
                    Filters
                </a>
                <ul class="dropdown-menu dropdown-menu-lg-start px-2 py-3"
                    aria-labelledby="navbarDropdownMenuLink2" data-popper-placement="left-start">
                    <li><a class="dropdown-item border-radius-md" href="javascript:;">Status: Paid</a></li>
                    <li><a class="dropdown-item border-radius-md" href="javascript:;">Status: Refunded</a></li>
                    <li><a class="dropdown-item border-radius-md" href="javascript:;">Status: Canceled</a></li>
                    <li>
                        <hr class="horizontal dark my-2">
                    </li>
                    <li><a class="dropdown-item border-radius-md text-danger" href="javascript:;">Remove
                            Filter</a></li>
                </ul>
            </div>
            <button class="btn btn-icon btn-outline-dark ms-2 export" data-type="csv" type="button">
                <span class="btn-inner--icon"><i class="ni ni-archive-2"></i></span>
                <span class="btn-inner--text">Export CSV</span>
            </button>
        </div>
    </div>

    <div class="row min-vh-90">

        <div class="card table-responsive">
            <div class="dataTable-wrapper dataTable-loading sortable  fixed-columns">
                <div class="dataTable-container">
                    <table class="table table-flush " id="datatable-basic">
                        <thead class="thead-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable=""><a href="#" class="dataTable-sorter">#</a></th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable=""><a href="#" class="dataTable-sorter">Name</a></th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable=""><a href="#" class="dataTable-sorter">Status</a></th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable=""><a href="#" class="dataTable-sorter"></a></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($gateways as $key => $gateway)
                            <tr>
                                <td class="text-sm font-weight-normal">{{$key + 1}}</td>
                                <td class="text-xs font-weight-normal">
                                    <div class="d-flex align-items-center">
                                        @if($gateway->status)
                                            <button
                                                class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 p-3 btn-sm d-flex align-items-center justify-content-center">
                                                <i class="fas fa-check-circle " style="font-size: 15px"
                                                   aria-hidden="true"></i></button>
                                        @else
                                            <button
                                                class="btn  btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 p-3 btn-sm d-flex align-items-center justify-content-center">
                                                <i class="fas fa-times-circle " style="font-size: 15px"
                                                   aria-hidden="true"></i></button>
                                        @endif
                                        <span>{{$gateway->status ? "Active" : "Disabled"}}</span>
                                    </div>
                                </td>
                                <td class="text-xs font-weight-normal">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs me-2 bg-gradient-dark">
                                            <span>{{ucfirst(substr($gateway->name,0,1))}}</span>
                                        </div>
                                        <span>{{$gateway->name}}</span>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-icon btn-sm btn-outline-dark ms-2 " type="button"
                                            onclick="openPaymentGatewayModal({{$gateway}})">
                                        <span class="btn-inner--icon"><i class="fas fa-edit"></i></span>
                                        <span class="btn-inner--text">Edit</span>
                                    </button>

                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" rowspan="5" class="text-sm font-weight-normal">
                                    <div class="min-vw-100">
                                        <h3 >No Gateways Configured! Please click the button to create a gateway !</h3>
                                        <button type="button" class="btn btn-icon bg-gradient-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-new-gateway">New Gateway
                                        </button>
                                    </div>

                                </td>
                            </tr>

                        @endforelse

                        {{$gatewaysCollection->links()}}

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modal-edit-gateway" tabindex="-1" aria-labelledby="modal-edit-gateway"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <form role="form" wire:submit.prevent="updatePaymentGateway" id="editPaymentGatewayForm">

                    @csrf

                    <div class="modal-header ">
                        <h3 class="modal-title font-weight-bolder text-info text-gradient justify-content-center">Edit
                            Gateway </h3>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="card card-plain justify-content-center">
                            <div class="card-header pb-0 ">
                                <p class="mb-0">Edit Gateway Details</p>
                            </div>
                            <div class="card-body">
                                <label>Gateway Name</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Name..." aria-label="Name"
                                           aria-describedby="name-addon"
                                           onfocus="focused(this)" onfocusout="defocused(this)" id="edit-gateway-name"
                                           wire:model.defer="gatewayName">
                                </div>

                                <label class="form-check-label" for="edit-gateway-status">Gateway Status:</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="edit-gateway-status"
                                           name="status" wire:model.defer="gatewayStatus"> Active
                                </div>

                                <div class="d-none">
                                    <label>Gateway Id</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" placeholder="Gateway ID..."
                                               aria-label="Gateway ID" aria-describedby="gateway-id"
                                               onfocus="focused(this)" onfocusout="defocused(this)" id="edit-gateway-id"
                                               wire:model="gatewayId" readonly>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="text-center">
                            <button type="submit" class="btn btn-round bg-gradient-success btn-lg w-100 mt-4 mb-0">
                                Save
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>


    @section('scripts')
        <script>


            function openPaymentGatewayModal(payload) {

                let editGatewayName = document.getElementById('edit-gateway-name');
                let editGatewayStatus = document.getElementById('edit-gateway-status');
                let editGatewayId = document.getElementById('edit-gateway-id');

                //bind to the form
                @this.gatewayName
                    = editGatewayName.value = payload.name;
                @this.gatewayId
                    = editGatewayId.value = payload.id;
                @this.gatewayStatus
                = editGatewayStatus.checked = payload.status;


                //trigger Modal;
                toggleModal('#modal-edit-gateway');

            }

            window.addEventListener('gatewayUpdated', event => {
                Swal.close();
                toggleModal('#modal-edit-gateway');
                salert("Payment Gateway Updated", "Success", "success");
            });

            window.addEventListener('gatewayCreated', event => {
                Swal.close();
                toggleModal('#modal-new-gateway');
                document.getElementById('addPaymentGatewayForm').reset();
                salert("Payment Gateway Created", "Success","success");

            });
            window.addEventListener('processingEvent', event => {
                Swal.fire({
                    title: 'Processing Please Wait!',
                    html: '<span class="spinner-border text-primary"></span>',
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEnterKey: false,
                });

            });


        </script>

        @if(count($gateways))
            <script>
                $('#datatable-basic').DataTable({
                    paging: false,
                    ordering: true,
                    info: false,
                    fixedHeight: true,
                    searchable: true,

                });
            </script>
        @endif
    @endsection


</div>


