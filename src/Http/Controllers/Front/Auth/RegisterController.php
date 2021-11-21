<?php

namespace Guysolamour\Administrable\Http\Controllers\Front\Auth;

use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Guysolamour\Administrable\Http\Controllers\BaseController;
use Illuminate\Support\Arr;

class RegisterController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return front_view('auth.register');
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'           => ['required', 'string', 'max:255'],
            'phone_number'   => ['nullable', 'string', 'max:255', 'unique:users'],
            'pseudo'         => ['nullable', 'string', 'max:255', 'unique:users'],
            'email'          => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \Illuminate\Foundation\Auth\User
     */
    protected function create(array $data)
    {
        return config('administrable.modules.user_dashboard.model')::create([
            'name'           => Arr::get($data, 'name'),
            'pseudo'         => Arr::get($data, 'pseudo'),
            'phone_number'   => Arr::get($data, 'phone_number'),
            'email'          => Arr::get($data, 'email'),
            'password'       => Hash::make(Arr::get($data, 'password')),
        ]);
    }
}
