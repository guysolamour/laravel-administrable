<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Auth\Access\AuthorizationException;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */

    public function redirectTo()
    {
        return config('administrable.auth_prefix_path');
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(config('administrable.guard') . '.auth');
        $this->middleware('signed')->only(config('administrable.guard') . '.verify');
        $this->middleware('throttle:6,1')->only(config('administrable.guard') . '.verify', 'resend');
    }

    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return $request->user(config('administrable.guard'))->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : back_view('auth.verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        if (! hash_equals((string) $request->route('id'), (string) $request->user(config('administrable.guard'))->getKey())) {
            throw new AuthorizationException;
        }

        if (! hash_equals((string) $request->route('hash'), sha1($request->user(config('administrable.guard'))->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($request->user(config('administrable.guard'))->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        if ($request->user(config('administrable.guard'))->markEmailAsVerified()) {
            event(new Verified($request->user(config('administrable.guard'))));
        }

        return redirect($this->redirectPath())->with('verified', true);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user(config('administrable.guard'))->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        $request->user(config('administrable.guard'))->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }

}
