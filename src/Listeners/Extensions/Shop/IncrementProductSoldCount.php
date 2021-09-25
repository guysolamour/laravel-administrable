<?php

namespace Guysolamour\Administrable\Listeners\Extensions\Shop;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Guysolamour\Administrable\Events\Shop\ConfirmCommandPayment;

class IncrementProductSoldCount
{
    /**
     * Handle the event.
     *
     * @param  ConfirmCommand  $event
     * @return void
     */
    public function handle(ConfirmCommandPayment $event)
    {
        $command = $event->command;

        $command->formated_products['items']->each(function ($product) {
            /**
             * @var \Illuminate\Database\Eloquent\Model|\Guysolamour\Administrable\Contracts\Shop\Shop
             */
            $model = $product->model;

            // au augmente le nombre d'achat du produit
            $model->increment('sold_count');

            $model->increment('sold_amount', $model->getPrice());

            // au réduit la quantité en stock
            if (shop_settings('stock_management') && $model->stock_management) {
                $model->decrement('stock');
            }
        });
        // CreateAndSendInvoice::class,

        // verifier le type de commande et envoyer la facture par mail
        // au client doit etre personnalisable en settings
        // si c'est un produit virtuel envoyer aussi le lien de telechargement
    }
}
