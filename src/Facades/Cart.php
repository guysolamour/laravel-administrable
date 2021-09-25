<?php

namespace Guysolamour\Administrable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Cart
 *
 * @method static \Guysolamour\Administrable\Models\Shop\Cart instance(string $instance)
 * @method static \Guysolamour\Administrable\Models\Shop\Cart globalInstance()
 * @method static \Guysolamour\Administrable\Models\Shop\Cart from(string $instance)
 * @method static \Guysolamour\Administrable\Models\Shop\Cart|\Illuminate\Support\Collection fromSession()
 * @method static \Guysolamour\Administrable\Models\Shop\Cart|\Illuminate\Support\Collection fromDatabase()
 * @method static \Guysolamour\Administrable\Models\Shop\Cart mergeFromSessionToDatabase()
 * @method static \Guysolamour\Administrable\Models\Shop\Cart|mixed getItem($cart, int $value, string $key = 'rowId')
 * @method static \Guysolamour\Administrable\Models\Shop\Cart add($model, int $quantity = 1, ?int $price = null, ?string $name = null, ?float $tax = null, int $discount = 0)
 * @method static \Guysolamour\Administrable\Models\Shop\Cart update($rowId, array $options)
 * @method static \Guysolamour\Administrable\Models\Shop\Cart setDiscount($model, int $value, bool $percentage = false)
 * @method static \Guysolamour\Administrable\Models\Shop\Cart setGlobalDiscount(int $discount, bool $percentage = false)
 * @method static \Guysolamour\Administrable\Models\Shop\Cart|\Illuminate\Support\Collection restore()
 * @method static \Guysolamour\Administrable\Models\Shop\Cart hydrate($data, $globals = [])
 * @method static \Guysolamour\Administrable\Models\Shop\Cart merge($data, array $item)
 * @method static \Guysolamour\Administrable\Models\Shop\Cart remove($rowId)
 * @method static \Guysolamour\Administrable\Models\Shop\Cart clear()
 * @method static \Guysolamour\Administrable\Models\Shop\Cart save($cart)
 * @method static \Guysolamour\Administrable\Models\Shop\Cart|\Illuminate\Support\Collection models()
 * @method static \Guysolamour\Administrable\Models\Shop\Cart rawContent
 * @method static \Guysolamour\Administrable\Models\Shop\Cart rawGlobals
 * @method static \Guysolamour\Administrable\Models\Shop\Cart|array content($data = null, $globals = null)
 * @method static bool isEmpty()
 * @method static int count()
 * @method static int total()
 *
 */
class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'administrable-cart';
    }
}
