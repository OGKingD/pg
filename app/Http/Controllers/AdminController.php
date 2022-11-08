<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;

class AdminController extends Controller
{


    public function adminlogin()
    {
        $title['title'] = "Administrator Login";
        return view('admin.admin_login', $title);
    }


    public function submitadminlogin(LoginRequest $request)
    {

        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');

    }


}
