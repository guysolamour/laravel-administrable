<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Auth;


use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class RegisterController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new admins as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
     */
    public function __construct()
    {
        $this->middleware(config('administrable.guard') . '.guest:' . config('administrable.guard'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'pseudo'     => ['required', 'string', 'max:255','unique:' . Str::plural(config('administrable.guard'))],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:' . Str::plural(config('administrable.guard'))],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function create(array $data)
    {
        /**
         * @var \Illuminate\Foundation\Auth\User
         */
        $guard = get_guard_model_class();

        return $guard::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'pseudo'     => $data['pseudo'],
            'email'      => $data['email'],
            'password'   => $data['password'],
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return back_view('auth.register');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard(config('administrable.guard'));
    }

}
