<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Settings;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index()
    {
        $data['title']='Clients';
        return view('admin.users',$data);

    }
    public function dashboard()
    {
        //check if user is an admin or normal user
        $user = \auth()->user();
        $data['title'] = "Dashboard";
        if (!is_null($user)) {
            if ($user->isAdmin()){
                //return to admin dashboard;
                return  redirect()->route('admin.dashboard');
            }
        }
        return view('dashboard',$data);
    }

    //
}
