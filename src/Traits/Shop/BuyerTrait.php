<?php

namespace Guysolamour\Administrable\Traits\Shop;


use Guysolamour\Administrable\Facades\Cart;

trait BuyerTrait {

    public function reviews()
    {
        return $this->morphMany(config('administrable.extensions.shop.models.review'), 'author');
    }


    public function orders()
    {
        return $this->hasMany(config('administrable.extensions.shop.models.order'));
    }

    public function coupons()
    {
        return $this->belongsToMany(config('administrable.extensions.shop.models.coupon'), 'shop_users_coupons', 'user_id', 'coupon_id')
            ->withPivot('used_count')
        ;
    }

    public function commands()
    {
        return $this->hasMany(config('administrable.extensions.shop.models.command'));
    }

    private function incrementCouponUsedCount(int $id) :void
    {
        $coupon = $this->coupons->filter(fn ($coupon) => $coupon->getKey() == $id)->first();

        if ($coupon) {
            $this->coupons()->updateExistingPivot($coupon->getKey(), [
                'used_count'  => $coupon->pivot->used_count + 1
            ]);
        } else {
            $this->coupons()->attach($id, ['used_count' => 1]);
        }
    }

    public function useGlobalCoupon(int $id) :void
    {
        $coupon = config('administrable.extensions.shop.models.coupon')::firstOrFail($id);

        $this->incrementCouponUsedCount($coupon->getKey());
    }


    public function useCoupon(int $id): void
    {
        $coupon = config('administrable.extensions.shop.models.coupon')::firstOrFail($id);

        $this->incrementCouponUsedCount($coupon->getKey());
    }


    public function getTotalExpenseAttribute(): int
    {
        return $this->orders->sum('total');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function sortByTotalExpense(?int $limit = null)
    {
        $users    = self::with('orders')->get()->each->append('total_expense');

        $filtered = $users->sortByDesc('total_expense')->filter(fn ($user) => $user->total_expense > 0);

        return $limit ? $filtered->take($limit) : $filtered;
    }

    /**
     *
     * @return Collection
     */
    public function getShoppingCartAttribute()
    {
        return Cart::restore();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getWhishlistCartAttribute()
    {
        return Cart::instance(config('administrable.extensions.shop.models.product')::WISHLIST_INSTANCE)->restore();
    }


    public function mergeShoppingCart(): void
    {
        Cart::mergeFromSessionToDatabase();
    }


    public function mergeWishlistCart(): void
    {
        Cart::instance(config('administrable.extensions.shop.models.product')::WISHLIST_INSTANCE)->mergeFromSessionToDatabase();
    }


    public function clearShoppingCart(): void
    {
        Cart::clear();
    }


    public function clearWhislistCart(): void
    {
        Cart::instance(config('administrable.extensions.shop.models.product')::WISHLIST_INSTANCE)->clear();
    }
}
