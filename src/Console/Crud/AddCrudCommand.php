<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Guysolamour\Administrable\Console\Crud\MakeCrudTrait;

class AddCrudCommand extends BaseCrudCommand
{
    use MakeCrudTrait;

    /**
     * @var array
     */
    protected $field_to_create = [];

    /**
     * @var boolean
     */
    protected $migrate = true;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:append:crud
                             {model : Model name }
                             {--f|fields= : Fields to append }
                             {--m|migrate=true : Run artisan migrate command }
                             ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add field to existed crudtab model';



    public function handle()
    {
        $this->model = $this->argument('model');
        $this->theme = config('administrable.theme');

        $this->migrate = $this->option('migrate') === 'true' ? true : false;


        if (!$this->modelsExists()) {
            $this->triggerError(
                sprintf("The [%s] model is not defined in [%s] file", $this->model, base_path('administrable.yml'))
            );
        }

        $this->data_map = $this->parseName($this->model);


        // Sanitize data
        $this->fields_names_to_create = $this->getOptionsFilteredData($this->option('fields'));

        if (empty($this->fields_names_to_create)) {
            $fields_names_to_create = array_values($this->guestFieldsToCreate());

            if (empty($fields_names_to_create)){
                $this->triggerError("You have to defined new fields in config file before. You can --fields option to specify which field you want to append");
            }

            $this->fields_names_to_create = $this->choice(
                'Wich field do you want to append ? (fields can be separated with comma)',
                $fields_names_to_create,
                array_key_first($fields_names_to_create), null, true
            );
        }

        $this->checkIfFieldHasAlreadyBeenAddedd();

        $this->fields_to_create = $this->getFieldsToCreate();


        $this->addFieldToModel();

        $this->addFieldToForm();

        $this->addFieldToViews();

        $this->addFieldToSeeder();

        $this->addFieldToMigration();

    }

    protected function getModelInstance() :\Illuminate\Database\Eloquent\Model
    {
        $model =  sprintf("%s\%s\%s", $this->data_map['{{namespace}}'], $this->data_map['{{modelsFolder}}'], $this->data_map['{{singularClass}}']);

        return new $model;
    }

    protected function guestFieldsToCreate() :array
    {
        $config_fields = $this->getCrudConfiguration(ucfirst($this->model));
        $clean_field_names = Arr::pluck($this->getCleanFields($config_fields), 'name');


        // remove daterange field
        $persisted_field_names = $this->getPersistedFieldNames();

        // remove persisted field
        $field_to_create = array_filter($clean_field_names, fn ($field_name) => !in_array($field_name, $persisted_field_names));

        // remove daterange field
        $field_to_create = array_filter($field_to_create, fn ($field_name) => !$this->checkIfDaterangeFieldHasBeenAdded($field_name, $persisted_field_names));

        return $field_to_create;
    }

    /**
     *
     * @param string $path
     * @return boolean
     */
    protected function checkIfCastsAttributeExistsOnModel(string $path) :bool
    {
        return preg_match('#protected \$casts = \[#', $this->filesystem->get($path));
    }

    /**
     *
     * @param string $path
     * @return boolean
     */
    protected function checkIfDaterangeTraitHasBeenImported(string $path) :bool
    {
        return preg_match('#\\DaterangeTrait#', $this->filesystem->get($path));
    }

    protected function appendFieldToFillable(string $model_stub, array $field, string $key) :string
    {
        // gerer le cas du fillable
        $search = 'public $fillable = [';

        if ($this->isDaterangeField($field)) {
            $model_stub = str_replace($search, $search . "'{$this->getRangeStartFieldName($field)}'," . "'{$this->getRangeEndFieldName($field)}',", $model_stub);
        } else {
            $model_stub = str_replace($search, $search . "'{$this->getFieldName($field)}',", $model_stub);
        }

        return $model_stub;
    }

