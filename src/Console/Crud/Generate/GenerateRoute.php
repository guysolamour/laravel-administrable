<?php

namespace Guysolamour\Administrable\Console\Crud\Generate;

use Illuminate\Support\Str;


class GenerateRoute extends BaseGenerate
{

    public function run()
    {
        if (!$this->crud->generateFieldIsAllowedFor($this)) {
            return [false, 'Skip creating route'];
        }

        $actions = $this->crud->getActions();
        $actions = $this->appendStoreAndUpdateAction($actions);

        $routes_path = $this->getPath();

        $routes_stub = $this->crud->getCrudTemplatePath("/routes/{$this->getRoutesStubsFolderPrefix()}.stub");

        $complied = $this->crud->filesystem->compliedFile($routes_stub, true, $this->data_map);

        $this->writeRoute($routes_path, $complied);

        return [$complied , $routes_path];
    }

    protected function writeRoute(string $path, string $complied) :void
    {
       $this->crud->filesystem->writeFile($path, $complied);
    }

	protected function appendStoreAndUpdateAction(array $actions) :array
	{
        if (in_array('create', $actions)) {
            $actions[] = 'store';
        }

        if (in_array('edit', $actions)) {
            $actions[] = 'update';
        }

        return $actions;
	}


    public function getParsedName(?string $name = null): array
    {
        return array_merge(
            $this->crud->getParsedName($name),
            $this->getRoutesParsedName(),
            [
                '{{exceptActions}}' => $this->getExcept(),
            ]
        );
    }

    protected function getPath(): string
    {
        $routes_path = base_path('/routes/web/' . $this->data_map['{{backLowerNamespace}}'] . '/');

        if ($subfolder = $this->crud->getSubFolder()) {
            $routes_path .= Str::lower($subfolder) . '/';
        }

        return $routes_path . $this->data_map['{{singularSlug}}'] . '.php';
    }

    protected function getRoutesStubsFolderPrefix(): string
    {
        $prefix =  config('administrable.route_controller_callable_syntax', true);

        return $prefix ? 'new' :  'old';
    }

    protected function getExcept() :string
    {
        $actions = $this->crud->getUnusedActions();

        if (empty($actions)){
            return '';
        }

        // the array_map allows you to add the single quotes around the field
        return sprintf("->except([%s])", join(", ", array_map(fn ($item) => "'$item'", $actions)));
    }
}
