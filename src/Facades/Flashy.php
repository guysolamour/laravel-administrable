<?php

namespace Guysolamour\Administrable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Flashy
 *
 * @method static \Guysolamour\Administrable\Flashy info(string $message, string $link = '#')
 * @method static \Guysolamour\Administrable\Flashy success(string $message, string $link = '#')
 * @method static \Guysolamour\Administrable\Flashy error(string $message, string $link = '#')
 * @method static \Guysolamour\Administrable\Flashy warning(string $message, string $link = '#')
 * @method static \Guysolamour\Administrable\Flashy primary(string $message, string $link = '#')
 * @method static \Guysolamour\Administrable\Flashy primaryDark(string $message, string $link = '#')
 * @method static \Guysolamour\Administrable\Flashy muted(string $message, string $link = '#')
 * @method static \Guysolamour\Administrable\Flashy mutedDark(string $message, string $link = '#')
 * @method static \Guysolamour\Administrable\Flashy message(string $message, string $link = '#')
 *
 */
class Flashy extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'flashy';
    }
}
