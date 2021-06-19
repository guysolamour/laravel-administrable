<?php

namespace Guysolamour\Administrable\Traits;


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
        $attributes = $this->dateranges;

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                if ($dates = request($attribute)) {
                    [$start_at, $end_at] = $this->parseRangeDates($dates);

                    $this->setAttribute("{$attribute}_" . config('administrable.daterange.start'), $start_at);
                    $this->setAttribute("{$attribute}_" . config('administrable.daterange.end'), $end_at);
                }
            }
        }
    }

    /**
     * @return void
     */
    private function proccesDatepickers()
    {
        $attributes = $this->datepickers;

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                if ($date = request($attribute)) {
                    $this->setAttribute($attribute, $date);
                }
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
            // daterange
            $model->proccesDateranges();
            $model->proccesDatepickers();
        });
    }
}
