<?php

namespace Guysolamour\Administrable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Shop
 *
 * @method static mixed defaultDeliver()
 * @method static string|null defaultClientPassword()
 */
class Shop extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'administrable-shop';
    }
}
