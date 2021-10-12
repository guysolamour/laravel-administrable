<?php

namespace Guysolamour\Administrable\Services\Shop;


class Shop
{

    public function defaultDeliver()
    {
        /**
         * @var \Guysolamour\Administrable\Settings\ShopSettings
         */
        $settings = shop_settings();

        /**
         * @var \Illuminate\Database\Eloquent\Model
         */
        $default_deliver = config('administrable.extensions.shop.models.deliver')::with('areas')->find($settings->default_deliver_id);

        if (!$default_deliver){
            return null;
        }

        $default_coveragearea = $default_deliver->areas->filter(fn($area) => $area->id == $settings->default_coveragearea_id)->first();

        if (!$default_coveragearea) {
            return null;
        }

        $default_deliver->setRelation('area', $default_coveragearea);

        return $default_deliver;
    }

    public function defaultClientPassword() :?string
    {
        return config('administrable.extensions.shop.client.default.password');
    }
}
