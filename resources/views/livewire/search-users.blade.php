<div >
    <div class="page-header   position-relative m-3 border-radius-xl">
        <img src="{{asset('assets/img/shapes/waves-white.svg')}}" alt="pattern-lines" class="position-absolute opacity-6 start-0 top-0 w-100">

    </div>
    <form role="form" action="#" wire:submit.prevent="searchUsers" >
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
                <div class="col-md-6">
                    <label>Name</label>

                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="fas fa-user-alt"></i></span>
                        <input type="text" required name="name" class="form-control" wire:model="name" wire:change.debounce.500ms="fetchUserByName(name)"   placeholder="Name" aria-label="First Name" >
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
