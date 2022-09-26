<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class SearchUsers extends Component
{
    /**
     * @var mixed
     */
    public  $emails;
    public  $emailtoSearch;
    /**
     * @var mixed
     */
    public  $names;

    public function render()
    {

        return view('livewire.search-users');
    }

    public function searchForUser()
    {
        //search for User;
        $this->emails = User::where('email', 'like', "%{$this->emailtoSearch}%")->get()->take(5);
        //change and search by phone when result is empty
    }

    public function retrieveUserFromSearch($emails)
    {
        $this->emitTo('show-users','refreshUsers',$emails);

    }


}
