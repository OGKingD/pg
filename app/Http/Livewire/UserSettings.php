<?php

namespace App\Http\Livewire;

use App\Models\Gateway;
use App\Models\MerchantWebhook;
use App\Models\User;
use App\Models\UserGateway;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class
UserSettings extends Component
{
    /**
     * @var \App\Models\UserSettings
     */
    public $settings;
    /**
     * @var User
     */
    public $selectedUser;
    public $userId,$userSettings,$selectedUserName,$merchantGateways,$bank_transfer_provider,
        $first_name,$last_name,$email,$avatar, $pageReloading, $editedUsersGateways,
        $merchantAvatar, $merchantWebhook;

    protected $listeners = ['editUserPaymentGateways'];

    use WithFileUploads;


    public function mount($id)
    {
        $this->userId = $id;
        $this->selectedUser= User::with(['usergateway','usersettings','webhook_url'])->firstWhere('id','=', $this->userId);
        $this->getUserData();

    }
    public function render()
    {
        $this->layout = 'layouts.admin.admin_dashboardapp';
        return view('livewire.user-settings',['selectedUser' => $this->selectedUser])->extends($this->layout, ["title" => "Settings"]);
    }

    public function getUserData()
    {
        if (!$this->pageReloading) {
            $this->dispatchBrowserEvent('openSettingsModal');
        }
        $config_details = [];

        $merchantGateways = $this->selectedUser['usergateway']['config_details'];
        $this->bank_transfer_provider = $this->selectedUser['usersettings']['values']['bank_transfer_provider'] ?? null;
        $this->first_name = $this->selectedUser['first_name'];
        $this->last_name = $this->selectedUser['last_name'];
        $this->email = $this->selectedUser['email'];
        $this->selectedUserName = $this->first_name . " ". $this->last_name;
        $this->merchantAvatar = false;
        if ($this->selectedUser['usersettings']){
            $this->merchantAvatar = $this->selectedUser['usersettings']['values']['avatar'];
        }
        if ($this->selectedUser['webhook_url']){
            $this->merchantWebhook = $this->selectedUser['webhook_url']['url'];
        }
        //Get all the gateways;
        $gateways = Gateway::select(['id','name'])->get();

        foreach ($gateways as $gateway) {
            $config_details[$gateway->id]['name'] = $merchantGateways[$gateway->id]['name'] ?? $gateway->name;
            $config_details[$gateway->id]['status'] = $merchantGateways[$gateway->id]["status"] ?? 0;

            $config_details[$gateway->id]['merchant_service'] = [
                "charge" => $merchantGateways[$gateway->id]['merchant_service']['charge'] ?? 0,
                'charge_factor' => $merchantGateways[$gateway->id]['merchant_service']['charge_factor'] ?? 0,
            ];
            $config_details[$gateway->id]['customer_service'] = [
                "charge" => $merchantGateways[$gateway->id]['customer_service']['charge'] ?? 0,
                'charge_factor' => $merchantGateways[$gateway->id]['customer_service']['charge_factor'] ?? 0,
            ];
        }
        $this->merchantGateways = $config_details;

        if (!$this->pageReloading){
            $this->dispatchBrowserEvent('settingsFetched');
        }

    }

    public function editUserPaymentGateways()
    {
        $config_details = [];
        $merchantGateways = json_decode($this->editedUsersGateways, true, 512, JSON_THROW_ON_ERROR);

        //Get all the gateways;
        $gateways = Gateway::select(['id','name'])->get();
        foreach ($gateways as $gateway) {
            //merchantService
            $config_details[$gateway->id]['merchant_service'] = [
                "charge" => $merchantGateways['merchant_service_charge+'.$gateway->id],
                'charge_factor' => $merchantGateways['merchant_service_charge_factor+'.$gateway->id],
            ];
            //customerService
            $config_details[$gateway->id]['customer_service'] = [
                "charge" => $merchantGateways['customer_service_charge+'.$gateway->id],
                'charge_factor' => $merchantGateways['customer_service_charge_factor+'.$gateway->id],
            ];
            $config_details[$gateway->id]['name'] = $gateway->name;
            $config_details[$gateway->id]['status'] =  (isset($merchantGateways["status+$gateway->id"])) ? 1 : 0;

        }


        $merchantGatewayUpdated = UserGateway::where('user_id',$this->userId)->first()->update([
            'config_details' => $config_details
        ]);

        if ($merchantGatewayUpdated) {
            $this->pageReloading = true;
            $this->dispatchBrowserEvent('merchantGatewayUpdated');
        }
    }

    public function updateBankTransferProvider()
    {
        $this->bank_transfer_provider = strtoupper($this->bank_transfer_provider);
        $data = [
            "bank_transfer_provider" => $this->bank_transfer_provider,
            "avatar" => $this->avatar,
        ];

        $values  = $data;

        if (!empty($this->bank_transfer_provider)) {
            if ($this->settings) {
                $values = array_merge($this->settings->values, $data);
            }
            $this->selectedUser->userSettings()->updateOrInsert([
                "user_id" => $this->userId,
            ], [
                'values' => json_encode($values, JSON_THROW_ON_ERROR)
            ]);
            $this->pageReloading = true;
            $this->dispatchBrowserEvent('merchantGatewayUpdated');
        }

    }

    public function uploadAvatar()
    {
        //avatar upload;
        $avatarName = $this->selectedUser['id'];
        if (isset($this->avatar)){
            /** @var TemporaryUploadedFile $avatar */
            $avatar = $this->avatar;
            $this->validate([
                'avatar' => 'image|max:5024', // 5MB Max
            ]);
            $allowedExtensions = ['jpg','jpeg','png'];
            foreach ($allowedExtensions as $allowedExtension) {
                $filename = public_path('assets/avatars/' . $avatarName . "." . $allowedExtension);
                if (file_exists($filename)){
                    //deleteFile;
                    unlink($filename);
                }
            }
            $avatarName .= ".".$avatar->getClientOriginalExtension();
            $this->merchantAvatar = $avatarName;

            $avatar->storeAs('avatars',$avatarName,'assets');
            $data = [
                "bank_transfer_provider" => $this->bank_transfer_provider,
                "avatar" => $avatarName,
            ];

            $values  = $data;
            if ($this->settings) {
                $values = array_merge($this->settings->values, $data);
            }
            $this->selectedUser->userSettings()->updateOrInsert([
                "user_id" => $this->userId,
            ], [
                'values' => json_encode($values, JSON_THROW_ON_ERROR)
            ]);
            $this->pageReloading = true;
            $this->dispatchBrowserEvent('merchantGatewayUpdated');

        }

    }


    public function blockUser($status)
    {
        //toggle users status;
        $user = User::where('id',$this->userId)->first();
        $user->update([
            "status" => ($status === 1) ? 0 : 1
        ]);
        $this->setPageReloading();

    }

    public function updateWebhook()
    {
        //updateWebhook;
        MerchantWebhook::updateOrCreate(['user_id' => $this->userId],[
            'url' => $this->merchantWebhook
        ])->update();
        $this->setPageReloading();
    }

    private function setPageReloading($status=true): void
    {
        $this->pageReloading = $status;
        $this->dispatchBrowserEvent('merchantGatewayUpdated');
    }

}
