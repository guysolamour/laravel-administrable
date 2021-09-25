<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\ModelTrait;
use Guysolamour\Administrable\Traits\SluggableTrait;

class CoverageArea extends BaseModel
{
    use ModelTrait;
    use SluggableTrait;


    public $fillable = ['name','description', 'slug'];


    protected $table = 'shop_coverageareas';

    /**
     * The field to use for sluggable
     *
     * @var string
     */
    protected $sluggablefield = 'name';

    // add relation methods below

    public function delivers()
    {
        return $this->belongsToMany(config('administrable.extensions.shop.models.deliver'), 'shop_deliver_area_prices', 'coveragearea_id' ,'deliver_id')
                    ->withPivot('price')
                    ->using(config('administrable.extensions.shop.models.deliverprice'));
    }

}
