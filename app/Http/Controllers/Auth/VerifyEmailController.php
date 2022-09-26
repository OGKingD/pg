<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        $dashboard = $request->user()->isAdmin() ? RouteServiceProvider::adminDashboard : RouteServiceProvider::HOME;
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($dashboard)->with('status','🍻  Cheers 🍻 ! Thank you for verifying your Email 🥂 ! ');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended($dashboard)->with('status','🍻  Cheers 🍻 ! Thank you for verifying your Email 🥂 ! ');
    }
}
