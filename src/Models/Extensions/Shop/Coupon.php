<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use App\Models\User;
use Guysolamour\Administrable\Facades\Cart;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\ModelTrait;
use Guysolamour\Administrable\Traits\DaterangeTrait;
use Guysolamour\Administrable\Casts\DaterangepickerCast;

class Coupon extends BaseModel
{
    use ModelTrait;
    use DaterangeTrait;

    protected $table = 'shop_coupons';

    public $fillable = [
        'code','description','remise_type','value', 'starts_at' ,'expires_at','min_expense',
        'max_expense','use_once','exclude_promo_products','products','exclude_products',
        'categories','exclude_categories', 'used_count', 'used_time_limit', 'used_by_user_limit'
    ];


    protected $dateranges = [
        'start_expire_dates' => ['starts_at', 'expires_at'],
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'use_once'               => 'boolean',
        'exclude_promo_products' => 'boolean',
        'value'                  => 'integer',
        'used_by_user_limit'     => 'integer',
        'used_time_limit'        => 'integer',
        'used_count'             => 'integer',
        'starts_at'              => DaterangepickerCast::class,
        'expires_at'             => DaterangepickerCast::class,
    ];


    public const REMISE_TYPES = [
        ['name' => 'percent_cart',   'label' => 'pourcentage panier'],
        ['name' => 'fixed_cart',     'label' => 'panier fixe'],
        ['name' => 'fixed_product',  'label' => 'produit fixe'],
        ['name' => 'percent_product','label' => 'pourcentage produit'],
    ];



    // add relation methods below

    public function users()
    {
        return $this->belongsToMany(User::class, 'shop_users_coupons', 'coupon_id', 'user_id')
            ->withPivot('used_count')
        ;
    }

    public function getRemiseTypeLabelAttribute()
    {
        foreach (self::REMISE_TYPES as $key => $value) {
           if ($value['name'] === $this->remise_type){
               return $value['label'];
           }
        }

        return null;
    }

    public function getstartExpireDatesAttribute()
    {
        $starts_at  = $this->starts_at ?? now();
        $expires_at = $this->expires_at ?? now();

        return $starts_at->format('d/m/Y h:i A') . ' - ' . $expires_at->format('d/m/Y h:i A');
    }

    public function getFormatedValueAttribute() :?string
    {
        switch ($this->remise_type) {
            case $this->isPercentageRemiseType():
                return $this->value . '%';
                break;
            case $this->isFixedCartRemiseType():
                return \format_price($this->value);
                break;
            case $this->isFixedProductRemiseType():
                return \format_price($this->value);
                break;
        }

        return null;
    }

