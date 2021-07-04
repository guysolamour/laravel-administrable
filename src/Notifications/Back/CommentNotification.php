<?php

namespace Guysolamour\Administrable\Notifications\Back;

use Illuminate\Bus\Queueable;
use App\Mail\Back\CommentMail;
use Guysolamour\Administrable\Module;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentNotification extends Notification
{
    use Queueable;

    public $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = Module::backMail('comment');

        return (new $mail($notifiable, $this->comment ))->to($notifiable->email);
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
            'commenter_name'  => $this->comment->getCommenterName(),
            'commenter_email' => $this->comment->getCommenterEmail(),
            'comment'         => $this->comment
        ];
    }
}
