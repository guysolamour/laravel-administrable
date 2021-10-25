<?php

namespace Guysolamour\Administrable\Forms\Back\Extensions\Ad;

use Kris\LaravelFormBuilder\Form;

class AdForm extends Form
{
    public function buildForm()
    {
        if ($this->getModel() && $this->getModel()->getKey()) {
            $method = 'PUT';
            $url    = back_route('extensions.ads.ad.update', $this->getModel());
        } else {
            $method = 'POST';
            $url    = back_route('extensions.ads.ad.store');
        }

        $this->formOptions = [
            'method' => $method,
            'url'    => $url,
            'name'   => get_form_name($this->getModel()),
        ];

        $this
            // add fields here
            ->add('name', 'text', [
                'label'  => 'Nom',

                'rules' => ['required', 'string',],
            ])

            ->add('online', 'select', [
                'choices' => ['1' => 'En ligne', '0' => 'Hors ligne'],
                'rules' => 'required',
                'label' => 'Visibilité',
                'attr'  => [
                    'class' => 'custom-select select2'
                ]
            ])

            ->add('type_id', 'entity', [
                "class" => config('administrable.extensions.ad.models.type'),
                // 'empty_value' => "Choisir l'auteur",
                "property" => 'label',
                "label" => 'Type',
                "rules" => 'required|exists:extensions_ad_types,id',
                "query_builder" => fn ($type) => $type->ad(),
                'attr' => [
                    'class' => 'custom-select select2'
                ]

            ])

            ->add('group_id', 'entity', [
                "class" => config('administrable.extensions.ad.models.group'),
                'empty_value' => "Choisir le groupe",
                "property" => 'name',
                "label" => 'Groupe de pubs',
                "rules" => 'required|exists:extensions_ad_groups,id',
                'attr' => [
                    'class' => 'custom-select select2'
                ]

            ])

            ->add('link', 'url', [
                'label'  => 'Lien',
                'rules' => ['nullable', 'string',],
                'attr' => [
                    'placeholder' => 'https://aswebagency.com'
                ]
            ])

            ->add('description', 'textarea', [
                'label'  => 'Description',

                'rules' => ['nullable', 'string',],
                'attr' => [
                    'data-tinymce',
                ]
            ])
            ->add('started_at', 'text', [
                'label' => 'Début de la publicité'
            ])
            ->add('ended_at', 'text', [
                'label' => 'Fin de la publicité'
            ]);
    }
}
