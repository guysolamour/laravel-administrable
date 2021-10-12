<?php

namespace Guysolamour\Administrable\Forms\Back\Extensions\Shop;

use Kris\LaravelFormBuilder\Form;

class DeliverForm extends Form
{
    public function buildForm()
    {
        if ( $this->getModel() && $this->getModel()->getKey() ) {
          $method = 'PUT';
          $url    = back_route('extensions.shop.deliver.update', $this->getModel() );
        } else {
          $method = 'POST';
          $url    = back_route('extensions.shop.deliver.store' );
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
            ->add('phone_number', 'text', [
                'label'  => 'Numéro de téléphone',
                'rules' => ['nullable',],
            ])
            ->add('email', 'text', [
                'label'  => 'Adresse courriel',
                'rules' => ['nullable',],
            ])
            ->add('default_deliver', 'select', [
                'choices' => ['1' => 'Oui', '0' => 'Non'],
                'rules' => 'required',
                'label' => 'Livreur par défaut'
            ])

            ->add('description', 'textarea', [
                'label'  => 'Description',
                'rules' => ['nullable',],
            ])
        ;

    }
}
