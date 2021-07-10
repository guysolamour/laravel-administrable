<?php

namespace Guysolamour\Administrable\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Guysolamour\Administrable\Module;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Notification;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class CommentController extends BaseController
{
    public function store(Request $request)
    {
        if (!config('administrable.comments.guest_commenting')) {
            $this->authorize('create', Module::model('comment'));
        }

        // Validate
        $request->validate([
            'guest_name'         => 'sometimes|required|string|max:255',
            'guest_email'        => 'sometimes|required|string|email|max:255',
            'commentable_type'   => 'required|string',
            'commentable_id'     => 'required|string|min:1',
            'content'            => 'required|string',
            'reply_notification' => 'required|in:on,off'
        ]);

        $model = $request->get('commentable_type')::findOrFail($request->get('commentable_id'));

        $comment = new (Module::model('comment'));

        if ($request->has('guest_name') || $request->has('guest_email')) {
            $comment->guest_name = $request->get('guest_name');
            $comment->guest_email = $request->get('guest_email');
        }else {
            $comment->commenter()->associate($request->user(config('administrable.guard')) ?? $request->user());
        }

        $comment->reply_notification = $request->get('reply_notification') === 'on' ? true : false;

        $comment->commentable()->associate($model);
        $comment->comment = $request->get('content');

        $guard = auth()->guard(config('administrable.guard'))->user();

        $comment->approved = $guard ? true : !config('administrable.comments.approval_required');
        $comment->save();

        // send notification to admins
        if (!$guard) {
            $notification = config('administrable.modules.comment.back.notification');
            Notification::send(Module::getGuardModel()::getNotifiables()->get(), new $notification($comment));
        }

        flashy(Lang::get('administrable::messages.controller.comment.create'));

        return Redirect::to(URL::previous() . '#comment-' . $comment->getKey());
    }


    /**
     * Creates a reply "comment" to a comment.
     */
    public function reply(Request $request, int $id)
    {
        $comment = Module::model('comment')::where('id', $id)->firstOrFail();

        $this->authorize('reply', $comment);

        $request->validate([
            'content' => 'required|string'
        ]);

        $guard = $request->user(config('administrable.guard'));

        $reply = new (Module::model('comment'));
        $reply->commenter()->associate($guard ?? $request->user());
        $reply->commentable()->associate($comment->commentable);
        $reply->parent()->associate($comment);
        $reply->comment = $request->get('content');
        $reply->approved = $guard ? true : !config('administrable.comments.approval_required');

        $reply->save();

        // check if  reply notification and send notifcation
        if ($comment->reply_notification && $comment->getCommenterEmail() != $reply->getCommenterEmail()) {
            $mail = config('administrable.modules.comment.front.replymail');

            Mail::to($comment->getCommenterEmail())->send(new $mail($comment));
        }

        flashy(Lang::get('administrable::messages.controller.comment.reply'));

        return Redirect::to(URL::previous() . '#comment-' . $reply->getKey());
    }

    /**
     * Updates the message of the comment.
     */
    public function update(Request $request, int $id)
    {
        $comment = Module::model('comment')::where('id', $id)->firstOrFail();

        $this->authorize('update', $comment);

        $request->validate([
            'content' => 'required|string'
        ]);

        $comment->update([
            'comment' => $request->get('content')
        ]);

        flashy(Lang::get('administrable::messages.controller.comment.edit'));

        return Redirect::to(URL::previous() . '#comment-' . $comment->getKey());
    }

    /**
     * Deletes a comment.
     */
    public function destroy(int $id)
    {
        $comment = Module::model('comment')::where('id', $id)->firstOrFail();

        $this->authorize('delete', $comment);
        $comment->delete();

        flashy(Lang::get('administrable::messages.controller.comment.delete'));

        return back();
    }

}
