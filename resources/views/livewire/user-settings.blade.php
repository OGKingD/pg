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

                    let htmlMessage = '<span class="spinner-border text-primary"></span>';
                    salert('Fetching Settings!','','info',false,false,false,false,htmlMessage);

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
                    formData.forEach( (value, key) => {
                        formValues[key] = value;
                    })
                    //bind value;
                    @this.
                    editedUsersGateways = JSON.stringify(formValues);
                    //trigger method;
                    Livewire.emit('editUserPaymentGateways');
                    let htmlMessage = '<span class="spinner-border text-primary"></span>';
                    salert('Updating Gateway Details!','','info',false,false,false,false,htmlMessage);

                }

                function resetApiKey() {
                    Swal.fire({
                        title: "Are you sure you want to reset API Key for {{$selectedUserName}}?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: 'rgba(226,24,24,0.83)',
                        confirmButtonText: 'Yes, Reset!',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        allowEnterKey: false,

                    }).then((result) => {
                        if (result.isConfirmed) {
                            let htmlMessage = '<span class="spinner-border spinner-border-lg text-primary"></span>\n';
                            salert('Reset in progress. Please Wait!','','info',false,false,false,false,htmlMessage);
                            Livewire.emit('resetApiKey');
                        }
                    });
                }

                window.addEventListener('apiKeyReset', event => {
                    copyTextToClipboard("api_key");
                });

                window.addEventListener('settingsUpdated', event => {

                    let message = event.detail.message ?? 'Settings Updated';
                    salert('Settings Updated!',message,'success',true);

                });


                function uploadAvatar(element) {
                    event.preventDefault();
                    const formData = new FormData(element);
                    let formValues = {};
                    formData.forEach(function (value, key) {
                        formValues[key] = value;
                    })
                    //bind value;
                    @this.
                    avatar = JSON.stringify(formValues);
                    //trigger method;
                    Livewire.emit('uploadAvatar');

                    let htmlMessage = '<span class="spinner-border text-primary"></span>';
                    salert('Uploading!','','info',false,false,false,false,htmlMessage);

                }


            </script>

        @endsection
    </div>
</div>
