<?php

namespace Guysolamour\Administrable\Http\Controllers\Front\Auth;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class ForgotPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;



    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return front_view('auth.passwords.email');
    }
}
