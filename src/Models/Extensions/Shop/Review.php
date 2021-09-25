<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\ModelTrait;

class Review extends BaseModel
{
    use ModelTrait;

    public $fillable = [
        'name','email','phone_number','note','content',
        'approved','product_id', 'author_id', 'author_type'
    ];

    protected $table = 'shop_reviews';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'approved' => 'boolean',
        'note'     => 'integer',
    ];

    // add relation methods below

    /**
     * Scope a query to only include
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    public function product()
    {
        return $this->belongsTo(config('administrable.extensions.shop.models.product'));
    }


    public function author()
    {
        return $this->morphTo();
    }

    /**
     * @return boolean
     */
    public function isGuest() :bool
    {
        return $this->name != null;
    }


    /**
     * @return string
     */
    public function getAuthorName() :string
    {
        if ($this->name){
            return $this->name;
        }

        return $this->author->name;
    }

    /**
     * @return string
     */
    public function getAuthorEmail() :string
    {
        if ($this->email){
            return $this->email;
        }

        return $this->author->email;
    }

    /**
     * @return string
     */
    public function getAuthorPhoneNumber() :string
    {
        if ($this->phone_number){
            return $this->phone_number;
        }

        return $this->author->phone_number;
    }

}
