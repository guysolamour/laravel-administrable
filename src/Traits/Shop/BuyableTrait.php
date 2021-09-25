<?php

namespace Guysolamour\Administrable\Traits\Shop;

use Illuminate\Support\Arr;

trait BuyableTrait
{
    public static $withoutAppends = false;


    public function attributes()
    {
        return $this->belongsToMany(config('administrable.extensions.shop.models.attribute'), 'shop_products_attributes');
    }


    public function reviews()
    {
        return $this->hasMany(config('administrable.extensions.shop.models.review'))->where('approved', true);
    }


    public function disapprovedReviews()
    {
        return $this->hasMany(config('administrable.extensions.shop.models.review'), 'product_id')->where('approved', false);
    }


    public function attribute()
    {
        return $this->belongsTo(config('administrable.extensions.shop.models.attribute'));
    }



    public function children()
    {
        return $this->hasMany(config('administrable.extensions.shop.models.product'), 'parent_id');
    }


    public function parent()
    {
        return $this->belongsTo(config('administrable.extensions.shop.models.product'), 'parent_id');
    }


    public function categories()
    {
        return $this->belongsToMany(config('administrable.extensions.shop.models.category'), 'shop_products_categories');
    }


    public function brand()
    {
        return $this->belongsTo(config('administrable.extensions.shop.models.brand'));
    }

    public function reviewsCount(int $note): int
    {
        return $this->reviews->filter(fn ($item) => $item->note == $note)->count();
    }


    public function getGalleryAttribute()
    {
        $front  = $this->front_image;
        $back   = $this->back_image;

        return [
            'front' => [
                'id'         => $front ? $front->getKey() : null,
                'name'       => $front ? $front->name : '',
                'url'        => $front ? $front->getUrl() : '',
                'collection' => config('administrable.media.collections.front'),
            ],
            'back' => [
                'id'         => $back ? $back->getKey() : null,
                'name'       => $back ? $back->name : '',
                'url'        => $back ? $back->getUrl() : '',
                'collection' => config('administrable.media.collections.back'),
            ],
            'images' => [
                'urls'       =>  array_map(fn($image) => [ 'id'   => $image->getKey(), 'name' => $image->name, 'url' =>  $image->getUrl()], $this->images ?? []),
                'collection' => config('administrable.media.collections.images'),
            ],

        ];
    }

    public function saveCategories($categories)
    {
        if (!empty($categories)) {
            $this->categories()->sync($categories);
        }

    }

    public function removeAttributes(string $attributes_ids)
    {
        $attributes_ids = array_filter(json_decode($attributes_ids, true));

        if (empty($attributes_ids)) {
            return;
        }

        $this->attributes()->detach($attributes_ids);

        $this->children()->whereIn('attribute_id', $attributes_ids)->get()->each->delete();
    }

    public function saveDeliversCoverageAreas(string $coverage_areas)
    {
        $coverage_areas = array_filter(json_decode($coverage_areas, true));

        if (empty($coverage_areas)){
            return;
        }

        $value = array_map(function($item) {
            return [
                'area_id' => $item['area']['id'],
                'delivers_ids' => Arr::pluck($item['delivers'], 'id'),
            ];
        }, $coverage_areas);


        $this->update(['coverage_areas' => $value]);
    }
    /**
     * Save or update attribute and save terms
     *
     * @param string $attributes
     * @return void
     */
    public function saveAttributes(string $attributes): void
    {
        if (empty($attributes)) {
            return;
        }

        $attributes = json_decode($attributes, true);

        if (empty($attributes)) {
            return;
        }

        foreach ($attributes as $attr) {
            // selectionner l'attribut s'il existe dans laliste dans le cas contraire en créer un
            if ($attribute_id = Arr::get($attr, 'id')) {
                $attribute = config('administrable.extensions.shop.models.attribute')::find($attribute_id);
            } else {
                $attribute = config('administrable.extensions.shop.models.attribute')::create(['name' => Arr::get($attr, 'name')]);
            }

            if ($attribute){
                $this->attributes()->syncWithoutDetaching(Arr::wrap($attribute->getKey()));
                // ensuite on enregistre les terms
                $attribute->saveTerms(Arr::get($attr, 'value'));
            }

        }
    }

