<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class SettingController extends BaseController
{
    public function edit()
    {

        $settings  = shop_settings();
        $delivers  = config('administrable.extensions.shop.models.deliver')::with('areas')->last()->get();

        return back_view('extensions.shop.settings.index', compact('settings', 'delivers'));
    }

    public function update(Request $request)
    {
        /**
         * @var \Guysolamour\Administrable\Settings\BaseSettings
         */
        $settings = shop_settings();

        $settings->update($request->all());

        flashy('Les réglages on été enregistrés');

        return back();
    }
}
