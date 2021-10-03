<?php

namespace Guysolamour\Administrable\Console\Crud\Generate;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\Crud\Field;


class GenerateModel extends BaseGenerate
{
    /**
     * @return array
     */
    public function run()
    {
        if (!$this->crud->generateFieldIsAllowedFor($this)) {
            return [false, 'Skip creating model'];
        }

        $stub  = $this->crud->filesystem->get($this->crud->getCrudTemplatePath('/models/model.stub'));
        $model = $this->crud->filesystem->compliedFile($stub, false, $this->data_map);


        $model = $this->addFillableOrGuardedProperty($model);
        $model = $this->addTableNameProperty($model);
        $model = $this->addTimestampProperty($model);
        $model = $this->addCastsProperty($model);
        $model = $this->addDaterangeCast($model);
        $model = $this->addDraftableTrait($model);
        $model = $this->addDaterangeTrait($model);
        $model = $this->addDaterangeTraitProperty($model);
        $model = $this->addDatepickerTraitProperty($model);
        $model = $this->addMediaTrait($model);
        $model = $this->loadSluggableTrait($model);

        $model_path = $this->getModelPath();

        $this->addRelations($model, $model_path);

        $this->writeModel($model_path, $model);

        return [$model, $model_path];
    }

    public function getParsedName(?string $name = null): array
    {
        return array_merge($this->crud->getParsedName($name), [
            '{{baseModelClass}}' => $this->getBaseModelClass(),
            '{{tableName}}'      =>  $this->crud->getTablename(),
            '{{slugField}}'      =>  $this->crud->getSlug(),
        ]);
    }

	public function getBaseModelClass() :string
	{
		return sprintf("%s\%s\BaseModel", $this->crud->getAppNamespace(), $this->crud->getModelsFolder());
	}

	// protected function addBaseModelNamespace(string $model) :string
	// {
    //     if ($this->crud->hasSubfolder()) {
    //         $search = "Traits\ModelTrait;";
    //         $replace = "use {$this->getBaseModelClass()};" . PHP_EOL;

    //         $model = str_replace($search, $search . PHP_EOL . $replace, $model);
    //     }

    //     return $model;
	// }

    protected function addTableNameProperty(string $model): string
    {
        $search = "{{tablename}}";

        /**
         * Si la table n'est pas passé explicitement et qu'on a pas de sous dossier
         */
        if ($this->crud->checkIfTableNameIsEmpty() && !$this->crud->hasSubfolder()) {
            return str_replace($search, '', $model);
        }

        $replace = <<<TEXT
        // The table associated with the model
            protected \$table = '{$this->crud->getTableName()}';
        TEXT;

        $model = str_replace($search,  $replace . PHP_EOL, $model);

        return $model;
    }

    protected function addTimestampProperty(string $model): string
    {
        if ($this->crud->getTimestamps()){
            return $model;
        }

        $search = 'use ModelTrait;';
        $replace = <<<TEXT
        {$search}

            // Indicates if the model should be timestamped.
            public \$timestamps = false;

        TEXT;

        $model = str_replace($search,    $replace, $model);

        return $model;
    }

    protected function addFillableProperty(string $model) :string
    {
        $search = '{{fillable}}';

        $fields = $this->crud->getFillable();

        $fillable = '';

        if (empty($fields) || is_bool($fields)){
            foreach ($this->crud->getFields() as $field) {

                /**
                 * @var Field $field
                 */

                if ($field->isPolymorphicRelation()){
                    continue;
                }

                if ($field->isDaterange()) {
                    $fillable .= "'{$field->getDaterangeStartFieldName()}'" . ', ';
                    $fillable .= "'{$field->getDaterangeEndFieldName()}'" . ', ';
                }else if ($field->isPolymorphic()){
                    $fillable .= "'{$field->getPolymorphicModelId()}'" . ', ';
                    $fillable .= "'{$field->getPolymorphicModelType()}'" . ', ';
                }else {
                    $fillable .= "'{$field->getName()}'" . ', ';
                }
            }

            // add slug field to the fillable properties
            if ($this->crud->getSlug()) {
                $fillable .= "'slug'";
            }
        }

        if (is_array($fields)) {
            foreach ($fields as $field) {
                $fillable .= "'{$field}', ";
            }
        }

        // retrait de la virgule en fin de ligne
        $fillable = rtrim($fillable, ', ');

        $replace =  <<<TEXT
        // The attributes that are mass assignable.
            public \$fillable = [$fillable];
        TEXT;

        return str_replace($search, $replace, $model);
    }

