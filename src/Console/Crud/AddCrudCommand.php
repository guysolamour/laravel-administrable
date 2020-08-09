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
            $this->triggerError("The --field option  is required");
        }

        $this->checkIfFieldHasAlreadyBeenAddedd();


        $this->fields_to_create = $this->getFieldsToCreate();

        $this->addFieldToModel();

        $this->addFieldToForm();

        $this->addFieldToViews();

        $this->addFieldToSeeder();

        $this->addFieldToMigration();

    }

    protected function addFieldToModel()
    {

        $path = app_path(sprintf("%s/%s.php", $this->data_map['{{modelsFolder}}'], $this->data_map['{{singularClass}}']));

        foreach ($this->fields_to_create as $field) {

            if ($this->isRelationField($this->getNonRelationType($field))){
                $this->triggerError(
                    sprintf("The [%s] model can not have a relation [%s] field",$this->model, $this->getFieldName($field))
                );
            }
            // add field in fillable property
            $this->replaceAndWriteFile(
                $this->filesystem->get($path),
                $search = "public \$fillable = [",
                $search . "'$field[name]',",
                $path
            );
        }

        $this->addRelations($this->filesystem->get($path), $path, $this->fields_to_create);

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
        $path = $this->generateMigrationFields($migration, $data_map, $this->fields_to_create, $this->getMigrationFileName(), true);

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

    protected function addFieldToViews()
    {
        $path = resource_path("views/{$this->data_map['{{backLowerNamespace}}']}/{$this->data_map['{{pluralSlug}}']}/");

        if ($this->filesystem->exists($index_view_path = $path . 'index.blade.php')) {
            $index_view = $this->filesystem->get($index_view_path);
            [$fields, $values] = $this->getIndexViewFields($this->fields_to_create, $this->data_map);

            $search = '{{-- add fields here --}}';
            $index_view = str_replace($search, $fields. PHP_EOL . $search, $index_view);


            $search = '{{-- add values here --}}';
            $index_view = str_replace($search, $values . PHP_EOL . $search, $index_view);

            if ($this->isTheadminTheme()){
                $values = str_replace('<td>', '<p>', $values);
                $values = str_replace('</td>', '</p>', $values);
                $search = '{{-- add quick values here --}}';
                $index_view = str_replace($search, $values . PHP_EOL . $search, $index_view);
            }


            $this->writeFile($index_view, $index_view_path);

            $this->triggerSuccess('Fields added to view' . $index_view_path);
        }
        if ($this->filesystem->exists($show_view_path = $path . 'show.blade.php')) {
            $show_view = $this->filesystem->get($show_view_path);

            $fields = $this->getShowViewFields($this->fields_to_create, $this->data_map, false);

            $search = '{{-- add fields here --}}';
            $show_view = str_replace($search, $fields. PHP_EOL . $search, $show_view);

            $this->writeFile($show_view, $show_view_path);
            $this->triggerSuccess('Fields added to view' . $show_view_path);
        }

        $configuration_edit_view_path = resource_path("views/{$this->data_map['{{backLowerNamespace}}']}/{$this->data_map['{{singularSlug}}']}/");

        if (
            $this->isConfigurationModel() &&
            $this->filesystem->exists($edit_view_path = $configuration_edit_view_path . 'edit.blade.php')){

            $configuration_edit_view = $this->filesystem->get($edit_view_path);

            $fields = $this->getEditViewFields($this->fields_to_create, $this->data_map);

            $search = '{{-- add fields here --}}';
            $configuration_edit_view = str_replace($search, $fields . PHP_EOL . $search, $configuration_edit_view);

            $this->writeFile($configuration_edit_view, $edit_view_path);
            $this->triggerSuccess('Fields added to view' . $edit_view_path);
        }

    }

    protected function addFieldToSeeder()
    {
        $seeder_path = database_path("/seeds/" . $this->data_map['{{pluralClass}}'] . 'TableSeeder.php');

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

        return array_filter($this->fields, fn ($item) => in_array($item['name'] ?? '', $this->fields_names_to_create));
    }

    protected function checkIfFieldHasAlreadyBeenAddedd()
    {
        $existed_field_names = Schema::getColumnListing($this->getCurrentModelTableName());

        foreach($this->fields_names_to_create as $field_name){
            if (in_array($field_name, $existed_field_names)){
                $this->triggerError("The {$field_name} field has already been defined in {$this->model} model.");
            }
        }
    }

    protected function isConfigurationModel(?string $model = null) :bool
    {
        if (is_null($model)){
            $model = $this->data_map['{{singularSlug}}'];
        }

        return Str::lower($model) == 'configuration';

    }



}
