<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use Illuminate\Support\Arr;
use Guysolamour\Administrable\Exceptions\CartException;
use Guysolamour\Administrable\Contracts\Shop\ShopContract;


class CartItem
{
    public int $rowId;

    public int $quantity;

    public int $qty;

    public int $price;

    public string $name;

    public ?string $image;

    public float $tax    = 0;

    public int $discount = 0;

    public int $subtotal;

    /**
     *
     * @var Shop|\Illuminate\Database\Eloquent\Model
     */
    public $model;


    public function __construct($item)
    {
        $this->rowId          = Arr::get($item, 'rowId');
        $this->quantity       = Arr::get($item, 'quantity');
        $this->qty            = Arr::get($item, 'quantity');
        $this->model          = $this->associateModel($item);
        $this->name           = $this->getName(Arr::get($item, 'name'));
        $this->price          = Arr::get($item, 'price');
        $this->image          = Arr::get($item, 'image');
        $this->tax            = Arr::get($item, 'tax');
        $this->discount       = Arr::get($item, 'discount');

        $this->subtotal       = $this->calculateSubtotal();
        $this->tax_total      = $this->calculateTaxTotal();
        $this->discount_total = $this->calculateDiscountTotal();
        $this->total          = $this->calculateTotal();

        return $this;
    }


    private function associateModel(array $item) :?ShopContract
    {
        if (is_null(Arr::get($item, 'model'))){
            return null;
        }

        return Arr::get($item, 'model')::find(Arr::get($item, 'rowId'));
    }


    private function getName(?string $name = null) : string
    {
        if ($name){
            return $name;
        }
        if (is_object($this->model) && method_exists($this->model, 'getName')) {
            return $this->model->getName();
        }
        if (is_object($this->model) && array_key_exists('name', $this->model->getAttributes())) {
            return $this->model->name;
        }

        throw new CartException("The name is required if the model is not an object and/or does not implements getName Method");
    }

    private function calculateSubtotal() : int
    {
        return round($this->price * $this->quantity);
    }


    private function calculateTaxTotal() : int
    {
        return round(($this->subtotal - $this->discount) * ($this->tax / 100));
    }


    private function calculateTotal() : int
    {
        return round($this->discount_total + $this->tax_total);
    }


    private function calculateDiscountTotal() : int
    {
        return round($this->subtotal - $this->discount);
    }

}
