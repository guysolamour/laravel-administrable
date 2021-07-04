<?php

namespace Guysolamour\Administrable\Http\Controllers\Back;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Guysolamour\Administrable\Module;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class CommentController extends BaseController
{
    use FormBuilderTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = Module::model('comment')::notes(false)->last()->get();

        return back_view('comments.index', compact('comments'));
    }


    /**
     *
     * Display the specified resource.
     *
     * @param  int  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $comment = Module::model('comment')::where('id', $id)->firstOrFail();

        return back_view('comments.show', compact('comment'));
    }

     /**
     * @param int $id
     * @param Request $request
     */
    public function reply(int $id, Request $request)
    {
        $comment = Module::model('comment')::where('id', $id)->firstOrFail();

        $request->validate([
            'comment'  => 'required',
            'child_id' => 'required',
        ]);

        $model = Module::model('comment');

        $reply                   = new $model;
        $reply->comment          = $request->get('comment');
        $reply->child_id         = $request->get('child_id');
        $reply->commentable_id   = $comment->commentable_id;
        $reply->commentable_type = $comment->commentable_type;

        /**
         * @var \Illuminate\Database\Eloquent\Model
         */
        $guard = auth()->guard(config('administrable.guard'))->user();

        if ( $request->get('guest_name') === $guard->full_name && $request->get('guest_email') === $guard->email){
            $reply->commenter_id    = $guard->id;
            $reply->commenter_type  = get_class($guard);
        }else {
            $reply->guest_name      = $request->get('guest_name');
            $reply->guest_email     = $request->get('guest_email');
        }

        $reply->save();

        // check if  reply notification and send notifcation
        if ($comment->reply_notification && $comment->getCommenterEmail() != $reply->getCommenterEmail()) {
            $mail = config('administrable.modules.comment.front.mail.replymail');

            Mail::to($comment->getCommenterEmail())->send(new $mail($comment));
        }

        flashy(Lang::get("administrable::messages.controller.comment.reply"));

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {

        $comment = Module::model('comment')::where('id', $id)->firstOrFail();

        $form = $this->getForm($comment, Module::backForm('comment'));

        return back_view('comments.edit', compact('comment', 'form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $comment = Module::model('comment')::where('id', $id)->firstOrFail();

        $form = $this->getForm($comment, Module::backForm('comment'));
        $form->redirectIfNotValid();
        $comment->update($request->all());

        flashy(Lang::get("administrable::messages.controller.comment.create"));

        return redirect()->to(back_route('comment.index'));
    }

    /**
     * Approved the comments.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approved(int $id)
    {
        $comment = Module::model('comment')::where('id', $id)->firstOrFail();

        $comment->approved();

        flashy(Lang::get("administrable::messages.controller.comment.approved"));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $comment = Module::model('comment')::where('id', $id)->firstOrFail();

        $comment->delete();

        flashy(Lang::get("administrable::messages.controller.comment.delete"));

        return redirect()->to(back_route('comment.index'));
    }

}
