<?php

namespace Guysolamour\Administrable\Traits;

use Illuminate\Support\Str;


trait HasUuid
{
    public static function bootHasUuid()
    {
        /**
         * @param \Illuminate\Database\Eloquent\Model|HasUuid $model
         */
        static::saving(function ($model) {
            $model->setAttribute($model->getUuidFieldName(), Str::uuid());
        });
    }

    public function getUuidFieldName() :string
    {
       return 'uuid';
    }

    public function getRouteKeyName(): string
    {
        return  $this->getUuidFieldName();
    }
}
