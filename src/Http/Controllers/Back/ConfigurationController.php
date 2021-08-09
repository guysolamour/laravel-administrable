<?php

namespace Guysolamour\Administrable\Http\Controllers\Back;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Guysolamour\Administrable\Module;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class ConfigurationController extends BaseController
{
    use FormBuilderTrait;
    

    public function edit()
    {
        $configuration = app(config('administrable.modules.configuration.model'));

        $form = $this->getForm($configuration, Module::backForm('configuration'));

        return back_view('configuration.edit', compact('configuration', 'form'));
    }

    public function store(Request $request)
    {
        $configuration = app(config('administrable.modules.configuration.model'));

        $form = $this->getForm($configuration, Module::backForm('configuration'));
        $form->redirectIfNotValid();

        $configuration->update($request->all());
        $configuration->storeLogo();

        flashy(Lang::get('administrable::messages.view.configuration.update'));

        return back();
    }
}
