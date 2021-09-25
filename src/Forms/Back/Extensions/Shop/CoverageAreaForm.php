<?php

namespace Guysolamour\Administrable\Forms\Back\Extensions\Shop;

use Kris\LaravelFormBuilder\Form;

class CoverageAreaForm extends Form
{
    public function buildForm()
    {
        if ( $this->getModel() && $this->getModel()->getKey() ) {
          $method = 'PUT';
          $url    = back_route('extensions.shop.coveragearea.update', $this->getModel() );
        } else {
          $method = 'POST';
          $url    = back_route('extensions.shop.coveragearea.store' );
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

                'rules' => ['required',],
            ])
            ->add('description', 'text', [
                'label'  => 'Description',

                'rules' => ['nullable',],
            ])
        ;
    }
}
