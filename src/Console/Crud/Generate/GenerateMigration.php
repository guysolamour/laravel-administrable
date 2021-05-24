<?php

namespace Guysolamour\Administrable\Console\Crud\Generate;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\Crud\Field;



class GenerateMigration extends BaseGenerate
{
    public function run()
    {
        if (!$this->crud->generateFieldIsAllowedFor($this)) {
            return [false, 'Skip creating migration'];
        }

        $migration_stub = $this->crud->getCrudTemplatePath('/migrations/provider.stub');

        $migration = $this->crud->filesystem->compliedFile($migration_stub, true, $this->data_map);

        $migration_path = $this->generateFields($migration);

        return [$migration, $migration_path];
    }

    protected function getUpAndDownMethods($fields_to_create) :array
    {
        $fields = PHP_EOL . PHP_EOL;
        $drop_fields = PHP_EOL . PHP_EOL;

        foreach ($fields_to_create as $field) {
            /**
             * @var Field $field
             */

            if ($field->isPolymorphicRelation()){
                continue;
            }

            if ($field->isSimpleRelation()) {
                if ($field->isSimpleOneToOneRelation() || $field->isSimpleOneToManyRelation()){
                    $fields .= <<<TEXT
                                \$table->foreignId('{$field->getName()}'){$this->getFieldAttributes($field)}->constrained('{$field->getRelationRelatedModelTableName()}','{$field->getRelationReferences()}')->onDelete('{$field->getRelationOnDelete()}');

                    TEXT;
                    $drop_fields .= <<<TEXT
                                \$table->dropColumn('{$field->getName()}');

                    TEXT;
                }
            } else if ($field->isDaterange()) {
                $fields .= <<<TEXT
                               \$table->timestamp('{$field->getDaterangeStartFieldName()}'){$this->getFieldAttributes($field)};
                               \$table->timestamp('{$field->getDaterangeEndFieldName()}'){$this->getFieldAttributes($field)};

                    TEXT;
                $drop_fields .= <<<TEXT
                                \$table->dropColumn('{$field->getDaterangeStartFieldName()}');
                                \$table->dropColumn('{$field->getDaterangeEndFieldName()}');

                    TEXT;
            }
            else if ($field->isPolymorphic()){
                if ($field->getNullable()){
                    $fields .= <<<TEXT
                               \$table->nullableMorphs('{$field->getPolymorphicMorphName()}');

                    TEXT;
                }else {
                    $fields .= <<<TEXT
                               \$table->morphs('{$field->getPolymorphicMorphName()}');

                    TEXT;
                }
                $drop_fields .= <<<TEXT
                           \$table->dropMorphs('{$field->getPolymorphicMorphName()}');

                    TEXT;
            }
            else {
                $fields .= <<<TEXT
                               \$table->{$this->getMigrationFieldType($field)}('{$field->getName()}'{$this->getMigrationFieldLength($field)}){$this->getFieldAttributes($field)};

                    TEXT;
                $drop_fields .= <<<TEXT
                           \$table->dropColumn('{$field->getName()}');

                    TEXT;
            }
        }

        return [$fields, $drop_fields];
    }

    protected function addSlug(string $migration_up, ?string $file_name = null) :string
    {
        if ($this->crud->getSlug() && !$file_name) {
            $migration_up .= '           $table->string(' . "'slug'" . ')->unique();';
        }

        return $migration_up;
    }

    protected function addTimestamps(string $migration_up, ?string $file_name = null) :string
    {
        if ($this->crud->getTimestamps() && !$file_name) {
            $migration_up .= PHP_EOL . '           $table->timestamps();';
        }

        return $migration_up;
    }

    protected function getSearchAndReplace(string $migration_up, ?string $file_name = null) :array
    {
        // compiled and write migration
        if ($file_name) {
            $search  = '// add up field here';
            $replace = $migration_up;
        } else {
            $search  = '$table->id();';
            $replace = $search . $migration_up;
        }

        return [$search, $replace];
    }

    protected function getPath(?string $file_name = null) :?string
    {
        $signature = date('Y_m_d_His');

        if ($file_name) {
            $migration_path = database_path('migrations/' . $signature . '_' . $file_name . '.php');
        } else {
            $migration_path = database_path('migrations/' . $signature . '_create_' . $this->data_map['{{tableName}}'] . '_table.php');
        }

        return  $migration_path;
    }

    protected function writeMigration(string $migration, string $migration_up, $migration_down, ?string $file_name, bool $down) :string
    {
        [$search, $replace] = $this->getSearchAndReplace($migration_up, $file_name);

        $migration_path = $this->getPath($file_name);


        if ($down) {
            $migration = str_replace('// add down field here', $migration_down, $migration);
        }

        $this->crud->filesystem->writeFile(
            $migration_path,
            str_replace($search, $replace, $migration)
        );

        return $migration_path;
    }

    protected function generateFields(string $migration,  ?string $file_name = null, bool $down = false): string
    {
        $fields_to_create = $this->crud->getFields();

        [$migration_up, $migration_down] = $this->getUpAndDownMethods($fields_to_create);

        $migration_up = $this->addSlug($migration_up, $file_name);
        $migration_up = $this->addTimestamps($migration_up, $file_name);

        // Pivot tables must be created before migration due to foreign keys
        $this->createManyToManyRelationPivotTable($fields_to_create);

        $migration_path = $this->writeMigration($migration, $migration_up, $migration_down, $file_name ,$down);


        return $migration_path;
    }

