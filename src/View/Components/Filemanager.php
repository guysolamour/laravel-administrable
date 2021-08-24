<?php

namespace Guysolamour\Administrable\View\Components;

use Illuminate\Support\Str;
use Illuminate\View\Component;
use Spatie\MediaLibrary\HasMedia;

class Filemanager extends Component
{
    public string $name;
    /**
     * @var \Spatie\MediaLibrary\HasMedia|\Illuminate\Database\Eloquent\Model
     */
    public $model;

    public string $model_name;

    public string $target;

    public string $collection;

    public string $routeprefix;

    public string $type;

    public array $config;


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(HasMedia $model, string $target, string $collection, string $type)
    {
        $this->name        = $this->getComponentName();
        $this->model       = $model;
        $this->model_name  = get_class($model);
        $this->target      = $target;
        $this->collection  = $collection;
        $this->type        = $type;
        $this->routeprefix = config('administrable.auth_prefix_path');
        $this->config      = config('administrable.media');
    }

    private function getComponentName() :string
    {
        return Str::afterLast(Str::lower(__CLASS__ . Str::lower(Str::random(5))), '\\');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('administrable::filemanager.filemanager');
    }
}
