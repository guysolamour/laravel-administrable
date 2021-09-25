<?php

namespace Guysolamour\Administrable\Traits\Shop;


trait MutatorsTrait
{
    public function setComplementaryProductsAttribute($value)
    {
        if ($value) {
            $this->attributes['complementary_products'] = json_encode($value);
        }
    }


    public function setCoverageAreasAttribute($value)
    {
        if ($value) {
            $this->attributes['coverage_areas'] = json_encode($value);
        }
    }



}
