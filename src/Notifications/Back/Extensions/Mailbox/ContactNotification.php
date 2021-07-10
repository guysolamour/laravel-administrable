<?php

namespace Guysolamour\Administrable\Notifications\Back\Extensions\Mailbox;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContactNotification extends Notification
{
    use Queueable;

    public $mailbox;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($mailbox)
    {
        $this->mailbox = $mailbox;
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toMail($notifiable)
    {
        $notification = config('administrable.extensions.mailbox.back.mail');

        return (new $notification($notifiable, $this->mailbox))->to($notifiable->email);
    }
}
