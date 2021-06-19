<?php

namespace Guysolamour\Administrable\Traits;

use Illuminate\Support\Arr;
use Spatie\MediaLibrary\InteractsWithMedia;
use Creativeorange\Gravatar\Facades\Gravatar;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


trait MediaableTrait
{
    use InteractsWithMedia;

    public function getFrontImageAttribute()
    {
        return $this->getMedia(config('media-library.collections.front.label', 'default'), ['select' => true])->first();
    }

    public function getFrontImageUrl(string $conversionName = ''): ?string
    {
        /** @var \App\Models\Media */
        $media = $this->front_image;

        if (!$media && $this->email) {
            return Gravatar::get($this->email);
        }

        return $media ? $media->getUrl($conversionName) : null;
    }

    public function getBackImageAttribute()
    {
        return $this->getMedia(config('media-library.collections.back.label', 'default'), ['select' => true])->first();
    }

    public function getBackImageUrl(string $conversionName = ''): ?string
    {
        /** @var \App\Models\Media */
        $media = $this->back_image;

        return $media ? $media->getUrl($conversionName) : null;
    }


    public function getImagesAttribute()
    {
        $medias = $this->getMedia(config('media-library.collections.images.label', 'default'), ['select' => true]);

        return $this->sortImages($medias);
    }

    public function getImageAttribute()
    {
        return $this->getFrontImageUrl();
    }

    public function getMediaCollections(): array
    {
        $collections = config('media-library.collections', []);

        if (property_exists($this, 'medialibrary_collections')) {
            if (empty($this->medialibrary_collections)) {
                $collections = [];
            }

            if (is_array($this->medialibrary_collections)) {
                $collections = $this->medialibrary_collections;
            }
        }

        $collections = collect($collections)->filter()->map(function ($collection, $key) {

            if (!Arr::exists($collection, 'label')) {
                Arr::set($collection, 'label', $key);
            }
            if (!Arr::exists($collection, 'conversion')) {
                Arr::set($collection, 'conversion', true);
            }
            if (!Arr::exists($collection, 'multiple')) {
                Arr::set($collection, 'multiple', false);
            }

            return $collection;
        });


        return $collections->toArray();
    }

    public function getMediaConversions(): array
    {
        $conversions =  config('media-library.conversions', []);

        if (property_exists($this, 'medialibrary_conversions')) {
            if (empty($this->medialibrary_conversions)) {
                $conversions = [];
            }

            if (is_array($this->medialibrary_conversions)) {
                $conversions = $this->medialibrary_conversions;
            }
        }

        $conversions = collect($conversions)->filter()->map(function ($collection, $key) {

            if (!Arr::exists($collection, 'label')) {
                Arr::set($collection, 'label', $key);
            }

            return $collection;
        });

        return $conversions->toArray();
    }

    public function registerMediaCollections(Media $media = null): void
    {
        $collections = $this->getMediaCollections();

        if ($collections) {
            foreach ($collections as $collection) {
                $this->addMediaCollection($collection['label'])
                    ->useDisk(
                        config('media-library.collections_disc')
                    )
                    ->withResponsiveImagesIf($collection['conversion']);
            }
        }
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $conversions = $this->getMediaConversions();

        if ($conversions) {
            foreach ($conversions as $key =>  $conversion) {
                $media_conversion = $this->addMediaConversion($key);

                if (Arr::exists($conversion, 'height')  && Arr::get($conversion, 'height')) {
                    $media_conversion->height($conversion['height']);
                }

                if (Arr::exists($conversion, 'width')  && Arr::get($conversion, 'width')) {
                    $media_conversion->width($conversion['width']);
                }
            }
        }
    }

    /**
     *
     * @param \Illuminate\Support\Collection $medias
     * @param string $sort_key
     */
    public function sortImages($medias, string $sort_key = 'order')
    {
        return $medias->sortBy(function ($media, $key) use ($sort_key) {
            return $media->getCustomProperty($sort_key);
        })->values()->all();
    }


    public static function bootMediaableTrait()
    {
        /**
         * @param \App\Models\BaseModel $model
         */
        static::created(function ($model) {

            $collections = $model->getMediaCollections();

            if ($collections) {
                foreach ($collections  as $collection) {

                    if (
                        'front-image' === $collection['label'] ||
                        'back-image'  === $collection['label'] ||
                        'images'      === $collection['label']
                    ) {
                        if (request($collection)) {
                            self::saveImages($model, request($collection['label']), $collection['label']);
                        }
                    }
                }
            }
        });
    }

    /**
     * @param array $images
     * @return void
     */
    private static function saveImages($model, $images, string $collection): void
    {
        $attributes = self::getCollectionAttributes(request($collection . '-attributes'));


        if ($images) {
            foreach ($images as $image) {
                $attr = $attributes[$image->getClientOriginalName()];

                $model->addMediaFromBase64(base64_encode(file_get_contents($image->path())))
                    ->setName($attr['name'])
                    ->withCustomProperties([
                        'order'  => $attr['order'],
                        'select' => $attr['select'],
                    ])
                    ->toMediaCollection($collection);
            }
        }
    }

    /**
     *
     * @param string $attributes
     * @return array
     */
    private static function getCollectionAttributes($attributes): array
    {
        $attr = [];
        foreach (json_decode($attributes, true) as $attribute) {
            foreach ($attribute as $key => $value) {
                $attr[$key] = $value;
            }
        }
        return $attr;
    }
}
