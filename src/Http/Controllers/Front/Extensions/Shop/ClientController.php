<?php

namespace App\Http\Controllers\Front\Shop;

use Illuminate\Http\Request;
use App\Forms\Front\UserForm;
use App\Http\Controllers\Controller;
use App\Forms\Front\ResetUserPasswordForm;
use Guysolamour\Administrable\Traits\FormBuilderTrait;

class ClientController extends Controller
{
    use FormBuilderTrait;

    public function show(Request $request)
    {
        $client = $request->user();

        return view('front.shop.dashboard.show', compact('client'));
    }

    public function edit(Request $request)
    {
        $form = $this->getForm($request->user(), UserForm::class);

        return view('front.shop.dashboard.edit', compact('form'));
    }


    public function update(Request $request)
    {
        $user = $request->user();
        $form = $this->getForm($user, UserForm::class);
        $form->redirectIfNotValid();

        $user->update($request->all());

        flashy("Les informations ont été mises à jour");

        return back();
    }

    public function editPassword(Request $request)
    {
        $form = $this->getForm($request->user(), ResetUserPasswordForm::class);

        return view('front.shop.dashboard.password', compact('form'));
    }
    public function changePassword(Request $request)
    {
        $user = $request->user();

        // Validate the form
        $form = $this->getForm($user, ResetUserPasswordForm::class);

        $form->redirectIfNotValid();

        // update password
        $user->update([
            'password' => $request->get('new_password')
        ]);

        flashy('Votre mot de passe a été mis à jour');

        return back();
    }
    public function updateAvatar(Request $request)
    {

    }
}
