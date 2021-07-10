<?php

namespace Guysolamour\Administrable\Models\Extensions\Blog;

use Cviebrock\EloquentSluggable\Sluggable;
use Guysolamour\Administrable\Models\BaseModel;

class Tag extends BaseModel
{
    use Sluggable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug'];


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'extensions_blog_tags';

    // Attributes


    // add relation methods below

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'extensions_blog_post_tag');
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
}
