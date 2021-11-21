<?php

namespace Guysolamour\Administrable\Http\Controllers\Front\Auth;

use  Illuminate\Http\Request;
use  Illuminate\Foundation\Auth\ResetsPasswords;
use  App\Providers\RouteServiceProvider;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class ResetPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


      /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request)
    {
        $token = $request->route()->parameter('token');

        return front_view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
