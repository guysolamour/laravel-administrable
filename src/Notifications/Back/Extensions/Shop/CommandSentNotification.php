<?php

namespace Guysolamour\Administrable\Notifications\Back\Extensions\Shop;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CommandSentNotification extends Notification
{
    use Queueable;

    /**
     *
     * @var \Guysolamour\Administrable\Models\Extensions\Shop\Command
     */
    public $command;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  \App\Models\Admin  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $drivers = ['mail', 'database'];

        if (
            $notifiable->getCustomField('notify_via_whatsapp') &&
            $notifiable->getCustomField('cb_whatsapp_apikey')
            ){
            $drivers[] = 'cbwhatsapp';
        }

        return $drivers;
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
                    ->subject("Nouvelle commande de la boutique")
                    ->line('Bonjour ' . $notifiable->full_name)
                    ->line('Une commande vient d\'être effectuée sur la boutique par ' . $this->command->client->name)
                    ->action('Voir la commande', back_route('extensions.shop.command.edit', $this->command))
                    ->line('Merci de traiter cette commande.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toCbWhatsapp($notifiable)
    {
        return "Bonjour *" . $notifiable->full_name . "*, une nouvelle commande  sur la boutique *" . config('app.name') . "* par *" . $this->command->client->name . "* pour un montant de *" . format_price($this->command->total_with_shipping) ."* et joignable au *". $this->command->client->phone_number ."*. Merci de traiter cette commande.";
    }
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'id'     => $this->command->id,
            'client' => $this->command->client->name,
            'total'  => $this->command->total_with_shipping,
        ];
    }
}
