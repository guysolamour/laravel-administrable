<?php

namespace Guysolamour\Administrable\Console\Crud\Rollback;

use Guysolamour\Administrable\Console\Crud\Field;
use Guysolamour\Administrable\Console\Crud\Generate\GenerateModel;

class RollbackModel extends GenerateModel
{
    public function run()
    {
        $path = $this->getModelPath();

        foreach ($this->crud->getFields() as $field) {

            if (!$field->isRelation()){
                continue;
            }

            $this->removeRelationMethod($field);
        }

        $this->crud->filesystem->delete($path);

        return 'Model file removed at ' . $path;
    }

    public function getParsedName(?string $name = null): array
    {
        return array_merge(
            $this->crud->getParsedName($name),
            $this->getRoutesParsedName(),
            [
                '{{sidebarViewModelWithSubfolder}}' => $this->sidebarViewModelWithSubfolder(),
            ]
        );
    }

    protected function sidebarViewModelWithSubfolder(): string
    {
        $model = '';

        if ($subfolder = $this->crud->getSubFolder()) {
            $model .= $subfolder . '.';
        }

        return $model . $this->crud->getParsedName()['{{singularSlug}}'];
    }

	private function removeRelationMethod(Field $field) :void
	{
        $related_model_path = $field->getRelationRelatedModelPath();

        if (!$this->crud->filesystem->exists($related_model_path)){
            return;
        }

        $start_key = '// ' . $this->data_map['{{sidebarViewModelWithSubfolder}}'] . ' relation';
        $end_key = '// end ' .  $this->data_map['{{sidebarViewModelWithSubfolder}}'] . ' relation';

        $complied = delete_all_between($start_key, $end_key, $this->crud->filesystem->get($related_model_path));

        $this->crud->filesystem->writeFile($related_model_path, $complied);

	}
}
