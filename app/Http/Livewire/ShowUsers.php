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

    protected $listeners = ['refreshUsers','editUserPaymentGateways','fetchUsersPaymentGateways'];

    public $selectedUser;
    public $selectedUserName;
    public $gateways;
    public $merchantGateways;
    public $refreshUsers = false;
    private $usersCollection;
    public $editedUsersGateways;

    public $searchUsers = false;



    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        $builder = User::with('usergateway')->where('type','=', 5);
        $this->usersCollection = !$this->searchUsers ? $builder->Paginate(10) : $builder->where('email', 'like', "{$this->searchUsers}%")->paginate(10);

        return view('livewire.show-users',["usersCollection" => $this->usersCollection, 'users' => $this->usersCollection]);
    }

    public function openEditPaymentGatewayModal($merchantGateways, $merchantId, $merchantName)
    {
        $this->dispatchBrowserEvent('openEditPaymentGatewayModal');
        $config_details = [];

        $merchantGateways = json_decode($merchantGateways, true, 512, JSON_THROW_ON_ERROR)['config_details'];
        $this->selectedUser = $merchantId;
        $this->selectedUserName = $merchantName;
        //Get all the gateways;
        $gateways = Gateway::select(['id','name'])->get();

        foreach ($gateways as $gateway) {
            $config_details[$gateway->id] = [
                "charge" => $merchantGateways[$gateway->id]['charge'] ?? 0,
                "name" => $merchantGateways[$gateway->id]['name'] ?? $gateway->name,
                'charge_factor' => $merchantGateways[$gateway->id]['charge_factor'] ?? 0,
            ];
            $config_details[$gateway->id]['status'] = $merchantGateways[$gateway->id]["status"] ?? 0;

        }
        $this->merchantGateways = $config_details;

        $this->dispatchBrowserEvent('gatewaysFetched');

    }

    public function searchUsers()
    {
        $builder = User::with('usergateway')->where('type','=', 5);
        $this->usersCollection = !$this->searchUsers ? $builder->Paginate(10) : $builder->where('email', 'like', "{$this->searchUsers}%")->paginate(10);

        $this->resetPage();

    }


    public function refreshUsers($email)
    {
        $this->searchUsers = $email;
        $this->dispatchBrowserEvent('setSearchField',['email' => $email]);

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

        if ($merchantGatewayUpdated) {
            $this->dispatchBrowserEvent('merchantGatewayUpdated');
        }
    }





}
