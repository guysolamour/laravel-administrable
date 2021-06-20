<?php

namespace Guysolamour\Administrable\Traits;

use Guysolamour\Administrable\Models\Seo;

trait SeoableTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }


    public function generateSeo(bool $request = true): void
    {
        /**
         * @var Seo
         */
        $seo = $this->seo ?: new Seo;

        if ($request && request('seo')) {
            $seo->fill(request('seo'));
        }

        $seo->setAttribute('html', $seo->generateTags($this));

        $this->seo()->save($seo);
    }

    public static function bootSeoableTrait()
    {
        /**
         * @param \Illuminate\Database\Eloquent\Model|\Guysolamour\Administrable\Traits\SeoableTrait $model
         */
        static::saved(function ($model) {
            if (request('seo')) {
                $model->generateSeo();
            }
        });
    }
}
