<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class EmailSearch extends Component
{

    /**
     * @var string
     */
    public $emailToSearch;
    /**
     * @var mixed
     */
    public $users = [];

    public function render()
    {
        return view('livewire.email-search');
    }
    public function searchForUser()
    {
        $this->passEmailToAllLivewireComponents($this->emailToSearch);

    }

    public function passEmailToAllLivewireComponents($email)
    {
        $this->emitTo('show-users','refreshUsers',$email);

    }
}
