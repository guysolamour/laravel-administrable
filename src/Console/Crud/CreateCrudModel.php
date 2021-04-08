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
     * @var string
     */
    private $table_name;

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
    /**
     * @var string
     */
    private $new_model_stub = '';



    public function __construct(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, bool $fillable, ?string $table_name, ?string $slug = null, bool $timestamps = false)
    {
        $this->model         = $model;
        $this->fields        = $fields;
        $this->actions       = $actions;
        $this->timestamps    = $timestamps;
        $this->slug          = $slug;
        $this->breadcrumb    = $breadcrumb;
        $this->theme         = $theme;
        $this->fillable      = $fillable;
        $this->table_name    = $table_name;

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
    public static function generate(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme,  bool $fillable, ?string $table_name, ?string $slug = null, bool $timestamps = false)
    {
        return (new CreateCrudModel($model, $fields, $actions, $breadcrumb, $theme, $fillable, $table_name, $slug, $timestamps ))
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

        $model = $this->addTableNameProperty($model);

        $model = $this->addTimestampProperty($model);

        $model = $this->addCastsProperty($model);

        $model = $this->addDaterangeTrait($model);

        $model = $this->addDaterangeTraitMethod($model);
        $model = $this->addDatepickerTraitMethod($model);


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
            return <<<TEXT
            // The attributes that aren't mass assignable.
                public \$guarded = [];
            TEXT;
        }


        $fillable = '';
        foreach ($this->fields as $field) {
            if ($this->isPolymorphicOneToOneRelation($field)) {
                continue;
            }

            if (
                ($this->getFieldName($field) !== 'morphs' || $this->getFieldName($field) !== 'guest') &&
                !$this->isPolymorphicField($field) && !$this->isDaterangeField($field)
            ) {

                $fillable .= "'{$this->getFieldName($field)}'" . ',';

            } else if ($this->isPolymorphicField($field)) {
                $fillable .= "'" . $this->getPolymorphicModelType($field) . "',";
                $fillable .= "'" . $this->getPolymorphicModelId($field) . "',";

            }else if($this->isDaterangeField($field)){
                $fillable .= "'{$this->getRangeStartFieldName($field)}'" . ',';
                $fillable .= "'{$this->getRangeEndFieldName($field)}'" . ',';
            }
        }

        // add slug field to the fillable properties
        if (!is_null($this->slug)) {
            $fillable .= "'slug'";
        }
        // remove the comma at the end of the string
        $fillable = rtrim($fillable, ',');


        return <<<TEXT
        // The attributes that are mass assignable.
            public \$fillable = [$fillable];
        TEXT;
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
        $model = str_replace($search, $search . PHP_EOL . PHP_EOL . $namespace, $model);


        $sluggable_trait = '    use Sluggable;';
        $search = "{\n";
        // insert the namespace in the model
        $model = str_replace($search, $search . $sluggable_trait, $model);

        // sluggable stub
        $sluggable_stub = file_get_contents($this->TPL_PATH . '/models/sluggable.stub');
        // replace the slug field vars
        $sluggable = strtr($sluggable_stub, $data_map);

        // insert in the model
        $search = '// add sluggable methods below' .  PHP_EOL . PHP_EOL;

        return str_replace($search, $search . $sluggable, $model);
    }


    /**
     * @param string $model
     * @return string
     */
    private function addTableNameProperty(string $model): string
    {
        $search = "{{tablename}}";

        if (!$this->table_name) {
            return str_replace($search, '', $model);
        }

        $replace = <<<TEXT
        // The table associated with the model
            protected \$table = '{$this->table_name}';
        TEXT;

        $model = str_replace($search,  $replace . PHP_EOL, $model);

        return $model;
    }

    /**
     * @param string $model
     * @return string
     */
    private function addTimestampProperty(string $model): string
    {
        if (!$this->timestamps) {
            $search = 'public $fillable';
            $replace = ' public $timestamps = false;' . PHP_EOL . PHP_EOL;

            $model = str_replace($search,  $replace . '     ' .  $search, $model);

            return $model;
        }

        return $model;
    }


    /**
     * @param string $model
     * @return string
     */
    private function addCastsProperty(string $model_stub): string
    {
        // si vide alors on return
        if (!$this->checkIfThereAreCastableFields()){
            // si pas de cast retirer le pseudo code dans le model
            return  str_replace('{{cast}}', '', $model_stub);
        }

        $template = <<<TEXT
        // The attributes that should be cast to native types.
            protected \$casts = [
        TEXT;

        foreach($this->fields as $field){
            if ($cast = $this->getFieldCast($field)){
                // la non indentation est importante c'est pour mieux formater le texte
                if ($this->isDaterangeField($field)){
                    $template .= "
        '{$this->getRangeStartFieldName($field)}' => '{$cast}',
        '{$this->getRangeEndFieldName($field)}'   => '{$cast}',";
                }else {
                    $template .= "
        '{$this->getFieldName($field)}' => '{$cast}',";
                }
            }
        }

        $template .= "
    ];";

        $model_stub = str_replace('{{cast}}', $template, $model_stub);



       return $model_stub;

    }

    public function checkIfFieldsContainsAdaterangeField() :bool
    {
        foreach ($this->fields as $field) {
            if ($this->isDaterangeField($field)){
                return true;
            }
        }

        return false;
    }
    public function checkIfFieldsContainsAdatepickerField() :bool
    {
        foreach ($this->fields as $field) {
            if ($this->isDatepickerField($field)){
                return true;
            }
        }

        return false;
    }

    private function addDaterangeTrait(string $model_stub) :string
    {
        if ($this->checkIfFieldsContainsAdaterangeField() || $this->checkIfFieldsContainsAdatepickerField()){
            $search = "use ModelTrait";
            $model_stub = str_replace($search , $search . ', DaterangeTrait', $model_stub);

            $search = "use {$this->parsename()['{{namespace}}']}\Traits\ModelTrait;";
            $model_stub = str_replace($search , $search . PHP_EOL ."use {$this->parsename()['{{namespace}}']}\Traits\DaterangeTrait;", $model_stub);
        }

        return $model_stub;
    }

    /**
     *
     * @param string $model_stub
     * @return string
     */
    private function addDatepickerTraitMethod(string $model_stub) :string
    {
        if (!$this->checkIfFieldsContainsAdaterangeField()) {
            return str_replace('{{datepicker}}', '', $model_stub);
        }

        $replace = <<<TEXT
        // The date pickers configuration array for this model.
            protected \$datepickers = [
        TEXT;

        foreach ($this->fields as $field) {
            if ($this->isDatepickerField($field)) {

                $replace .= <<<TEXT

                            '{$this->getFieldName($field)}',
                TEXT;
            }
        }

        $replace .= <<<TEXT

                ];
        TEXT;

        return str_replace('{{datepicker}}', $replace, $model_stub);
    }
    /**
     *
     * @param string $model_stub
     * @return string
     */
    private function addDaterangeTraitMethod(string $model_stub) :string
    {
        if (!$this->checkIfFieldsContainsAdaterangeField()) {
            return str_replace('{{daterange}}', '', $model_stub);
        }

        $replace = <<<TEXT
        // The date ranges configuration array for this model.
            protected \$dateranges = [

        TEXT;

        foreach ($this->fields as $field) {
            if ($this->isDaterangeField($field)) {

                $replace .= <<<TEXT

                            '{$this->getFieldName($field)}',
                TEXT;
            }
        }

        $replace .= <<<TEXT

                ];
        TEXT;

        return str_replace('{{daterange}}', $replace, $model_stub);
    }

    /**
     * on recupere les casts sur tous les champs
     * si vide alors on return
     *
     * @return bool
     */
    private function checkIfThereAreCastableFields() :bool
    {
        $casts = array_filter(Arr::pluck($this->fields, 'cast', 'name'));

        return !empty($casts);
    }
}
