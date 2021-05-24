<?php

namespace Guysolamour\Administrable\Console\Crud\Rollback;

use Guysolamour\Administrable\Console\Crud\Generate\GenerateSeed;

class RollbackSeed extends GenerateSeed
{
    public function run()
    {
        $path = $this->getPath();

        $this->removeEntryInDatabaseSeederFile();

        $this->crud->filesystem->delete($path);

        return  'Seed file removed at ' . $path;
    }
    

    private function removeEntryInDatabaseSeederFile() :void
    {
        $database_seeder_path = database_path('seeders/DatabaseSeeder.php');
        $database_seeder = $this->crud->filesystem->get($database_seeder_path);

        $search = ' $this->call(' .  $this->data_map['{{pluralClass}}'] . 'TableSeeder::class' . ");";
        $database_seeder = str_replace($search, "", $database_seeder);

        $this->crud->filesystem->writeFile($database_seeder_path, $database_seeder);
    }

}
