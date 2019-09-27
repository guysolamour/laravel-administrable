<?php

namespace Guysolamour\Administrable\Facades;

use Illuminate\Support\Facades\Facade;

class Administrable extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'administrable';
    }
}
