<?php

namespace Guysolamour\Administrable\Mail\Back\Extensions\Shop;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class ProductSoldOutMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;

    public $products;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($admin, Collection $products)
    {
        $this->admin    = $admin;
        $this->products = $products;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                ->from(config('mail.from.address'))
                ->subject('Produits en rupture de stock ' . config('app.name'))
                ->markdown('emails.back.shop.productsoldout');
    }
}