    /**
     *
     * @param string $code
     * @param User|int|null $user
     * @return array
     */
    public static function apply(string $code, $user = null) :array
    {
        /**
         * @var \App\Models\User $user
         */
        if (is_null($user)){
            $user = auth()->user();
        }else if (is_int($user)){
            $user = User::find($user);
        }

        // verifie si l'utilisation de code coupon a été activée
        if (!shop_settings('coupon')){
            return ['error', "L'utilisation de code coupon a été désactivée."];
        }

        /**
         * @var \Guysolamour\Administrable\Models\Shop\Coupon $coupon
         */
        $coupon = self::where('code', $code)->first();


        // si on ne trouve pas de coupon
        if (!$coupon) {
            return ['error', "Le code coupon entré n'est pas valide ! Merci de réessayer."];
        }

        // date de début
        if ($coupon->starts_at->isFuture()) {
            return ['error', "Le code coupon sera valide à partir du " . $coupon->starts_at->format('d/m/Y h:i')  . " ."];
        }

        // date de fin
        if ($coupon->expires_at->isPast()) {
            return ['error', "Le code coupon a expiré le " . $coupon->expires_at->format('d/m/Y h:i')  . " ."];
        }

        // limite d'utilisation par code
        if ($coupon->reachUsedLimit()) {
            return ['error', "Le nombre d'utilisation de ce code coupon a été atteint"];
        }

        if ($coupon->hasAlreadyBeenUsedBy($user) && $coupon->isOnlyBeUsedOne()) {
            return ['error', "Vous avez déjà utilisé ce coupon car il est utilisable qu'une seule fois"];
        }

        if ($coupon->userReachUsedLimit($user)) {
            return ['error', "Vous avez déjà utilisé ce coupon le nombre de fois autorisé"];
        }

        $cart_total = Cart::total();

        if ($min_expense = $coupon->min_expense) {
            // si le panier est inférieur au depense min on back
            if ($cart_total > $min_expense) {
                return ['error', "Le montant [" . format_price($cart_total) . "] de votre panier doit être supérieur ou égale au montant minimum de dépense [" . format_price($min_expense) . "] qu'exige le coupon."];
            }
        }

        if ($max_expense = $coupon->max_expense) {
            // si le panier est supérieur au depense max on back
            if ($cart_total > $max_expense) {
                return ['error', "Le montant [" . format_price($cart_total) . "] de votre panier doit être inférieur ou égale au montant maximun de dépense [" . format_price($max_expense) . "] qu'exige le coupon."];
            }
        }

        $models = Cart::models();

        if ($coupon->isPercentageRemiseType() || $coupon->isFixedCartRemiseType()) {

            foreach ($models as $model) {

                // si le coupon n'autorise pas les articles en promotion
                if ($coupon->exclude_promo_products && $model->isInPromotion()) {
                    return ['error', "Le produit [" . $model->name . "] en promotion n'est pas couvert par ce code coupon."];
                }

                // si le produit est exclu
                if (!empty($coupon->exclude_products)) {
                    if (in_array($model->getKey(), $coupon->exclude_products)) {
                        return ['error', "Le produit [" . $model->name . "] ne fait pas partie des produits couverts par ce code coupon"];
                    }
                }

                // si le produit ne fait partie des produits autorisés
                if (!empty($coupon->products)) {
                    if (!in_array($model->getKey(), $coupon->products)) {
                        return ['error', "Le produit [" . $model->name . "] ne fait pas partie des produits couverts par ce code coupon. Veuillez retirer le produit du panier si vous voulez utiliser ce code coupon."];
                    }
                }

                // si la catégorie est exclue
                if (!empty($coupon->exclude_categories)) {
                    foreach ($model->categories as $category) {
                        if (in_array($category->getKey(), $coupon->exclude_categories)) {
                            return ['error', "La catégorie [" . $category->name . "] du produit [" . $model->name . "] ne fait pas partie des catégories couvertes par ce code coupon. Veuillez retirer le produit du panier si vous voulez utiliser ce code coupon."];
                        }
                    }
                }

                // si la catégorie ne fait pas partie des catégoris sélectionnées
                if (!empty($coupon->categories)) {
                    foreach ($model->categories as $category) {
                        if (!in_array($category->getKey(), $coupon->categories)) {
                            return ['error', "La catégorie [" . $category->name . "] du produit [" . $model->name . "] ne fait pas partie des catégories couvertes par ce code coupon. Veuillez retirer le produit du panier si vous voulez utiliser ce code coupon."];
                        }
                    }
                }
            }

            Cart::setGlobalDiscount($coupon->value, $coupon->isPercentageRemiseType());

            if ($user){
                $user->useGlobalCoupon($coupon);
            }

            // emit event
            $coupon->incrementUsedCount();

            return ['success', 'Le code coupon a bien été appliqué.'];
        }

        if ($coupon->isFixedProductRemiseType() || $coupon->isPercentageProductRemiseType()) {

            foreach ($models as $key => $model) {

                // si le coupon n'autorise pas les articles en promotion
                if ($coupon->exclude_promo_products && $model->isInPromotion()) {
                    $models->forget($key);
                }

                // si le produit est exclu
                if (!empty($coupon->exclude_products)) {
                    if (in_array($model->getKey(), $coupon->exclude_products)) {
                        $models->forget($key);
                    }
                }

                // si le produit ne fait partie des produits autorisés
                if (!empty($coupon->products)) {
                    if (!in_array($model->getKey(), $coupon->products)) {
                        $models->forget($key);
                    }
                }

                // si la catégorie est exclue
                if (!empty($coupon->exclude_categories)) {
                    foreach ($model->categories as $category) {
                        if (in_array($category->getKey(), $coupon->exclude_categories)) {
                            $models->forget($key);
                            break;
                        }
                    }
                }

                // si la catégorie ne fait pas partie des catégoris sélectionnées
                if (!empty($coupon->categories)) {
                    foreach ($model->categories as $category) {
                        if (!in_array($category->getKey(), $coupon->categories)) {
                            $models->forget($key);
                            break;
                        }
                    }
                }
            }

            if ($models->isEmpty()) {
                return ['error' => "Aucun produit du panier n'est couvert par ce code coupon"];
            }

            // appliquer la remise
            $models->each(function ($model) use ($coupon, $user) {
                Cart::setDiscount($model, $coupon->value, $coupon->isPercentageProductRemiseType());

                if ($user) {
                    $user->useCoupon($coupon);
                }

                // emit event
                $coupon->incrementUsedCount();
            });

            return ['success' => "Le code coupon a bien été appliqué."];
        }
    }

