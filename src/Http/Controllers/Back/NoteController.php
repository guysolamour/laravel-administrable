<?php

namespace Guysolamour\Administrable\Http\Controllers\Back;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Http\Controllers\BaseController;
use Guysolamour\Administrable\Module;

class NoteController extends BaseController
{
    public function store(Request $request)
    {
        $request->validate([
            'commenter_id'     => 'sometimes|required|integer',
            'commenter_type'   => 'sometimes|required|string',
            'commentable_id'   => 'required|integer',
            'commentable_type' => 'required|string',
            'comment'          => 'required|string',
        ]);

        /**
         * @var \Illuminate\Database\Eloquent\Model
         */
        $note = new (Module::model('comment'));
        $note->commenter_id     = $request->input('commenter_id') ?: get_guard('id');
        $note->commenter_type   = $request->input('commenter_type') ?: get_class(get_guard());
        $note->commentable_id   = $request->input('commentable_id');
        $note->commentable_type = $request->input('commentable_type');
        $note->comment          = trim($request->input('comment'));
        $note->save();

        if (request()->ajax()) {
            return response()->json($note->load('commenter'));
        }

        flashy('La note a bien été mise à jour');

        return back();
    }


    public function update(int $id, Request $request)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);

        /**
         * @var \Illuminate\Database\Eloquent\Model
         */
        $comment = Module::model('comment')::where('id', $id)->firstOrFail();

        $comment->update([
            'comment' => trim($request->input('comment')),
        ]);

        if (request()->ajax()) {
            return response()->json($comment->load('commenter'));
        }

        flashy('La note a bien été mise à jour');

        return back();
    }


    public function destroy(int $id)
    {
        /**
         * @var \Illuminate\Database\Eloquent\Model
         */
        $comment = Module::model('comment')::where('id', $id)->firstOrFail();
        $comment->delete();

        if (request()->ajax()){
            return response()->json($comment);
        }

        flashy('La note a bien été supprimée');

        return back();
    }
}



