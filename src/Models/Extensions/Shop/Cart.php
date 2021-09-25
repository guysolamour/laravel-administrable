<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Guysolamour\Administrable\Models\Extensions\Shop\CartItem;


class Cart {
    /**
     * @var string
     */
    private const DEFAULT_INSTANCE = 'default';

    /**
     * @var string
     */
    private const GLOBAL_INSTANCE  = 'global';


    /**
     *
     * @var string
     */
    private $instance;

    /**
     * Current authenticated user
     *
     * @var Illuminate\Foundation\Auth\User|null
     */
    private $user;



    public function __construct()
    {
        $this->setInstance(self::DEFAULT_INSTANCE);

        $this->user = Auth::user();
    }

    /**
     * Set cart instance
     *
     * @param string|null $instance
     * @return self
     */
    private function setInstance(?string $instance = null) :self
    {
        $this->instance = "shop_${instance}_instance";

        return $this;
    }

    /**
     * Set cart instance like shopping or wishlist
     *
     * @param string $instance
     * @return self
     */
    public function instance(string $instance) :self
    {
        $this->setInstance($instance);

        return $this;
    }
    /**
     * Set cart instance like shopping or wishlist
     *
     * @param string $instance
     * @return string
     */
    public function globalInstance() :string
    {
        return "shop_global_" . self::GLOBAL_INSTANCE . "_instance";
    }

    /**
     * Alias for instance method
     *
     * @param string $instance
     * @return self
     */
    public function from(string $instance) :self
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Get card from session or database
     *
     * @return Collection
     */
    private function get()
    {
        return $this->user ? $this->fromDatabase() : $this->fromSession();
    }
    /**
     * Get card from session or database
     *
     * @return Collection
     */
    private function fromGlobals()
    {
        if ($this->user){
            return $this->getInDatabase('globals');
        }
        return session($this->globalInstance(), []);
    }

    private function getInDatabase($key, bool $decode = true)
    {
        $content =  DB::table(config('administrable.extensions.shop.cart_dbname'))->where([
            'user_id'  => $this->user->id,
            'instance' => $this->instance,
        ])->value($key);

        if (!$content){
            return [];
        }

        return $decode ? json_decode($content, true) : $content;
    }

    /**
     * Get cart from session
     *
     * @return Collection
     */
    public function fromSession()
    {
        return session($this->instance, collect());
    }

    /**
     * Get cart from database
     * @return Collection
     */
    public function fromDatabase()
    {
        if ($this->user){
            return collect($this->getInDatabase('content'));
        }
        return collect();
    }

    public function mergeFromSessionToDatabase()
    {

        $session_cart = $this->fromSession();

        if ($session_cart->isEmpty()){
            return;
        }

        $db_cart = $this->fromDatabase();

        $cart = $session_cart->merge($db_cart);

        $this->save($cart);
    }


    /**
     * Get  current cart item
     *
     * @param Collection $cart
     * @param integer $value
     * @param string $key
     * @return mixed
     */
    public function getItem($cart, int $value, string $key = 'rowId')
    {
        return $cart->firstWhere($key, $value);
    }


    /**
     * Add an item to cart
     *
     * @param  Model|int $model
     * @param  integer $quantity
     * @param  int|null $price
     * @param  float|null $tax
     * @param  integer $discount
     * @return self
     */
    public function add($model, int $quantity = 1, ?int $price = null, ?string $name = null, ?float $tax = null, int $discount = 0) :self
    {
        $id = $this->getRowId($model);

        $cart = $this->get();

        if ($cartItem = $this->getItem($cart, $id)) {
            $this->incrementQuantity($cartItem, $quantity);
        }else {
            $this->create($cart, $model, $id, $quantity, $price, $name, $tax, $discount);
        }

        return $this;
    }

    private function setItem(array $item)
    {
        return [
            'rowId'    =>  Arr::get($item, 'id'),
            'name'     =>  Arr::get($item, 'name'),
            'model'    =>  is_object(Arr::get($item, 'model')) ? get_class(Arr::get($item, 'model')) : null,
            'image'    =>  $this->getNewItemImage(Arr::get($item, 'model'),  Arr::get($item, 'image')),
            'price'    =>  $this->getNewItemPrice(Arr::get($item, 'model'),  Arr::get($item, 'price')),
            'quantity' =>  Arr::get($item, 'quantity', 1),
            'tax'      =>  $this->getTax(Arr::get($item, 'tax')),
            'discount' =>  Arr::get($item, 'discount', 0),
        ];
    }

