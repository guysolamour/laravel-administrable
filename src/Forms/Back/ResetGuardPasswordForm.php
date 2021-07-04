<?php

namespace Guysolamour\Administrable\Forms\Back;

use Kris\LaravelFormBuilder\Form;
use Illuminate\Support\Facades\Lang;

class ResetGuardPasswordForm extends Form
{
    public function buildForm()
    {
        $this->formOptions = [
            'method' => 'PUT',
            'url'    => back_route( config('administrable.guard') . '.change.password', $this->getModel()),
            'name'   => get_form_name($this->getModel())
        ];

        $this
            // add fields here

            //		    ->add('old_password', 'password', [
            //			    'label' => 'Mot de passe actuel',
            //			    'rules' => 'required|min:6'
            //		    ])
            ->add('new_password', 'password', [
                'label' => Lang::get('administrable::messages.view.guard.newpassword'),
                'rules' => 'required|min:8|confirmed'
            ])
            ->add('new_password_confirmation', 'password', [
                'label' => Lang::get('administrable::messages.view.guard.newpasswordconfirmation'),
                'rules' => 'required|min:8'
            ]);
    }
}
