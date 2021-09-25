<?php

namespace Guysolamour\Administrable\Services\Shop;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Guysolamour\Administrable\Contracts\Shop\RecentlyViewContract;


class RecentlyView
{
    /**
     * @param string|\Guysolamour\Administrable\Contracts\Shop\RecentlyViewContract $model
     * @param integer|null $id
     * @return void
     */
    public function push($model, int $id = null) :void
    {
        if ($this->exists($model)){
            return;
        }

        $key = $this->getKey($model);
        $id = $id ?: $this->getId($model);

        $this->storeInCookie($key, $id);
    }

    /**
     * @param string|\Guysolamour\Administrable\Contracts\Shop\RecentlyViewContract $model
     * @return array|null
     */
    public function get($model) :?array
    {
        $key = $model;

        if (is_object($model)) {
            if ($model instanceof RecentlyViewContract) {
                $key = $model->getRecentlyViewKey();
            } else {
                throw new \Exception('The model must implements RecentlyViewContract');
            }
        }

        return Arr::get($this->getInCookie(), $key, []);
    }

    public function load($model) :Collection
    {
        return $model::whereIn('id', $this->get($model))->get() ?? collect();
    }


    public function loadWithout($model) :Collection
    {
        $id = $this->getId($model);
        $ids = array_filter($this->get($model), fn($item) => $item !== $id);

        return $model::whereIn('id', $ids)->get() ?? collect();
    }

    public function exists($model) :bool
    {
        $id = $this->getId($model);

        $ids = $this->get($model);

        foreach ($ids as $value ) {
            if ($id === $value){
                return true;
            }
        }

        return false;
    }

    private function getId($model) :?int
    {
        if (is_object($model)) {
            if ($model instanceof RecentlyViewContract) {
                return $model->getRecentlyViewId();
            } else {
                throw new \Exception('The model must implements RecentlyViewContract');
            }
        }

        return null;
    }

    private function getKey($model) :?string
    {
        if (is_string($model)){
            return $model;
        }


        if (is_object($model)) {
            if ($model instanceof RecentlyViewContract) {
                return $model->getRecentlyViewKey();
                // $id  = $model->getRecentlyViewId();
            } else {
                throw new \Exception('The model must implements RecentlyViewContract');
            }
        }

        return null;
    }

    private function getCookieName(): string
    {
        return Str::lower(str_replace(' ', '', config('app.name') . '_recently_viewed'));
    }

    private function storeInCookie(string $key, int $id) :void
    {
        $recent = $this->getInCookie();

        $recent[$key][] = $id;

        Cookie::queue(Cookie::make($this->getCookieName(), json_encode($recent), config('administrable.extensions.shop.recently_view_cookie_duration')));
    }

    private function getInCookie() :array
    {
        return json_decode(Cookie::get($this->getCookieName()), true) ?? [];
    }
}
