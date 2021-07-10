<?php

namespace Guysolamour\Administrable\Http\Controllers\Back\Extensions\Mailbox;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Guysolamour\Administrable\Module;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class MailboxController extends BaseController
{
      /**
       * Display a listing of the resource.
       *
       * @return \Illuminate\Http\Response
       */
      public function index()
      {
          $mailboxes = config('administrable.extensions.mailbox.model')::last()->get();

          $unread = config('administrable.extensions.mailbox.model')::unRead()->count();

          return back_view('extensions.mailboxes.mailboxes.index', compact('mailboxes','unread'));
      }


      /**
       * Display the specified resource.
       *
       * @param  string  $slug
       * @return \Illuminate\Http\Response
       */
      public function show(string $slug)
      {
          $mailbox = config('administrable.extensions.mailbox.model')::where('slug', $slug)->firstOrFail();

          $mailbox->markAsRead();

          return back_view('extensions.mailboxes.mailboxes.show', compact('mailbox'));
      }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $slug)
    {
        $mailbox = config('administrable.extensions.mailbox.model')::where('slug', $slug)->firstOrFail();

        $mailbox->delete();

        flashy(Lang::get('administrable::extensions.mailbox.controller.delete'));

        return redirect()->to(back_route('extensions.mailbox.mailbox.index'));
    }

    public function saveNote(string $slug, Request $request)
    {
        $mailbox = config('administrable.extensions.mailbox.model')::where('slug', $slug)->firstOrFail();

        $request->validate([
            'comment'   => 'required|min:10',
            'color'     => 'required',
            'email'     => 'required_if:save,0',
            'subject'   => 'required_if:save,0',
        ], [
            'comment.required'      => Lang::get('administrable::extensions.mailbox.validation.comment_required'),
            'email.required_if'     => Lang::get('administrable::extensions.mailbox.validation.email_required_if'),
            'subject.required_if'   => Lang::get('administrable::extensions.mailbox.validation.subject_required_if'),
        ]);

        $note = new (Module::model('comment'));
        $note->commenter()->associate(get_guard());
        $note->commentable()->associate($mailbox);
        $note->comment = $request->get('comment');
        $note->guest_name = $request->get('color');
        $note->reply_notification = false;

        $note->save();

        // send email
        if ($request->get('save') == 0 && $request->get('email') && $request->get('subject')) {
            $notification = config('administrable.extensions.mailbox.back.note_mail');

            Mail::to($request->get('email'))->send(new $notification($request->get('subject'), $note));
        }

        flashy(Lang::get('administrable::extensions.mailbox.controller.note.create'));

        return back();
    }


    public function updateNote(string $slug, int $comment_id, Request $request)
    {
        $comment = Module::model('comment')::where('id', $comment_id)->firstOrFail();

        $request->validate([
            'comment' => 'required|min:10',
            'color'   => 'required',
        ], [
            'content.required'   => 'La note ne peut pas être vide',
            'color.required'     => "La couleur est obligatoire",
        ]);

        $comment->update([
            'comment'       => $request->get('comment'),
            'guest_name'    => $request->get('color'),
        ]);

        flashy(Lang::get('administrable::extensions.mailbox.controller.note.update'));

        return back();
    }

    public function deleteNote(string $slug, int $comment_id)
    {
        $comment = Module::model('comment')::where('id', $comment_id)->firstOrFail();

        $comment->delete();

        flashy(Lang::get('administrable::extensions.mailbox.controller.note.delete'));

        return back();
    }
}
