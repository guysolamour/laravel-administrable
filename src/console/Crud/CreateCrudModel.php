<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Str;


class CreateCrudModel
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
     * CreateModel constructor.
     * @param string $name
     * @param array $fields
     * @param null|string $slug
     * @param bool $timestamps
     */
    private function __construct(string $name , array $fields, ?string $slug = null, bool $timestamps = false)
    {

        $this->name = $name;
        $this->fields = array_chunk($fields, 3);;
        $this->slug = $slug;
        $this->timestamps = $timestamps;
    }

    /**
     * @param string $name
     * @param array $fields
     * @param null|string $slug
     * @param bool $timestamps
     * @return string
     */
    public static function generate(string $name , array $fields, ?string $slug = null, bool $timestamps = false)
    {

        return
            (new CreateCrudModel($name,$fields,$slug,$timestamps))
            ->createModel();
    }

    /**
     * @return string
     */
    private function createModel() :array
    {
        try {
            $stub = file_get_contents($this->TPL_PATH . '/models/model.stub');
            $data_map = $this->parseName($this->name);

            $model_path = app_path('Models/'.$data_map['{{singularClass}}'].'.php');

            $model = strtr($stub, $data_map);


            $model = $this->loadSluggableTrait($model, $data_map);

            $this->createDirIfNotExists($model_path);

            // add model and base model
            $result = $this->loadModelAndBaseModel($data_map, $model_path, $model);

            return [$result,$model_path];

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * @param string $name
     * @return array
     */
    private function parseName(string $name) :array
    {
        return $parsed = [
            '{{namespace}}' => $this->getNamespace(),
            '{{pluralCamel}}' => Str::plural(Str::camel($name)),
            '{{pluralSlug}}' => Str::plural(Str::slug($name)),
            '{{pluralSnake}}' => Str::plural(Str::snake($name)),
            '{{pluralClass}}' => Str::plural(Str::studly($name)),
            '{{singularCamel}}' => Str::singular(Str::camel($name)),
            '{{singularSlug}}' => Str::singular(Str::slug($name)),
            '{{singularSnake}}' => Str::singular(Str::snake($name)),
            '{{singularClass}}' => Str::singular(Str::studly($name)),
            '{{fillable}}' => $this->getFillables(),
            '{{timestamps}}' => $this->getTimetsamps(),
            '{{slugField}}' => $this->slug,
        ];
    }

    /**
     * @return string
     */
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
                // 0 is the index of the name's field
                if ($k === 0) {
                    $fillable .= "'$field'" . ',';

                }
            }
        }
        // add slug field to the fillable properties
        if (!is_null($this->slug)) {
            $fillable .= "'{$this->slug}'";
            $fillable .= ",'slug'";
        }

        // remove the comma at the end of the string
        $fillable = rtrim($fillable,',');

        return $fillable;
    }

    /**
     * @param $model
     * @param $data_map
     * @return mixed
     */
    private function loadSluggableTrait($model, $data_map): string
    {
        if (!is_null($this->slug)) {
            // the namespace
            $sluggable_trait = '    use \Cviebrock\EloquentSluggable\Sluggable;';
            $slug_mw_bait = "{\n";
            // insert the namespace in the model
            $model = str_replace($slug_mw_bait, $slug_mw_bait . $sluggable_trait, $model);

            // sluggable stub
            $sluggable_stub = file_get_contents($this->TPL_PATH . '/models/sluggable.stub');
            // replace the slug field vars
            $sluggable = strtr($sluggable_stub, $data_map);

            // insert in the model
            $route_mw_bait = 'public $timestamps = ' . $this->getTimetsamps() . ';' . "\n\n\n";

            $model = str_replace($route_mw_bait, $route_mw_bait . $sluggable, $model);

        }
        return $model;
    }

    /**
     * @param $data_map
     * @param $model_path
     * @param $model
     */
    private function loadModelAndBaseModel($data_map, $model_path, $model): bool
    {
        if (!file_exists(app_path('Models/BaseModel.php'))) {

            $base_model_stub = file_get_contents($this->TPL_PATH . '/models/BaseModel.stub');
            $base_model = strtr($base_model_stub, $data_map);
            $base_model_path = app_path('Models/BaseModel.php');
            file_put_contents($base_model_path, $base_model);
        }


        return $this->writeFile($model_path,$model);
    }

}
