<?php

namespace Guysolamour\Administrable\Http\Controllers\Front\Dashboard;


use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function showEditProfileForm()
    {
        return front_view('dashboard.profile.edit', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'phone_number'   => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore(auth()->id())],
            'pseudo'         => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore(auth()->id())],
            'custom_fields'  => ['required', 'array'],
        ]);

        auth()->user()->update($data);

        flashy('Votre profil a bien été mis à jour. ');

        return back()->with('success', 'Votre profil a bien été mis à jour.');
    }

    public function showEditPasswordForm()
    {
        return front_view('front.dashboard.password.edit', ['user' => auth()->user()]);
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($data['password'])
        ]);

        flashy('Le mot de passe a bien été mis à jour. ');

        return back()->with('success', 'Le mot de passe a bien été mis à jour.');
    }
}
