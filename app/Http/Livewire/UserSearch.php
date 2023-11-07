<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class UserSearch extends Component
{
    public $username;
    public $users = [];

    public function render()
    {
        return view('livewire.user-search');
    }

    public function searchForUser()
    {
        //search for User;
        if (empty($this->username)){
            $this->users = [];
        }
        if (!empty($this->username)){
            $this->users = User::select(['id','last_name','first_name'])->where('type',5)->whereRaw('(`first_name` like "%c%" or `last_name` like "%c%")')->get()->take(5);
        }
        //change and search by phone when result is empty
    }

}
