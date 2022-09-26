<div>
    <div class="row">

        @forelse($invoices as $invoice)
            <div class="col-lg-2 col-sm-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex mb-4">
                            <span class="justify-content-start  font-weight-bolder text-black-50" style="word-break: break-all">{{$invoice->invoice_no}}</span>
                            <div class="justify-content-end dropdown ">
                                <a href="javascript:;" class="text-secondary ps-4" id="dropdownCam" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end me-sm-n4 px-2 py-3" aria-labelledby="dropdownCam" style="">
                                    <li><a class="dropdown-item border-radius-md" href="javascript:;">Pause</a></li>
                                    <li><a class="dropdown-item border-radius-md" href="javascript:;">Stop</a></li>
                                    <li><a class="dropdown-item border-radius-md" href="javascript:;">Schedule</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item border-radius-md text-danger" href="javascript:;">Remove</a></li>
                                </ul>
                            </div>
                        </div>
                        <i class="fas fa-file-invoice fa-7x">

                        </i> <br>
                        <span class="mt-4 mb-0 font-weight-bold">{{$invoice->name}}</span> <br>
                        <span class="mt-4 mb-0 font-weight-bolder">{{number_format($invoice->amount,2)}}</span> <br>
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
                                     src="{{asset('assets/img/illustrations/printing-invoices.svg')}}" alt="chat-img">
                            </div>
                        </div>

                    </div>
                </div>
            </div>


        @endforelse

        <div class="col-lg-2 col-sm-5 mb-4 mr-4">
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
                <form role="form"  method="post" name="invoiceAdd" id="invoiceAdd" onsubmit="addInvoice(this)">
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
                                                       style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAAAXNSR0IArs4c6QAAAfBJREFUWAntVk1OwkAUZkoDKza4Utm61iP0AqyIDXahN2BjwiHYGU+gizap4QDuegWN7lyCbMSlCQjU7yO0TOlAi6GwgJc0fT/fzPfmzet0crmD7HsFBAvQbrcrw+Gw5fu+AfOYvgylJ4TwCoVCs1ardYTruqfj8fgV5OUMSVVT93VdP9dAzpVvm5wJHZFbg2LQ2pEYOlZ/oiDvwNcsFoseY4PBwMCrhaeCJyKWZU37KOJcYdi27QdhcuuBIb073BvTNL8ln4NeeR6NRi/wxZKQcGurQs5oNhqLshzVTMBewW/LMU3TTNlO0ieTiStjYhUIyi6DAp0xbEdgTt+LE0aCKQw24U4llsCs4ZRJrYopB6RwqnpA1YQ5NGFZ1YQ41Z5S8IQQdP5laEBRJcD4Vj5DEsW2gE6s6g3d/YP/g+BDnT7GNi2qCjTwGd6riBzHaaCEd3Js01vwCPIbmWBRx1nwAN/1ov+/drgFWIlfKpVukyYihtgkXNp4mABK+1GtVr+SBhJDbBIubVw+Cd/TDgKO2DPiN3YUo6y/nDCNEIsqTKH1en2tcwA9FKEItyDi3aIh8Gl1sRrVnSDzNFDJT1bAy5xpOYGn5fP5JuL95ZjMIn1ya7j5dPGfv0A5eAnpZUY3n5jXcoec5J67D9q+VuAPM47D3XaSeL4AAAAASUVORK5CYII=&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%;">
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
                                        <label  class="col-form-label " for="due_date">Due Date</label>
                                        <div class="input-group">
                                            <span class="input-group-prepend">
                                              <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </span>
                                            <input class="form-control datepicker" placeholder="Please select date" type="text" name="due_date" id="due_date" onfocus="focused(this)" onfocusout="defocused(this)">

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


</div>

@section('scripts')
    <script>
        function addInvoice(element) {
            event.preventDefault();
            console.log(element)
            const formData = new FormData(element);
            let formValues = {};
            formData.forEach(function (value, key) {
                formValues[key] = value;
            });
            //bind value;
        @this.invoiceDetails = JSON.stringify(formValues);
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

        window.addEventListener('invoiceAdded', event => {
            invoiceAdded();
        });
    </script>

@endsection
