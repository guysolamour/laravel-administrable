<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;


use Spatie\MediaLibrary\HasMedia;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\ModelTrait;
use Guysolamour\Administrable\Traits\MediaableTrait;
use Guysolamour\Administrable\Traits\SluggableTrait;

class Brand extends BaseModel implements HasMedia
{
    use ModelTrait;
    use SluggableTrait;
    use MediaableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_brands';


    public $fillable = ['name','description','slug'];

    /**
     * The field to use for sluggable
     *
     * @var string
     */
    protected $sluggablefield = 'name';

    // add relation methods below


    public function products()
    {
        return $this->hasMany(config('administrable.extensions.shop.models.product'));
    }


}
