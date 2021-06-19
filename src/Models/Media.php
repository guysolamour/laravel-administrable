<?php

namespace Guysolamour\Administrable\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'url', 'thumb_url', 'thumb_sm_url',
        'human_size', 'date_for_humans', 'select'
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

    public function getDateForHumansAttribute()
    {
        return $this->created_at->format('d/m/Y h:s');
    }


    public function getHumanSizeAttribute()
    {
        return $this->human_readable_size;
    }


    public function getSelectAttribute()
    {
        return $this->getCustomProperty('select');
    }

    // Methods

    public function select(bool $action = true): void
    {
        $this->setCustomProperty('select', $action);
        $this->save();
    }

    public function unSelect(): void
    {
        $this->select(false);
    }

    // add relation methods below
}
