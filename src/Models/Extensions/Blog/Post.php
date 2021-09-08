<?php

namespace Guysolamour\Administrable\Models\Extensions\Blog;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Cviebrock\EloquentSluggable\Sluggable;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\SeoableTrait;
use Guysolamour\Administrable\Traits\DaterangeTrait;
use Guysolamour\Administrable\Traits\DraftableTrait;
use Guysolamour\Administrable\Traits\MediaableTrait;
use Guysolamour\Administrable\Traits\CommentableTrait;
use Guysolamour\Administrable\Casts\DaterangepickerCast;

class Post extends BaseModel implements HasMedia
{
    use Sluggable;
    use DaterangeTrait;
    use SeoableTrait;
    use MediaableTrait;
    use DraftableTrait;
    use CommentableTrait;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public const TYPES = [
        'text'  => ['name' => 'text',  'label'  => 'Texte'],
        'video' => ['name' => 'video', 'label'  => 'Vidéo'],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'content', 'author_id', 'slug', 'video_link', 'online', 'allow_comment', 'published_at'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'extensions_blog_posts';

    /**
     * The model's default values for seo.
     *
     * @var array
     */
    protected $seo_default_mapping = [
        'title'   => ['og:title', 'page:title', 'twitter:title'],
        'content' => ['page:meta:description', 'og:description', 'twitter:description'],
    ];

    protected $medialibrary_collections = [
        'seo'            => ['conversion' => true],
        'front'          => ['label' => 'front-image', 'description' => 'Image à la une', 'conversion' => true],
        'attachments'    => ['label' => 'attachments', 'conversion' => true],
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'online'        => 'boolean',
        'allow_comment' => 'boolean',
        'created_at'    => DaterangepickerCast::class,
        'published_at'  => DaterangepickerCast::class,
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'online'        => true,
        'allow_comment' => true,
    ];


    protected $datepickers = [
        'created_at',
        'published_at',
    ];


    // Attributes

    public function getPreviousAttribute()
    {
        return self::where('id', '<', $this->getKey())->orderByDesc('id')->first();
    }

    public function getNextAttribute()
    {
        return self::where('id', '>', $this->getKey())->orderBy('id')->first();
    }


    // add relation methods below

    public function categories()
    {
        return $this->belongsToMany(config('administrable.extensions.blog.category.model'), 'extensions_blog_post_category');
    }


    public function tags()
    {
        return $this->belongsToMany(config('administrable.extensions.blog.tag.model'), 'extensions_blog_post_tag');
    }


    public function author()
    {
        return $this->belongsTo(get_guard_model_class(), 'author_id');
    }

    public function setVideoLinkAttribute($value)
    {
        $this->attributes['video_link'] = Str::after($value, '?v=');
    }

    public function getRelatedPosts(int $length = 3)
    {
        return self::whereHas('categories', function ($q) {
            $q->whereIn('id', $this->categories->modelKeys());
        })->where('id', '!=', $this->id)->with(['media', 'categories'])->take($length)->get();
    }

    public function getRandomPosts(int $length = 3)
    {
        $posts =  self::where('id', '!=', $this->getKey())->with(['media', 'categories'])->get()->shuffle();

        return random_elements($posts, $length);
    }

    public function scopeProgrammaticaly($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function isVideo(): bool
    {
        return $this->attributes['type'] == self::TYPES['video']['name'];
    }

    /**
     * @return void
     */
    public function publishProgrammaticaly()
    {
        $this->update([
            'published_at'  => null,
            'online'        => true,
        ]);
    }


    // relation a mettre si post doit etre creer


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
            'slug' => ['source' => 'title']
        ];
    }

    public static function booted()
    {
        parent::booted();

        /**
         * @param $this $model
         */
        static::saved(function ($model) {
            $model->saveCategories();
            $model->saveTags();
        });
    }

    public function saveCategories(): void
    {
        if ($categories = request('categories')) {
            if (is_string($categories)) {
                $categories = json_decode($categories, true);
            }
            $this->categories()->sync($categories);
        }
    }

    public function saveTags(): void
    {
        if ($tags = request('tags')) {
            if (is_string($tags)) {
                $tags = json_decode($tags);
            }
            $this->tags()->sync($tags);
        }
    }
}
