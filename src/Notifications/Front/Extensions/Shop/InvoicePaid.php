<?php

namespace Guysolamour\Administrable\Notifications\Front\Extensions\Shop;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaid extends Notification
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Votre facture est disponible')
                    ->line('Bonjour votre achat dans notre boutique à été éffectué avec succès.')
                    ->line('Vous trouverez votre facture en pièce jointe')
                    // ->action('Notification Action', url('/'))
                    ->line('Merci et à bientôt dans notre boutique!')
                    ->attach($this->order->invoice_path);
                    ;
    }

}
