<?php

namespace Guysolamour\Administrable\Models\Extensions\Ad;

use Guysolamour\Administrable\Models\BaseModel;


class Group extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'online', 'slider', 'visible_ads',
        'height', 'width', 'type_id'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'extensions_ad_groups';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'online' => 'boolean',
        'slider' => 'boolean',
    ];


    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function ads()
    {
        return $this->hasMany(Ad::class, 'group_id');
    }

    public static function getByKey(int $key)
    {
        return static::with(['type', 'ads' => fn ($query) => $query->online(), 'ads.type', 'ads.media'])->find($key);
    }

    public function getAds()
    {
        $ads = $this->ads;

        // random_elements()
        if ($this->isRandomAds()) {
            $ads = $ads->shuffle();
        } else if ($this->isSortByAsc()) {
            $ads = $ads->sortBy('created_at');
        } else if ($this->isSortByDesc()) {
            $ads = $ads->sortByDesc('created_at');
        }
        // sort ads
        if ($this->visible_ads > 0) {
            $ads = random_elements($ads, $this->visible_ads);
        }

        return $ads;
    }

    public function isRandomAds(): bool
    {
        return $this->type->name === 'random_ads';
    }

    public function isSortByAsc(): bool
    {
        return $this->type->name === 'sort_asc_ads';
    }

    public function isSortByDesc(): bool
    {
        return $this->type->name === 'sort_desc_ads';
    }
}
