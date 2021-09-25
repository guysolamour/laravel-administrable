<?php

namespace Guysolamour\Administrable\Forms\Back\Extensions\Shop;

use Kris\LaravelFormBuilder\Form;

class CategoryForm extends Form
{
    public function buildForm()
    {
        if ( $this->getModel() && $this->getModel()->getKey() ) {
          $method = 'PUT';
          $url    = \back_route('extensions.shop.category.update', $this->getModel() );
        } else {
          $method = 'POST';
          $url    = back_route('extensions.shop.category.store' );
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
            ->add('description', 'textarea', [
                'label'  => 'Description',

                'rules' => ['nullable','string',],
            ])
        ;

    }
}
