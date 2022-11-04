<div class="container-fluid py-4 ">
    <div class="d-sm-flex justify-content-between">

        <div>
            <div class="page-header   position-relative m-3 border-radius-xl">
                <img src="{{asset('assets/img/shapes/waves-white.svg')}}" alt="pattern-lines" class="position-absolute opacity-6 start-0 top-0 w-100">

            </div>
            <form role="form" action="#" wire:submit.prevent="searchTransactions" >
                @csrf

                <div class="pb-lg-3 pb-3 pt-2 postion-relative z-index-2">
                    <h3 class="text">Search</h3>

                    <div class="row">
                        <div class="col-md-6">
                            <label>Email</label>

                            <div class="row">
                                <div class="">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text"><i class="fas fa-envelope-open"> &nbsp; </i></span>
                                        <input type="text"  name="email" class="form-control" wire:model="emailtoSearch"  wire:keydown.debounce.450ms="searchForUser()"  placeholder="Email" aria-label="Email" >
                                    </div>
                                </div>

                                <div class="">

                                    @if(!empty($emails))

                                        <div>
                                            <ul class="list-group">
                                                @foreach($emails as $email)
                                                    <li class="list-group-item" wire:keydown="retrieveUserFromSearch('{{$email->email}}')">{{$email->email}}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif


                                </div>
                            </div>

                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success mx-3"> Search </button>
                            <button type="submit" class="btn btn-danger mx-3"> Reset </button>
                        </div>

                    </div>
                </div>

            </form>

        </div>

    </div>
    <div class="d-block">
        <button class="btn btn-icon btn-outline-dark ms-2 export" data-type="csv" type="button">
            <span class="btn-inner--icon"><i class="ni ni-archive-2"></i></span>
            <span class="btn-inner--text">Export CSV</span>
        </button>
    </div>
    <div class="row min-vh-90">

        <div class="card table-responsive">
            <div class="dataTable-wrapper dataTable-loading sortable  fixed-columns">
                <div class="dataTable-container">
                    <table class="table table-flush " id="datatable-basic">
                        <thead class="thead-light">
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                data-sortable="">
                                <a href="#" class="dataTable-sorter">#</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sortable="">
                                <a href="#" class="dataTable-sorter">Transaction Ref</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sortable="">
                                <a href="#" class="dataTable-sorter">Merchant Transaction Ref</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sortable="">
                                <a href="#" class="dataTable-sorter">Gateway</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sortable="">
                                <a href="#" class="dataTable-sorter">Amount</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sortable="">
                                <a href="#" class="dataTable-sorter">Fee</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sortable="">
                                <a href="#" class="dataTable-sorter">Total</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sortable="">
                                <a href="#" class="dataTable-sorter">Status</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sortable="">
                                <a href="#" class="dataTable-sorter">Flag</a>
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sortable="">
                                <a href="#" class="dataTable-sorter">Date</a>
                            </th>


                        </tr>
                        </thead>
                        <tbody>
                        @forelse($transactions as $k=>$val)
                            <tr>
                                <td class="text-sm font-weight-normal">{{++$k}}</td>
                                <td class="text-sm font-weight-normal">{{$val->transaction_ref}}</td>
                                <td class="text-sm font-weight-normal">{{$val->merchant_transaction_ref}}</td>
                                <td class="text-sm font-weight-normal">{{ $val->gateway->name??  "N/A"}}</td>
                                <td> &#{{nairaSymbol()}} {{number_format($val->amount,'2','.','')}}</td>
                                <td>&#{{nairaSymbol()}} {{number_format($val->fee,'2','.','')}}</td>
                                <td>&#{{nairaSymbol()}} {{number_format($val->total,'2','.','')}}</td>
                                <td class="text-sm font-weight-normal">{{$val->status}}</td>
                                <td class="text-sm font-weight-normal">{{$val->flag}}</td>

                                <td>{{date("Y/m/d h:i:A", strtotime($val->created_at))}}</td>
                            </tr>
                        @empty

                            <tr>
                                <td colspan="4" rowspan="5" class="text-sm font-weight-normal">
                                    <div class="justify-content-start card card-plain">
                                        <div class="card-header pb-0 text-start">
                                            <h2 class="font-weight-bolder">😢 There are no Transactions Yet!</h2>
                                            <p class="mb-0 text-center">Once Users start transacting they'll appear
                                                here.</p>
                                        </div>

                                    </div>

                                </td>
                            </tr>

                        @endforelse

                        {{ $transactionsCollection->links() }}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>


    @section('scripts')

        @if(count($transactions))
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


