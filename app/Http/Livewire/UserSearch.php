<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class UserSearch extends Component
{
    public $username;
    private $currentUserType;
    public $users = [];

    public function render()
    {
        $this->currentUserType = auth()->user()->type;
        return view('livewire.user-search');
    }

    public function searchForUser()
    {
        //search for User;
        if (empty($this->username)){
            $this->users = [];
        }
        if (!empty($this->username)){

            $userQuery = User::select(['id', 'last_name', 'first_name'])->
                whereRaw('`first_name` like "%'.$this->username.'%" or `last_name` like "%'.$this->username.'%"');
            //change query for normal users;
            if ($this->currentUserType > 2 ){
                $type = 5;
            }
            if (isset($type)){
                $this->users = $userQuery->where('type',$type);
            }

            $this->users = $userQuery->get()->take(5);
        }
        //change and search by phone when result is empty
    }

}
