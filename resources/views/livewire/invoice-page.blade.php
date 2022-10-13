<div>
    <div class="row ">
        @forelse($invoices as $invoice)

            <div class="col-md-2 mt-md-0 mb-4">
                <div class="card">
                    <div class=" dropdown">
                        <a href="javascript:;" class="text-secondary ps-4" id="dropdownCam"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end me-sm-n4 px-2 py-3"
                            aria-labelledby="dropdownCam" style="">

                            @if($invoice->status === "pending")
                                <li>
                                    <a class="dropdown-item border-radius-md" href="javascript:;"
                                       wire:click="openEditInvoiceModal({{$invoice}})">Edit</a>
                                </li>
                                <li class="d-none" id="invoiceLink"> {{config('app.url')}}/payment/process/{{$invoice->invoice_no}} </li>
                                <li>
                                    <a class="dropdown-item border-radius-md" href="javascript:;" onclick="copyTextToClipboard('invoiceLink')">Copy Link </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item border-radius-md text-danger"
                                       href="javascript:;">Remove</a>
                                </li>
                            @endif

                        </ul>
                    </div>

                    <div class="card-header mx-4 p-3 text-center">
                        <div
                            class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                            <i class="fas fa-file-invoice-dollar opacity-10" aria-hidden="true"></i>
                        </div>

                    </div>

                    <div class="card-body pt-0 p-3 text-center">
                        <h6 class="text-center mb-0">{{$invoice->invoice_no}}</h6>
                        <span class="badge text-white text-xs
                            @if($invoice->status === "successful") bg-gradient-success
                            @elseif ($invoice->status === "pending" ) bg-gradient-warning
                            @elseif ($invoice->status === "failed" ) bg-gradient-danger
                            @endif">{{$invoice->status}}</span>
                        <br>
                        <span class="text-xs">{{$invoice->name}}</span>
                        <hr class="horizontal dark my-3">
                        <h5 class="mb-0">&#8358;{{number_format($invoice->amount,2)}}</h5>
                        <span class="text-xs">Due Date: {{\Carbon\Carbon::parse()->diffInDays($invoice->due_date)}} day(s)</span>

                    </div>
                </div>
            </div>

        @empty

            <div class="col-lg-10 col-sm-8 mt-lg-0 mt-4">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="justify-content-start card card-plain">
                            <div class="card-header pb-0 text-start">
                                <h2 class="font-weight-bolder">You have not created any Invoices yet!</h2>
                                <p class="mb-0">Your Invoices would appear here.</p>
                            </div>

                        </div>

                    </div>

                    <div class="col-lg-8">
                        <div class=" m-3 px-7 border-radius-lg d-flex flex-column justify-content-end">
                            <img src="{{asset('assets/img/shapes/pattern-lines.svg')}}" alt="pattern-lines"
                                 class="position-absolute opacity-4 start-0">
                            <div class="">
                                <img class="max-width-500 w-100 position-relative z-index-2"
                                     src="{{asset('assets/img/illustrations/printing-invoices.svg')}}"
                                     alt="chat-img">
                            </div>
                        </div>

                    </div>
                </div>
            </div>


        @endforelse

        <div class="col-md-2 mt-md-0 mb-4  ">
            <a role="button" href="javascript:;" data-bs-toggle="modal" data-bs-target="#modal-new-invoice">
                <div class="card h-100  bg-info">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                        <i class="fa fa-plus text-white mb-3" aria-hidden="true"></i>
                        <h5 class="text-white"> New Invoice </h5>
                    </div>
                </div>
            </a>
        </div>
    </div>


    {{--    Modal for Invoice comes here--}}

    <div class="modal fade" id="modal-new-invoice" tabindex="-1" aria-labelledby="modal-new-invoice"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <form role="form" method="post" name="invoiceAdd" id="invoiceAdd" onsubmit="addInvoice(this)">
                    @csrf

                    <div class="modal-header ">
                        <h3 class="modal-title font-weight-bolder text-info text-gradient justify-content-center">
                            Create New Invoice </h3>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="card card-plain justify-content-center">
                            <div class="card-header pb-0 ">
                                <p class="mb-0">Enter Invoice Details</p>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <label>
                                                Name
                                                <input type="text" name="item_name" class="form-control"
                                                       placeholder="Invoice Name" required="" onfocus="focused(this)"
                                                       onfocusout="defocused(this)"
                                                >
                                            </label>

                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <label>
                                                Quantity:
                                                <input type="number" name="quantity" class="form-control" value="1"
                                                       required="">
                                            </label>

                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <label>
                                                Amount:
                                                <input type="number" step="any" class="form-control" name="amount"
                                                       required="">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <label>
                                                Email:
                                                <input type="email" name="email" class="form-control" placeholder=""
                                                       required="">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label class="col-form-label " for="due_date">Due Date</label>
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                              <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </span>
                                            <input class="form-control datepicker" placeholder="Please select date"
                                                   type="text" name="due_date" id="due_date" onfocus="focused(this)"
                                                   onfocusout="defocused(this)">

                                        </div>
                                    </div>


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

    <div class="modal fade" id="modal-edit-invoice" tabindex="-1" aria-labelledby="modal-edit-invoice"
         aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <form role="form" method="post" name="invoiceEdit" id="invoiceEdit" onsubmit="updateInvoice(this)">
                    @csrf

                    <div class="modal-header ">
                        <h3 class="modal-title font-weight-bolder text-info text-gradient justify-content-center">
                            Edit Invoice {!! $invoiceNo !!}
                        </h3>
                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="card card-plain justify-content-center">
                            <div class="card-header pb-0 ">
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <label>
                                                Name
                                                <input type="text" name="item_name" class="form-control"
                                                       id="edit-invoice-name"
                                                       placeholder="Invoice Name" required="" onfocus="focused(this)"
                                                       onfocusout="defocused(this)" value="{{$invoiceName}}">
                                            </label>

                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <label>
                                                Quantity:
                                                <input type="number" name="quantity" class="form-control"
                                                       value="{{$invoiceQuantity}}" id="edit-invoice-quantity"
                                                       required="">
                                            </label>

                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <label>
                                                Amount:
                                                <input type="number" step="any" class="form-control" name="amount"
                                                       id="edit-invoice-amount"
                                                       required="" value="{{$invoiceAmount}}">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <label>
                                                Email:
                                                <input type="email" name="email" class="form-control" placeholder=""
                                                       id="edit-invoice-email" required="" value="{{$invoiceEmail}}">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label class="col-form-label " for="due_date">Due Date</label>
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                              <span class="input-group-text"><i class="fas fa-calendar-alt"> </i></span>
                                            </span>
                                            <input class="form-control datepicker" placeholder="Please select date"
                                                   type="text" name="due_date" id="due_date" onfocus="focused(this)"
                                                   onfocusout="defocused(this)" value="{{$invoiceDueDate}}">

                                        </div>
                                    </div>


                                </div>


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


