<?php

namespace Guysolamour\Admin\Console;


use Illuminate\Console\Command;
use Illuminate\Container\Container;

class MakeCrudCommand extends Command
{

    protected $model = '';
    protected $fields = [];
    protected $timestamps;

    protected const TYPES = [
        'string','text','boolean','date','datetime','decimal','float','enum','double','integer',
        'ipAdress','longText','mediumText','mediumInterger',
    ];

    protected  const TPL_PATH = __DIR__. '/../templates/crud';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:make:crud 
                            {model : Model name.}
                             {--s|slug= : The field to slugify}                            
                             {--t|timestamps : Determine if the model is timestamped}                            
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create, model, migration and all views';


    public function handle()
    {
        $this->timestamps = $this->option('timestamps');

       // dd($this->option('slug'));
        $this->model = $this->argument('model');

        $fields = $this->getFields();

        $this->fields = array_chunk($fields, 3);



        //dd($this->fields);
        // Create the model
        $this->createModel($this->model);

    }

    private function createModel(string $name)
    {
        try {
            $stub = file_get_contents(self::TPL_PATH . '/models/model.stub');
            $data_map = $this->parseName($name);

            $model_path = app_path('Models/'.$data_map['{{singularClass}}'].'.php');

            $model = strtr($stub, $data_map);

            if (!is_null($this->option('slug'))) {
                // the namespace
                $sluggable_trait = '    use \Cviebrock\EloquentSluggable\Sluggable;';
                $slug_mw_bait = "{\n";
                // insert the namespace in the model
                $model = str_replace($slug_mw_bait, $slug_mw_bait . $sluggable_trait, $model);

                // sluggable stub
                $sluggable_stub = file_get_contents(self::TPL_PATH . '/models/sluggable.stub');
                // replace the slug field vars
                $sluggable = strtr($sluggable_stub, $data_map);

                // insert in the model
                $route_mw_bait = 'public $timestamps = ' . $this->getTimetsamps() . ';'. "\n\n\n";

                $model = str_replace($route_mw_bait, $route_mw_bait . $sluggable, $model);

            }

            $dir = dirname($model_path);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            // add model and base model

            if (!file_exists(app_path('Models/BaseModel.php'))){

                $base_model_stub = file_get_contents(self::TPL_PATH . '/models/BaseModel.stub');
                $base_model = strtr($base_model_stub, $data_map);
                $base_model_path = app_path('Models/BaseModel.php');
                file_put_contents($base_model_path, $base_model);
            }


            file_put_contents($model_path, $model);

            return $model_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    protected function parseName(string $name)
    {
        return $parsed = array(
            '{{namespace}}' => $this->getNamespace(),
            '{{pluralCamel}}' => str_plural(camel_case($name)),
            '{{pluralSlug}}' => str_plural(str_slug($name)),
            '{{pluralSnake}}' => str_plural(snake_case($name)),
            '{{pluralClass}}' => str_plural(studly_case($name)),
            '{{singularCamel}}' => str_singular(camel_case($name)),
            '{{singularSlug}}' => str_singular(str_slug($name)),
            '{{singularSnake}}' => str_singular(snake_case($name)),
            '{{singularClass}}' => str_singular(studly_case($name)),
            '{{fillable}}' => $this->getFillables(),
            '{{timestamps}}' => $this->getTimetsamps(),
            '{{slugField}}' => $this->option('slug'),
        );
    }

    private function getTimetsamps()
    {
        return $this->timestamps ? 'false' : 'true';
    }


    /**
     * Get the different field
     * @return string
     */
    private function getFillables() :string
    {
        $fillable = '';
        foreach ($this->fields as $fields) {
            foreach ($fields as $k => $field) {
                // o is the index of the name's field
                if ($k === 0) {
                    $fillable .= "'$field'" . ',';

                }
            }
        }
        // add slug fiel to the fillable properties
        if (null != $this->option('slug')) {
            $fillable .= "'{$this->option('slug')}'";
        }


        // remove the comma at the end of the string
        $fillable = rtrim($fillable,',');

        return $fillable;
    }


    private function getFields()
    {

        $fields = [];

        $fields[] = $this->ask('Field');
        $fields[] = $this->anticipate('Type', self::TYPES);
        $fields[] = $this->ask('Rules');

        if ($this->confirm('Add another field?')) {
           $fields =  array_merge($fields,$this->getFields());
           //return [$fields];
//            $fields[] = $this->getFields();
        }
        //$fields[] = $this->ask('Add another field');

        return $fields;
    }

    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    protected function getNamespace()
    {
        $namespace = Container::getInstance()->getNamespace();
        return rtrim($namespace, '\\');
    }
}
