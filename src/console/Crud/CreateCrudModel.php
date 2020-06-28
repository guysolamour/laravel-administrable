<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;


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



    public function __construct(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug = null, bool $timestamps = false)
    {
        $this->model         = $model;
        $this->fields        = $fields;
        $this->actions       = $actions;
        $this->timestamps    = $timestamps;
        $this->slug          = $slug;
        $this->breadcrumb    = $breadcrumb;
        $this->theme         = $theme;

        $this->filesystem    = new Filesystem;
    }


    /**
     * @param string $name
     * @param array $fields
     * @param null|string $slug
     * @param bool $timestamps
     * @param bool $polymorphic
     * @return array
     */
    public static function generate(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug = null, bool $timestamps = false)
    {

        return (new CreateCrudModel($model, $fields, $actions, $breadcrumb, $theme, $slug, $timestamps))
            ->createModel();
    }


    /**
     * @return array
     */
    private function createModel(): array
    {

        $stub = $this->filesystem->get($this->TPL_PATH . '/models/model.stub');


        $data_map = $this->parseName($this->model);

        $model_path = app_path(sprintf("%s/%s.php", $data_map['{{modelsFolder}}'], $data_map['{{singularClass}}']));

        $model = $this->compliedFile($stub, false, $data_map);


        $model = $this->addTimestampProperty($model);


        $model = $this->loadSluggableTrait($model, $data_map);


        $this->createDirectoryIfNotExists($model_path, false);


        $this->addRelations($model, $model_path);

        if (!$this->filesystem->exists($model_path)) {
            $this->writeFile(
                $model,
                $model_path
            );
        }


        return [$model, $model_path];
    }

    /**
     * Parse guard name
     * Get the guard name in different cases
     * @param string $name
     * @return array
     */
    protected function parseName(?string $name = null): array
    {
        if (!$name)
            $name = $this->model;

        return [
            '{{namespace}}'            =>  $this->getNamespace(),
            '{{pluralCamel}}'          =>  Str::plural(Str::camel($name)),
            '{{pluralSlug}}'           =>  Str::plural(Str::slug($name)),
            '{{pluralSnake}}'          =>  Str::plural(Str::snake($name)),
            '{{pluralClass}}'          =>  Str::plural(Str::studly($name)),
            '{{singularCamel}}'        =>  Str::singular(Str::camel($name)),
            '{{singularSlug}}'         =>  Str::singular(Str::slug($name)),
            '{{singularSnake}}'        =>  Str::singular(Str::snake($name)),
            '{{singularClass}}'        =>  Str::singular(Str::studly($name)),
            '{{frontNamespace}}'       =>  ucfirst(config('administrable.front_namespace')),
            '{{frontLowerNamespace}}'  =>  Str::lower(config('administrable.front_namespace')),
            '{{backNamespace}}'        =>  ucfirst(config('administrable.back_namespace')),
            '{{backLowerNamespace}}'   =>  Str::lower(config('administrable.back_namespace')),
            '{{modelsFolder}}'         =>  $this->getCrudConfiguration('folder', 'Models'),
            '{{administrableLogo}}'    =>  asset(config('administrable.logo_url')),
            '{{theme}}'                =>  $this->theme,
            '{{guard}}'                =>  config('administrable.guard', 'admin'),
            '{{fillable}}'             =>  $this->getFillables(),
            '{{slugField}}'            =>  $this->slug,
        ];
    }

    protected function addRelations(string $model, string $model_path)
    {
        $related_model = '';
        $default_model = '';

        foreach ($this->fields as $field) {
            if ($this->isRelationField($field['type'])) {
                // on recupere le modele
                [$model_stub, $related_stub] = $this->getModelAndRelatedModelStubs($field);

                $data_map = array_merge(
                    $this->parseName($this->model),
                    $this->parseRelationName($this->model, $this->getRelatedModel($field)),
                    ['{{morphFieldName}}' => $this->getFieldName($field)],
                    ['{{morphRelationableName}}' => $this->getMorphRelationableName($field)],
                );

                $default_model  .=  $this->compliedFile($model_stub, false, $data_map);
                $related_model  .= $this->compliedFile($related_stub, false, $data_map);


                // add local foreign key
                if ($this->isSimpleOneToOneRelation($field) || $this->isSimpleOneToManyRelation($field)) {
                    $related_path = app_path($data_map['{{modelsFolder}}'] . '/' . $this->modelNameWithoutNamespace($this->getRelatedModel($field)) . '.php');

                    if ($local_keys = $this->getRelationLocalForeignKey($field)) {
                        $replace = '';

                        if ($local_foreing_key = Arr::get($local_keys, 'foreign_key')) {
                            $replace .= ", '{$local_foreing_key}'";
                            // $search =  $data_map['{{modelSingularClass}}'] . '::class';
                            // $related = str_replace($search,   $search . ", '{$local_foreing_key}'", $related);
                        }
                        if ($local_local_key = Arr::get($local_keys, 'local_key')) {
                            $replace .= ", '{$local_local_key}'";
                        }

                        $search =  $data_map['{{modelSingularClass}}'] . '::class';
                        if (!empty($replace)) {
                            $related_model = str_replace($search,   $search . $replace, $related_model);
                        }
                    }

                    if ($related_keys = $this->getRelationRelatedForeignKey($field)) {
                        $replace = '';
                        if ($related_foreing_key = Arr::get($related_keys, 'foreign_key')) {
                            $replace .= ", '{$related_foreing_key}'";
                            // $search =  $data_map['{{modelSingularClass}}'] . '::class';
                            // $related = str_replace($search,   $search . ", '{$local_foreing_key}'", $related);
                        }
                        if ($related_local_key = Arr::get($related_keys, 'other_key')) {
                            $replace .= ", '{$related_local_key}'";
                        }

                        $search =  $data_map['{{relatedSingularClass}}'] . '::class';
                        if (!empty($replace)) {
                            $default_model = str_replace($search,   $search . $replace, $default_model);
                        }
                    }
                } else if ($this->isSimpleManyToManyRelation($field)) {
                    $related_path = app_path($data_map['{{modelsFolder}}'] . '/' . $this->modelNameWithoutNamespace($this->getRelatedModel($field)) . '.php');

                    if ($local_keys = $this->getRelationLocalForeignKey($field)) {

                        $replace = '';
                        /**
                         * Si la table intermédiaire n'est pas défini, on la devine car pour passer les clés étrangères
                         * il faut le nom de la table
                         */
                        if (!$intermediate_table = $this->getRelationIntermediateTable($field)) {
                            $intermediate_table = $this->guestIntermediataTableName($field);

                            $search =  $data_map['{{modelSingularClass}}'] . '::class';;
                            $related_model .= str_replace($search,   $search . ", '{$intermediate_table}'", $related_model);
                        }

                        if (Arr::get($local_keys, 'join_key') && Arr::get($local_keys, 'foreign_key')) {

                            $local_join_key = Arr::get($local_keys, 'join_key');
                            $local_foreign_key = Arr::get($local_keys, 'foreign_key');

                            $replace .= ", '{$local_join_key}'";
                            $search =  $data_map['{{modelSingularClass}}'] . '::class,' . " '$intermediate_table'";
                            $related_model = str_replace($search,   $search . ", '{$local_join_key}'", $related_model);


                            $replace .= ", '{$local_foreign_key}'";
                            $search =  $data_map['{{modelSingularClass}}'] . '::class,' . " '$intermediate_table'" . ", '$local_join_key'";
                            $related_model = str_replace($search,   $search . ", '{$local_foreign_key}'", $related_model);
                        }
                    }


                    if ($related_keys = $this->getRelationRelatedForeignKey($field)) {
                        if (!$intermediate_table = $this->getRelationIntermediateTable($field)) {
                            $intermediate_table = $this->guestIntermediataTableName($field);

                            $search =  $data_map['{{relatedSingularClass}}'] . '::class';;
                            $default_model = str_replace($search,   $search . ", '{$intermediate_table}'", $default_model);
                        }

                        if (Arr::get($local_keys, 'join_key') && Arr::get($local_keys, 'foreign_key')) {

                            $related_foreign_key = Arr::get($local_keys, 'foreign_key');
                            $related_join_key = Arr::get($local_keys, 'join_key');


                            $replace .= ", '{$related_foreign_key}'";
                            $search =  $data_map['{{relatedSingularClass}}'] . '::class,' . " '$intermediate_table'" . ", '$related_join_key'";


                            $related_join_key = Arr::get($local_keys, 'join_key');
                            $replace .= ", '{$related_join_key}'";
                            $search =  $data_map['{{relatedSingularClass}}'] . '::class,' . " '$intermediate_table'";
                            $default_model = str_replace($search,   $search . ", '{$related_join_key}'", $default_model);

                            $default_model = str_replace($search,   $search . ", '{$related_foreign_key}'", $default_model);
                        }
                    }


                    if (
                        !$this->getRelationLocalForeignKey($field) &&
                        !$this->getRelationRelatedForeignKey($field) &&
                        $this->getRelationIntermediateTable($field)
                    ) {
                        $intermediate_table = $this->getRelationIntermediateTable($field);
                        $search =  $data_map['{{relatedSingularClass}}'] . '::class';
                        $default_model = str_replace($search,   $search . ", '{$intermediate_table}'", $default_model);

                        $search =  $data_map['{{modelSingularClass}}'] . '::class';;
                        $related_model = str_replace(
                            $search,
                            $search . ", '{$intermediate_table}'",
                            $related_model
                        );
                    }
                } else if ($this->isPolymorphicOneToOneRelation($field) || $this->isPolymorphicOneToManyRelation($field)) {
                }
                // else if ($this->isPolymorphicOneToManyRelation($field)){

                // }

            } else if ($this->isPolymorphicField($field)) {
                // related model

                $data_map = array_merge($this->parseName(), ['{{morphFieldName}}' => $this->getFieldName($field)]);
                $stub = $this->compliedFile($this->TPL_PATH . '/models/morphTo.stub', true, $data_map);

                $search = '// add relation methods below';


                $default_model = str_replace($search,   $search . "\n\n" . $stub, $model);
            }
        }




        if (!empty($related_model) && !empty($related_path)) {
            // related model
            $search = '// add relation methods below';
            $this->replaceAndWriteFile(
                $this->filesystem->get($related_path),
                $search,
                $search . PHP_EOL . PHP_EOL . $related_model,
                $related_path
            );
        }


        // default model
        if (!empty($default_model)) {
            $search = '// add relation methods below';
            $complied = str_replace($search,   $search . "\n\n" . $default_model . "\n\n", $model);
            $this->writeFile(
                $complied,
                $model_path
            );
        }
    }


    /**
     * Get the different field
     * @return string
     */
    private function getFillables(): string
    {
        $fillable = '';
        foreach ($this->fields as $field) {
            if ($this->isPolymorphicOneToOneRelation($field)) {
                continue;
            }

            if (
                ($field['name'] !== 'morphs' || $field['name'] !== 'guest') &&
                !$this->isPolymorphicField($field)
            ) {

                $fillable .= "'{$field['name']}'" . ',';
            } else if ($this->isPolymorphicField($field)) {
                $fillable .= "'" . $this->getPolymorphicModelType($field) . "',";
                $fillable .= "'" . $this->getPolymorphicModelId($field) . "',";
            }
        }

        // add slug field to the fillable properties
        if (!is_null($this->slug)) {
            $fillable .= "'slug'";
        }
        // remove the comma at the end of the string
        $fillable = rtrim($fillable, ',');

        return $fillable;
    }

    /**
     * @param $model
     * @param $data_map
     * @return mixed
     */
    private function loadSluggableTrait($model, $data_map): string
    {
        if (!$this->slug) {
            return $model;
        }

        // the namespace
        $namespace = 'use Cviebrock\EloquentSluggable\Sluggable;';
        $search = sprintf("namespace %s\%s;", $data_map['{{namespace}}'], $data_map['{{modelsFolder}}']);
        $model = str_replace($search, $search . "\n\n" . $namespace, $model);


        $sluggable_trait = '    use Sluggable;';
        $search = "{\n";
        // insert the namespace in the model
        $model = str_replace($search, $search . $sluggable_trait, $model);

        // sluggable stub
        $sluggable_stub = file_get_contents($this->TPL_PATH . '/models/sluggable.stub');
        // replace the slug field vars
        $sluggable = strtr($sluggable_stub, $data_map);

        // insert in the model
        $search = '// add sluggable methods below' . "\n\n";

        return str_replace($search, $search . $sluggable, $model);
    }


    /**
     * @param $field
     * @return array
     */
    protected function getModelAndRelatedModelStubs(array $field): array
    {
        if ($this->isSimpleOneToOneRelation($field)) {
            $model_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/simple/onetoone/belongsTo.stub');
            $related_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/simple/onetoone/hasOne.stub');
        } else if ($this->isSimpleOneToManyRelation($field)) {
            $model_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/simple/onetomany/belongsTo.stub');
            $related_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/simple/onetomany/hasMany.stub');
        }

        // else if ($this->isSimpleManyToOneRelation($field)) {
        //     $model_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/simple/manytoone/hasMany.stub');
        //     $related_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/simple/manytoone/belongsTo.stub');
        // }

        else if ($this->isSimpleManyToManyRelation($field)) {

            $model_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/simple/manytomany/belongsToMany.stub');
            $related_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/simple/manytomany/relatedBelongsToMany.stub');
        } else if ($this->isPolymorphicOneToOneRelation($field)) {

            $model_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/polymorphic/onetoone/morphOne.stub');
            $related_stub = '';
            // $related_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/polymorphic/onetoone/hasOne.stub');
        }
        // else if ($this->isPolymorphicOneToManyRelation($field)) {

        //     $model_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/polymorphic/onetomany/morphMany.stub');
        //     $related_stub = '';
        //     // $related_stub = $this->filesystem->get($this->TPL_PATH . '/models/OneToOne/hasOne.stub');
        // }
        // else if ($this->isPolymorphicManyToOneRelation($field)) {

        //     $model_stub = $this->filesystem->get($this->TPL_PATH . '/models/relations/polymorphic/manytoone/morphMany.stub');
        //     $related_stub = '';
        // }

        return [$model_stub, $related_stub];
    }

    /**
     * @param string $model
     * @return string
     */
    private function addTimestampProperty(string $model): string
    {
        if (!$this->timestamps) {
            $search = 'public $fillable';
            $replace = ' public $timestamps = false;' . "\n\n";

            $model = str_replace($search,  $replace . '     ' .  $search, $model);

            return $model;
        }

        return $model;
    }
}
