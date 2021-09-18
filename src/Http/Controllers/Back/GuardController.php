<?php

namespace Guysolamour\Administrable\Http\Controllers\Back;

use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\Form;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Lang;
use Guysolamour\Administrable\Module;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class GuardController extends BaseController
{
    use FormBuilderTrait;

    public function profile(string $pseudo)
    {
        $guard = Module::getGuardModel()::where('pseudo', $pseudo)->firstOrFail();

        $edit_form  = $this->getForm($guard, config('administrable.modules.guard.back.form'));
        $reset_form = $this->getForm($guard, config('administrable.modules.guard.back.resetpasswordform'));

        return back_view('guards.show', compact('guard', 'edit_form', 'reset_form'));
    }

    public function index()
    {
        $guards = Module::getGuardModel()::get();

        $form = $this->getForm(new (Module::getGuardModel()), config('administrable.modules.guard.back.createform'));

        return back_view('guards.index', compact('guards', 'form'));
    }

    public function create()
    {
        if (!get_guard()->can('create-' . config('administrable.guard'))) {
            abort(403);
        }

        $form = $this->getForm(new (Module::getGuardModel()), config('administrable.modules.guard.back.createform'));

        return back_view('guards.create', compact('form'));
    }

    public function store(Request $request)
    {
        if (!get_guard()->can('create-' . config('administrable.guard'))) {
            abort(403);
        }

        $form = $this->getForm(new (Module::getGuardModel()), config('administrable.modules.guard.back.createform'));

        $form->redirectIfNotValid();

        $guard = Module::getGuardModel()::create($request->all());

        if ($role = $request->input('role')) {
            $guard->syncRoles($role);
        }

        flashy(Lang::get("administrable::messages.controller.guard.create"));

        return redirect()->to(back_route(config('administrable.guard') . '.profile', $guard));
    }

    public function changePassword(Request $request, string $pseudo)
    {
        $guard = Module::getGuardModel()::where('pseudo', $pseudo)->firstOrFail();

        if (!get_guard()->can('update-' . config('administrable.guard') . '-password', $guard)) {
            abort(403);
        }
        // Validate the form
        $reset_form = $this->getForm($guard, config('administrable.modules.guard.back.resetpasswordform'));

        $this->redirectIfNotValid($reset_form);

        // update password
        $guard->update([
            'password' => $request->get('new_password')
        ]);

        flashy(Lang::get("administrable::messages.controller.guard.passwordupdate"));

        return redirect()->to(back_route( config('administrable.guard') . '.profile', $guard));
    }

    private function redirectIfNotValid(Form $form)
    {
        if (!$form->isValid()) {
            flashy()->error(Lang::get("administrable::messages.controller.guard.error"));
        }
        $form->redirectIfNotValid();
    }

    public function update(Request $request, string $pseudo)
    {
        $guard = Module::getGuardModel()::where('pseudo', $pseudo)->firstOrFail();

        if (!get_guard()->can('update-' . config('administrable.guard'), $guard)) {
            abort(403);
        }

        $form = $this->getForm($guard, config('administrable.modules.guard.back.form'));

        $this->redirectIfNotValid($form);

        $guard->update($request->all());

        if ($role = $request->input('role')){
            $guard->syncRoles($role);
        }

        flashy(Lang::get("administrable::messages.controller.guard.update"));

        return redirect()->to(back_route(config('administrable.guard') . '.profile', $guard));
    }

    public function updateAvatar(Request $request)
    {
        $guard = Module::getGuardModel()::find($request->get('id'));

        if (!get_guard()->can('change-' . config('administrable.guard') . '-avatar', $guard)) {
            abort(403);
        }

        $guard->update([
            'avatar' => $request->input('avatar')
        ]);

        return $guard;
    }

    public function markNotificationsAsRead()
    {
        auth()->guard(config('administrable.guard'))->user()
            ->unreadNotifications()->update(['read_at' => now()]);

        flashy(Lang::get("administrable::messages.controller.guard.notificationread"));

        return back();
    }

    public function delete(string $pseudo)
    {
        $guard = Module::getGuardModel()::where('pseudo', $pseudo)->firstOrFail();

        if (!get_guard()->can('delete-' . config('administrable.guard'), $guard)){
            abort(403);
        }

        $guard->delete();

        flashy(Lang::get("administrable::messages.controller.guard.delete"));

        return redirect()->to(back_route(config('administrable.guard') .'.index'));
    }
}
