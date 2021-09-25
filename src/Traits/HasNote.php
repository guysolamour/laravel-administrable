<?php

namespace Guysolamour\Administrable\Traits;

use Guysolamour\Administrable\Module;
use Guysolamour\Administrable\Traits\CommentableTrait;


trait HasNote {

    use CommentableTrait;

    public function notes()
    {
        return $this->comments();
    }

    public function saveNote()
    {
        request()->validate([
            'note.content' => 'sometimes|required',
            'note.color'   => 'sometimes|required',
            'note.email'   => 'sometimes|required_if:save,0',
            'note.subject' => 'sometimes|required_if:save,0',
        ], [
            'note.content.required'    => 'La note ne peut pas être vide',
            'note.email.required_if'   => "L'email est obligatoire et doit être valide pour envoyer un email",
            'note.subject.required_if' => "Le sujet est obligatoire et doit être valide pour envoyer un email",
        ]);

        /**
         * @var \Guysolamour\Administrable\Models\Comment
         */
        $note = new (Module::model('comment'));
        $note->comment            = request('note.content');
        $note->guest_name         = request('note.guest_name') ?? request('note.color');
        $note->guest_email        = request('note.guest_email');
        $note->reply_notification = false;
        $note->commenter()->associate(get_guard());
        $note->commentable()->associate($this);

        $note->save();
    }


    public static function bootHasNote()
    {
        /**
         * @param $this $model
         */
        static::saved(function ($model) {
            if (request('note') && !empty(request('note'))){
                $model->saveNote();
            }
        });
    }
}
