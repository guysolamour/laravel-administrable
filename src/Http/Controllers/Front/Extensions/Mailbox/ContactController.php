<?php

namespace Guysolamour\Administrable\Http\Controllers\Front\Extensions\Mailbox;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class ContactController extends BaseController
{
    use FormBuilderTrait;

    public function create()
    {
        $form = $this->getForm(new (config('administrable.extensions.mailbox.model')), config('administrable.extensions.mailbox.front.form'), false);

        $page = get_meta_page('contact');

        return front_view('extensions.mailboxes.create', compact('form', 'page'));
    }

    public function store(Request $request)
    {
        $form = $this->getForm( new (config('administrable.extensions.mailbox.model')), config('administrable.extensions.mailbox.front.form'), false );
        $form->redirectIfNotValid();

        $mailbox = config('administrable.extensions.mailbox.model')::create( $request->all() );

        if($request->get('send_copy')){
            $mail = config('administrable.extensions.mailbox.front.mail');
            Mail::to($mailbox->email)->send(new $mail($mailbox));
        }

        $notification = config('administrable.extensions.mailbox.back.notification');
        Notification::send(get_guard_model_class()::getNotifiables()->get(), new $notification($mailbox));

        if ($request->ajax()) {
            return response()->json(['sucess' => Lang::get('administrable::extensions.mailbox.controller.create')]);
        }

        flashy( Lang::get('administrable::extensions.mailbox.controller.create') );

        return back();
    }
}
