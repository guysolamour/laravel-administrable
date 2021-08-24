<?php

namespace Guysolamour\Administrable\Traits;


trait HasMediaTrait
{
    public function getDateForHumansAttribute()
    {
        return $this->created_at?->format('d/m/Y h:s');
    }

    public function getSelectAttribute()
    {
        return $this->getCustomProperty('select');
    }


    public function getOrderAttribute()
    {
        return $this->getCustomProperty('order');
    }

    public function select(bool $action = true): void
    {
        $this->setCustomProperty('select', $action);
        $this->save();
    }

    public function unSelect(): void
    {
        $this->select(false);
    }
}

