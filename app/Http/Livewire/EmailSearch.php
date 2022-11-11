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
    public $emails = [];

    public function render()
    {
        return view('livewire.email-search');
    }
    public function searchForUser()
    {
        //search for User;
        if (empty($this->emailToSearch)){
            $this->emails = [];
        }
        if (!empty($this->emailToSearch)){
            $this->emails = User::select(['id','email'])->where('email', 'like', "%{$this->emailToSearch}%")->get()->take(5);
        }
        //change and search by phone when result is empty
    }

    public function passEmailToAllLivewireComponents($email)
    {
        $this->emitTo('show-users','refreshUsers',$email);

    }
}
