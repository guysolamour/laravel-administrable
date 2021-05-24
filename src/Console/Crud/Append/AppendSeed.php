<?php

namespace Guysolamour\Administrable\Console\Crud\Append;

use Guysolamour\Administrable\Console\Crud\Generate\GenerateSeed;


class AppendSeed extends GenerateSeed
{
    public function run()
    {
        $path   = $this->getPath();

        if (!$this->crud->filesystem->exists($path)){
            return;
        }

        $seeder = $this->generateSeederFields($this->crud->filesystem->get($path));

        $this->crud->filesystem->writeFile(
            $path,
            $seeder
        );

        return 'Seeder append' . $path;
    }


}