    protected function addGuardedProperty(string $model) :string
    {
        $search = '{{fillable}}';

        $fields = $this->crud->getGuarded();

        $guarded = '';

        if (is_bool($fields) && $fields){
            $guarded .= '';
        }
        elseif (is_array($fields) && !empty($fields)){
            foreach ($fields as $field) {
                $guarded .= "'{$field}', ";
            }
        }

        $guarded = rtrim($guarded, ', ');


        $replace =  <<<TEXT
        // The attributes that are not mass assignable.
            public \$guarded = [{$guarded}];
        TEXT;

        return str_replace($search, $replace, $model);
    }


    protected function addFillableOrGuardedProperty(string $model): string
    {
        if ($this->crud->isFillable()){
            $model = $this->addFillableProperty($model);
        }
        else if ($this->crud->isGuarded()){
            $model = $this->addGuardedProperty($model);
        }

        return $model;
    }


    protected function addCastsProperty(string $model): string
    {
        $search = '{{cast}}';
        // si vide alors on return
        if (!$this->crud->checkIfThereAreCastableFields()) {
            // si pas de cast retirer le pseudo code dans le model
            return  str_replace($search, '', $model);
        }

        $template = <<<TEXT
        // The attributes that should be cast to native types.
            protected \$casts = [
        TEXT;

        foreach ($this->crud->getFields() as $field) {
            /**
             * @var Field $field
             */
            if ($cast = $field->getCast()) {

                // la non indentation est importante c'est pour mieux formater le texte
                if ($field->isDatepicker()){
                    $template .= "
        '{$field->getName()}' => {$cast},";
                }
                else if ($field->isDaterange()) {
                    $template .= "
        '{$field->getDaterangeStartFieldName()}' => {$cast},
        '{$field->getDaterangeEndFieldName()}'   => {$cast},";
                } else {
                    $template .= "
        '{$field->getName()}' => '{$cast}',";
                }
            }
        }

        $template .= "
    ];";

        return str_replace($search, $template, $model);

    }

    protected function addDaterangeCast(string $model)
    {
        if (!$this->crud->checkIfThereAreDaterangeFields() && !$this->crud->checkIfThereAreDatepickerFields()) {
            return $model;
        }


        $search = "use Guysolamour\Administrable\Traits\ModelTrait;";
        $model = str_replace($search, $search . PHP_EOL . "use Guysolamour\Administrable\Casts\DaterangepickerCast;", $model);

        return $model;
    }

    protected function addMediaTrait(string $model): string
    {
        if (!$this->crud->hasImagemanager()) {
            return $model;
        }

        $search = "use Guysolamour\Administrable\Traits\ModelTrait;";
        $model = str_replace($search, $search . PHP_EOL . "use Guysolamour\Administrable\Traits\MediaableTrait;", $model);

        $search = "use ModelTrait;";
        $model = str_replace($search, $search . PHP_EOL . '    use MediaableTrait;', $model);


        $search = "extends BaseModel";
        $model = str_replace($search, $search . ' implements HasMedia' , $model);

        $search = "use Guysolamour\Administrable\Traits\ModelTrait;";
        $model = str_replace($search, $search . PHP_EOL . "use Spatie\MediaLibrary\HasMedia;", $model);

        return $model;
    }

    protected function addDraftableTrait(string $model): string
    {
        if (!$this->crud->checkIfThereAreBooleanFields()) {
            return $model;
        }

        $search = "use ModelTrait;";
        $model = str_replace($search, $search . PHP_EOL . '    use DraftableTrait;' , $model);

        $search = "use Guysolamour\Administrable\Traits\ModelTrait;";
        $model = str_replace($search, $search . PHP_EOL . "use Guysolamour\Administrable\Traits\DraftableTrait;", $model);

        return $model;
    }

    protected function addDaterangeTrait(string $model): string
    {
        if (!$this->crud->checkIfThereAreDaterangeFields() && !$this->crud->checkIfThereAreDatepickerFields()) {
            return $model;
        }

        $search = "use ModelTrait;";
        $model = str_replace($search, $search . PHP_EOL . '    use DaterangeTrait;' , $model);

        $search = "use Guysolamour\Administrable\Traits\ModelTrait;";
        $model = str_replace($search, $search . PHP_EOL . "use Guysolamour\Administrable\Traits\DaterangeTrait;", $model);

        return $model;
    }


