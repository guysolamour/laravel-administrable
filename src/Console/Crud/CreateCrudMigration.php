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




}
