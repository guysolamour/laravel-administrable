<?php

namespace Guysolamour\Administrable\Models\Extensions\Testimonial;

use Cviebrock\EloquentSluggable\Sluggable;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\DraftableTrait;
use Guysolamour\Administrable\Traits\MediaableTrait;
use Spatie\MediaLibrary\HasMedia;

class Testimonial extends BaseModel implements HasMedia
{
    use Sluggable;
    use DraftableTrait;
    use MediaableTrait;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'extensions_testimonials';




    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'job', 'online', 'slug', 'content'];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'online' => 'boolean',
    ];

    // Attributes



    // add relation methods below



    // add sluggable methods below

    public function getRelatedForm(): string
    {
        return config('administrable.extensions.testimonial.back.form');
    }

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
}
