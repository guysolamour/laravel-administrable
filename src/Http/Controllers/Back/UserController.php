<?php

namespace Guysolamour\Administrable\Http\Controllers\Back;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Guysolamour\Administrable\Module;
use Illuminate\Support\Facades\Validator;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;


class UserController extends BaseController
{
    use FormBuilderTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Module::getUserModel()::latest()->get();

        return back_view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $form = $this->getForm(Module::getUserModel(), Module::backForm('user'));

        return back_view('back.users.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $this->getForm(Module::getUserModel(), Module::backForm('user'));

        $form->redirectIfNotValid();

        Module::getUserModel()::create([
            'name'              => $request->get('name'),
            'pseudo'            => $request->get('pseudo'),
            'email'             => $request->get('email'),
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ]);

        flashy(Lang::get("administrable::messages.controller.user.create"));

        return redirect()->route('back.user.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $user = Module::getUserModel()::where('id', $id)->firstOrFail();

        return back_view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $user = Module::getUserModel()::where('id', $id)->firstOrFail();
        $form = $this->getForm($user, Module::backForm('user'));

        return back_view('users.edit', compact('user', 'form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $user = Module::getUserModel()::where('id', $id)->firstOrFail();
        $form = $this->getForm($user, Module::backForm('user'));
        $form->redirectIfNotValid();

        $user->update([
            'name'              => $request->get('name'),
            'pseudo'            => $request->get('pseudo'),
            'email'             => $request->get('email'),
        ]);

        flashy(Lang::get("administrable::messages.controller.user.update"));

        return redirect()->to(back_route('user.index'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $user = Module::getUserModel()::where('id', $id)->firstOrFail();

        $user->delete();

        flashy(Lang::get("administrable::messages.controller.user.delete"));

        return redirect()->to(back_route('user.index'));
    }

    /**
     * @param Request $request
     * @param int $id
     */
    public function changePassword(Request $request, int  $id)
    {
        $user = Module::getUserModel()::where('id', $id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            flashy()->error(Lang::get("administrable::messages.controller.user.password.error"));

            return back()->withErrors($validator)->withInput();
        }

        // update password
        $user->update([
            'password' => bcrypt($request->input('password'))
        ]);

        flashy(Lang::get("administrable::messages.controller.user.password.change"));

        return redirect()->to(back_route('user.index'));
    }
}
