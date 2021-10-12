<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;


class ClientController extends BaseController
{
    use FormBuilderTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::latest()->with('orders')->get();

        return back_view('extensions.shop.clients.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->getForm(new User, config('administrable.extensions.shop.forms.back.client'));

        return back_view('extensions.shop.clients.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->getForm(new User, config('administrable.extensions.shop.forms.back.client'));

        $form->redirectIfNotValid();

        User::create([
            'name'              => $request->get('name'),
            'pseudo'            => $request->get('pseudo'),
            'email'             => $request->get('email'),
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ]);

        flashy('L\' utilisateur a bien été ajouté');

        return redirect()->to(back_route_path('extensions.shop.users.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return back_view('extensions.shop.clients.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $form = $this->getForm($user, $user->form);

        return back_view('extensions.shop.clients.edit', compact('user', 'form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $form = $this->getForm($user, config('administrable.extensions.shop.forms.back.client'));

        $form->redirectIfNotValid();

        $user->update([
            'name'              => $request->get('name'),
            'pseudo'            => $request->get('pseudo'),
            'email'             => $request->get('email'),
        ]);

        flashy('L\' utilisateur a bien été modifié');

        return redirect()->to(back_route_path('extensions.shop.user.index'));
    }



    /**
     * @param Request $request
     * @param User $user
     */
    public function changePassword(Request $request, User  $user)
    {
        $validator = Validator::make($request->all(), [
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            flashy()->error('Erreur lors de la modification des mots de passe.');
            return back()->withErrors($validator)->withInput();
        }

        // update password
        $user->update([
            'password' => bcrypt($request->input('password'))
        ]);

        flashy('Votre mot de passe a été mis à jour');

        return redirect()->to(back_route_path('extensions.shop.user.index'));
    }
}
