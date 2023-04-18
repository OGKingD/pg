<div>
    {{-- The whole world belongs to you. --}}
    <div class="input-group input-group-merge input-group-alternative mb-3">
            <span class="input-group-text">
                    <i class="fa fa-envelope-circle-check" style="font-size: 15px;"></i>
                </span>
        <input id="customer_email" type="text" placeholder="Email Address"
               class="form-control" name="customer_email" wire:model.debounce.550ms="emailToSearch"
               wire:keydown.debounce.550ms="searchForUser()"
        >

    </div>
    @if(count($users))

        <div id="userListing" >
            <ul class="list-group">
                @foreach($users as $user)
                    <li class="list-group-item"  onclick="setUserField('email','{{$user->email}}','{{$user->id}}')" >
                        {{$user->email}}
                    </li>
                @endforeach
            </ul>
            <div class="d-flex justify-content-end">
                <button type="button"  class=" bg-danger mx-0"> <i class="fa fa-refresh"></i></button>
            </div>
        </div>

    @endif

    <label hidden>
        <input type="number" name="user_id" hidden id="userId">
    </label>



</div>
