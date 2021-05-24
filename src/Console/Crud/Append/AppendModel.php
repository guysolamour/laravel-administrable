<?php

namespace Guysolamour\Administrable\Console\Crud\Append;

use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\Crud\Field;
use Guysolamour\Administrable\Console\Crud\Generate\GenerateModel;


class AppendModel extends GenerateModel
{
    public function run()
    {
        $path = $this->getModelPath();

        foreach ($this->crud->getFields() as $field) {
            $model_stub = $this->crud->filesystem->get($path);

            $model_stub = $this->addCastableField($model_stub, $field, $path);
            $model_stub = $this->importDaterangeTraitAndProperties($model_stub, $field, $path);
            $model_stub = $this->appendFieldToFillableProperty($model_stub, $field, $path);

            $model_stub = $this->loadSluggableTrait($model_stub);

            $this->crud->filesystem->writeFile( $path, $model_stub);
        }

        $this->addRelations($model_stub, $path);

        return 'Fields added to model' . $path;
    }

    private function checkIfFieldsContainsASluggableField() :bool
    {
       return  $this->crud->getFields()->filter(fn(Field $field) => $field->getName() === $this->crud->getSlug())->isNotEmpty();
    }

    protected function loadSluggableTrait(string $model): string
    {
        if (!$this->checkIfFieldsContainsASluggableField()) {
            return $model;
        }

        return parent::loadSluggableTrait($model);
    }



    private function appendFieldToFillableProperty(string $model_stub, Field $field): string
    {

        if ($this->crud->getGuarded()){
            return $model_stub;
        }

        // gerer le cas du fillable
        $search = 'public $fillable = [';

        if ($field->isDaterange()) {
            $model_stub = str_replace($search, $search . "'{$field->getDateRangeStartFieldName()}'," . "'{$field->getDateRangeEndFieldName()}',", $model_stub);
        } else {
            $model_stub = str_replace($search, $search . "'{$field->getName()}',", $model_stub);
        }

        // add slug field to the fillable properties
        if ($this->crud->getSlug()) {
            $model_stub = str_replace($search, $search . "'slug',", $model_stub);

        }

        return $model_stub;
    }

    public function checkIfCastsAttributeExistsOnModel(string $path): bool
    {
        return preg_match('#protected \$casts = \[#', $this->crud->filesystem->get($path));
    }

    private function importDaterangeTrait(string $model_stub, Field $field, string $path) :string
    {
        if ($this->checkIfDaterangeTraitHasBeenImported($path)) {
            return $model_stub;
        }

        // add trait
        $search = '// The attributes that are mass assignable.';
        $model_stub = str_replace($search, 'use DaterangeTrait;' . PHP_EOL . PHP_EOL . '    ' . $search, $model_stub);

        // add namespace on the top
        $search = "use {$this->data_map['{{namespace}}']}\Traits\ModelTrait;";
        $replace = "use {$this->data_map['{{namespace}}']}\Traits\DaterangeTrait;";
        return str_replace($search, $search . PHP_EOL .  $replace, $model_stub);

        // add attributes
    }

    private function getDatepickerOrDaterangeAttributeName(Field $field) :string
    {
        return  $field->isDatepicker() ? '$datepickers' : '$dateranges';
    }

    protected function importDaterangeTraitAndProperties(string $model_stub, Field $field, string $path): string
    {
        if ($field->isDatepicker() || $field->isDaterange()) {
            $model_stub = $this->importDaterangeTrait($model_stub, $field, $path);

            $model_stub = $this->addFieldInDaterangesOrDatepickersAttribute($model_stub, $field);

            $model_stub = $this->appendFieldInDaterangesOrDatepickersAttribute($model_stub, $field);
        }

        return $model_stub;
    }

    private function checkIfDaterangeOrDatepickerAttributeExists(string $model_stub, string $key) :bool
    {
        return Str::contains($model_stub, "protected {$key} = [");
    }

    private function addFieldInDaterangesOrDatepickersAttribute(string $model_stub, Field $field): string
    {
        $key = $this->getDatepickerOrDaterangeAttributeName($field);

        if ($this->checkIfDaterangeOrDatepickerAttributeExists($model_stub, $key)){
            return $model_stub;
        }


        $replace = <<<TEXT
                        // The date {$key} configuration array for this model.
                            protected $key = [

                        TEXT;

        $replace .= <<<TEXT

                            ];
                        TEXT;

        $search = "// add relation methods below";

        return  str_replace($search, $replace . PHP_EOL . PHP_EOL . '  ' . $search, $model_stub);
    }

    private function appendFieldInDaterangesOrDatepickersAttribute(string $model_stub, Field $field): string
    {
        $key = $this->getDatepickerOrDaterangeAttributeName($field);

        $search = "protected $key = [";

        $replace = "          '{$field->getName()}',";

        return  str_replace($search, $search . PHP_EOL . '  ' . $replace, $model_stub);
    }

    private function checkIfDaterangeTraitHasBeenImported(string $path): bool
    {
        return preg_match('#\\DaterangeTrait#', $this->crud->filesystem->get($path));
    }


    private function addCastableField(string $model_stub, Field $field, string $path): string
    {
        if ($cast = $field->getCast()) {
            if ($this->checkIfCastsAttributeExistsOnModel($path)) {
                $search = 'protected $casts = [';

                if ($field->isDaterange()) {
                    $replace = "'{$field->getDateRangeStartFieldName()}' => '{$cast}',
        '{$field->getDateRangeEndFieldName()}' => '{$cast}', ";
                } else {
                    $replace = "'{$field->getName()}' => '{$cast}',";
                }
                // gerer le cas du boolean plus tars
                $model_stub = str_replace($search, $search . PHP_EOL . "        {$replace}", $model_stub);
            } else {

                $search = '// add relation methods below';
                $template = <<<TEXT
                        // The attributes that should be cast to native types.
                            protected \$casts = [
                        TEXT;
                if ($field->isDatepicker()) {
                    $template .= "
            '{$field->getName()}' => '{$cast}',";
                } else if ($field->isDaterange()) {
                    $template .= "
            '{$field->getDateRangeStartFieldName()}' => '{$cast}',
            '{$field->getDateRangeEndFieldName()}' => '{$cast}',";
                }
                $template .= "
    ];";

                $model_stub = str_replace($search, $template . PHP_EOL . '    ' .  $search, $model_stub);
            }
        }
        return $model_stub;
    }


}
