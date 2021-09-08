<?php

namespace Guysolamour\Administrable\Traits;

use Illuminate\Support\Str;


trait SeoableTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function seo()
    {
        return $this->morphOne(config('administrable.modules.seo.model'), 'seoable');
    }

    private function setDefaultSeoDataMapping($tags)
    {
        if (!property_exists($this, 'seo_default_mapping')) {
            return $tags;
        }

        foreach ($this->seo_default_mapping as $key => $seo_attributes) {
            if (!is_array($seo_attributes)) {
                continue;
            }

            foreach ($seo_attributes as  $value) {
                if (is_null($tags->getAttributeValue($value))) {
                    $tags[$value] = Str::limit(strip_tags($this->getAttributeValue($key), 300));
                }
            }
        }

        return $tags;
    }

    public function generateSeo(bool $request = true): void
    {
        $seo = $this->seo ?: new (config('administrable.modules.seo.model'));

        if ($request && request('seo')) {
            $seo->fill(request('seo'));
        }

        $this->setDefaultSeoDataMapping($seo);

        $seo->setAttribute('html', $seo->generateTags($this));

        $this->seo()->save($seo);
    }

    public static function bootSeoableTrait()
    {
        /**
         * @param \Illuminate\Database\Eloquent\Model|\Guysolamour\Administrable\Traits\SeoableTrait $model
         */
        static::saved(function ($model) {
            // dd( 'salut');
            if (request()->has('seo')) {
                $model->generateSeo();
            }
        });
    }
}
