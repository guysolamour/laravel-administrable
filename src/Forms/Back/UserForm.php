<?php

namespace Guysolamour\Administrable\Forms\Back;

use Illuminate\Validation\Rule;
use Kris\LaravelFormBuilder\Form;
use Illuminate\Support\Facades\Lang;

class UserForm extends Form
{
    public function buildForm()
    {
        if ($this->getModel() && $this->getModel()->getKey()) {
            $method = 'PUT';
            $url    = back_route('user.update', $this->getModel());
        } else {
            $method = 'POST';
            $url    = back_route('user.store');
        }

        $this->formOptions = [
            'method' => $method,
            'url'    => $url,
            'name'   => get_form_name($this->getModel()),
        ];

	    $this
            // add fields here
            ->add('name','text', [
                    'label' => Lang::get("administrable::messages.view.user.name"),
                ])
            ->add('pseudo','text', [
                'label' => Lang::get("administrable::messages.view.user.pseudo"),
                'rules' => 'required|min:2',
            ])
            ->add('email', 'email', [
                'label' => Lang::get("administrable::messages.view.user.email"),
                'rules' => [
                    'required','email',
                    Rule::unique('users')->ignore($this->getModel())
                ],
        ])
        ;
    }
}
