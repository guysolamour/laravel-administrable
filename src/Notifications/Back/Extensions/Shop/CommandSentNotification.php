<?php

namespace Guysolamour\Administrable\Notifications\Back\Extensions\Shop;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CommandSentNotification extends Notification
{
    use Queueable;

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
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
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
    public function toArray($notifiable)
    {
        return [
            'client' => $this->command->client->name,
            'url'    => back_route('shop.command.show', $this->command),
        ];
    }
}
