<?php

namespace Guysolamour\Administrable\Forms\Back\Extensions\Shop;

use Kris\LaravelFormBuilder\Form;

class AttributeForm extends Form
{
    public function buildForm()
    {
        if ( $this->getModel() && $this->getModel()->getKey() ) {
          $method = 'PUT';
          $url    = back_route( 'extensions.shop.attribute.update', $this->getModel() );
        } else {
          $method = 'POST';
          $url    = back_route('extensions.shop.attribute.store' );
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
            ])
            ->add('attribute_terms', 'text', [
                'label'  => 'Valeur',
                'value'  => $this->getModel()->terms_list,
                'attr' => [
                    'placeholder' => 'Entrez les valeurs séparés par une virgule'
                ]
            ])

            ->add('description', 'textarea', [
                'label'  => 'Description',

                'rules' => ['nullable','string',],
            ])


        ;

    }
}
