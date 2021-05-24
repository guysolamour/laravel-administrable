<?php

namespace Guysolamour\Administrable\Console\Crud\Append;

use Guysolamour\Administrable\Console\Crud\Generate\GenerateViews;


class AppendViews extends GenerateViews
{
    public function run()
    {
        $this->appendFieldsToIndexView();
        $this->appendFieldsToShowView();
        $this->appendDatepickerAndDaterangeToFormView();

        return 'Fields added to form' . $this->getPath();
    }

    private function appendDatepickerAndDaterangeToFormView()
    {
        $path = $this->getPath('_form');

        if (!$this->crud->filesystem->exists($path)) {
            return;
        }

        $view = $this->addDatepickerAndDaterange($this->crud->filesystem->get($path));

        $this->crud->filesystem->writeFile($path, $view);
    }


    protected function appendFieldsToShowView(): void
    {
        $path = $this->getPath('show');

        if (!$this->crud->filesystem->exists($path)) {
            return;
        }

        $view = $this->crud->filesystem->get($path);

        $fields = $this->getShowViewFields();

        $search = '{{-- add fields here --}}';
        $view = str_replace($search, $fields . PHP_EOL . $search, $view);

        $this->crud->filesystem->writeFile($path, $view);
    }


    private function appendFieldsToIndexView()
    {
        if (!$this->crud->hasAction('index')){
            return;
        }

        $path = $this->getPath('index');

        if (!$this->crud->filesystem->exists($path)){
            return;
        }

        $view = $this->crud->filesystem->get($path);

        [$fields, $values] = $this->getFormatedIndexViewFieldsAndValues();


        $search = '{{-- add fields here --}}';
        $view = str_replace($search, $fields . PHP_EOL . $search, $view);

        $search = '{{-- add values here --}}';
        $view = str_replace($search, $values . PHP_EOL . $search, $view);

        if ($this->crud->isTheadminTheme()) {
            $values = str_replace('<td>', '<p>', $values);
            $values = str_replace('</td>', '</p>', $values);
            $search = '{{-- add quick values here --}}';
            $view = str_replace($search, $values . PHP_EOL . $search, $view);
        }

        $this->crud->filesystem->writeFile($path, $view);

    }

}
