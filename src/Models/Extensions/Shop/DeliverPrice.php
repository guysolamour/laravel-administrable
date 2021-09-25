<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DeliverPrice extends Pivot
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['formated_price'];


    public function getFormatedPriceAttribute() :string
    {
        return format_price($this->attributes['price']);
    }
}

