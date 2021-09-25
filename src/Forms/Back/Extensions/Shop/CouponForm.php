<?php

namespace Guysolamour\Administrable\Forms\Back\Extensions\Shop;

use Kris\LaravelFormBuilder\Form;

class CouponForm extends Form
{
    public function buildForm()
    {
        if ( $this->getModel() && $this->getModel()->getKey() ) {
          $method = 'PUT';
          $url    = back_route('extensions.shop.coupon.update', $this->getModel() );
        } else {
          $method = 'POST';
          $url    = back_route('extensions.shop.coupon.store' );
        }

        $this->formOptions = [
          'method' => $method,
          'url'    => $url,
          'name'   => get_form_name($this->getModel()),
        ];

        $this
            // add fields here
            ->add('code', 'text', [
                'label'  => 'Code',

                'rules' => ['required',],
            ])
            ->add('description', 'textarea', [
                'label'  => 'Description',

                'rules' => ['nullable',],
                'attr' => ['rows' => 5]
            ])
            ->add('remise_type', 'select', [
                'choices' => $this->getRemiseTypes(),
                'rules' => 'required',
                'label' => 'Type de remise',
                'attr'  => [
                    'class' => 'custom-select select2'
                ]
            ])

            ->add('value', 'number', [
                'label'  => 'Valeur du code promo',
                'rules' => ['required',],
            ])
            ->add('start_expire_dates', 'text', [
                'label'  => 'Date de début & d\'expiration du code',
                'rules' => ['required',],
            ])
            ->add('min_expense', 'number', [
                'label'  => 'Dépense minimale',

                'rules' => ['nullable',],
            ])
            ->add('max_expense', 'number', [
                'label'  => 'Dépense maximum',

                'rules' => ['nullable',],
            ])
            ->add('use_once', 'select', [
                'choices' => ['0' => 'Non', '1' => 'Oui'],
                'rules' => 'required',
                'label' => 'Utilisation individuelle uniquement'
            ])
            ->add('exclude_promo_products', 'select', [
                'choices' => ['0' => 'Non', '1' => 'Oui'],
                'rules' => 'required',
                'label' => 'Exclure les articles en promo'
            ])

            ->add('products', 'entity', [
                "class" => config('administrable.extensions.shop.models.product'),
                "property" => 'name',
                "label" => 'Produits',
                'multiple' => true,
                'selected'  => $this->getModel()->products,
                "rules" => 'nullable|exists:shop_products,id',
                // 'empty_value' => 'Selectionner les produits',
                'attr' => [
                    'class' => 'form-control select2',
                ]

            ])

            ->add('exclude_products', 'entity', [
                "class" => config('administrable.extensions.shop.models.product'),
                "property" => 'name',
                "label" => 'Exclure des produits',
                'selected'  => $this->getModel()->exclude_products,
                "rules" => 'nullable|exists:shop_products,id',
                // 'empty_value' => 'Selectionner les produits',
                'attr' => [
                    'class' => 'form-control select2',
                    'multiple' => true,
                ]

            ])
            ->add('categories', 'entity', [
                "class" => config('administrable.extensions.shop.models.category'),
                "property" => 'name',
                "label" => 'Catégories',
                'selected'  => $this->getModel()->categories,
                "rules" => 'nullable|exists:shop_categories,id',
                // 'empty_value' => 'Selectionner les catégories',
                'attr' => [
                    'class' => 'form-control select2',
                    'multiple' => true,
                ]
            ])
            ->add('exclude_categories', 'entity', [
                "class" => config('administrable.extensions.shop.models.category'),
                "property" => 'name',
                "label" => 'Exclure les catégories',
                "rules" => 'nullable|exists:shop_categories,id',
                'selected'  => $this->getModel()->exclude_categories,
                // 'empty_value' => 'Selectionner les catégories',
                'attr' => [
                    'class' => 'form-control select2',
                    'multiple' => true,
                ]
            ])

            ->add('used_time_limit', 'number', [
                'label'  => 'Limite d’utilisation par code',

                'rules' => ['nullable',],
            ])
            ->add('used_by_user_limit', 'number', [
                'label'  => 'Limite d’utilisation par utilisateur',

                'rules' => ['nullable',],
            ])
        ;

    }



    private function getRemiseTypes() :array
    {
        $choices = [];

        foreach (config('administrable.extensions.shop.models.coupon')::REMISE_TYPES as $value) {
            $choices[$value['name']] = ucfirst($value['label']);
        }

        return $choices;
    }
}
