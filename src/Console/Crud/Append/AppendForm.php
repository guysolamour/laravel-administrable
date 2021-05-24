<?php

namespace Guysolamour\Administrable\Console\Crud\Append;

use Guysolamour\Administrable\Console\Crud\Generate\GenerateForm;


class AppendForm extends GenerateForm
{
    public function run()
    {
        $path   = $this->getPath();
        $fields = $this->getFormFields();
        $form   = $this->crud->filesystem->get($path);

        if ($this->crud->filesystem->exists($path)) {
            $this->writeForm($fields, $form);
        }

        return 'Fields added to form' . $path;
    }

}