    /**
     *
     * @param array $options
     * @return void
     */
    private function create($cart, $model, int $id, int $quantity, ?int $price, ?string $name, ?float $tax, int $discount) :void
    {
        $cart[] = $this->setItem(compact('id', 'name', 'model', 'price', 'quantity', 'tax', 'discount'));

        $this->save($cart);
    }

    /**
     *
     * @param float|null $tax
     * @return float
     */
    private function getTax(?float $tax = null) :float
    {
        if ($tax){
            return $tax;
        }

        if (shop_settings('tva')) {
            return shop_settings('tva_percentage');
        }

        return 0.0;
    }


    /**
     *
     * @param Collection $cartItem
     * @param integer $quantity
     * @return void
     */
    private function incrementQuantity($cartItem, int $quantity)
    {
        $this->update($cartItem['rowId'], ['quantity' => $cartItem['quantity'] + $quantity]);
    }


    private function getNewItemImage($model, ?string $image) :?string
    {
        if ($image) {
            return $image;
        }

        if (is_object($model) && method_exists($model, 'getFrontImageUrl')) {
            return $model->getFrontImageUrl();
        }

        if (is_object($model) && array_key_exists('image', $model->getAttributes())) {
            return $model->image;
        }

        throw new \Exception("The image is required if the model is not an object and/or does not implements getFrontImageUrl Method");
    }

    /**
     * @param  Model|int $model
     * @param  integer|null $price
     * @return integer
     */
    private function getNewItemPrice($model,?int $price) :int
    {
        if ($price){
            return $price;
        }

        if (is_object($model) && method_exists($model, 'getPrice')){
            return $model->getPrice();
        }

        if (is_object($model) && array_key_exists('price', $model->getAttributes())) {
            return $model->price;
        }

        throw new \Exception("The price is required if the model is not an object and/or does not implements getPrice Method");
    }

    /**
     * Edit item in cart
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    private function edit(string $key, $value, int $id)
    {
        $cart =  $this->get();

        $cart->transform(function($item) use ($id, $key, $value) {
            if ($item['rowId'] == $id && array_key_exists($key, $item)){
                $item[$key] = $value;
            }
            return $item;
        });

        $this->save($cart);
    }

    /**
     *
     * @param \App\Models\Model|integer $rowId
     * @return integer|string
     */
    private function getRowId($rowId)
    {
        return is_object($rowId) ? $rowId->getKey() : $rowId;
    }

    /**
     *
     * @param \Illuminate\Database\Eloquent\Model|integer $rowId
     * @param array $options
     * @return void
     */
    public function update($rowId, array $options)
    {
        $rowId = $this->getRowId($rowId);

        foreach($options as $key => $value){
            $this->edit($key, $value, $rowId);
        }
    }

    /**
     * @var \Illuminate\Database\Eloquent\Model|int $model
     * @var int $value
     */
    public function setDiscount($model, int $value, bool $percentage = false)
    {
        $rowId = $this->getRowId($model);

        $cart = $this->get();

        $cartItem = $this->getItem($cart, $rowId);

        $value = $percentage ? (int) round($cartItem['price'] * ($value / 100)) : $value;

        // recuperer le cart item d'abord
        $this->update($model, ['discount' => $cartItem['discount'] + $value]);
    }

    /**
     * Set discount to all elements in the cart
     *
     * @param integer $discount
     * @param boolean $percentage
     * @return self
     */
    public function setGlobalDiscount(int $discount, bool $percentage = false) :self
    {

        $globals = $this->fromGlobals();

        $globals['discount'] =  $percentage ? (int) round($this->content()['subtotal'] * ($discount / 100)) : $discount;


        $this->saveGlobals($globals);


        return $this;
    }

    /**
     * @return Collection
     */
    public function restore()
    {
        return $this->content();
    }

    /**
     *
     * @param Collection $data
     * @return array
     */
    public function hydrate($data, $globals = [])
    {
        if (is_null($data) && is_null($globals)){
            return $this->getRawCartContent();

        }
        return $this->content($data, $globals);
    }


    /**
     *
     * @param Collection $data
     * @param array $item
     * @return Collection
     */
    public function merge($data, array $item)
    {
        $data->push($this->setItem($item));

        return $data;
    }


    /**
     * Remove item from cart
     *
     * @param \App\Models\Model|int $rowId
     * @return void
     */
    public function remove($rowId)
    {
        $rowId = $this->getRowId($rowId);

        $cart = $this->get()->filter(fn($item) => $item['rowId'] != $rowId);

        $this->save($cart);
    }

