<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Filesystem\Filesystem;


class CreateCrudController
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
        return (new CreateCrudController($model, $fields, $actions, $breadcrumb, $theme, $slug, $timestamps, $entity))
            ->loadController();
    }


    /**
     * @return string
     */
    private function loadController(): string
    {
        $data_map = $this->parseName($this->model);

        $controller_name = $data_map['{{singularClass}}'];

        $controllers_path = app_path('/Http/Controllers/' . $data_map['{{backNamespace}}']);

        [$stub, $path] = $this->loadStubAndPath($controllers_path, $controller_name);

        $complied = $this->loadAndRegisterControllerStub($stub, $data_map);

        $this->createDirectoryIfNotExists($path, false);

        $this->writeFile($complied, $path, false);

        return $controllers_path;
    }

    /**
     * @param $stub
     * @param $data_map
     * @return string
     */
    private function loadAndRegisterControllerStub($stub, $data_map): string
    {
        $complied = $this->compliedFile($stub, true, $data_map);

        $actions = ['index', 'show', 'create', 'edit', 'delete'];

        foreach ($actions as $action) {
            if ($this->hasAction($action)) {
                $complied = $this->addAction($action, $complied,  $data_map);
            }
        }

        // Retirer les tags des actions non utilisées
        $complied = $this->removeActionTags($complied);


        return $complied;
    }


    protected function removeActionTags(string $complied): string
    {
        $tags = ['{{indexMethod}}', '{{createMethod}}', '{{showMethod}}', '{{editMethod}}', '{{deleteMethod}}'];
        return str_replace($tags, '', $complied);
    }


    protected function addAction(string $action, string $complied,  array $data_map): string
    {
        $stub = $this->TPL_PATH . "/controllers/actions/{$action}.blade.stub";
        $method = $this->compliedFile($stub, true, $data_map);

        if ('index' === $action && $this->isTheadminTheme()) {
            $stub = $this->TPL_PATH . "/controllers/actions/{$this->theme}/{$action}.blade.stub";
            $method = $this->compliedFile($stub, true, $data_map);
        }

        return str_replace("{{{$action}Method}}", $method, $complied);
    }

    /**
     * @param $controllers_path
     * @param $controller_name
     * @return array
     */
    private function loadStubAndPath($controllers_path, $controller_name): array
    {
        $stub = $this->TPL_PATH . '/controllers/Controller.stub';


        $path = $controllers_path . "/{$controller_name}Controller.php";

        return [$stub, $path];
    }
}
