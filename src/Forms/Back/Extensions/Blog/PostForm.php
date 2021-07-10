<?php

namespace Guysolamour\Administrable\Forms\Back\Extensions\Blog;

use Kris\LaravelFormBuilder\Form;

class PostForm extends Form
{
    public function buildForm()
    {

        if ($this->getModel() && $this->getModel()->getKey()) {
            $method = 'PUT';
            $url    = back_route('extensions.blog.post.update', $this->getModel());
        } else {
            $method = 'POST';
            $url    = back_route('extensions.blog.post.store');
        }

        $this->formOptions = [
            'method' => $method,
            'url'    => $url,
            'name'   => get_form_name($this->getModel()),
        ];

	    $this
        // add fields here

		    ->add('title','text', [
			      'label' => 'Titre',
			      'rules' => 'required',
            ])
            //  ->add('slug','text', [
            //   'label' => 'Url',
            //   // 'rules' => 'required',
            // ])
            // ->add('categories[]', 'entity', [
            //     "class" => config('administrable.extensions.blog.category.model'),
            //     "property" => 'name',
            //     "label" => 'Catégories',
            //     'attr' => [
            //         'multiple',
            //         'class' => 'custom-select select2'
            //     ]

            // ])
            // ->add('tags[]', 'entity', [
            //     "class" => config('administrable.extensions.blog.tag.model'),
            //     "property" => 'name',
            //     "label" => 'Etiquettes',
            //     'attr' => [
            //         'multiple',
            //         'class' => 'custom-select select2'
            //     ]

            // ])
            ->add('author_id', 'entity', [
                "class" => get_guard_model_class(),
                "property" => 'first_name',
                "label" => 'Auteur',
                "rules" => 'required|exists:admins,id',
                'attr' => [
                    'class' => 'custom-select select2'
                ]

            ])
            // ->add('online', 'select', [
            //     'choices' => ['0' => 'Non', '1' => 'Oui'],
            //     'rules' => 'required',
            //     'label' => 'En ligne',
            //     'attr'  => [
            //         'class' => 'custom-select select2'
            //     ]
            // ])
            ->add('allow_comment', 'select', [
                'choices' => ['1' => 'Oui', '0' => 'Non'],
                'label' => 'Autoriser les commentaires',
                'rules' => 'required',
                'attr'  => [
                    'class' => 'custom-select select2'
                ]
            ])
		    ->add('content','textarea', [
                'label' => 'Contenu',
                'rules' => 'required',
                'attr' => [
                    'data-required',
                    'data-tinymce'
                ]
		    ])
        ;
    }
}
