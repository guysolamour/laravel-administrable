<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop;

use Guysolamour\Administrable\Http\Controllers\BaseController;

class ShopController extends BaseController
{
    public function settings()
    {
        /**
         * @var \Guysolamour\Administrable\Settings\BaseSettings
         */
        $settings = shop_settings();

        $products = config('administrable.extensions.shop.models.product')::principal()->last()->get();

        return back_view('extensions.shop.settings.index', compact('settings', 'products'));
    }
}
