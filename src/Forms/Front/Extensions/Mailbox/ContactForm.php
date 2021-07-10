<?php

namespace Guysolamour\Administrable\Forms\Front\Extensions\Mailbox;

use Illuminate\Support\Facades\Lang;
use Kris\LaravelFormBuilder\Form;

class ContactForm extends Form
{
	public function buildForm()
	{
		$this->formOptions = [
			'method'      => 'POST',
			'url'         => front_route('extensions.mailbox.mailbox.store'),
		];


		$this
			->add('name','text', [
				'label' => Lang::get('administrable::extensions.mailbox.view.name'),
                'rules' => 'required|min:2',
             ])

			->add('email', 'email', [
				'label' => Lang::get('administrable::extensions.mailbox.view.email'),
                'rules' => 'required|email',
            ])

			->add('phone_number', 'text', [
				'label' => Lang::get('administrable::extensions.mailbox.view.phone_number'),
                'rules' => 'required',
			])
			->add('content', 'textarea', [
				'label' => Lang::get('administrable::extensions.mailbox.view.content'),
                'rules' => 'required|min:3',
			])

       ->add('send_copy', 'checkbox', [
              'label' => Lang::get('administrable::extensions.mailbox.view.send_copy'),
          ])

			;
	}
}
