<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use Spatie\MediaLibrary\HasMedia;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\ModelTrait;
use Guysolamour\Administrable\Traits\SeoableTrait;
use Guysolamour\Administrable\Traits\DraftableTrait;
use Guysolamour\Administrable\Traits\MediaableTrait;
use Guysolamour\Administrable\Traits\SluggableTrait;
use Guysolamour\Administrable\Traits\CustomFieldsTrait;
use Guysolamour\Administrable\Traits\Shop\BuyableTrait;
use Guysolamour\Administrable\Traits\Shop\MutatorsTrait;
use Guysolamour\Administrable\Traits\Shop\AccessorsTrait;
use Guysolamour\Administrable\Contracts\Shop\ShopContract;
use Guysolamour\Administrable\Traits\Shop\RecentlyViewTrait;
use Guysolamour\Administrable\Contracts\Shop\RecentlyViewContract;

class Product extends BaseModel implements HasMedia,  RecentlyViewContract , ShopContract
{
    use ModelTrait;
    use SeoableTrait;
    use BuyableTrait;
    use SluggableTrait;
    use MediaableTrait;
    use DraftableTrait;
    use CustomFieldsTrait;
    use AccessorsTrait;
    use MutatorsTrait;
    use RecentlyViewTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_products';

    /**
     * The field to use for sluggable
     *
     * @var string
     */
    protected $sluggablefield = 'name';


    public const TYPE = [
        'physic'  => ['name' => 'physic',  'label'  => 'Physique'],
        'virtual' => ['name' => 'virtual', 'label'  => 'Virtuel'],
    ];

    /**
     * @var string
     */
    public const CART_INSTANCE = 'shopping';

    /**
     * @var string
     */
    public const WISHLIST_INSTANCE = 'wishlist';


    /**
     * @var array
     */
    public $fillable = [
        'name','type','description','short_description','price','promotion_price',
        'stock_management','stock','safety_stock','has_review','online', 'download',
        'show_attributes','complementary_products','parent_id','slug', 'brand_id',
        'promotion_start_at', 'promotion_end_at', 'command_note', 'stars', 'width',
        'height', 'weight', 'attribute_id', 'variable', 'coverage_areas', 'term_id',
        'sold_count', 'sold_amount', 'custom_fields'
    ];


    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_in_promotion', 'formated_price', 'formated_promotion_price',
        'promotion_percentage', 'title', 'type_label',
    ];


    protected $attributes = [
        'online' => true,
        'type'   => self::TYPE['physic']['name'],
        'name'   => '',
        'price'  => 0,
    ];


    public $custom_fields_name = 'custom_fields';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'online'           => 'boolean',
        'download'         => 'boolean',
        'has_review'       => 'boolean',
        'stock_management' => 'boolean',
        'show_attributes'  => 'boolean',
        'variable'         => 'boolean',
        'width'            => 'integer',
        'height'           => 'integer',
        'weight'           => 'integer',
        'sold_count'       => 'integer',
        'sold_amount'      => 'integer',
        'custom_fields'    => 'array',

    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'promotion_start_at', 'promotion_end_at'];



    public function promotionIsEnded() :?bool
    {
        return $this->promotion_end_at?->isPast();
    }


    public function promotionIsStarted() :?bool
    {
        return $this->promotion_start_at?->isPast();
    }

    public function isNew() :bool
    {
        return $this->created_at->isPast() && $this->created_at->diffInWeeks(now()) == 0;
    }

    /**
     *
     * @return boolean
     */
    public function isInPromotion() :bool
    {
        return !is_null($this->promotion_price);
    }
    // add relation methods below

}
