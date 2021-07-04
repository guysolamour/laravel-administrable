<?php

namespace Guysolamour\Administrable\Forms\Back;

use Kris\LaravelFormBuilder\Form;
use Illuminate\Support\Facades\Lang;

class ConfigurationForm extends Form
{
    public function buildForm()
    {
        $this->formOptions = [
            'method' => 'POST',
            'url'    => back_route('configuration.store'),
        ];

        $this
            // add fields here
            ->add('email', 'email', [
                'label' => Lang::get("administrable::messages.view.configuration.email"),
                'rules' => 'nullable|email',
                'value' => $this->getModel()->email,
            ])
            ->add('postal', 'text', [
                'label' => Lang::get("administrable::messages.view.configuration.postal"),
                'rules' => 'nullable|string',
                'value' => $this->getModel()->postal,
            ])
            ->add('area', 'text', [
                'label' => Lang::get("administrable::messages.view.configuration.area"),
                'rules' => 'nullable|string',
                'value' => $this->getModel()->area,
            ])
            ->add('cell', 'text', [
                'label' => Lang::get("administrable::messages.view.configuration.cell"),
                'rules' => 'nullable|string',
                'value' => $this->getModel()->cell,
            ])
            ->add('phone', 'text', [
                'label' => Lang::get("administrable::messages.view.configuration.phone"),
                'label' => 'Numéro de téléphone',
                'rules' => 'nullable|string',
                'value' => $this->getModel()->phone,
            ])
            ->add('whatsapp', 'text', [
                'label' => Lang::get("administrable::messages.view.configuration.whatsapp"),
                'rules' => 'nullable|string',
                'value' => $this->getModel()->whatsapp,
            ])
            ->add('youtube', 'url', [
                'label' => Lang::get("administrable::messages.view.configuration.youtube"),
                'rules' => 'nullable|url',
                'value' => $this->getModel()->youtube,
            ])
            ->add('twitter', 'url', [
                'label' => Lang::get("administrable::messages.view.configuration.twitter"),
                'rules' => 'nullable|url',
                'value' => $this->getModel()->twitter,
            ])
            ->add('facebook', 'url', [
                'label' => Lang::get("administrable::messages.view.configuration.facebook"),
                'rules' => 'nullable|url',
                'value' => $this->getModel()->facebook,
            ])
            ->add('linkedin', 'url', [
                'label' => Lang::get("administrable::messages.view.configuration.linkedin"),
                'rules' => 'nullable|url',
                'value' => $this->getModel()->linkedin,
            ])
            ->add('logo', 'file', [
                'label' => Lang::get("administrable::messages.view.configuration.logo"),
                'rules' => 'nullable|image',
                'value' => $this->getModel()->logo,
            ])
            ->add('about', 'textarea', [
                'label' => Lang::get("administrable::messages.view.configuration.about"),
                'rules' => 'nullable|string',
                'value' => $this->getModel()->about,
            ]);
    }
}
