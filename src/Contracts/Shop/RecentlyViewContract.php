<?php

namespace Guysolamour\Administrable\Contracts\Shop;

interface RecentlyViewContract
{
    public function getRecentlyViewKey() :string;

    public function getRecentlyViewId() :int;
}

