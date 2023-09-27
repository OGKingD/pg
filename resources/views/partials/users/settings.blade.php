{{--    //Settings Modal;--}}
{{--<div class="modal fade" id="modal-user-settings" tabindex="-1" aria-labelledby="modal-user-settings"--}}
{{--     aria-hidden="true" style="display: none;">--}}
{{--    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">--}}
{{--    </div>--}}
{{--</div>--}}
<div>
    {{--            //content for Modal come here--}}

    <div class="py-lg-3">
        <div class="row mb-5">
            <div class="col-lg-10 mx-auto text-center">
                <h3 class="mb-1 font-weight-bolder">
                    {{$selectedUserName}} Settings!
                </h3>
            </div>
            {{--                    //sidebar--}}
            <div class="col-lg-3">
                <div class="card position-sticky top-1">
                    <ul class="nav flex-column bg-white border-radius-lg p-3">
                        <li class="nav-item">
                            <a class="nav-link text-body" data-scroll="" href="#profile">
                                <i class="fa fa-user text-dark me-2"></i>
                                <span class="text-sm">Profile</span>
                            </a>
                        </li>

                        <li class="nav-item pt-2">
                            <a class="nav-link text-body" data-scroll="" href="#basic-info">
                                <i class="fa fa-user-friends text-dark me-2"></i>
                                <span class="text-sm">Basic Info</span>
                            </a>
                        </li>
                        <li class="nav-item pt-2">
                            <a class="nav-link text-body" data-scroll="" href="#password">
                                <i class="fa fa-key text-dark me-2"></i>
                                <span class="text-sm">Change Password</span>
                            </a>
                        </li>

                        <li class="nav-item pt-2">
                            <a class="nav-link text-body" data-scroll="" href="#gateways">
                                <i class="fa fa-credit-card text-dark me-2"></i>
                                <span class="text-sm">Payment Gateways</span>
                            </a>
                        </li>

                        <li class="nav-item pt-2">
                            <a class="nav-link text-body" data-scroll="" href="#virtual_accounts">
                                <i class="fa fa-bank text-dark me-2"></i>
                                <span class="text-sm">Virtual Accounts</span>
                            </a>
                        </li>

                        <li class="nav-item pt-2">
                            <a class="nav-link text-body" data-scroll="" href="#notifications">
                                <i class="fa fa-bell-concierge text-dark me-2"></i>
                                <span class="text-sm">Notifications</span>
                            </a>
                        </li>

                        <li class="nav-item pt-2">
                            <a class="nav-link text-body" data-scroll="" href="#delete">
                                <i class="fa fa-delete-left text-dark me-2"></i>
                                <span class="text-sm">Delete Account</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-9 mt-lg-0 mt-4">

                <div class="card card-body" id="profile">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-sm-auto col-4">
                            <div class="avatar avatar-xl position-relative">
                                <img @if($merchantAvatar) src="{{asset('assets/avatars/'.$merchantAvatar)}}" @else  src="{{asset('assets/img/saanapay.png')}} " @endif alt="User Avatar"
                                     class="w-100 border-radius-lg shadow-sm">
                            </div>
                        </div>
                        <div class="col-sm-auto col-8 my-auto">
                            <div class="h-100">
                                <h5 class="mb-1 font-weight-bolder">
                                    {{$selectedUserName}}
                                </h5>
                                <p class="mb-0 font-weight-bold text-sm">
                                    {{$selectedUser['business_name']}}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-auto ms-sm-auto mt-sm-0 mt-3 d-flex">
                            @if($selectedUser['status'])
                                <span class="badge badge-success ms-auto">Enabled</span>
                            @else
                                <span class="badge badge-danger ms-auto">Disabled</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card mt-4" id="basic-info">
                    <div class="card-header">
                        <h5>Basic Info</h5>
                    </div>
                    <div class="card-body pt-0">

                        <form role="form" method="post" enctype="multipart/form-data" wire:submit.prevent="uploadAvatar">
                            @csrf
                            <div class="row">
                                <div class="col">
                                    <label class="form-label mt-4">Avatar/Image</label>
                                    <div class="input-group">

                                        <input id="avatar" name="avatar" class="form-control" type="file"
                                               onfocus="focused(this)" wire:model.defer="avatar"
                                               onfocusout="defocused(this)">
                                    </div>
                                    <label hidden>
                                        <input type="number" hidden="" value="{{$selectedUser['id']}}" name="user_id">
                                    </label>

                                </div>
                            </div>

                            <button class="btn bg-gradient-dark btn-sm mt-2 mb-0" type="submit">Upload</button>

                        </form>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label" for="firstName">First Name</label>
                                <div class="input-group">
                                    <input id="firstName" name="firstName" class="form-control" type="text"
                                           required="required" onfocus="focused(this)"
                                           wire:model.defer="first_name"
                                           onfocusout="defocused(this)" value="{{$selectedUser['first_name']}}">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="lastName">Last Name</label>
                                <div class="input-group">
                                    <input id="lastName" name="lastName" class="form-control" type="text"
                                           placeholder="Thompson" required="required" onfocus="focused(this)"
                                           wire:model.defer="last_name"
                                           onfocusout="defocused(this)" value="{{$selectedUser['last_name']}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label mt-4" for="email">Email</label>
                                <div class="input-group">
                                    <input id="email" name="email" class="form-control" type="email"
                                           wire:model.defer="email"
                                           placeholder="example@email.com" onfocus="focused(this)"
                                           onfocusout="defocused(this)" value="{{$selectedUser['email']}}">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card mt-4" id="password">
                    <div class="card-header">
                        <h5>Change Password</h5>
                    </div>
                    <div class="card-body pt-0">
                        <label class="form-label" for="password">Current password</label>
                        <div class="form-group">
                            <input class="form-control" type="password" placeholder="Current password"
                                   onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                        <label class="form-label">New password</label>
                        <div class="form-group">
                            <input class="form-control" type="password" placeholder="New password"
                                   onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                        <label class="form-label">Confirm new password</label>
                        <div class="form-group">
                            <input class="form-control" type="password" placeholder="Confirm password"
                                   onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                        <h5 class="mt-5">Password requirements</h5>
                        <p class="text-muted mb-2">
                            Please follow this guide for a strong password:
                        </p>
                        <ul class="text-muted ps-4 mb-0 float-start">
                            <li>
                                <span class="text-sm">One special characters</span>
                            </li>
                            <li>
                                <span class="text-sm">Min 6 characters</span>
                            </li>
                            <li>
                                <span class="text-sm">One number (2 are recommended)</span>
                            </li>
                            <li>
                                <span class="text-sm">Change it often</span>
                            </li>
                        </ul>
                        <button class="btn bg-gradient-dark btn-sm float-end mt-6 mb-0">Update password</button>
                    </div>
                </div>


                <div class="card mt-4" id="gateways">
                    <div class="card-header">
                        <h5>Payment Gateways</h5>
                        <p class="text-sm ">Here you can setup and manage {{$selectedUserName}} payment
                            gateways. Configure the charges and status of vairous payment channels/gateways. </p>
                    </div>
                    <div class="card-body pt-0">
                        @include('partials.users.edit-users-gateways')
                    </div>
                </div>

                <div class="card mt-4" id="virtual_accounts">
                    <div class="card-header">
                        <h5>Virtual Accounts</h5>
                        <p class="text-sm">
                            Choose how you provider to power virtual/dynamic accounts for Bank Transfer. These
                            settings
                            apply to bank transfer method.</p>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-8">
                                <label class="form-label" for="bank_transfer_provider"> Virtual Account/ Bank
                                    Transfer Provider: </label>
                                <div class="input-group">
                                    <select class="form-control form-select " id="virtual_account_provider"
                                            title="Virtual Account" name="bank_transfer_provider"
                                            wire:model.defer="bank_transfer_provider">
                                        <option value="">Choose Provider</option>
                                        <option value="9PSB">9PSB</option>
                                        <option value="PROVIDUS">PROVIDUS</option>
                                    </select>
                                </div>
                                <button class="btn bg-gradient-dark btn-sm mt-2 mb-0"
                                        wire:click="updateBankTransferProvider">Update
                                </button>

                            </div>

                        </div>


                    </div>
                </div>

                <div class="card mt-4" id="notifications">
                    <div class="card-header">
                        <h5>Notifications</h5>
                        <p class="text-sm">Choose how you receive notifications. These notification settings
                            apply to the things you’re watching.</p>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th class="ps-1" colspan="4">
                                        <p class="mb-0">Activity</p>
                                    </th>
                                    <th class="text-center">
                                        <p class="mb-0">Email</p>
                                    </th>
                                    <th class="text-center">
                                        <p class="mb-0">Push</p>
                                    </th>
                                    <th class="text-center">
                                        <p class="mb-0">SMS</p>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="ps-1" colspan="4">
                                        <div class="my-auto">
                                            <span class="text-dark d-block text-sm">Mentions</span>
                                            <span class="text-xs font-weight-normal">Notify when another user mentions you in a comment</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" checked="" type="checkbox"
                                                   id="flexSwitchCheckDefault11">
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" type="checkbox"
                                                   id="flexSwitchCheckDefault12">
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" type="checkbox"
                                                   id="flexSwitchCheckDefault13">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-1" colspan="4">
                                        <div class="my-auto">
                                            <span class="text-dark d-block text-sm">Comments</span>
                                            <span class="text-xs font-weight-normal">Notify when another user comments your item.</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" checked="" type="checkbox"
                                                   id="flexSwitchCheckDefault14">
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" checked="" type="checkbox"
                                                   id="flexSwitchCheckDefault15">
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" type="checkbox"
                                                   id="flexSwitchCheckDefault16">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-1" colspan="4">
                                        <div class="my-auto">
                                            <span class="text-dark d-block text-sm">Follows</span>
                                            <span class="text-xs font-weight-normal">Notify when another user follows you.</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" type="checkbox"
                                                   id="flexSwitchCheckDefault17">
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" checked="" type="checkbox"
                                                   id="flexSwitchCheckDefault18">
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" type="checkbox"
                                                   id="flexSwitchCheckDefault19">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-1" colspan="4">
                                        <div class="my-auto">
                                            <p class="text-sm mb-0">Log in from a new device</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" checked="" type="checkbox"
                                                   id="flexSwitchCheckDefault20">
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" checked="" type="checkbox"
                                                   id="flexSwitchCheckDefault21">
                                        </div>
                                    </td>
                                    <td>
                                        <div
                                            class="form-check form-switch mb-0 d-flex align-items-center justify-content-center">
                                            <input class="form-check-input" checked="" type="checkbox"
                                                   id="flexSwitchCheckDefault22">
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="card mt-4" id="delete">
                    <div class="card-header">
                        <h5>Delete Account</h5>
                        <p class="text-sm mb-0">Once you delete your account, there is no going back. Please be
                            certain.</p>
                    </div>
                    <div class="card-body d-sm-flex pt-0">
                        <div class="d-flex align-items-center mb-sm-0 mb-4">
                            <div class="me-2">
                                <span class="text-dark font-weight-bold d-block text-sm">Confirm</span>
                                <span class="text-xs d-block">I want to delete my account.</span>
                            </div>
                        </div>
                        @if($selectedUser['status'])
                            <button class="btn btn-outline-secondary btn-warning mb-0 ms-auto" type="button"
                                    name="button"
                                    wire:click="blockUser('{{$selectedUser['email']}}',{{$selectedUser['status']}})">
                                Deactivitate
                            </button>
                        @else
                            <button class="btn btn-outline-secondary btn-success mb-0 ms-auto" type="button"
                                    name="button"
                                    wire:click="blockUser('{{$selectedUser['email']}}',{{$selectedUser['status']}})">
                                Activate
                            </button>
                        @endif

                        <button class="btn bg-gradient-danger mb-0 ms-2" type="button" name="button">Delete
                            Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


