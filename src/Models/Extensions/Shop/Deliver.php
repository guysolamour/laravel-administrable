<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use Spatie\MediaLibrary\HasMedia;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\ModelTrait;
use Guysolamour\Administrable\Traits\MediaableTrait;
use Guysolamour\Administrable\Traits\SluggableTrait;

class Deliver extends BaseModel implements HasMedia
{
    use ModelTrait;
    use MediaableTrait;
    use SluggableTrait;

    public $fillable = ['name', 'phone_number', 'email', 'description', 'slug'];


    protected $table = 'shop_delivers';



    /**
     * The field to use for sluggable
     *
     * @var string
     */
    protected $sluggablefield = 'name';




    public function getAreaPricesAttribute()
    {
        return  $this->areas->map(fn ($deliver) => ['id' => $deliver->getKey(), 'price' => (int) $deliver->pivot->price]);
    }


    public function saveAreasAndPrices(string $areas) :void
    {
        $areas = json_decode($areas, true);

        if (empty($areas)){
            return;
        }

        $keys = [];

        foreach ($areas as  $value) {
            $keys[$value['id']] = ['price' => $value['price']];
        }

        $this->areas()->sync($keys);
    }


    // add relation methods below
    public function areas()
    {
        return $this->belongsToMany(config('administrable.extensions.shop.models.coveragearea'), 'shop_deliver_area_prices', 'deliver_id', 'coveragearea_id')
                ->withPivot('price')
                ->using(config('administrable.extensions.shop.models.deliverprice'))
                ;
    }


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();

        /**
         * @param $this $model
         */
        static::saved(function ($model) {
            if ($areas = request('area_prices')) {
                $model->saveAreasAndPrices($areas);
            }
        });

    }

}