    public function removeVariations(string $variations_id)
    {
        $variations_id = json_decode($variations_id, true);

        if (empty($variations_id)) {
            return;
        }

        $this->children()->whereIn('id', $variations_id)->get()->each->delete();
    }

    public function saveVariations(string $variations)
    {
        $variations = json_decode($variations, true);

        if (empty($variations)) {
            return;
        }

        foreach ($variations as $variation) {

            if (Arr::get($variation, 'id')) {
                $child = $this->updateVariation($variation);
            } else {
                $child = $this->createVariation($variation);
            }

            // Cette verification est importante car la variation peut avoir été supprimée
            if ($child){
                $child->saveGalleryImages($variation);
            }
        }
    }


    public function updateVariation(array $variation)
    {
        /**
         * @var self
         */
        $child =  $this->children()->where('id', $variation['id'])->first();

        if (!$child){
            return;
        }

        $child->update(array_merge($child->toArray(), $variation, [
            'promotion_start_at' => $this->promotion_start_at,
            'promotion_end_at'   => $this->promotion_end_at,
        ]));

        return $child;
    }

    public function createVariation(array $variation): self
    {
        return $this->children()->create(array_merge($variation, [
            'promotion_start_at' => $this->promotion_start_at,
            'promotion_end_at'   => $this->promotion_end_at,
            'type'               => $this->type,
            'width'              => $this->width,
            'height'             => $this->height,
            'weight'             => $this->weight,
            'command_note'       => $this->command_note,
            'short_description'  => $this->short_description,
            'stock_management'   => $this->stock_management,
            'has_review'         => $this->has_review,
            'online'             => $this->online,
            'brand_id'           => $this->brand_id,
            'attribute_id'       => config('administrable.extensions.shop.models.attribute')::findByName(Arr::get($variation, 'attribute'))->first()->getKey(),
            'term_id'            => config('administrable.extensions.shop.models.attributeterm')::findByName(Arr::get($variation, 'term'))->first()->getKey(),
        ]));
    }

    public function removeGalleryImages(string $ids) :void
    {
        $ids = array_filter(json_decode($ids, true));

        config('media-library.media_model')::whereIn('id', $ids)->get()->each->delete();
    }

    private function isImagesCollection(string $collection) :bool
    {
        return $collection === config('administrable.media.collections.images.label');
    }

    private  function saveGalleryImages(array $variation)
    {
        $images = Arr::get($variation, 'gallery');

        if (!$images) {
            return;
        }

        foreach ($images as $image) {

            $collection = $image['collection']['label'];

            // on gere cette collection plus tard
            if ($this->isImagesCollection($collection)) {
                $this->saveImagesCollectionVariationImages($image);
                continue;
            }

            // si l'image a un id on continue car rien a faire
            if (Arr::get($image, 'id')) {
                continue;
            }


            if (!empty($image['name']) && !empty($image['url'])) {
                // on supprime les anciennes images car pour front et back on
                // ne conserve qu'une seule image
                $this->clearMediaCollection($collection);

                $this->saveVariationImage($image, $collection);
            }
        }
    }

    private function saveImagesCollectionVariationImages(array $images) :void
    {
        foreach ($images['urls'] as $image) {
            if ($image['id']){
                continue;
            }

            if (!empty($image['name']) && !empty($image['url'])) {
                $this->saveVariationImage($image, $images['collection']['label']);
            }
        }
    }

    public function saveVariationImage(array $image, string $collection)
    {
        return $this
            ->addMediaFromBase64($image['url'])
            ->usingName($image['name'])
            ->usingFileName($image['name'])
            ->withCustomProperties([
                'order'  => 1,
                'select' => true,
            ])
            ->toMediaCollection($collection);
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

    /**
     * Scope a query to only include
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStock($query, bool $value = true)
    {
        return $query->where('stock_management', $value);
    }

    /**
     * Scope a query to only include
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSoldOut($query)
    {
        return $query->stock()->whereColumn('stock', '<=', 'safety_stock');
    }

    /**
     * Get most sales products
     *
     * @param  int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function mostSales(int $limit = 10)
    {
        return self::get()->sortByDesc('sold_count')->filter(fn ($product) => $product->sold_count > 0)->take($limit);
    }

}
