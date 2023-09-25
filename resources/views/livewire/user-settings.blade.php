<div class="container-fluid py-4 ">
    <div class="d-sm-flex justify-content-between">
        @if($selectedUser)
            @include('partials.users.settings')
        @endif

        @section('scripts')
            <script>
                addEventListener('openSettingsModal', function () {
                    openSettingsModal()
                });

                addEventListener('setSearchField', function () {
                    let value = event.detail.email;
                    setUserField(value);
                    document.getElementById('customer_email').value = value;

                });

                function openSettingsModal() {

                    Swal.fire({
                        title: 'Fetching Settings!',
                        html: '<span class="spinner-border text-primary"></span>',
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEnterKey: false,
                    })

                }


                function settingsFetched() {
                    Swal.close();
                    // $('#modal-edit-user-gateway').modal('show');
                    // toggleModal('#modal-user-settings');

                }

                window.addEventListener('settingsFetched', event => {
                    settingsFetched();
                });

                function editUserPaymentGateways(element) {
                    event.preventDefault();
                    const formData = new FormData(element);
                    let formValues = {};
                    formData.forEach(function (value, key) {
                        formValues[key] = value;
                    });
                    //bind value;
                    @this.
                    editedUsersGateways = JSON.stringify(formValues);
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


                window.addEventListener('merchantGatewayUpdated', event => {

                    Swal.fire({
                        title: 'Settings Updated',
                        text: "Success!",
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    })
                });

                function uploadAvatar(element) {
                    event.preventDefault();
                    const formData = new FormData(element);
                    let formValues = {};
                    formData.forEach(function (value, key) {
                        formValues[key] = value;
                    });
                    //bind value;
                    @this.
                    avatar = JSON.stringify(formValues);
                    //trigger method;
                    Livewire.emit('uploadAvatar');
                    Swal.fire({
                        title: 'Uploading!',
                        html: '<span class="spinner-border text-primary"></span>',
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEnterKey: false,
                    });

                }


            </script>

        @endsection
    </div>
</div>