    /**
     * Remove all items in card
     *
     * @return void
     */
    public function clear()
    {
        $this->user ? $this->clearInDatabase() : $this->clearInSession();
    }

    private function clearInDatabase() :void
    {
        DB::table(config('administrable.extensions.shop.cart_dbname'))
            ->where('user_id', $this->user->id)
            ->where('instance', $this->instance)
            ->delete();
    }

    private function clearInSession() :void
    {
        session()->forget([$this->instance, $this->globalInstance()]);
    }


    /**
     * Save cart in session or database
     * @param Collection $cart
     */
    public function save($cart)
    {
        $this->user ? $this->saveInDatabase($cart) : $this->saveInSession($cart);

    }


    private function saveInSession($cart)
    {
        session()->put($this->instance, $cart);
    }

    /**
     *
     * @param Collection $cart
     * @return void
     */
    private function saveInDatabase($cart)
    {
        $this->storeInDatabase('content', $cart->toJson());
    }

    private function storeInDatabase(string $key, $value)
    {
        DB::table(config('administrable.extensions.shop.cart_dbname'))
            ->updateOrInsert(
                [
                    'user_id'  => $this->user->id,
                    'instance' => $this->instance,
                ],
                [
                    $key  =>  $value
                ]
            );
    }

    /**
     * Save cart in session or database
     */
    private  function saveGlobals($globals)
    {
        $this->user ? $this->saveGlobalsInDatabase($globals) : $this->saveGlobalsInSession($globals);
    }


    private function saveGlobalsInSession($data)
    {
        session()->put($this->globalInstance(), $data);
    }


    private function saveGlobalsInDatabase($data)
    {
        $this->storeInDatabase('globals', json_encode($data));
    }

    /**
     * Get all items related models
     * @return \Illuminate\Support\Collection
     */
    public function models()
    {
        $items = $this->content()['items'];

        return $items->pluck('model');
    }


    public function rawContent()
    {
        return $this->get();
    }


    public function rawGlobals()
    {
        return $this->fromGlobals();
    }


    private function getRawCartContent() :array
    {
        return [
            'instance'     => $this->instance,
            'length'       =>  0,
            'tax'          =>  0,
            'discount'     =>  0,
            'all_discount' =>  0,
            'subtotal'     =>  0,
            'total'        =>  0,
            'globals'      => [],
            'items'        => [],
        ];
    }


    /**
     * Return all items in card
     * @param Collection $data
     * @param array $globals
     * @return array
     */
    public function content($data = null, $globals = null)
    {

        $cart = $data ?: $this->get();
        $cartItems = $cart->mapInto(CartItem::class);

        $globals = is_null($globals) ? $this->fromGlobals() : $globals;

        return $this->calculate($cartItems, $globals);
    }

    private function calculate($cartItems, $globals = []) :array
    {
        return array_merge($this->getRawCartContent(), [
            'length'      => $cartItems->count(),
            'tax'         => $tax = $cartItems->sum('tax_total'),
            'discount'    => $discount = $cartItems->sum('discount'),
            'all_discount' => $cartItems->isNotEmpty() ? $discount + Arr::get($globals, 'discount', 0) : 0,
            'subtotal'    => $cartItems->sum('subtotal'),
            'total'       => $cartItems->isNotEmpty() ? $this->getTotal($cartItems, $globals, $tax, $discount) : 0,
            'globals'     => $cartItems->isNotEmpty() ? $globals : [],
            'items'       => $cartItems,
        ]);
    }



    /**
     *
     * @param Collection $cartItems
     * @param Collection $globals
     * @return integer
     */
    private function getTotal($cartItems, $globals, float $tax, int $discount) : int
    {
        /**
         * Le total se calcule comme suit
         * si remise gloable on le soustrait du sous total
         * car les remises sont faites avant tva
         */
       $total = Arr::get($globals, 'discount') ? $cartItems->sum('subtotal') - $globals['discount'] : $cartItems->sum('subtotal');

        return ($total + $tax) - $discount;
    }

    /**
     * Check if cart is empty
     *
     * @return boolean
     */
    public function isEmpty() :bool
    {
       return $this->get()->isEmpty();
    }

    /**
     * Count all items in cart
     *
     * @return integer
     */
    public function count() :int
    {
        return $this->get()->count();
    }


    /**
     * Get cart total price
     *
     * @return int
     */
    public function total() :int
    {
        return $this->content()['items']->sum('total');
    }

}
