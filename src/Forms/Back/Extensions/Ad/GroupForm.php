<?php

namespace Guysolamour\Administrable\Forms\Back\Extensions\Ad;

use Kris\LaravelFormBuilder\Form;

class GroupForm extends Form
{
    public function buildForm()
    {
        if ($this->getModel() && $this->getModel()->getKey()) {
            $method = 'PUT';
            $url    = back_route('extensions.ads.group.update', $this->getModel());
        } else {
            $method = 'POST';
            $url    = back_route('extensions.ads.group.store');
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
            ->add('slider', 'select', [
                'choices' => ['1' => 'Oui', '0' => 'Non'],
                'rules' => 'required',
                'label' => 'Diaporama',
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
                "query_builder" => fn ($type) => $type->group(),
                'attr' => [
                    'class' => 'custom-select select2'
                ]

            ])
            ->add('visible_ads', 'select', [
                'choices' => $this->getVisiblePubsChoices(),
                'rules' => 'required',
                'label' => 'Pubs visible',
                'rules' => ['integer', 'min:0'],
                'attr'  => [
                    'class' => 'custom-select select2'
                ]
            ])

            ->add('width', 'number', [
                'label'  => 'Largeur',
                'rules' => ['nullable', 'integer', 'min:1'],
            ])

            ->add('height', 'number', [
                'label'  => 'Hauteur',
                'rules' => ['nullable', 'integer', 'min:1'],
            ])

            ->add('description', 'textarea', [
                'label'  => 'Description',

                'rules' => ['nullable', 'string',],
            ]);
    }

    private function getVisiblePubsChoices(): array
    {
        $choices = ['0' => 'Toutes'];

        for ($i = 1; $i < 11; $i++) {
            $choices[$i] = $i;
        }

        return $choices;
    }
}
