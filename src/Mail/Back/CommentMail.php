<?php

namespace Guysolamour\Administrable\Mail\Back;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Lang;

class CommentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;

    public $comment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($admin, $comment)
    {
        $this->admin   = $admin;
        $this->comment = $comment;
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
            ->subject(Lang::get('Nouveau commentaire ') . $this->comment->getCommenterName())
            ->markdown('administrable::emails.back.comment');
    }
}