    protected function addDaterangeTraitProperty(string $model): string
    {
        $search = '{{daterange}}';

        if (!$this->crud->checkIfThereAreDaterangeFields()) {
            return str_replace($search, '', $model);
        }

        $replace = "
    // The date ranges configuration array for this model." . PHP_EOL . '    protected $dateranges = [';


        foreach ($this->crud->getFields() as $field) {
            /**
             * @var Field $field
             */
            if ($field->isDaterange()) {

                $replace = <<<TEXT
                        {$replace}
                        '{$field->getName()}',
                TEXT;
            }
        }

        $replace .= <<<TEXT

            ];
        TEXT;

        return str_replace($search, $replace, $model);
    }

    protected function addDatepickerTraitProperty(string $model): string
    {
        $search = '{{datepicker}}';

        if (!$this->crud->checkIfThereAreDatepickerFields()) {
            return str_replace($search, '', $model);
        }

        $replace = "
    // The date pickers configuration array for this model." . PHP_EOL . '    protected $datepickers = [';


        foreach ($this->crud->getFields() as $field) {
            /**
             * @var Field $field
             */
            if ($field->isDatepicker()) {

                $replace = <<<TEXT
                        {$replace}
                        '{$field->getName()}',
                TEXT;
            }
        }

        $replace .= <<<TEXT

            ];
        TEXT;

        return str_replace($search, $replace, $model);
    }


    protected function loadSluggableTrait(string $model): string
    {
        if (!$this->crud->getSlug()) {
            return str_replace('{{sluggable}}', '', $model);
        }

        $data_map = $this->getParsedName();

        // the namespace
        $namespace = 'use Guysolamour\Administrable\Traits\SluggableTrait;';
        $search = sprintf("namespace %s\%s;", $data_map['{{namespace}}'], $data_map['{{modelsNamespace}}']);
        $model = str_replace($search, $search . PHP_EOL . PHP_EOL . $namespace, $model);


        $sluggable_trait = '    use SluggableTrait;';
        $search = "use ModelTrait;";
        // insert the namespace in the model
        $model = str_replace($search, $search . PHP_EOL . $sluggable_trait , $model);


        // sluggable stub
        $sluggable = $this->crud->filesystem->compliedFile($this->crud->getCrudTemplatePath('/models/sluggable.stub'), true, $data_map);

        // insert in the model
        $model =  str_replace('{{sluggable}}', $sluggable, $model);

        return $model;
    }


    protected function addRelations(string $model, string $model_path) :void
    {
        $default_model = '';


        $fields =  $this->crud->getFields();

        foreach ($fields as $field) {
            $related_model = '';

            [$model_stub, $related_stub] = $field->getModelAndRelatedModelStubs();


            if ($field->isSimpleRelation()) {
                $data_map = array_merge(
                    $this->getParsedName(),
                    $field->parseRelationName(),
                    ['{{morphFieldName}}' => $field->getName()],
                    ['{{morphRelationableName}}' => $field->getMorphRelationableName()],
                );

                $default_model  .=  $this->crud->filesystem->compliedFile($model_stub, false, $data_map);
                $related_model  .= $this->crud->filesystem->compliedFile($related_stub, false, $data_map);

                // add local foreign key
                if ($field->isSimpleOneToOneRelation() || $field->isSimpleOneToManyRelation()) {
                    $related_path = $field->getRelationRelatedModelPath($data_map);

                    $related_model = $this->addSimpleOneToOneRelationLocalKeys($related_model, $field, $data_map);
                    $default_model = $this->addSimpleOnToOneRelationForeingKeys($default_model, $field, $data_map);

                } else if ($field->isSimpleManyToManyRelation()) {
                    $related_path = $field->getRelationRelatedModelPath();

                    [$related_model, $default_model] = $this->addSimpleManyToManyRelationForeignKeys($default_model, $related_model, $field, $data_map);
                }


            } else if ($field->isPolymorphicRelation()) {

                $data_map = array_merge(
                    $this->getParsedName(),
                    $field->parseRelationName(),
                    [
                        '{{morphFieldName}}' => $field->getName(),
                        '{{pluralMorphFieldName}}' => Str::plural($field->getName()),
                        '{{morphRelationableName}}' => $field->getPolymorphicRelationMorphName()
                    ],
                );


                if ($field->isPolymorphicOneToOneRelation() || $field->isPolymorphicOneToManyRelation()){
                    $default_model .= $this->crud->filesystem->compliedFile($model_stub, false, $data_map);
                }
                elseif ($field->isPolymorphicManyToManyRelation()) {
                    $related_path = $field->getRelationRelatedModelPath();

                    $default_model .= $this->crud->filesystem->compliedFile($model_stub, false, $data_map);
                    $related_model .= $this->crud->filesystem->compliedFile($related_stub, false, $data_map);

                }

            }else if ($field->isPolymorphic()){
                $data_map = array_merge($this->getParsedName(), ['{{morphFieldName}}' => $field->getPolymorphicMorphName()]);

                $default_model .= $this->crud->filesystem->compliedFile($this->crud->getCrudTemplatePath('/models/morphTo.stub'), true, $data_map);
            }


            if (!empty($related_model) && !empty($related_path)){
               $this->writeRelatedRelationModel($field, $related_model, $related_path);
            }

        }


        // default model
        if (!empty($default_model)) {
           $this->writeDefaultRelationModel($default_model, $model_path, $model);
        }

    }

    protected function getPolymorphicRelationStub(string $stub_name) :string
    {
        return $this->crud->getCrudTemplatePath("/models/relations/polymorphic/{$stub_name}.stub");
    }

    protected function addSimpleOnToOneRelationForeingKeys(string $default_model, Field $field, array $data_map) :string
    {
         if ($related_keys = $field->getRelationRelatedForeignKey()) {
            $replace = '';
            if ($related_foreing_key = Arr::get($related_keys, 'foreign_key')) {
                $replace .= ", '{$related_foreing_key}'";
            }
            if ($related_local_key = Arr::get($related_keys, 'owner_key')) {
                $replace .= ", '{$related_local_key}'";
            }

            $search =  $data_map['{{relatedSingularClass}}'] . '::class';
            if (!empty($replace)) {
                $default_model = str_replace($search,   $search . $replace, $default_model);
            }
        }

        return $default_model;
    }


    protected function addSimpleOneToOneRelationLocalKeys(string $related_model,Field $field, array $data_map) :string
    {
        if ($local_keys = $field->getRelationLocalForeignKey()) {
            $replace = '';

            if ($local_foreing_key = Arr::get($local_keys, 'foreign_key')) {
                $replace .= ", '{$local_foreing_key}'";
            }
            if ($local_local_key = Arr::get($local_keys, 'local_key')) {
                $replace .= ", '{$local_local_key}'";
            }

            $search =  $data_map['{{modelSingularClass}}'] . '::class';
            if (!empty($replace)) {
                $related_model = str_replace($search,   $search . $replace, $related_model);
            }
        }

        return $related_model;
    }

    protected function addSimpleManyToManyRelationIntermediateTableBeforeKeys(Field $field, string $model, string $search) :array
    {
        $intermediate_table = $field->guestIntermediateTableName(true);
        $search =  $search . '::class';;

        return [
            $intermediate_table,
            str_replace($search,   $search . ", '{$intermediate_table}'", $model),
        ];
    }

    protected function addManyToManyRelationLocalKeys(Field $field, string $related_model, array $data_map) :string
    {
        if (!$field->getRelationLocalForeignKey()){
            return $related_model;
        }

        $local_keys = $field->getRelationLocalForeignKey();

        $replace = '';
        /**
         * If the intermediate table is not defined, we guess it because to pass the foreign keys
         * you need the table name
         */
        [$intermediate_table, $related_model] = $this->addSimpleManyToManyRelationIntermediateTableBeforeKeys($field, $related_model,  $data_map['{{modelSingularClass}}']);


        if (
            Arr::exists($local_keys, 'join_key') &&
            Arr::exists($local_keys, 'foreign_key')
        ) {

            $local_join_key = Arr::get($local_keys, 'join_key');
            $local_foreign_key = Arr::get($local_keys, 'foreign_key');

            $replace .= ", '{$local_join_key}'";
            $search =  $data_map['{{modelSingularClass}}'] . '::class,' . " '$intermediate_table'";
            $related_model = str_replace($search,   $search . ", '{$local_join_key}'", $related_model);


            $replace .= ", '{$local_foreign_key}'";
            $search =  $data_map['{{modelSingularClass}}'] . '::class,' . " '$intermediate_table'" . ", '$local_join_key'";
            $related_model = str_replace($search,   $search . ", '{$local_foreign_key}'", $related_model);
        }

        return $related_model;

    }

    protected function addManyToManyRelationForeignKeys(Field $field, string $default_model, array $data_map) :string
    {
        if (!$field->getRelationRelatedForeignKey()) {
            return $default_model;
        }

        $related_keys = $field->getRelationRelatedForeignKey();

        $replace = '';

        [$intermediate_table, $default_model] = $this->addSimpleManyToManyRelationIntermediateTableBeforeKeys($field, $default_model,  $data_map['{{relatedSingularClass}}']);


        if (Arr::get($related_keys, 'join_key') && Arr::get($related_keys, 'foreign_key')) {

            $related_foreign_key = Arr::get($related_keys, 'foreign_key');
            $related_join_key = Arr::get($related_keys, 'join_key');


            $replace .= ", '{$related_foreign_key}'";
            $search =  $data_map['{{relatedSingularClass}}'] . '::class,' . " '$intermediate_table'" . ", '$related_join_key'";


            $related_join_key = Arr::get($related_keys, 'join_key');
            $replace .= ", '{$related_join_key}'";
            $search =  $data_map['{{relatedSingularClass}}'] . '::class,' . " '$intermediate_table'";
            $default_model = str_replace($search,   $search . ", '{$related_join_key}'", $default_model);

            $default_model = str_replace($search,   $search . ", '{$related_foreign_key}'", $default_model);
        }

        return $default_model;
    }

    protected function addSimpleManyToManyRelationForeignKeys(string $default_model, string $related_model, Field $field, array $data_map) :array
    {
        $related_model = $this->addManyToManyRelationLocalKeys($field, $related_model, $data_map);
        $default_model = $this->addManyToManyRelationForeignKeys($field, $default_model, $data_map);


        if (
            !$field->getRelationLocalForeignKey() &&
            !$field->getRelationRelatedForeignKey() &&
            $field->getRelationIntermediateTable()
        ) {
            $intermediate_table = $field->getRelationIntermediateTable(true);

            $search =  $data_map['{{relatedSingularClass}}'] . '::class';
            $default_model = str_replace($search,   $search . ", '{$intermediate_table}'", $default_model);

            $search =  $data_map['{{modelSingularClass}}'] . '::class';;
            $related_model = str_replace(
                $search,
                $search . ", '{$intermediate_table}'",
                $related_model
            );
        }


        return [$related_model, $default_model];
    }


	protected function writeDefaultRelationModel(string $default_model, string $model_path, string $model) :void
	{
        $model = $this->addDefaultRelationModelUseNamespaceOnTop($model);

        $search = '// add relation methods below';
        $complied = str_replace($search,   $search . PHP_EOL. PHP_EOL . $default_model . PHP_EOL . PHP_EOL, $model);
        $this->crud->filesystem->writeFile(
            $model_path,
            $complied
        );
	}

    protected function addDefaultRelationModelUseNamespaceOnTop( string $model) :string
    {
        foreach ($this->crud->getRelationFields() as $field) {
            if ($this->crud->modelAndRelatedModelAreInTheSameFolder($field->getRelationRelatedModel())){
                continue;
            }

            $search = "namespace {$this->getUseNamespace($this->crud->getModelWithNamespace())};";

            $model =   str_replace($search, $search . PHP_EOL . PHP_EOL . "use {$field->getRelationRelatedModel()};", $model);

        }

        return $model;
    }

    protected function getUseNamespace(string $related_class) :string
    {
         // on veut récupérer tous les elements du tableau sauf le dernier
        $namespace = explode('\\', $related_class);
        unset($namespace[array_key_last($namespace)]);
        $namespace = join('\\', $namespace);

        return $namespace;
    }

    protected function addRelatedModelUseNamespaceOnTop(Field $field, string $related_model) :string
    {
        $related_class = $field->getRelationRelatedModel();

        if ($this->crud->modelAndRelatedModelAreInTheSameFolder($related_class)) {
            return $related_model;
        }

        $search = "namespace {$this->getUseNamespace($related_class)};";

        return str_replace($search, $search . PHP_EOL. PHP_EOL . "use {$this->crud->getModelWithNamespace()};", $related_model);
    }

    protected function writeRelatedRelationModel(Field $field, string $related_model, string $related_path) :void
    {
        $related_model_stub = $this->crud->filesystem->get($related_path);

        $related_model_stub = $this->addRelatedModelUseNamespaceOnTop($field, $related_model_stub);

        // related model
        $search = '// add relation methods below';
        $this->crud->filesystem->replaceAndWriteFile(
            $related_model_stub,
            $search,
            $search . PHP_EOL . PHP_EOL . $related_model,
            $related_path
        );
    }

	protected function writeModel(string $model_path, string $model) :void
	{
        if (!$this->crud->filesystem->exists($model_path)) {
            $this->crud->filesystem->writeFile(
                $model_path,
                $model
            );
        }
	}

	protected function getModelPath() :string
	{
        $data_map = $this->data_map;
        $model_path = app_path(sprintf("%s/%s.php", $data_map['{{modelsFolderWithSubFolder}}'], $data_map['{{singularClass}}']));

        return $model_path;
	}
}

