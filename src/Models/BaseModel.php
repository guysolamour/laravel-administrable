<?php

namespace Guysolamour\Administrable\Models;

use Illuminate\Database\Eloquent\Model;
use Guysolamour\Administrable\Traits\ModelTrait;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

abstract class BaseModel extends Model
{
    use Cachable;
    use ModelTrait;

    /**
     * Get the lasts items ordered by id DESC
     * @param $query
     * @param int $limit
     * @return mixed
     */
    public function scopeLast($query, int $limit = null)
    {
        if (is_null($limit)) {
            return $query->orderByDesc('id');
        }

        return $query->limit($limit)->orderByDesc('id');
    }


    

}
