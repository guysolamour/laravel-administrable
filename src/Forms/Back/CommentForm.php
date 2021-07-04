<?php

namespace Guysolamour\Administrable\Forms\Back;

use Illuminate\Support\Facades\Lang;
use Kris\LaravelFormBuilder\Form;

class CommentForm extends Form
{
    public function buildForm()
    {

        if ($this->getModel() && $this->getModel()->getKey()) {
            $method = 'PUT';
            $url    = back_route('comment.update', $this->getModel());
        } else {
            $method = 'POST';
            $url    = back_route('comment.store');
        }

        $this->formOptions = [
            'method' => $method,
            'url'    => $url,
            'name'   => get_form_name($this->getModel()),
        ];


        if ($this->getModel()->commenter) {
            $form = $this
                ->add('commenter', 'text', [
                    'label' => Lang::get('administrable::messages.view.comment.name'),
                    'value' => $this->getModel()->commenter->name,
                    'attr'  => [
                        'disabled'
                    ]
                ]);
        }else {
            $form = $this
                ->add('guest_name', 'text', [
                    'label' => Lang::get('administrable::messages.view.comment.name'),
                    'rules' => 'required',
                ])
                ->add('guest_email', 'text', [
                    'label' => Lang::get('administrable::messages.view.comment.email'),
                    'rules' => 'required',
                ]);
        }
	    $form
            // add fields here
		    ->add('commentable','text', [
			      'label' => Lang::get('administrable::messages.view.comment.target'),
                  'value' => $this->getModel()->commentable->title,
                    'attr'  => [
                        'disabled'
                    ]
            ])

            ->add('approved', 'select', [
                'choices' => ['0' => 'Non', '1' => 'Oui'],
                'rules' => 'required',
                'label' => Lang::get('administrable::messages.view.comment.approved'),
                'attr'  => [
                    'class' => 'custom-select'
                ]
            ])

		    ->add('comment','textarea', [
			      'label' => Lang::get('administrable::messages.view.comment.name'),
			      'rules' => 'required',
                    'attr' => [
                        'data-required',
                        'data-tinymce'
                    ]
		      ])
        ;
    }
}
