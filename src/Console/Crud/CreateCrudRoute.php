<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Filesystem\Filesystem;


class CreateCrudRoute
{

    use MakeCrudTrait;

    /**
     * @var string
     */
    private $model;
    /**
     * @var array
     */
    private $fields;
    /**
     * @var array
     */
    private $actions;
    /**
     * @var string
     */
    private $breadcrumb;
    /**
     * @var string
     */
    private $theme;
    /**
     * @var string
     */
    private $slug;
    /**
     * @var bool
     */
    private $timestamps;
    /**
     * @var bool
     */
    private $entity;

    /**
     * CreateCrudController constructor.
     * @param string $model
     * @param array $fields
     */
    public function __construct(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug, bool $timestamps, bool $entity)
    {

        $this->model       = $model;
        $this->fields      = $fields;
        $this->actions     = $actions;
        $this->slug        = $slug;
        $this->breadcrumb  = $breadcrumb;
        $this->theme       = $theme;
        $this->slug        = $slug;
        $this->timestamps  = $timestamps;
        $this->entity      = $entity;

        $this->filesystem   = new Filesystem;
    }


    /**
     * @param string $model
     * @param array $fields
     * @return string
     */
    public static function generate(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug, bool $timestamps, bool $entity)
    {
        return (new CreateCrudRoute($model, $fields, $actions, $breadcrumb, $theme, $slug, $timestamps, $entity))
            ->loadRoutes();
    }

    /**
     * @return string
     */
    protected function loadRoutes(): string
    {
        $actions = $this->actions;


        $unusedActions = array_diff($this->ACTIONS, $actions);

        if (in_array('create', $unusedActions)) {
             $unusedActions[] = 'store';
        }

        if (in_array('edit', $unusedActions)) {
             $unusedActions[] = 'update';
        }


        if (!empty($unusedActions)) {
            // the array_map allows you to add the single quotes around the field
            $excerpt = sprintf("->except(%s)", join(", ", array_map(fn ($item) => "'$item'", $unusedActions)));
        }

        $data_map = array_merge(
            $this->parseName($this->model),
            ['{{exceptActions}}' => !empty($excerpt) ? $excerpt : '']
        );


        $routes_path = base_path('/routes/web/' . $data_map['{{backLowerNamespace}}'] . "/{$data_map['{{singularSlug}}']}.php");
        $routes_stub = $this->TPL_PATH . "/routes/{$this->getRoutesStubsFolderPrefix()}.stub";

        $complied = $this->compliedFile($routes_stub, true, $data_map);

        $this->createDirectoryIfNotExists($routes_path, false);

        $this->writeFile($complied, $routes_path, false);


        return $routes_path;
    }
}
