<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $data = inspirationalText();
        $data['title'] = "Login";

        return view('auth.login', $data);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     */
    public function store(LoginRequest $request)
    {

        $request->authenticate();
        $request->session()->regenerate();
        $ip_address = $request->ip();
        $user = User::whereid(auth()->user()->id)->first();
        if ($ip_address != $user->ip_address) {
            send_email($user->email, $user->username, 'Suspicious Login Attempt', ' Your account was just accessed from an unknown IP address<br> <b> ' . $ip_address . '</b><br>If this was you, please you can ignore this message or reset your account password.', [], 'error');
        }
        $user->last_login = Carbon::now();
        $user->ip_address = $ip_address;
        $user->save();
        //2fa authentication goes here;
        if ($user->twofactor_auth) {
            //set 2fa flag to true
            session()->put('2fa',true);
            $user->send2fa();
            return redirect()->route('2fa');
        }

        return redirect()->route('user.dashboard');

    }

    /**
     * Destroy an authenticated session.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function twoFactor()
    {
        $data['title'] = "Two Factor Authentication ";
        return view('auth.2fa', $data);

    }

    public function twoFactorVerify(Request $request)
    {
        //get OTP sent to user;
        $otp = $request->user()->otp;
        $userOtp = implode("",$request->otp);
        if ($otp){
            //validate otp entered matches otp on record;
            if ($otp->otp === $userOtp){
                //validate time has not elapsed 15mins;
                $diffInMinutes = Carbon::parse()->diffInMinutes($otp->updated_at);
                if ($diffInMinutes > 15){
                    return redirect()->back()->with("error","Oops 😬!  Code Entered has Expired, Kindly Generate a new Code. ");
                }
                //success Purge 2fa from session;
                session()->forget('2fa');
                return redirect()->route('user.dashboard')->with("success","Verification Successful");
            }
            return redirect()->back()->with("error","Invalid Code Entered! ");
        }
        return redirect()->back()->with("error","Unable To Retrieve Code! ");


    }

    public function twoFactorResend(Request $request)
    {
        try {
            $request->user()->send2fa();
            return true;

        } catch (\Exception $e) {
            logger($e->getMessage(), (array) $e);
        }
        return false;


    }
}
