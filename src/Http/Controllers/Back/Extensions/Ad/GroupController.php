<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Ad;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;


class GroupController extends BaseController
{
    use FormBuilderTrait;

    public function index()
    {
        $groups = config('administrable.extensions.ad.models.group')::with('type')->get();

        return back_view('extensions.ad.groups.index', compact('groups'));
    }

    public function create()
    {
        $form = $this->getForm(new (config('administrable.extensions.ad.models.group')), config('administrable.extensions.ad.forms.back.group'));

        return back_view('extensions.ad.groups.create', compact('form'));
    }

    public function store(Request $request)
    {
        $form = $this->getForm(new (config('administrable.extensions.ad.models.group')), config('administrable.extensions.ad.forms.back.group'));

        $form->redirectIfNotValid();

        config('administrable.extensions.ad.models.group')::create($request->all());

        flashy('L\' élément a bien été ajouté');

        return redirect_backroute('extensions.ads.group.index');
    }

    public function show(int $id)
    {
        $group = config('administrable.extensions.ad.models.group')::findOrFail($id);

        return back_view('extensions.ad.groups.show', compact('group'));
    }

    public function edit(int $id)
    {
        $group = config('administrable.extensions.ad.models.group')::findOrFail($id);

        $form = $this->getForm($group, config('administrable.extensions.ad.forms.back.group'));

        return back_view('extensions.ad.groups.edit', compact('group', 'form'));
    }

    public function update(Request $request, int $id)
    {
        $group = config('administrable.extensions.ad.models.group')::findOrFail($id);

        $form = $this->getForm($group, config('administrable.extensions.ad.forms.back.group'));
        $form->redirectIfNotValid();

        $group->update($request->all());

        flashy('L\' élément a bien été modifié');

        return redirect_backroute('extensions.ads.group.index');
    }

    public function destroy(int $id)
    {
        $group = config('administrable.extensions.ad.models.group')::findOrFail($id);

        $group->delete();

        flashy('L\' élément a bien été supprimé');

        return redirect()->route('back.extensions.ads.group.index');
    }
}
