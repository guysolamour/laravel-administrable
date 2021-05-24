<?php

namespace Guysolamour\Administrable\Console\Crud\Rollback;

use Guysolamour\Administrable\Console\Crud\Generate\GenerateRoute;

class RollbackRoute extends GenerateRoute
{
    public function run()
    {
        $path = $this->getPath();

        $this->crud->filesystem->delete($path);

        return  'Route file removed at ' . $path;
    }
}
