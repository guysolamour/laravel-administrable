<?php

namespace Guysolamour\Administrable\Forms\Back\Extensions\Shop;

use Kris\LaravelFormBuilder\Form;

class ReviewForm extends Form
{
    public function buildForm()
    {
        if ( $this->getModel() && $this->getModel()->getKey() ) {
          $method = 'PUT';
          $url    = back_route( 'extensions.shop.review.update', $this->getModel() );
        } else {
          $method = 'POST';
          $url    = back_route( 'extensions.shop.review.store' );
        }

        $this->formOptions = [
          'method' => $method,
          'url'    => $url,
          'name'   => get_form_name($this->getModel()),
        ];

        $form = $this
            // add fields here
            ->add('name', 'text', [
                'label'  => 'Nom',

                'rules' => ['nullable','string',],
            ])
            ->add('email', 'text', [
                'label'  => 'Adresse courriel',

                'rules' => ['nullable','email',],
            ])
            ->add('phone_number', 'text', [
                'label'  => 'Numéro de téléphone',

                'rules' => ['nullable','string',],
            ])
            ->add('product_id', 'entity', [
                'class'         => config('administrable.extensions.shop.models.product'),
                'empty_value'   => 'sélectionnez le produit',
                'property'      => 'name',
                'label'         => 'Produit',
                'rules'         => ['required', 'exists:shop_products,id',],
            ]);

            if (shop_settings('required_note')){
               $form->add('note', 'select', [
                    'choices' => ['1' => '1 étoile', '2' => '2 étoiles', '3' => '3 étoiles', '4' => '4 étoiles', '5' => '5 étoiles'],
                    'rules' => 'nullable|in:1,2,3,4,5',
                    'empty_value' => 'Choisir une note',
                    'label' => 'Note',
                    'attr' => [
                        'class' => 'form-control'
                    ]
                ]);
            }

            $form->add('content', 'textarea', [
                'label'  => 'Contenu',

                'rules' => ['required', 'min:20'],
            ])
            ->add('approved', 'select',[
                'choices' => ['1' => 'Oui', '0' => 'Non'],
                'rules' => 'required',
                'label' => 'Approuvé'
            ])
        ;

    }
}
