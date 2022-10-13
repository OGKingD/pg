<?php

namespace App\Http\Livewire;

use App\Models\Gateway;
use App\Models\MerchantGateway;
use App\Models\User;
use App\Models\UserGateway;
use Livewire\Component;
use Livewire\WithPagination;

class ShowUsers extends Component
{
    use WithPagination;

    protected $listeners = ['refreshUsers','editUserPaymentGateways'];

    public $users;
    public $selectedUser;
    public $selectedUserName;
    public $gateways;
    public $editedUsersGateways;

    public $searchUsers = false;



    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        if ((isset($this->gateways) && is_array($this->gateways))) {
            $selUser = User::whereId($this->selectedUser)->first();
            $this->selectedUserName = $selUser->first_name. " ". $selUser->last_name;
            $this->dispatchBrowserEvent('gatewaysFetched');
        }

        $builder = User::with('usergateway')->where('type','=', 5);
        $users_tpl = !$this->searchUsers ? $builder->Paginate(10) : $builder->where('email', 'like', "{$this->searchUsers}%")->paginate(10);

        $this->users = collect($users_tpl->items());

        return view('livewire.show-users',["usersCollection" => $users_tpl]);
    }


    public function refreshUsers($email)
    {
        $this->searchUsers = $email;

    }

    public function blockUser($email, $status)
    {
        if ($status){
            //toggle users status;
            $user = User::where('email',$email)->first();
            $user->update([
                "status" => $status === "block" ? 1 : 0
            ]);


        }

    }

    public function editUserPaymentGateways()
    {
        $config_details = [];
        $merchantGateways = json_decode($this->editedUsersGateways, true, 512, JSON_THROW_ON_ERROR);

        //Get all the gateways;
        $gateways = Gateway::select('id')->get();
        foreach ($gateways as $gateway) {
            $config_details[$gateway->id] = [
                "charge" => $merchantGateways['charge+'.$gateway->id],
                "name" => $merchantGateways['name+'.$gateway->id],
                'charge_factor' => $merchantGateways['charge_type+'.$gateway->id],
                'status' =>   (isset($merchantGateways["status+$gateway->id"])) ? 1 : 0
            ];
        }


        $merchantGatewayUpdated = UserGateway::where('user_id',$this->selectedUser)->first()->update([
            'config_details' => $config_details
        ]);
        logger($merchantGatewayUpdated);

        if ($merchantGatewayUpdated) {
            $this->dispatchBrowserEvent('merchantGatewayUpdated');
        }
    }





}
