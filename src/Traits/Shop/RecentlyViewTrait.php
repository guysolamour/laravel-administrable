<?php

namespace Guysolamour\Administrable\Traits\Shop;


trait RecentlyViewTrait
{
    public function getRecentlyViewKey() :string
    {
        return get_class($this);
    }

    public function getRecentlyViewId(): int
    {
        return $this->getKey();
    }
}