    /**
     * @param string $model_stub
     * @param array $field
     * @param string $key
     * @return string
     */
    protected function appendFieldInDaterangesPickersArray(string $model_stub, array $field, string $key) :string
    {
        $search = "protected $key = [";

        $replace = "          '{$this->getFieldName($field)}',";

        return  str_replace($search, $search . PHP_EOL . '  ' . $replace, $model_stub);
    }

    /**
     * @param string $model_stub
     * @param array $field
     * @param string $key
     * @return string
     */
    protected function addFieldInDaterangesPickersArray(string $model_stub, array $field, string $key) :string
    {
        $replace = <<<TEXT
                        // The date {$key} configuration array for this model.
                            protected $key = [

                        TEXT;

        $replace .= <<<TEXT
                                '{$this->getFieldName($field)}',
                        TEXT;

        $replace .= <<<TEXT

                            ];
                        TEXT;

        $search = "// add relation methods below";

        return  str_replace($search, $replace . PHP_EOL . PHP_EOL . '  ' . $search, $model_stub);
    }

    /**
     * @param string $model_stub
     * @param array $field
     * @param string $path
     * @return string
     */
    protected function importDaterangeTraitAndAttributes(string $model_stub, array $field, string $path) :string
    {
        if ($this->isDatepickerField($field) || $this->isDaterangeField($field)) {
            if ($this->checkIfDaterangeTraitHasBeenImported($path)) {
                if ($this->isDatepickerField($field)) {
                    $model_stub = $this->appendFieldInDaterangesPickersArray($model_stub, $field, '$datepickers');
                } else {
                    $model_stub = $this->appendFieldInDaterangesPickersArray($model_stub, $field, '$dateranges');
                }
            } else {
                // add trait
                $search = '// The attributes that are mass assignable.';
                $model_stub = str_replace($search, 'use DaterangeTrait;' . PHP_EOL . PHP_EOL . '    ' . $search, $model_stub);

                // add namespace on the top
                $search = "use {$this->data_map['{{namespace}}']}\Traits\ModelTrait;";
                $replace = "use {$this->data_map['{{namespace}}']}\Traits\DaterangeTrait;";
                $model_stub = str_replace($search, $search . PHP_EOL .  $replace, $model_stub);

                // add attributes
                if ($this->isDatepickerField($field)) {
                    $model_stub = $this->addFieldInDaterangesPickersArray($model_stub, $field, '$datepickers');
                } else {
                    $model_stub = $this->addFieldInDaterangesPickersArray($model_stub, $field, '$dateranges');
                }
            }
        }

        return $model_stub;
    }

    /**
     * @param string $model_stub
     * @param array $field
     * @param string $path
     * @return string
     */
    protected function addCastableField(string $model_stub, array $field, string $path) :string
    {
        if ($cast = $this->getFieldCast($field)) {
            if ($this->checkIfCastsAttributeExistsOnModel($path)) {
                $search = 'protected $casts = [';

                if ($this->isDaterangeField($field)) {
                    $replace = "'{$this->getRangeStartFieldName($field)}' => '{$cast}',
        '{$this->getRangeEndFieldName($field)}' => '{$cast}', ";
                } else {
                    $replace = "'{$this->getFieldName($field)}' => '{$cast}',";
                }
                // gerer le cas du boolean plus tars
                $model_stub = str_replace($search, $search . PHP_EOL . "        {$replace}", $model_stub);
            } else {

                $search = '// add relation methods below';
                $template = <<<TEXT
                        // The attributes that should be cast to native types.
                            protected \$casts = [
                        TEXT;
                if ($this->isDatepickerField($field)) {
                    $template .= "
            '{$this->getFieldName($field)}' => '{$cast}',";
                } else if ($this->isDaterangeField($field)) {
                    $template .= "
            '{$this->getRangeStartFieldName($field)}' => '{$cast}',
            '{$this->getRangeEndFieldName($field)}' => '{$cast}',";
                }
                $template .= "
    ];";

                $model_stub = str_replace($search, $template . PHP_EOL . '    ' .  $search, $model_stub);
            }

        }
        return $model_stub;
    }

