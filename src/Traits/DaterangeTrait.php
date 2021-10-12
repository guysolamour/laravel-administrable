<?php

namespace Guysolamour\Administrable\Traits;

use Illuminate\Support\Arr;


trait DaterangeTrait
{

    public function getDateranges(): array
    {
        return $this->dateranges ?? [];
    }


    public function getDatepickers(): array
    {
        return $this->datepickers ?? [];
    }


    private function parseRangeDates(string $dates): array
    {
        return  array_map(fn ($date) => trim($date), explode(' - ', $dates));
    }


    /**
     * @return void
     */
    private function proccesDateranges()
    {
        $attributes = $this->getDateranges();

        if (empty($attributes)){
            return;
        }

        foreach ($attributes as $key => $attribute){

            $dates = is_array($attribute) ? $dates = request($key) : request($attribute);

            if (!$dates){
                continue;
            }

            [$start_at, $end_at] = $this->parseRangeDates($dates);

            $this->setAttribute($this->getDaterangeStartAtFieldName($attribute), $start_at);
            $this->setAttribute($this->getDaterangeEndAtFieldName($attribute), $end_at);
        }

    }

    private function getDaterangeStartAtFieldName($attribute) :string
    {
        return is_array($attribute)
                    ? Arr::first($attribute)
                    : "{$attribute}_" . config('administrable.daterange.start');
    }

    private function getDaterangeEndAtFieldName($attribute) :string
    {
        return is_array($attribute)
                    ? Arr::last($attribute)
                    : "{$attribute}_" . config('administrable.daterange.end');
    }

    /**
     * @return void
     */
    private function proccesDatepickers()
    {
        $attributes = $this->getDatepickers();

        if (empty($attributes)) {
            return;
        }

        foreach ($attributes as $attribute) {
            if ($date = request($attribute)) {
                $this->setAttribute($attribute, $date);
            }
        }
    }


    /**
     * @return void
     */
    public static function bootDaterangeTrait()
    {
        /**
         * @param \Illuminate\Database\Eloquent\Model $model
         */
        static::saving(function ($model) {
            $model->proccesDateranges();
            $model->proccesDatepickers();
        });
    }
}
