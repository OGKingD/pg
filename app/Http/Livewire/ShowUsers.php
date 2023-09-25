<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ShowUsers extends Component
{
    use WithPagination;

    protected $listeners = ['refreshUsers','editUserPaymentGateways','fetchUsersPaymentGateways','uploadAvatar'];

    public $selectedUser;
    public $selectedUserName;
    public $gateways;
    public $merchantGateways;
    public $refreshUsers = false;
    private $usersCollection;
    public $editedUsersGateways;
    public $bank_transfer_provider,$first_name,$last_name,$email,$avatar;

    public $searchUsers = false;



    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        $builder = User::where('type','=', 5);
        $this->usersCollection = !$this->searchUsers ? $builder->Paginate(10) : $builder->where('email', 'like', "{$this->searchUsers}%")->paginate(10);

        return view('livewire.show-users',["usersCollection" => $this->usersCollection, 'users' => $this->usersCollection]);
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




}
