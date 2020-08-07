<?php

namespace Guysolamour\Administrable\Console\Crud;



use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class CreateCrudMigration
{

    use MakeCrudTrait;
    /**
     * @var string
     */
    private $model;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $actions;

    /**
     * @var string
     */
    private $breadcrumb;
    /**
     * @var string
     */
    private $theme;

    /**
     * @var null|string
     */
    private $slug;

    /**
     * @var bool
     */
    private $timestamps;


    /**
     * CreateCrudMigration constructor.
     *
     * @param string $model
     * @param array $fields
     * @param array $actions
     * @param string|null $breadcrumb
     * @param string $theme
     * @param string|null $slug
     * @param boolean $timestamps
     */
    public function __construct(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug, bool $timestamps, bool $entity, bool $seeder)
    {
        $this->model        = $model;
        $this->fields       = $fields;
        $this->actions      = $actions;
        $this->breadcrumb   = $breadcrumb;
        $this->theme        = $theme;
        $this->slug         = $slug;
        $this->timestamps   = $timestamps;
        $this->entity       = $entity;
        $this->seeder       = $seeder;

        $this->filesystem   = new Filesystem;
    }

    /**
     * @param string $name
     * @param array $fields
     * @param null|string $slug
     * @param bool $timestamps
     * @param bool $polymorphic
     * @return array|string
     */
    public static function generate(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug, bool $timestamps, bool $entity, bool $seeder)
    {
        return (new CreateCrudMigration($model, $fields, $actions, $breadcrumb, $theme, $slug, $timestamps, $entity, $seeder))
            ->loadMigrations();
    }

    /**
     * @return array|string
     */
    protected function loadMigrations()
    {

        $data_map = $this->parseName($this->model);


        $migration_stub = $this->TPL_PATH . '/migrations/provider.stub';
        $migration = $this->compliedFile($migration_stub, true, $data_map);
        $migration_path = $this->generateMigrationFields($migration, $data_map);


        if ($this->seeder) {
            $seeder_stub = $this->TPL_PATH . '/migrations/seed.stub';
            $seeder_path = database_path("/seeds/" . $data_map['{{pluralClass}}'] . 'TableSeeder.php');
            $seeder = $this->compliedFile($seeder_stub, true, $data_map);

            $seeder = $this->generateSeederFields($seeder);
            $this->registerSeederInDatabaseSeeder($data_map);

            $this->writeFile(
                $seeder,
                $seeder_path
            );
        }

        return [$migration_path, $seeder_path ?? ''];
    }


    protected function registerSeederInDatabaseSeeder(array $data_map)
    {

        $database_seeder_path = database_path('seeds/DatabaseSeeder.php');

        $search = "    {";

        foreach($this->fields as $field){

            if (!is_array($field)){
                continue;
            }

            if ($this->isRelationField(Arr::get($field, 'type'))){
                $related_model = Str::plural($this->getRelatedModelWithoutNamespace($field));
                $search = ' $this->call(' .  $related_model . 'TableSeeder::class' . ");";
            }
        }

        $database_seeder = $this->filesystem->get($database_seeder_path);
        $seeder = $data_map['{{pluralClass}}'] . 'TableSeeder::class';
        $this->replaceAndWriteFile(
            $database_seeder,
            $search,
            <<<TEXT
            $search
                     \$this->call($seeder);
            TEXT,

            $database_seeder_path
        );

        return $database_seeder_path;
    }


    protected function generateSeederFields(string $seeder): string
    {
        $seed_fields = "\n";

        foreach ($this->fields as $field) {
            // allow to generate the slug in the seed by putting the variable $ slug in front
            if ($field['type'] === "string") {
                $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->text(50),';
            }

            if ($field['type'] === 'image') {
                $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->imageUrl,';
            }

            if ($field['type'] === 'text') {
                $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->realText(150),';
            }

            if ($field['type'] === 'integer' || $field['type'] === 'mediumInteger'  || $field['type'] === 'bigInteger') {
                $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->randomNumber(),';
            }

            if ($field['type'] === 'boolean') {
                $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->randomElement([true,false]),';
            }

            if ($this->isRelationField($this->getNonRelationType($field))) {
                if ($this->isSimpleRelation($field)) {
                    $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->randomElement(' . $this->getRelatedModel($field) . '::pluck(\'id\')' . '),';
                }
            }
        }

        // compiled and write migration
        $search = 'create([';
        return str_replace($search, $search . $seed_fields, $seeder);
    }


    protected function generateMigrationFields(string $migration, array $data_map): string
    {
        $fields = "\n\n";

        foreach ($this->fields as $field) {
            if ($this->isRelationField($this->getNonRelationType($field))) {

                if ($this->isSimpleRelation($field)) {
                    $fields .= <<<TEXT
                                \$table->foreignId('{$this->getFieldName($field)}'){$this->getFieldAttributes($field)}->constrained('{$this->getModelTableName($this->getRelatedModel($field))}','{$this->getFieldReferences($field)}')->onDelete('{$this->getFieldOnDelete($field)}');
                    TEXT;
                    // $fields .= <<<TEXT
                    //            \$table->foreign('{$this->getFieldName($field)}')->references('{$this->getFieldReferences($field)}')->on('{$this->getModelTableName($this->getRelatedModel($field))}')->onDelete('{$this->getFieldOnDelete($field)}');

                    // TEXT;
                }
            } else if ($this->isPolymorphicField($field)) {
                $fields .= <<<TEXT
                           \$table->morphs('{$this->getMorphRelationableName($field)}');

                TEXT;
            } else {

                $fields .= <<<TEXT
                           \$table->{$this->getMigrationFieldType($field)}('{$this->getFieldName($field)}'{$this->getMigrationFieldLength($field)}){$this->getFieldAttributes($field)};

                TEXT;
            }
        }

        // add slug field and the linked field
        if ($this->slug) {
            $fields .= '           $table->string(' . "'slug'" . ')->unique();';
        }
        //

        //add timestamps
        if ($this->timestamps) {
            $fields .= "\n" . '           $table->timestamps();';
        }

        // Pivot tables must be created before migration due to foreign customers
        $this->createManyToManyRelationPivotTable();


        // compiled and write migration
        $search = '$table->id();';

        $signature = date('Y_m_d_His');
        $migration_path = database_path('migrations/' . $signature . '_create_' . $data_map['{{pluralSnake}}'] . '_table.php');

        $this->writeFile(
            str_replace($search, $search . $fields, $migration),
            $migration_path
        );

        return $migration_path;
    }

    protected function getModelForeignKey(array $field): string
    {
        $key = Arr::get($this->getRelationLocalForeignKey($field), 'foreign_key');

        if (!$key) {
            $key = $this->parseName($this->model)['{{singularSlug}}'] . '_id';
        }

        return $key;
    }

    protected function getRelatedModelForeignKey(array $field): string
    {
        $key = Arr::get($this->getRelationRelatedForeignKey($field), 'foreign_key');

        if (!$key) {
            $key = $this->parseRelationName($this->model, $this->getRelatedModel($field))['{{relatedSingularSlug}}'] . '_id';
        }

        return $key;
    }




    protected function createManyToManyRelationPivotTable()
    {

        foreach ($this->fields as $field) {
            if ($this->isSimpleManyToManyRelation($field)) {
                $data_map = array_merge(
                    $this->parseRelationName($this->model, $this->getRelatedModel($field)),
                    ['{{intermediateTableName}}' => $this->getRelationIntermediateTable($field, true)],
                    ['{{intermediateClassName}}' => $this->getIntermediateClassName($field)],
                    ['{{onDelete}}' => $this->getFieldOnDelete($field)],
                    ['{{modelForeignKey}}' => $this->getModelForeignKey($field)],
                    ['{{relatedForeignKey}}' => $this->getRelatedModelForeignKey($field)],
                );

                $pivot_migration_stub = $this->TPL_PATH . '/migrations/pivot.stub';

                $pivot_migration = $this->compliedFile($pivot_migration_stub, true, $data_map);

                // Plus 5 generates the pivot table because of foreign keys
                $signature  = date('Y_m_d_') . (date('His') + 5);
                $pivot_migration_path = database_path('migrations/' . $signature . '_' . Str::snake($this->getIntermediateClassName($field)) . '.php');

                $this->writeFile(
                    $pivot_migration,
                    $pivot_migration_path
                );
            }
        }
    }


    protected function getMigrationFieldLength(array $field): string
    {
        if ($length = Arr::get($field, 'length')) {
            return ", $length";
        }
        return '';
    }



    protected function getFieldAttributes(array $field): string
    {
        $constraints = $this->getFieldConstraints($field);

        $attr = '';

        if ($default = Arr::get($field, 'default')){
            if (is_bool($default)) {

                $attr .= sprintf("->default(%s)", $default ? 'true' : 'false');
            }else {
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


    protected function getModelTableName(string  $model): string
    {
        // we recover the name of the model without namespace
        $model = explode('\\', $model);

        return  strtolower(Str::plural(Str::studly(end($model))));
    }
}
