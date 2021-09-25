<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\ModelTrait;
use Guysolamour\Administrable\Traits\SluggableTrait;

class AttributeTerm extends BaseModel
{
    use ModelTrait;
    use SluggableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_attribute_terms';


    public $fillable = ['name', 'description', 'slug', 'order',  'attribute_id'];

    /**
     * The field to use for sluggable
     *
     * @var string
     */
    protected $sluggablefield = 'name';


    /**
     * Scope a query to only include
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    // add relation methods below

    public function attribute()
    {
        return $this->belongsTo(config('administrable.extensions.shop.models.attribute'));
    }
}
