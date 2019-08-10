<?php

namespace Guysolamour\Admin\Console;


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

    public static function generate(string $name, array $fields, ?string $slug = null, bool $timestamps = false) :string
    {
       return (new CreateCrudMigration($name,$fields,$slug,$timestamps))
            ->loadMigrations();
    }

    protected function loadMigrations()
    {
        try {

            $data_map = $this->parseName($this->name);




            $signature = date('Y_m_d_His');


            $migrations = array(
                [
                    'stub' => $this->TPL_PATH . '/migrations/provider.stub',
                    'path' => database_path('migrations/' . $signature . '_create_' . $data_map['{{pluralSnake}}'] . '_table.php'),
                ]
            );

            $seed_stub = file_get_contents($this->TPL_PATH.'/migrations/seed.stub');
            $seeder = strtr($seed_stub, $data_map);

            foreach ($migrations as $migration) {
                $stub = file_get_contents($migration['stub']);
                $complied = strtr($stub, $data_map);

                // generate the differents fields



                $fields = "\n";
                $seed_fields = "\n";
                foreach ($this->fields as $field) {
                    $fields .= '            $table->' . $field[1] . '(' . "'$field[0]'" . ');' . "\n";
                    if ($field[1] === 'string'){

                        $seed_fields.=  "\n" . "                '$field[0]'  => ".'$faker->text(),';
                    }

                    if ($field[1] === 'text'){

                        $seed_fields.=  "\n" . "                '$field[0]'  => ".'$faker->realText(150),';
                    }

                    if ($field[1] === 'integer'){

                        $seed_fields.=  "\n" . "                '$field[0]'  => ".'mt_rand(0,100),';
                    }

                    if ($field[1] === 'boolean'){

                        $seed_fields.=  "\n" . "                '$field[0]'  => ".'$faker->randomElement([true,false]),';
                    }
                }
                // add slug field
                if (!is_null($this->slug)) {
                    $fields .= '            $table->string(' . "'slug'" . ');';
                    $seed_fields.=  "\n" . "                'slug'  => ".'$faker->slug,';

                }


                // add timestamps
                if (!$this->timestamps) {
                    $fields .= "\n" . '            $table->timestamps();';
                }

                // migration replace
                $slug_mw_bait = '$table->bigIncrements(\'id\');';
                $model = str_replace($slug_mw_bait, $slug_mw_bait . $fields, $complied);
                file_put_contents($migration['path'], $model);

                // seeder replace
                $seed_mw_bait = 'create([';



                $seed = str_replace($seed_mw_bait, $seed_mw_bait . $seed_fields, $seeder);
                $seed_path = database_path('/seeds/'.$data_map['{{pluralClass}}'] . 'TableSeeder.php');

                file_put_contents($seed_path, $seed);
                $this->registerSeed();
                //dd($seed);

            }

            return database_path('migrations');

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    protected function registerSeed()
    {
        try {

            $data_map = $this->parseName($this->name);

            $database_seeder_path = database_path('seeds/DatabaseSeeder.php');

            $database_seeder = file_get_contents($database_seeder_path);

            /********** seed **********/

//            $route_mw = file_get_contents($stub_path . '/seeds/DatabaseSeeder.stub');
//
//
//            $route_mw = strtr($route_mw, $data_map);


//            $route_mw_bait = '// $this->call(UsersTableSeeder::class);'."\n";
            $route_mw_bait = "call([";
            $route_mw = "\n            " . $data_map['{{pluralClass}}'] . 'TableSeeder::class,';


            $database_seeder = str_replace($route_mw_bait, $route_mw_bait . $route_mw, $database_seeder);

            // Overwrite config file
            file_put_contents($database_seeder_path, $database_seeder);

            return $database_seeder_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }
}
