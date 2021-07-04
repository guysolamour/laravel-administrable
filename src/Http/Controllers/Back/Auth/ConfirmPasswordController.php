<?php


namespace Guysolamour\Administrable\Http\Controllers\Back\Auth;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class ConfirmPasswordController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */
    use ConfirmsPasswords;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(config('administrable.guard') . '.auth');
    }

    /**
     * Where to redirect admins after registration.
     *
     * @var string
     */
    public function redirectTo()
    {
        return config('administrable.auth_prefix_path');
    }


    /**
     * Display the password confirmation view.
     *
     * @return \Illuminate\Http\Response
     */
    public function showConfirmForm()
    {
        return back_view('auth.passwords.confirm');
    }

    /**
     * Reset the password confirmation timeout.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function resetPasswordConfirmationTimeout(Request $request)
    {
        $request->session()->put(config('administrable.guard')  . '.auth.password_confirmed_at', time());
    }

    /**
     * Get the password confirmation validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'password' => 'required|password:' . config('administrable.guard'),
        ];
    }
}