    protected function addFieldToModel()
    {

        $path = app_path(sprintf("%s/%s.php", $this->data_map['{{modelsFolder}}'], $this->data_map['{{singularClass}}']));

        foreach ($this->fields_to_create as $field) {

            if ($this->isRelationField($this->getNonRelationType($field))){
                $this->triggerError(
                    sprintf("The [%s] model can not have a relation [%s] field", $this->model, $this->getFieldName($field))
                );
            }

            $model_stub = $this->filesystem->get($path);

            // on ajoute le cast
            $model_stub = $this->addCastableField($model_stub, $field, $path);

            $model_stub = $this->importDaterangeTraitAndAttributes($model_stub, $field, $path);

            $model_stub = $this->appendFieldToFillable($model_stub, $field, $path);

            $this->writeFile($model_stub, $path);
        }

        $this->addRelations($model_stub, $path, $this->fields_to_create);

        $this->triggerSuccess('Fields added to model' . $path);
    }

    protected function addFieldToMigration()
    {
        $migration_stub = $this->TPL_PATH . '/migrations/add.stub';

        $data_map = array_merge($this->data_map, [
            '{{migrationTableName}}' => $this->getCurrentModelTableName(),
            '{{migrationFileName}}' => ucfirst($this->getMigrationFileName() . 'Table')
        ]);
        $migration = $this->compliedFile($migration_stub, true, $data_map);
        $path = $this->generateMigrationFields($migration, $data_map, $this->fields_to_create, $data_map['{{migrationFileName}}'], true);

        $this->triggerSuccess('Fields added to migration' . $path);

        // Migrate
        if ($this->migrate) {
            $this->info(PHP_EOL . 'Migrate...');
            $this->call('migrate');
        }

    }

    protected function addFieldToForm()
    {
        $fields =  $this->getFormFields($this->fields_to_create);
        $form_path = app_path('Forms/' . $this->data_map['{{backNamespace}}'] . "/{$this->data_map['{{singularClass}}']}Form.php");

        if ($this->filesystem->exists($form_path)){
            $this->registerFormFields($fields, $this->filesystem->get($form_path), $form_path);
            $this->triggerSuccess('Fields added to form' . $form_path);
        }
    }

    /**
     * @param string $path
     * @return void
     */
    protected function appendFieldToIndexView(string $path) :void
    {
        $index_view_path = $path . 'index.blade.php';

        if (!$this->filesystem->exists($index_view_path)){
            return;
        }

        $index_view = $this->filesystem->get($index_view_path);
        [$fields, $values] = $this->getIndexViewFields($this->fields_to_create, $this->data_map);

        $search = '{{-- add fields here --}}';
        $index_view = str_replace($search, $fields . PHP_EOL . $search, $index_view);

        $search = '{{-- add values here --}}';
        $index_view = str_replace($search, $values . PHP_EOL . $search, $index_view);

        if ($this->isTheadminTheme()) {
            $values = str_replace('<td>', '<p>', $values);
            $values = str_replace('</td>', '</p>', $values);
            $search = '{{-- add quick values here --}}';
            $index_view = str_replace($search, $values . PHP_EOL . $search, $index_view);
        }

        $this->writeFile($index_view, $index_view_path);

        $this->triggerSuccess('Fields added to view' . $index_view_path);
    }
    /**
     * @param string $path
     * @return void
     */
    protected function appendFieldToShowView(string $path) :void
    {
        $show_view_path = $path . 'show.blade.php';

        if (!$this->filesystem->exists($show_view_path)){
            return;
        }

        $show_view = $this->filesystem->get($show_view_path);

        $fields = $this->getShowViewFields($this->fields_to_create, $this->data_map, false);

        $search = '{{-- add fields here --}}';
        $show_view = str_replace($search, $fields . PHP_EOL . $search, $show_view);


        $this->writeFile($show_view, $show_view_path);
        $this->triggerSuccess('Fields added to view' . $show_view_path);
    }

