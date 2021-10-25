<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Ad;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;


class AdController extends BaseController
{
    use FormBuilderTrait;

    public function index()
    {
        $ads = config('administrable.extensions.ad.models.ad')::with(['type', 'group'])->latest()->get();

        return back_view('extensions.ad.ads.index', compact('ads'));
    }

    public function create()
    {
        $form = $this->getForm(new (config('administrable.extensions.ad.models.ad')), config('administrable.extensions.ad.forms.back.ad'));

        return back_view('extensions.ad.ads.create', compact('form'));
    }

    public function store(Request $request)
    {
        $form = $this->getForm(new (config('administrable.extensions.ad.models.ad')), config('administrable.extensions.ad.forms.back.ad'));

        $form->redirectIfNotValid();

        config('administrable.extensions.ad.models.ad')::create($request->all());

        flashy('L\' élément a bien été ajouté');

        return redirect_backroute('extensions.ads.ad.index');
    }

    public function show(int $id)
    {
        $ad = config('administrable.extensions.ad.models.ad')::findOrFail($id);

        return back_view('extensions.ad.ads.show', compact('ad'));
    }


    public function edit(int $id)
    {
        $ad   = config('administrable.extensions.ad.models.ad')::findOrFail($id);
        $form = $this->getForm($ad, config('administrable.extensions.ad.forms.back.ad'));

        return back_view('extensions.ad.ads.edit', compact('ad', 'form'));
    }

    public function update(Request $request, int $id)
    {
        $ad   = config('administrable.extensions.ad.models.ad')::findOrFail($id);

        $form = $this->getForm($ad, config('administrable.extensions.ad.forms.back.ad'));
        $form->redirectIfNotValid();

        $ad->update($request->all());

        flashy('L\' élément a bien été modifié');

        return redirect_backroute('extensions.ads.ad.index');
    }

    public function destroy(int $id)
    {
        $ad   = config('administrable.extensions.ad.models.ad')::findOrFail($id);

        $ad->delete();

        flashy('L\' élément a bien été supprimé');

        return redirect_backroute('extensions.ads.ad.index');
    }
}
