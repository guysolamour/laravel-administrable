<?php

namespace Guysolamour\Administrable\Forms\Back;

use Illuminate\Support\Str;
use Kris\LaravelFormBuilder\Form;
use Illuminate\Support\Facades\Lang;

class CreateGuardForm extends Form
{
    public function buildForm()
    {
        $guard = config('administrable.guard');

        $this->formOptions = [
            'method' => 'POST',
            'url'    => back_route("{$guard}.store"),
            'name'   => get_form_name($this->getModel())
        ];

        $this
            // add fields here

            ->add('pseudo', 'text', [
                'label' => Lang::get('administrable::messages.view.guard.pseudo'),
                'rules' => ['required', 'min:2', 'unique:' . Str::plural($guard)],
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
                'rules'  => ['required', 'email', 'unique:' . Str::plural($guard)],
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
            ])
            ->add('password', 'password', [
                'label' => Lang::get('administrable::messages.view.guard.password'),
                'rules'  => 'required|min:8|confirmed',
            ])
            ->add('password_confirmation', 'password', [
                'label' => Lang::get('administrable::messages.view.guard.passwordconfirmation'),
                'rules'  => 'required|min:8',
            ])
            ;
    }
}
