<?php

namespace Guysolamour\Administrable\Console\Crud\Rollback;

use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\Crud\Field;
use Guysolamour\Administrable\Console\Crud\Generate\GenerateMigration;

class RollbackMigration extends GenerateMigration
{
    public function run()
    {
        $path = $this->getPath();

        foreach ($this->crud->getFields() as $field) {

            if (!$field->isSimpleManyToManyRelation() || $field->isPolymorphicManyToManyRelation()) {
                continue;
            }

            $this->removeRelationMigration($field);

        }

        $this->removeOtherRelatedMigrations();

        $this->crud->filesystem->delete($path);

        return  'Migration file removed at ' . $path;
    }

    private function removeOtherRelatedMigrations() :void
    {
        $paths = $this->getFilesPaths()->filter(function ($file)  {
            return Str::endsWith($file, "to_{$this->data_map['{{tableName}}']}_table.php");
        });

        if ($paths->isEmpty()){
            return;
        }

        $this->crud->filesystem->delete($paths->toArray());
    }

    protected function getPath(?string $file_name = null): ?string
    {
        $name = '_create_' . $this->data_map['{{tableName}}'] . '_table.php';

        $paths = $this->getFilesPaths()->filter(fn ($file) => Str::contains(Str::lower($file), $name));

        return $paths->first();
    }

    private function getRelationName(Field $field) :?string
    {
        if ($field->isPolymorphicManyToManyRelation()){
            return $field->getPolymorphicRelationIntermediateTable();
        }

        return $field->getSimpleRelationIntermediateTable();
    }

    private function getFilesPaths() :\Illuminate\Support\Collection
    {
        $migrations_path =  database_path('migrations');

        $files = collect($this->crud->filesystem->allFiles($migrations_path));

        $paths = $files->map(fn ($file) => $file->getPathname());

        return $paths;
    }

    private function removeRelationMigration(Field $field) :void
    {
        $name = $this->getRelationName($field);

        $paths = $this->getFilesPaths()->filter(fn ($file) => Str::contains(Str::lower($file), $name));

        if ($paths->isEmpty()){
            return;
        }

        $this->crud->filesystem->delete($paths->toArray());
    }

}
