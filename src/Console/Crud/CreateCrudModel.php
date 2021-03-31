<?php

namespace Guysolamour\Administrable\Console\Crud;

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



    public function __construct(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, bool $fillable, ?string $slug = null, bool $timestamps = false)
    {
        $this->model         = $model;
        $this->fields        = $fields;
        $this->actions       = $actions;
        $this->timestamps    = $timestamps;
        $this->slug          = $slug;
        $this->breadcrumb    = $breadcrumb;
        $this->theme         = $theme;
        $this->fillable      = $fillable;

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
    public static function generate(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme,  bool $fillable, ?string $slug = null, bool $timestamps = false)
    {
        return (new CreateCrudModel($model, $fields, $actions, $breadcrumb, $theme, $fillable, $slug, $timestamps ))
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



    /**
     * Get the different field
     * @return string
     */
    private function getFillables(): string
    {
        if (!$this->fillable){
            return 'protected $guarded = [];';
        }


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


        return "public \$fillable = [$fillable];";
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
