<?php

namespace Guysolamour\Administrable\Listeners\Extensions\Shop;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Guysolamour\Administrable\Events\Shop\ConfirmCommandPayment;

class CreateCommandOrder
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

        $command->order()->create([
            'amount'         => $command->total,
            'deliver_name'   => $command->deliver_name,
            'deliver_price'  => $command->deliver_price,
            'user_id'        => $command->user_id,
        ]);

        // $command->generatePdf();

        // verifier le type de commande et envoyer la facture par mail
        // au client doit etre personnalisable en settings
        // si c'est un produit virtuel envoyer aussi le lien de telechargement
    }
}
