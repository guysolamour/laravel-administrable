<?php

namespace Guysolamour\Administrable\Models;

use Illuminate\Support\Facades\Route;
use Cviebrock\EloquentSluggable\Sluggable;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\SeoableTrait;

class Page extends BaseModel
{
    use Sluggable;
    use SeoableTrait;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'name', 'route'];



    protected $with = ['metatags'];


    // Attributes

    public function getUriAttribute(): ?string
    {
        if (!Route::has($this->attributes['route'])) {
            return null;
        }

        return route($this->attributes['route']);
    }

    public function getFrontRoute(): string
    {
        return route('front.about.index');
    }


    public function getTag(string $code, ?string $key = null)
    {
        $tag = $this->metatags->filter(fn ($tag) => $tag->code == $code)->first();

        if ($tag) {
            return $key ? $tag->$key : $tag;
        }

        return '';
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function metatags()
    {
        return $this->hasMany(config('administrable.modules.pagemeta.model'), 'page_id');
    }

    public function getRelatedForm(): string
    {
        return config('administrable.modules.page.back.form');
    }


    // add sluggable methods below

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'name']
        ];
    }

    public static function booted()
    {
        parent::booted();

        /**
         * @param self $model
         */
        static::created(function ($model) {
            $model->metatags()->create([
                'code'    => 'defaultgroup',
                'name'    => 'Groupe par défaut',
                'title'   => 'Groupe par défaut',
                'type'    =>  config('administrable.modules.pagemeta.model')::TYPES['group']['value'],
            ]);
        });
    }
}
