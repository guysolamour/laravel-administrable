<?php

namespace Guysolamour\Administrable\Contracts\Shop;

interface ShopContract
{
    public function getPrice();

    public function getName(): string;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attributes();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disapprovedReviews();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand();

    /**
     * @return integer
     */
    public function reviewsCount(int $note) :int;

    /**
     *
     * @return boolean
     */
    public function isInPromotion(): bool;
}

