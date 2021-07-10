<?php

namespace Guysolamour\Administrable\Forms\Back\Extensions\Blog;

use Kris\LaravelFormBuilder\Form;

class CategoryForm extends Form
{
    public function buildForm()
    {
        if ($this->getModel() && $this->getModel()->getKey()) {
            $method = 'PUT';
            $url    = back_route('extensions.blog.category.update', $this->getModel());
        } else {
            $method = 'POST';
            $url    = back_route('extensions.blog.category.store');
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
        ;
    }
}
