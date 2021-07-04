<?php

namespace Guysolamour\Administrable\Forms\Back;

use Illuminate\Support\Str;
use Kris\LaravelFormBuilder\Form;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Route;

class PageForm extends Form
{
    public function buildForm()
    {
        if ($this->getModel() && $this->getModel()->getKey()) {
            $method = 'PUT';
            $url    = route('back.page.update', $this->getModel());
        } else {
            $method = 'POST';
            $url    = route('back.page.store');
        }

        $this->formOptions = [
            'method' => $method,
            'url'    => $url,
            'name'   => get_form_name($this->getModel()),
        ];

        $this
            // add fields here
            ->add('code', 'text', [
                'label' => Lang::get("administrable::messages.view.page.code"),
                'rules' => 'required',
            ])
            ->add('name', 'text', [
                'label' => Lang::get("administrable::messages.view.page.name"),
                'rules' => 'required',
            ])
            ->add('route', 'select', [
                'choices' => $this->getRoutesNames(),
                'rules' => 'nullable|route_exists',
                'label' => Lang::get("administrable::messages.view.page.uri"),
                'attr' => [
                    'class' => 'form-control select2'
                ]
            ]);
    }

    private function getRoutesNames(): array
    {
        $routes = collect(Route::getRoutes()->getRoutesByName());

        $routes = $routes->filter(function ($route, $key) {
            return Str::startsWith($key, Str::lower(config('administrable.front_namespace'))) ||  $key === 'home';
        })->keys();

        $routes = $routes->mapWithKeys(fn ($route) => [$route => $route])->all();

        return $routes;
    }
}
