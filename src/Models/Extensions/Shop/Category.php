<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use Spatie\MediaLibrary\HasMedia;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\ModelTrait;
use Guysolamour\Administrable\Traits\MediaableTrait;
use Guysolamour\Administrable\Traits\SluggableTrait;

class Category extends BaseModel implements HasMedia
{
    use ModelTrait;
    use SluggableTrait;
    use MediaableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_categories';


    public $fillable = ['name', 'description', 'slug'];

    /**
     * The field to use for sluggable
     *
     * @var string
     */
    protected $sluggablefield = 'name';


    // add relation methods below

    // products relation
    public function products()
    {
      return $this->belongsToMany(config('administrable.extensions.shop.models.product'), 'shop_products_categories');
    }
    // end products relation


    public function parent()
    {
        return $this->belongsTo(config('administrable.extensions.shop.models.category'), 'parent_id');
    }


    public function children()
    {
        return $this->hasMany(config('administrable.extensions.shop.models.category'), 'parent_id');
    }

    /**
     * Scope a query to only include
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrincipal($query)
    {
        return $query->whereNull('parent_id');
    }

}
