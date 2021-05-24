<?php

namespace Guysolamour\Administrable\Console\Crud\Generate;

use Illuminate\Support\Str;


class GenerateController extends BaseGenerate
{
    public function run()
    {
        if (!$this->crud->generateFieldIsAllowedFor($this)) {
            return [false, 'Skip creating controller'];
        }

        $path = $this->getPath($this->data_map);

        $stub_path = $this->crud->getCrudTemplatePath('/controllers/Controller.stub');

        $complied = $this->loadAndRegisterControllerStub($stub_path);

        $this->writeController($path, $complied);

        return [$complied, $path];
    }


    protected function writeController(string $path, string $complied)
    {
        $this->crud->filesystem->writeFile($path, $complied);
    }


    protected function addAction(string $action, string $complied): string
    {
        $stub = $this->crud->getCrudTemplatePath("/controllers/actions/{$action}.blade.stub");
        $method = $this->crud->filesystem->compliedFile($stub, true, $this->data_map);

        if ('index' === $action && $this->crud->isTheadminTheme()) {
            $stub = $this->crud->getCrudTemplatePath("/controllers/actions/{$this->theme}/{$action}.blade.stub");
            $method = $this->crud->filesystem->compliedFile($stub, true, $this->data_map);
        }

        return str_replace("{{{$action}Method}}", $method, $complied);
    }


    protected function loadAndRegisterControllerStub(string $stub): string
    {
        $complied = $this->crud->filesystem->compliedFile($stub, true, $this->data_map);

        foreach ($this->crud->getActions() as $action) {
            $complied = $this->addAction($action, $complied,  $this->data_map);
        }

        return $this->removeUnusedActionTags($complied);
    }

    protected function removeUnusedActionTags(string $complied): string
    {
        $tags = ['{{indexMethod}}', '{{createMethod}}', '{{showMethod}}', '{{editMethod}}', '{{deleteMethod}}'];

        return str_replace($tags, '', $complied);
    }


    protected function getPath(): string
    {
        $form_path = app_path('/Http/Controllers/') . $this->data_map['{{backNamespace}}'] . '/';

        if ($subfolder = $this->crud->getSubFolder()) {
            $form_path .= Str::ucfirst($subfolder) . '/';
        }

        return $form_path . $this->data_map['{{singularClass}}'] . 'Controller.php';
    }

    protected function getViewsParsedName() :array
    {
        return [
            '{{indexView}}'       => $this->getView('index'),
            '{{showView}}'        => $this->getView('show'),
            '{{createView}}'      => $this->getView('create'),
            '{{storeView}}'       => $this->getView('store'),
            '{{editView}}'        => $this->getView('edit'),
            '{{updateView}}'      => $this->getView('update'),
            '{{deleteView}}'      => $this->getView('delete'),
        ];
    }

    protected function getView(string $action): string
    {

        $data_map = $this->crud->getParsedName();

        $view = $data_map['{{backLowerNamespace}}'] . '.';

        if ($subfolder = $this->crud->getSubFolder()) {
            $view .= $subfolder . '.';
        }

        $view .= $data_map['{{pluralSlug}}'] . '.' . $action;

        return $view;
    }

    public function getParsedName(?string $name = null): array
    {
        return array_merge(
            $this->crud->getParsedName($name),
            $this->getRoutesParsedName(),
                        $this->getViewsParsedName()
             );
    }
}
