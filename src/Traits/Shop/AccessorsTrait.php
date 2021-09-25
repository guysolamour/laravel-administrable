<?php

namespace Guysolamour\Administrable\Traits\Shop;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait AccessorsTrait
{
    /**
     * @return boolean
     */
    public function getIsInPromotionAttribute()
    {
        return $this->isInPromotion();
    }

    /**
     * @return float
     */
    public function getPromotionPercentageAttribute()
    {
        if (!$this->isInPromotion()) {
            return null;
        }

        return round((($this->promotion_price - $this->price) / $this->price) * 100, 2);
    }


    /**
     * @return float
     */
    public function getAvailablePercentageAttribute()
    {
        if (!$this->stock_management) {
            return null;
        }

        return round((($this->stock - $this->sold_count) / $this->stock) * 100, 2);
    }


    /**
     * @return string
     */
    public function getTitleAttribute()
    {
        return $this->name;
    }


    public function getDescription(?int $length = null) :?string
    {
        $description = $this->description ?: $this->short_description;

        return $length ? Str::limit($description, $length) : $description;
    }


    /**
     * @return string
     */
    public function getFormatedPriceAttribute()
    {
        return format_price($this->price);
    }

    /**
     * @return string
     */
    public function getFormatedPromotionPriceAttribute()
    {
        return format_price($this->promotion_price);
    }


    /**
     * @return string|null
     */
    public function getTypeLabelAttribute(): ?string
    {
        if (!$this->type) {
            return null;
        }
        return self::TYPE[$this->type]['label'];
    }


    /**
     *
     * @param string $value
     * @return array
     */
    public function getComplementaryProductsAttribute($value)
    {
        $ids = json_decode($value, true);

        if (is_array($ids)) {
            return array_map(fn ($item) => (int) $item, $ids);
        }

        return $ids ?? [];
    }


    public function getDeliversCoverageAreasAttribute() :array
    {
        $coverage_areas = json_decode($this->coverage_areas, true);

        if (!$coverage_areas) {
            return [];
        }

        $coverage_areas = array_map(function ($item) {
            $area = config('administrable.extensions.shop.models.coveragearea')::find($item['area_id']);

            if (!$area) {
                return null;
            }

            return [
                'area'     => $area,
                'delivers' => config('administrable.extensions.shop.models.deliver')::whereIn('id', $item['delivers_ids'])->with('areas')->get(),
            ];
        }, $coverage_areas);

        return array_filter($coverage_areas);
    }


    public function getComplementaryAttribute($value)
    {
        $ids = Arr::wrap(Arr::get($this->attributes, 'complementary_products', []));

        return $this->whereIn('id', $ids)->get();
    }

    /**
     * @return integer
     */
    public function getPrice() :int
    {
        return $this->isInPromotion() ? $this->promotion_price : $this->price;
    }



    public function getName() :string
    {
        return $this->attributes['name'];
    }

    public function getReviewsNoteAvg(): int
    {
        return $this->reviews->avg('note');
    }

    /**
     * Conversion du type en tableau numeric pour le javascript
     *
     * @return array
     */
    public static function getTypes(): array
    {
        $types = [];

        foreach (self::TYPE as $type) {
            $types[] = $type;
        }

        return $types;
    }

}
