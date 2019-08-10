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


            foreach ($migrations as $migration) {
                $stub = file_get_contents($migration['stub']);
                $complied = strtr($stub, $data_map);

                // generate the differents fields
                $fields = "\n";
                foreach ($this->fields as $field) {
                    $fields .= '            $table->'.$field[1].'('. "'$field[0]'".');'."\n";

                }

                // add slug field
                if(!is_null($this->slug)){
                    $fields .= '            $table->string('. "'slug'" .');';
                }

                // add timestamps
                if (!$this->timestamps) {
                    $fields .= "\n" . '            $table->timestamps();';
                }


                $slug_mw_bait = '$table->bigIncrements(\'id\');';
                // insert the namespace in the model
                $model = str_replace($slug_mw_bait, $slug_mw_bait . $fields, $complied);

                //dd($model);

              //  $model = str_replace($route_mw_bait, $route_mw_bait . $sluggable, $model);


//                $dir = dirname($migration['path']);
//                if (!is_dir($dir)) {
//                    mkdir($dir, 0755, true);
//                }

                file_put_contents($migration['path'], $model);
            }

            return database_path('migrations');

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }
}
