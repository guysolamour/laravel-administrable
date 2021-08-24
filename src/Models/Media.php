<?php

namespace Guysolamour\Administrable\Models;

use Guysolamour\Administrable\Traits\HasMediaTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use HasMediaTrait;
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'url', 'thumb_url', 'thumb_sm_url', 'order',
        'human_readable_size', 'select', 'date_for_humans'
    ];


    // Custom attributes

    public function getUrlAttribute()
    {
        return $this->getFullUrl();
    }

    public function getThumbUrlAttribute()
    {
        return $this->getFullUrl('thumb');
    }

    public function getThumbSmUrlAttribute()
    {
        return $this->getFullUrl('thumb-sm');
    }

}
