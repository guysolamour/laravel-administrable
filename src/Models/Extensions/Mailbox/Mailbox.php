<?php

namespace Guysolamour\Administrable\Models\Extensions\Mailbox;

use Cviebrock\EloquentSluggable\Sluggable;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\CommentableTrait;

class Mailbox extends BaseModel
{
    use Sluggable;
    use CommentableTrait;

    /**
    * The table associated with the model.
    *
    * @var string
    */
    protected $table = 'extensions_mailboxes';


    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    public $fillable = ['email', 'phone_number', 'content', 'name', 'slug', 'read'];


    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [
        'read' => 'boolean',
    ];


    public function notes()
    {
        return $this->comments();
    }


    // Attributes
    public function scopeUnread($query)
    {
        return $query->where('read',false);
    }

    public function scopeRead($query)
    {
        return $query->where('read',true);
    }


    // add relation methods below

    public function markAsRead()
    {
        if (!$this->read) {
            $this->update(['read' => true]);
        }
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