</div>

@section('scripts')
    <script>
        function addInvoice(element) {
            event.preventDefault();
            const formData = new FormData(element);
            let formValues = {};
            formData.forEach(function (value, key) {
                formValues[key] = value;
            });
            //bind value;
        @this.invoiceDetails
            = JSON.stringify(formValues);
            //trigger method;
            Livewire.emit('addInvoice');
            Swal.fire({
                title: 'Generating Invoice Please wait!',
                html: '<span class="spinner-border text-primary"></span>',
                allowEscapeKey: false,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEnterKey: false,
            });

        }

        function invoiceAdded() {
            salert("Invoice Added Successfully", "Success", 'success');
            toggleModal('#modal-new-invoice');
            document.getElementById('invoiceAdd').reset();
        }

        function invoiceUpdated() {
            salert("Invoice Updated Successfully", "Success", 'success');
            toggleModal('#modal-edit-invoice');
        }

        window.addEventListener('invoiceAdded', event => {
            invoiceAdded();
        });

        window.addEventListener('openEditInvoiceModal', event => {
            //trigger Modal;
            openEditInvoiceModal();
        })

        function openEditInvoiceModal() {

            toggleModal('#modal-edit-invoice');
        }

        function updateInvoice(element) {
            event.preventDefault();
            const formData = new FormData(element);
            let formValues = {};
            formData.forEach(function (value, key) {
                formValues[key] = value;
            });
            //bind value;
        @this.invoiceDetails
            = JSON.stringify(formValues);
            //trigger method;
            Livewire.emit('updateInvoice');
            Swal.fire({
                title: 'Updating Invoice Please wait!',
                html: '<span class="spinner-border text-primary"></span>',
                allowEscapeKey: false,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEnterKey: false,
            });
        }

        window.addEventListener('invoiceUpdated', event => {
            invoiceUpdated();
        });
    </script>

@endsection
