<?php

namespace Guysolamour\Administrable\Mail\Front\Extensions\Mailbox;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMeContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(Lang::get('administrable::extensions.mailbox.mail.front.subject') . config('app.name'))
            ->markdown('administrable::emails.front.extensions.mailbox.sendmessagecopy');
    }
}