    public function getParsedName(?string $name = null): array
    {
        return array_merge($this->crud->getParsedName($name), [
            '{{tableName}}' => $this->crud->getTableName(),
            '{{className}}' => Str::ucfirst(Str::camel($this->crud->getTableName())),
        ]);
    }

    protected function getFieldAttributes(Field $field): string
    {
        $constraints = $field->getConstraints();

        $attr = '';

        if ($default = $field->getDefault()) {
            if (is_bool($default)) {

                $attr .= sprintf("->default(%s)", $default ? 'true' : 'false');
            } else {
                $attr .= sprintf("->default('%s')", $default);
            }
        }


        if (empty($constraints)) {
            return $attr;
        }

        foreach ($constraints as $constraint) {
            if (is_array($constraint)) {
                if ($constraint['value'] === true) {
                    $value = 'true';
                } else if ($constraint['value'] === false) {
                    $value = 'false';
                } else if (is_string($constraint['value'])) {
                    $value = "'{$constraint['value']}'";
                } else {
                    $value = $constraint['value'];
                }
                $attr .= sprintf("->%s(%s)", $constraint['name'], $value);
            } else {
                $attr .=  "->$constraint()";
            }
        }


        return $attr;
    }

    protected function getIntermediateClassName(Field $field): string
    {
        $class_name = Str::singular(Str::studly($field->getRelationIntermediateTable(true)));

        return sprintf("Create%sPivotTable", $class_name);
    }

    protected function getPolymorphicIntermediateClassName(Field $field): string
    {
        $class_name = Str::ucfirst(Str::plural($field->getPolymorphicRelationMorphName()));

        return sprintf("Create%sPivotTable", $class_name);
    }

    protected function getModelForeignKey(Field $field): string
    {
        $key = Arr::get($field->getRelationLocalForeignKey(), 'foreign_key');

        if (!$key) {
            $key = $this->getParsedName()['{{singularSlug}}'] . '_id';
        }

        return $key;
    }

    protected function getRelatedModelForeignKey(Field $field): string
    {
        $key = Arr::get($field->getRelationRelatedForeignKey(), 'foreign_key');

        if (!$key) {
            $key = $field->parseRelationName()['{{relatedSingularSlug}}'] . '_id';
        }

        return $key;
    }



    /**
     * @param Field[] $fields_to_create
     */
    protected function createManyToManyRelationPivotTable($fields_to_create) :void
    {
        foreach ($fields_to_create as $field) {
            if ($field->isSimpleManyToManyRelation()) {
                $data_map = array_merge(
                    $field->parseRelationName(),
                    [
                    '{{modelTableName}}'        => $this->crud->getTableName(),
                    '{{intermediateTableName}}' => $field->getSimpleRelationIntermediateTable(),
                    '{{intermediateClassName}}' => $this->getIntermediateClassName($field),
                    '{{onDelete}}'              => $field->getRelationOnDelete(),
                    '{{modelForeignKey}}'       => $this->getModelForeignKey($field),
                    '{{relatedForeignKey}}'     => $this->getRelatedModelForeignKey($field),
                    '{{relatedTableName}}'      => $field->getRelationRelatedModelTableName(),
                    ],
                );

                $pivot_migration_stub = $this->crud->getCrudTemplatePath('/migrations/pivot/simple.stub');
                $pivot_migration = $this->crud->filesystem->compliedFile($pivot_migration_stub, true, $data_map);

                $this->writePivotTableMigration($field, $pivot_migration);
            }
            else if ($field->isPolymorphicManyToManyRelation()){
                $data_map = array_merge(
                    $field->parseRelationName(),
                    [
                        '{{intermediateTableName}}' => $field->getPolymorphicRelationIntermediateTable(),
                        '{{intermediateTableName}}' => Str::plural($field->getPolymorphicRelationMorphName()),
                        '{{intermediateClassName}}' => $this->getPolymorphicIntermediateClassName($field),
                        '{{polymorphicMorphName}}' => $field->getPolymorphicRelationMorphName(),
                        '{{relatedPolymorphicId}}' => Str::lower($field->getRelationRelatedModelWithoutNamespace()) . '_id',
                    ],
                );

                $pivot_migration_stub = $this->crud->getCrudTemplatePath('/migrations/pivot/polymorphic.stub');
                $pivot_migration = $this->crud->filesystem->compliedFile($pivot_migration_stub, true, $data_map);

                $this->writePivotTableMigration($field, $pivot_migration);
            }
        }
    }

    protected function writePivotTableMigration(Field $field, $pivot_migration) :void
    {
        $signature  = date('Y_m_d_') . (date('His') + 5);
        $pivot_migration_path = database_path('migrations/' . $signature . '_' . Str::snake($this->getIntermediateClassName($field)) . '.php');

        $this->crud->filesystem->writeFile(
            $pivot_migration_path,
            $pivot_migration,
        );
    }

    protected function getMigrationFieldType(Field $field): ?string
    {
        $type = $field->getType();
        // if the value is an array it is that we have a relation type field
        // no need to go further
        if ($field->isRelation()){
            return '';
        }

        if ($type === 'image') {
            return 'string';
        }

        if ($type === 'datetime') {
            return 'timestamp';
        }

        return Str::lower($type);
    }

    protected function getMigrationFieldLength(Field $field): string
    {
        if (!$field->isText()) {
            return '';
        }

        if ($length = $field->getLength()) {
            return ", $length";
        }
        return '';
    }
}
