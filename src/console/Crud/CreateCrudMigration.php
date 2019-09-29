<?php

namespace Guysolamour\Administrable\Console\Crud;


class CreateCrudMigration
{

    use MakeCrudTrait;
    /**
     * @var string
     */
    private $name;
    /**
     * @var array
     */
    private $fields;
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
     * @param string $name
     * @param array $fields
     * @param null|string $slug
     * @param bool $timestamps
     */
    public function __construct(string $name, array $fields, ?string $slug = null, bool $timestamps = false)
    {
        $this->name = $name;
        $this->fields = array_chunk($fields,3);
        $this->slug = $slug;
        $this->timestamps = $timestamps;
    }

    /**
     * @param string $name
     * @param array $fields
     * @param null|string $slug
     * @param bool $timestamps
     * @return array|string
     */
    public static function generate(string $name, array $fields, ?string $slug = null, bool $timestamps = false)
    {
       return (new CreateCrudMigration($name,$fields,$slug,$timestamps))
            ->loadMigrations();
    }

    /**
     * @return array|string
     */
    protected function loadMigrations()
    {
        try {

            $data_map = $this->parseName($this->name);

            $signature = date('Y_m_d_His');

            $migrations = [
                [
                    'stub' => $this->TPL_PATH . '/migrations/provider.stub',
                    'path' => database_path('migrations/' . $signature . '_create_' . $data_map['{{pluralSnake}}'] . '_table.php'),
                ]
            ];

            $seeder = $this->loadAndResgisterSeed($data_map);

            foreach ($migrations as $migration) {
                $complied = $this->loadAndRegisterMigration($migration, $data_map);

                // generate the differents fields
                [$fields, $seed_fields] = $this->generateFields();





                // seeder replace
                [$seed_result,$seed_file] = $this->createMigration($seed_fields, $seeder, $data_map);

                // create migration if the seed was generate previously
                if($seed_result){
                    $migration_result = $this->registerMigrationFields($fields, $complied, $migration);
                    $this->registerSeed();

                }


                return [isset($migration_result),$migration['path'],$seed_result,$seed_file];

            }


        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * @return string
     */
    protected function registerSeed() :string
    {
        try {

            $data_map = $this->parseName($this->name);

            $database_seeder_path = database_path('seeds/DatabaseSeeder.php');

            $database_seeder = file_get_contents($database_seeder_path);
            $route_mw_bait = "    {";
            $replace = '$this->call(' .  $data_map['{{pluralClass}}'] . 'TableSeeder::class'. ");";
            $route_mw = "\n        " . $replace;


            $database_seeder = str_replace($route_mw_bait, $route_mw_bait . $route_mw, $database_seeder);

            // Overwrite config file
            file_put_contents($database_seeder_path, $database_seeder);

            return $database_seeder_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * @param $data_map
     * @return string
     */
    private function loadAndResgisterSeed($data_map): string
    {
        $seed_stub = file_get_contents($this->TPL_PATH . '/migrations/seed.stub');
        $seeder = strtr($seed_stub, $data_map);
        return $seeder;
    }

    /**
     * @param $migration
     * @param $data_map
     * @return string
     */
    protected function loadAndRegisterMigration($migration, $data_map): string
    {
        $stub = file_get_contents($migration['stub']);
        $complied = strtr($stub, $data_map);
        return $complied;
    }

    /**
     * @return array
     */
    protected function generateFields(): array
    {
        $fields = "\n";
        $seed_fields = "\n";
        foreach ($this->fields as $field) {
            $fields .= '            $table->' . $this->getFieldType($field[1]) . '(' . "'$field[0]'" . ');' . "\n";


            if ($field[1] === 'string') {

                $seed_fields .= "\n" . "                '$field[0]'  => " . '$faker->text(),';
            }

            if ($field[1] === 'image') {

                $seed_fields .= "\n" . "                '$field[0]'  => " . '$faker->imageUrl,';
            }

            if ($field[1] === 'text') {

                $seed_fields .= "\n" . "                '$field[0]'  => " . '$faker->realText(150),';
            }

            if ($field[1] === 'integer') {

                $seed_fields .= "\n" . "                '$field[0]'  => " . 'mt_rand(0,100),';
            }

            if ($field[1] === 'boolean') {

                $seed_fields .= "\n" . "                '$field[0]'  => " . '$faker->randomElement([true,false]),';
            }
        }
        // add slug field and the linked field
        if (!is_null($this->slug)) {
            $fields .= '            $table->string(' . "'{$this->slug}'" . ');' . "\n";
            $fields .= '            $table->string(' . "'slug'" . ');';
            $seed_fields .= "\n" . "                '{$this->slug}'  => " . '$faker->realText(50),';
            $seed_fields .= "\n" . "                'slug'  => " . '$faker->slug,';

        }
        // add timestamps
        if (!$this->timestamps) {
            $fields .= "\n" . '            $table->timestamps();';
        }
        return [$fields, $seed_fields];
    }

    /**
     * @param $fields
     * @param $complied
     * @param $migration
     */
    protected function registerMigrationFields($fields, $complied, $migration): bool
    {
        $slug_mw_bait = '$table->bigIncrements(\'id\');';
        $model = str_replace($slug_mw_bait, $slug_mw_bait . $fields, $complied);
        return $this->writeFile($migration['path'], $model);
    }

    /**
     * @param $seed_mw_bait
     * @param $seed_fields
     * @param $seeder
     * @param $data_map
     * @return array
     */
    protected function registerEntryInDatabaseSeeder($seed_mw_bait, $seed_fields, $seeder, $data_map): array
    {
        $seed = str_replace($seed_mw_bait, $seed_mw_bait . $seed_fields, $seeder);
        $seed_file = $data_map['{{pluralClass}}'] . 'TableSeeder.php';
        $seed_path = database_path('/seeds/' . $seed_file);
        return array($seed, $seed_file, $seed_path);
    }

    /**
     * @param $seed_fields
     * @param $seeder
     * @param $data_map
     * @return mixed
     */
    protected function createMigration($seed_fields, $seeder, $data_map)
    {
        $seed_mw_bait = 'create([';

        list($seed, $seed_file, $seed_path) = $this->registerEntryInDatabaseSeeder($seed_mw_bait, $seed_fields, $seeder, $data_map);

        $result = $this->writeFile($seed_path,$seed);
        return [$result,$seed_file];
    }


}
