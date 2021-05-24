<?php

namespace Guysolamour\Administrable\Console\Crud\Rollback;

use Guysolamour\Administrable\Console\Crud\Generate\GenerateController;

class RollbackController extends GenerateController
{
    public function run()
    {
        $path = $this->getPath();

        $this->crud->filesystem->delete($path);

        return  'Controller file removed at ' . $path;
    }
}