    protected function appendDatepickerToFormView(string $path)
    {
        $show_view_path = $path . '_form.blade.php';

        if (!$this->filesystem->exists($show_view_path)){
            return;
        }

        $show_view = $this->addDatepickerAndDaterange($this->filesystem->get($show_view_path), $this->fields_to_create, $this->data_map);

        $this->writeFile($show_view, $show_view_path);
        $this->triggerSuccess('Fields added to view' . $show_view_path);
    }

    protected function addFieldToViews()
    {
        $path = resource_path("views/{$this->data_map['{{backLowerNamespace}}']}/{$this->data_map['{{pluralSlug}}']}/");

        // index view
        $this->appendFieldToIndexView($path);

        // show view
        $this->appendFieldToShowView($path);

        // update form if datepicker or daterange field
        $this->appendDatepickerToFormView($path);

    }

    protected function addFieldToSeeder()
    {
        $seeder_path = database_path("/seeders/" . $this->data_map['{{pluralClass}}'] . 'TableSeeder.php');

        if ($this->filesystem->exists($seeder_path)){
            $seeder = $this->generateSeederFields($this->filesystem->get($seeder_path), $this->fields_to_create);

            $this->writeFile(
                $seeder,
                $seeder_path
            );
        }

    }

    protected function getMigrationFileName() :string
    {
        $names = Arr::pluck($this->fields_to_create, 'name');

        $file_name = 'Add';

        foreach ($names as $name) {
            if (Str::endsWith($name, '_id')){
                $name = Str::before($name, '_id');
            }
            $file_name .= ucfirst($name . 'And');
        }

        $file_name = rtrim($file_name, 'And') .  "FieldsTo{$this->data_map['{{pluralClass}}']}";

        return  Str::camel($file_name);
    }

    protected function getFieldsToCreate() :array
    {
        $config_fields = $this->getCrudConfiguration(ucfirst($this->model));
        $this->fields = $this->getCleanFields($config_fields);

        foreach($this->fields_names_to_create as $field_name){
            if (!Arr::exists($this->fields, $field_name)) {
                $this->triggerError(
                    sprintf("The [%s] field is not defined in [%s] model.", $field_name, $this->model)
                );
            }
        }

        return array_filter($this->fields, fn ($field) => in_array($this->getFieldName($field) ?? '', $this->fields_names_to_create));
    }

    protected function checkIfDaterangeFieldHasBeenAdded(string $field_name, ?array $persisted_field_names = null) :bool
    {
        $persisted_field_names ??= $this->getPersistedFieldNames();

        foreach ($persisted_field_names as $persisted_field_name) {
            // si le champ se termine par start_at ou _end_at et qu'il commence par le nom du champ en cours
            // cela signifie qu'il a déjà été ajouté
            if (
                (Str::endsWith($persisted_field_name, config('administrable.daterange.start')) ||
                Str::endsWith($persisted_field_name, config('administrable.daterange.end'))) &&
                Str::startsWith($persisted_field_name, $field_name)
            ){
                return true;
            }
        }

        return false;
    }


    protected function getPersistedFieldNames() :array
    {
        return Schema::getColumnListing($this->getCurrentModelTableName());
    }

    protected function checkIfFieldHasAlreadyBeenAddedd()
    {
        $persisted_field_names = $this->getPersistedFieldNames();

        foreach($this->fields_names_to_create as $field_name){
            if (in_array($field_name, $persisted_field_names) || $this->checkIfDaterangeFieldHasBeenAdded($field_name, $persisted_field_names)){
                $this->triggerError("The {$field_name} field has already been defined in {$this->model} model.");
            }

        }
    }

    protected function isConfigurationModel(?string $model = null) :bool
    {
        $model ??= $this->data_map['{{singularSlug}}'];

        return Str::lower($model) == 'configuration';
    }



}
