<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Str;


class CreateCrudModel
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
     * @var null|string
     */
    private $slug;
    /**
     * @var bool
     */
    private $timestamps;
    /**
     * @var bool
     */
    private $polymorphic;

    private $new_model_stub = '';


    /**
     * CreateModel constructor.
     * @param string $name
     * @param array $fields
     * @param null|string $slug
     * @param bool $timestamps
     * @param bool $polymorphic
     */
    private function __construct(string $model , array $fields, ?string $slug = null, bool $timestamps = false, bool $polymorphic = false)
    {

        $this->model = $model;
        $this->fields = $fields;
        $this->slug = $slug;
        $this->timestamps = $timestamps;
        $this->polymorphic = $polymorphic;
    }


    /**
     * @param string $name
     * @param array $fields
     * @param null|string $slug
     * @param bool $timestamps
     * @param bool $polymorphic
     * @return array
     */
    public static function generate(string $name , array $fields, ?string $slug = null, bool $timestamps = false, bool $polymorphic = false)
    {

        return
            (new CreateCrudModel($name,$fields,$slug,$timestamps,$polymorphic))
            ->createModel();
    }


    /**
     * @return array
     */
    private function createModel() :array
    {

        if ($this->polymorphic){
            $stub = file_get_contents($this->TPL_PATH . '/models/poly.stub');
        }else {
            $stub = file_get_contents($this->TPL_PATH . '/models/model.stub');
        }

        $data_map = $this->parseName($this->model);

        $model_path = app_path('Models/'.$data_map['{{singularClass}}'].'.php');

        $model = strtr($stub, $data_map);

        $model = $this->addTimestampProperty($model);

        $model = $this->loadSluggableTrait($model, $data_map);

        $this->createDirIfNotExists($model_path);

        // add model and base model
        $result = $this->loadModelAndBaseModel($data_map, $model_path, $model);



        return [$result,$model_path];

    }

    /**
     * @param string $name
     * @return array
     */
    private function parseName(string $name) :array
    {
        return [
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
            '{{slugField}}' => $this->slug,
        ];
    }


    private function parseRelationName(string $model_name, string $related_full_name) :array
    {
        // on recupere le nom du modele sans le namespace
        $related = $this->modelNameWithoutNamespace($related_full_name);
        return [
            '{{modelPluralSlug}}' => Str::plural(Str::slug($model_name)),
            '{{modelPluralClass}}' => Str::plural(Str::studly($model_name)),
            '{{modelSingularClass}}' => Str::studly($model_name),
            '{{modelSingularSlug}}' => Str::singular(Str::slug($model_name)),
            '{{relatedSingularClass}}' => Str::singular(Str::studly($related)),
            '{{relatedPluralSlug}}' => Str::plural(Str::slug($related)),
            '{{relatedPluralClass}}' => Str::plural(Str::studly($related)),
            '{{relatedSingularSlug}}' => Str::singular(Str::slug($related)),
            '{{relatedNamespace}}'  => $this->getRelatedNamespace($related_full_name),
        ];
    }

    /**
     * @param string $full_name
     * @return string
     */
    private function getRelatedNamespace(string $full_name) :string
    {
        $parts = explode('\\', $full_name);
        // remove the model name
        array_pop($parts);

        return ('\\' . join('\\', $parts) . '\\');

    }

    /**
     * @param $field
     * @return bool
     */
    private function isOneToManyRelation($field) :bool
    {
        return $field['type']['relation']['name'] === 'One to Many';
    }

    private function isManyToOneRelation($field) :bool
    {
        return $field['type']['relation']['name'] === 'Many to One';
    }

    private function isOneToOneRelation($field) :bool
    {
        return $field['type']['relation']['name'] === 'One to One';
    }
    private function isOneToManyPolymorphicRelation($field) :bool
    {
        return $field['type']['relation']['name'] === 'One To Many (Polymorphic)';
    }


    /**
     * @param $model
     * @param $model_path
     * @return bool
     */
    private function addRelations($model, $model_path){
        foreach ($this->fields as $field) {
            if ($this->isRelationField($field['type'])){
                // check if the related model already exists
                $this->checkIfRelatedModelExists($field);

                // on recupere le modele
                [$model_stub, $related_stub] = $this->getModelAndRelatedModelStubs($field);




                $data_map = $this->parseRelationName($this->model, $this->getRelatedModel($field));


                /**
                 * mettre le resultat dans l'instance afin de le conserver et ne pas le perdre
                 */
                $this->new_model_stub .=   strtr($model_stub , $data_map) . PHP_EOL;

                $related = strtr($related_stub, $data_map);


                if (!empty($field['guest'])) {
                    $replace = '->withDefault(function($'. $data_map['{{relatedSingularSlug}}'] .', $'. $data_map['{{modelSingularSlug}}'] . '){' . PHP_EOL;
                            foreach ($field['guest'] as $value) {
                                $replace .=     '           $'. $data_map['{{relatedSingularSlug}}'] .'->'. $value . ' = $' . $data_map['{{modelSingularSlug}}'] . '->'. $value . ';' . PHP_EOL;
                            }
                    $replace.=  '       });';

                    $this->new_model_stub = str_replace(' ;', $replace   , $this->new_model_stub);
                }


                $related_path = app_path('Models/' . $this->modelNameWithoutNamespace($this->getRelatedModel($field)).'.php');

                if (!file_exists($related_path)){
                    $related_path = app_path($this->modelNameWithoutNamespace($this->getRelatedModel($field)).'.php');
                }

                $related_model = file_get_contents($related_path);


                $search = '// add relation methods below';

                // search if the model

                $related_file = str_replace($search,    $search . "\n" . $related    , $related_model);


                file_put_contents($related_path,$related_file);



            }
        }


        $search = '// add relation methods below';

        $model_file = str_replace($search,   $search . "\n" . $this->new_model_stub    , $model);
       // dd($this->new_model_stub, $model_path. $model_file);

       // dd($this->new_model_stub);
        return $this->writeFile($model_path,$model_file);
    }




    /**
     * Get the different field
     * @return string
     */
    private function getFillables() :string
    {
        $fillable = '';
        foreach ($this->fields as $field) {
            if (
                $field['name'] !== 'morphs' || $field['name'] !== 'guest'
                ){

                $fillable .= "'{$field['name']}'" . ',';
            }
        }

        // add polymrphic type and id to the fillable properties
        if ($this->polymorphic) {
            $fillable .= "'". strtolower($this->model) . "able_type',";
            $fillable .= "'". strtolower($this->model) . "able_id',";
        }

        // add slug field to the fillable properties
        if (!is_null($this->slug)) {
            $fillable .= "'slug'";
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
            $namespace = 'use Cviebrock\EloquentSluggable\Sluggable;';
            $search = 'namespace '. $this->getNamespace().'\Models;';
            $model = str_replace($search, $search . "\n\n" . $namespace, $model);


            $sluggable_trait = '    use Sluggable;';
            $slug_mw_bait = "{\n";
            // insert the namespace in the model
            $model = str_replace($slug_mw_bait, $slug_mw_bait . $sluggable_trait, $model);

            // sluggable stub
            $sluggable_stub = file_get_contents($this->TPL_PATH . '/models/sluggable.stub');
            // replace the slug field vars
            $sluggable = strtr($sluggable_stub, $data_map);

            // insert in the model
            $route_mw_bait = '// add sluggable methods below' . "\n\n";

            $model = str_replace($route_mw_bait, $route_mw_bait . $sluggable, $model);

        }

        return $model;
    }


    /**
     * @param $data_map
     * @param $model_path
     * @param $model
     * @return bool
     */
    private function loadModelAndBaseModel($data_map, $model_path, $model): bool
    {
        if (!file_exists(app_path('Models/BaseModel.php'))) {

            $base_model_stub = file_get_contents($this->TPL_PATH . '/models/BaseModel.stub');
            $base_model = strtr($base_model_stub, $data_map);
            $base_model_path = app_path('Models/BaseModel.php');
            file_put_contents($base_model_path, $base_model);
        }
        // add model and relations to
        return $this->addRelations($model,$model_path);
    }

    /**
     * @param $field
     * @return array
     */
    private function getModelAndRelatedModelStubs($field): array
    {
        if ($this->isOneToManyRelation($field)) {

            $model_stub = file_get_contents($this->TPL_PATH . '/models/belongsTo.stub');
            $related_stub = file_get_contents($this->TPL_PATH . '/models/hasMany.stub');
        }

        if ($this->isManyToOneRelation($field)) {

            $model_stub = file_get_contents($this->TPL_PATH . '/models/ManyToOne/belongsTo.stub');
            $related_stub = file_get_contents($this->TPL_PATH . '/models/ManyToOne/hasMany.stub');
        }

        if ($this->isOneToOneRelation($field)) {

            $model_stub = file_get_contents($this->TPL_PATH . '/models/OneToOne/belongsTo.stub');
            $related_stub = file_get_contents($this->TPL_PATH . '/models/OneToOne/hasOne.stub');
        }
        if ($this->isOneToManyPolymorphicRelation($field)) {

            // create image model and migration here

            $model_stub = file_get_contents($this->TPL_PATH . '/models/OneToManyPoly/MorphMany.stub');
            $related_stub = file_get_contents($this->TPL_PATH . '/models/OneToManyPoly/MorphTo.stub');
        }
        return [$model_stub, $related_stub];
    }

    /**
     * @param string $model
     * @return string
     */
    private function addTimestampProperty(string $model) :string
    {
        // stop if the timestamps is null because the option was not given
        if (!$this->timestamps) {
            return $model;
        }


        // insert in the model
        $search = 'public $fillable' ;
        $replace = ' public $timestamps = false;' . "\n\n";

        $model = str_replace($search,  $replace .'     '.  $search, $model);

        return $model;
    }


    private function checkIfRelatedModelExists($field)
    {
        if(!class_exists($this->getRelatedModel($field))){
            // if the model is image its mean that we have to generate a polymorphic image model
            // so we do not exit
            if ($this->getRelatedModel($field) !== 'App\\Models\\Image'){
                die("The related model [{$this->getRelatedModel($field)}] does not exists. You nedd to create if first." . PHP_EOL);
            }
        }
    }

}