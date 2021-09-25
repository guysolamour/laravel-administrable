<?php

namespace Guysolamour\Administrable\Forms\Back\Extensions\Shop;

use Kris\LaravelFormBuilder\Form;

class ProductForm extends Form
{
    public function buildForm()
    {
        if ( $this->getModel() && $this->getModel()->getKey() ) {
          $method = 'PUT';
          $url    = back_route('extensions.shop.product.update', $this->getModel() );
        } else {
          $method = 'POST';
          $url    = back_route('extensions.shop.product.store' );
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

                'rules' => ['required:string',],
            ])
            ->add('description', 'textarea', [
                'label'  => 'Description du produit',

                'rules' => ['required','string',],
                'attr'  => ['data-tinymce']
            ])
            ->add('short_description', 'textarea', [
                'label'  => 'Description courte du produit',

                'rules' => ['nullable','string',],
            'attr'  => ['data-tinymce']
            ])
            ->add('price', 'number', [
                'label'  => 'Prix',

                'rules' => ['required','integer',],
            ])
            ->add('promotion_price', 'number', [
                'label'  => 'Promotion_price',

                'rules' => ['nullable','integer',],
            ])
            ->add('stock_management', 'select', [
                'label'  => 'Stock_management',

                'rules' => ['nullable',],
            ])
            ->add('stock', 'number', [
                'label'  => 'Stock',

                'rules' => ['nullable','integer',],
            ])
            ->add('safety_stock', 'number', [
                'label'  => 'Safety_stock',

                'rules' => ['nullable','integer',],
            ])
            ->add('has_review', 'select', [
                'label'  => 'Has_review',

                'rules' => ['required',],
            ])
            ->add('online', 'select', [
                'label'  => 'Online',

                'rules' => ['required',],
            ])
            ->add('show_attributes', 'select', [
                'label'  => 'Show_attributes',

                'rules' => ['required',],
            ])
            ->add('complementary_products', 'text', [
                'label'  => 'Complementary_products',

                'rules' => ['nullable','string',],
            ])
        ;

    }
}
