<?php

namespace Guysolamour\Administrable\Forms\Back;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Kris\LaravelFormBuilder\Form;
use Illuminate\Support\Facades\Lang;

class GuardForm extends Form
{
    public function buildForm()
    {
        $this->formOptions = [
            'method' => 'PUT',
            'url'    => back_route(config('administrable.guard') . '.update', $this->getModel()),
            'name'   => get_form_name($this->getModel())
        ];

        $this
            // add fields here
            ->add('pseudo', 'text', [
                'label' => Lang::get('administrable::messages.view.guard.pseudo'),
                'rules' => [
                    'required', 'min:2',
                    Rule::unique(Str::plural(config('administrable.guard')))->ignore($this->getModel())
                ],
            ])
            ->add('first_name', 'text', [
                'label' => Lang::get('administrable::messages.view.guard.firstname'),
                'rules' => 'required|min:2',
            ])
            ->add('last_name', 'text', [
                'label' => Lang::get('administrable::messages.view.guard.lastname'),
                'rules' => 'required|min:2',
            ])
            ->add('email', 'email', [
                'label' => Lang::get('administrable::messages.view.guard.email'),
                'rules' => [
                    'required', 'email',
                    Rule::unique(Str::plural(config('administrable.guard')))->ignore($this->getModel())
                ],
            ])
            ->add('facebook', 'text', [
                'label' => Lang::get('administrable::messages.view.guard.facebook'),
            ])
            ->add('twitter', 'text', [
                'label' => Lang::get('administrable::messages.view.guard.twitter'),
            ])
            ->add('linkedin', 'text', [
                'label' => Lang::get('administrable::messages.view.guard.linkedin'),
            ])
            ->add('phone_number', 'text', [
                'label' => Lang::get('administrable::messages.view.guard.telephone'),
            ])
            ->add('website', 'text', [
                'label' => Lang::get('administrable::messages.view.guard.website'),
            ])
            ->add('about', 'textarea', [
                'label' => Lang::get('administrable::messages.view.guard.about'),
                'attr' => [
                    'data-tinymce'
                ]
            ]);
    }
}