    /**
     *
     * @param User|null $user
     * @return boolean
     */
    public function userReachUsedLimit(?User $user) :bool
    {
        if (is_null($user) || is_null($this->used_by_user_limit)) {
            return false;
        }

        $coupon = $user->coupons->filter(fn ($item) => $item->getKey() == $this->getKey())->first();

        return $this->used_by_user_limit <= $coupon->pivot->used_count;
    }

    /**
     * @return boolean
     */
    public function reachUsedLimit() :bool
    {
        if (is_null($this->used_time_limit)){
            return false;
        }
        return $this->used_time_limit <= $this->users->count();
    }

    /**
     * @param User|null $user
     * @return boolean
     */
    public function hasAlreadyBeenUsedBy(?User $user) :bool
    {
        if (is_null($user)){
            return false;
        }
        return $user->coupons->filter(fn ($item) => $item->getKey() == $this->getKey())->isNotEmpty();
    }
    /**
     *
     * @param User $user
     * @return boolean
     */
    public function isOnlyBeUsedOne() :bool
    {
        return $this->use_once;
    }

    /**
     * @param string|null $value
     * @return array
     */
    public function getProductsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /*
     * @param string|null $value
     * @return array
     */
    public function getExcludeProductsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
    /**
     * @param string|null $value
     * @return array
     */
    public function getCategoriesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }
    /**
     * @param string|null $value
     * @return array
     */
    public function getExcludeCategoriesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }


    public function isPercentageRemiseType() :bool
    {
        return $this->remise_type === self::REMISE_TYPES[0]['name'];
    }

    public function isFixedCartRemiseType()
    {
        return $this->remise_type === self::REMISE_TYPES[1]['name'];
    }

    public function isFixedProductRemiseType()
    {
        return $this->remise_type === self::REMISE_TYPES[2]['name'];
    }

    public function isPercentageProductRemiseType()
    {
        return $this->remise_type === self::REMISE_TYPES[3]['name'];
    }

    private function incrementUsedCount($amount = 1)
    {
        $this->increment('used_count', $amount);
        $this->save();
    }

    public static function booted()
    {
        parent::booted();

        /**
         * @param self $model
         */
        static::saving(function ($model) {
            // dd($model);
            $model->products           = request('products') ? json_encode(request('products')) : "";
            $model->exclude_products   = request('exclude_products') ? json_encode(request('exclude_products')) : "";
            $model->categories         = request('categories') ? json_encode(request('categories')) : "";
            $model->exclude_categories = request('exclude_categories') ? json_encode(request('exclude_categories')) : "";
        });
    }
}
