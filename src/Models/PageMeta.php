<?php

namespace Guysolamour\Administrable\Models;

use Guysolamour\Administrable\Traits\MediaableTrait;
use Spatie\MediaLibrary\HasMedia;

class PageMeta extends BaseModel implements HasMedia
{
    use MediaableTrait;

    const TYPES = [
        'text'               => ['label' => 'Texte', 'value'           => 1],
        'simpletext'         => ['label' => 'Texte (simple)', 'value'  => 4],
        'image'              => ['label' => 'Image', 'value'           => 2],
        'video'              => ['label' => 'Vidéo', 'value'           => 3],
        'attachedfile'       => ['label' => 'Autres fichiers', 'value' => 5],
        'group'              => ['label' => 'Groupe de champ', 'value' => 6],
    ];


    const DEFAULT_GROUP_CODE = 'defaultgroup';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'page_metas';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'name', 'title', 'type', 'content', 'page_id', 'parent_id'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url', 'video_url', 'image', 'attached_file_url'];



    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'image' => 'boolean',
    ];




    // Attributes
    public function getImageUrlAttribute()
    {
        return $this->image?->getUrl();
    }

    public function getAttachedFileUrlAttribute()
    {
        return $this->attachedfile?->getUrl();
    }

    public function getImageAttribute()
    {
        if ($this->isImage()) {
            return $this->attachedfile;
        }
    }

    public function attachedfile()
    {
        return $this->belongsTo(config('media-library.media_model'), 'content');
    }


    public function getVideoUrlAttribute()
    {
        if (!$this->isVideo()) return '';

        return $this->content;
    }

    public function children()
    {
        return $this->hasMany(config('administrable.modules.pagemeta.model'), 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(config('administrable.modules.pagemeta.model'), 'parent_id');
    }


    /**
     *
     * @return boolean
     */
    public function isImage(): bool
    {
        return $this->type == self::TYPES['image']['value'];
    }

    /**
     *
     * @return boolean
     */
    public function isAttachedFile(): bool
    {
        return $this->type == self::TYPES['attachedfile']['value'];
    }

    /**
     *
     * @return boolean
     */
    public function isVideo(): bool
    {
        return $this->type == self::TYPES['video']['value'];
    }

    /**
     *
     * @return boolean
     */
    public function isText(): bool
    {
        return $this->type == self::TYPES['text']['value'];
    }

    /**
     *
     * @return boolean
     */
    public function isSimpleText(): bool
    {
        return $this->type == self::TYPES['simpletext']['value'];
    }


    /**
     * Scope a query to only include
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeGroupField($query)
    {
        return $query->whereNull('parent_id')->where('type', self::TYPES['group']['value']);
    }



    public static function booted()
    {
        parent::booted();

        /**
         * @param self $model
         */
        static::creating(function ($model) {
            if (!$model->title) {
                $model->title = $model->name;
            }
        });

        /**
         * @param self $model
         */
        static::deleted(function ($model) {
            if ($model->children) {
                $model->children->each->delete();
            }
        });
    }

    // add relation methods below
}
