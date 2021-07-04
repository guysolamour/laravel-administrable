<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(config('administrable.guard') . '.guest:' . config('administrable.guard'),
            ['except' => 'logout']
        );
    }


     /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ],['login.required' => 'Le Pseudo ou l\'Email est requis']);
    }

     /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return [$this->username() => $request->input('login'), 'password' => $request->input('password')];
    }


    public function username()
    {
        return $this->guessUsername();
    }


    private function guessUsername() :string
    {
        return filter_var(request('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'pseudo';
    }


    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function redirectTo()
    {
        return config('administrable.auth_prefix_path');
    }



    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard(config('administrable.guard'));
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return back_view('auth.login');
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect()->route(config('administrable.guard') . '.dashboard');
    }

    /**
    * The user has logged out of the application.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return mixed
    */
    protected function loggedOut(Request $request)
    {
        return redirect()->home();
    }

}
