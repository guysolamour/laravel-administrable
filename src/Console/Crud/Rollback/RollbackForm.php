<?php

namespace Guysolamour\Administrable\Console\Crud\Rollback;

use Guysolamour\Administrable\Console\Crud\Generate\GenerateForm;

class RollbackForm extends GenerateForm
{
    public function run()
    {
        $path = $this->getPath();

        $this->crud->filesystem->delete($path);

        return  'Form file removed at ' . $path;
    }

}
