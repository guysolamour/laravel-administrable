<?php

namespace Guysolamour\Administrable\Console;

use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;

trait CommandTrait
{


    protected $models_config = null;

    /**
     * @var string
     */
    protected  $TPL_PATH = __DIR__ . '/../stubs/crud';

    /**
     * @var string
     */
    protected $IMAGEMANAGER = 'imagemanager';

    /**
     * @var string[]
     */
    protected $ACTIONS = ['index', 'show', 'create', 'edit', 'delete'];

    /**
     * @var string[]
     */
    protected $ONDELETERULES = ['cascade' => 'cascade', 'setnull' => 'set null'];


    /**
     * @var string[]
     */
    protected $TYPES = [
        'string', 'text', 'mediumText', 'longText', 'json',
        'date', 'datetime',
        'boolean', 'enum',
        'decimal', 'float', 'double',
        'integer', 'mediumInterger', 'bigInteger',
        'ipAdress', 'image', 'relation',
        'polymorphic'
    ];


    /**
     * @var array
     */
    protected $RELATION_NAMES = [
        'simple'      => ['o2o' => 'one to one', 'o2m' => 'one to many', 'm2m' => 'many to many',],
        'polymorphic' => ['o2o' => 'one to one', 'o2m' => 'one to many'],
    ];

    /**
     * @var array
     */
    protected $RELATION_TYPES = [
        'simple'      => 'simple',
        'polymorphic' => 'polymorphic'
    ];

    /**
     * @var array
     */
    protected $RELATION_CONSTRAINTS = [
        'cascade' => 'cascade',
        'setnull' => 'set null'
    ];


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

    protected function parseConfigurationYamlFile()
    {
        if (!$this->models_config) {
            $this->models_config = Yaml::parseFile(base_path('administrable.yaml'));
        }

        return $this->models_config;
    }


    protected function getCrudConfiguration(string $key, $default = null)
    {
        return  Arr::get($this->parseConfigurationYamlFile(), $key, $default);
    }

    protected function getAllCrudConfigModels()
    {
        $models = [];

        foreach ($this->parseConfigurationYamlFile() as $key => $value) {
            if (!is_array($value) || !Arr::isAssoc($value)) continue;

            $models[] = $key;
        }
        return $models;
    }

    protected function getUnusedCrudConfigModels()
    {
        $models = [];

        foreach ($this->getAllCrudConfigModels() as $value) {
            $path = app_path($this->getCrudConfiguration('folder', 'Models') . '/' . ucfirst($value) . '.php');
            if ($this->filesystem->exists($path)) continue;

            $models[] = $value;
        }
        return $models;
    }



    /**
     *
     * @param string $file // Must be the path or the file content
     * @param boolean $get_content
     * @return string
     */
    protected function compliedFile(string $file, bool $get_content = true, ?array $data_map = null): string
    {
        $file = $get_content ? $this->filesystem->get($file) : $file;
        $data_map = $data_map ?: $this->parseName();

        return strtr($file, $data_map);
    }


    /**
     * @param string|object|array $files
     * @param string $path
     * @return void
     */
    protected function compliedAndWriteFile($files, string $path, string $filename_prefix = ''): void
    {
        
        if (is_array($files)) {
            foreach ($files as $file) {
                $this->compliedAndWriteFile($file, $path, $filename_prefix);
            }
            return;
        }

        $data_map = $this->parseName();

        $stub = $this->isSingleFile($files) ? $files : $this->filesystem->get($files->getRealPath());

        $this->createDirectoryIfNotExists(
            $path,
            !$this->isSingleFile($files)
        );
        $complied = strtr($stub, $data_map);

        $this->writeFile(
            $complied,
            $this->isSingleFile($files) ? $path : $path . '/' . $filename_prefix . $files->getFilenameWithoutExtension() . '.php'
        );
    }


    protected function isSingleFile($file): bool
    {
        return is_string($file);
    }

    /**
     * @param mixte $complied
     * @param string $path
     * @return void
     */
    protected function writeFile($complied, string $path, bool $overwrite = true): bool
    {

        if ($overwrite) {
            $this->filesystem->put(
                $path,
                $complied
            );
            return true;
        }

        if (!$this->filesystem->exists($path)) {
            $this->filesystem->put(
                $path,
                $complied
            );
            return true;
        }

        return false;
    }


    /**
     * Create a folder
     * @param string $path
     * @param boolean $folder Used to find out if the path passed is a folder or file
     * @return void
     */
    protected function createDirectoryIfNotExists(string $path, bool $folder = true): void
    {

        $dir = $folder ? $path : $this->filesystem->dirname($path);

        if ($this->filesystem->missing($dir)) {
            $this->filesystem->makeDirectory($dir, 0755, true);
        }
    }



    protected function isImageableModel(array $fields)
    {
        return array_key_exists($this->IMAGEMANAGER, $fields);
    }

    protected function isImageableField(string $field): bool
    {
        return $field === $this->IMAGEMANAGER;
    }



    /**
     * @param string|object|array $files
     * @param string $search
     * @param string $path
     * @return void
     */
    protected function replaceAndWriteFile($files, string $search, $replace, string $path)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                $this->replaceAndWriteFile($file, $search, $replace, $path);
            }
            return;
        }

        $stub = $this->isSingleFile($files) ? $files : $this->filesystem->get($files->getRealPath());
        // $stub = $this->filesystem->get($files->getRealPath());
        $this->createDirectoryIfNotExists(
            $path,
            !$this->isSingleFile($files)
        );
        $complied = str_replace($search, $replace,  $stub);

        $this->writeFile(
            $complied,
            $this->isSingleFile($files) ? $path : $path . '/' . $files->getFilenameWithoutExtension() . '.php'
        );
    }

    protected function hasPolymorphicField(): bool
    {
        foreach ($this->fields as $field) {
            if ($this->isPolymorphicField($field)) {
                return true;
            }
        }

        return false;
    }

    protected function getFieldName(array $field): ?string
    {
        return Str::lower(Arr::get($field, 'name'));
    }

    protected function getFieldRules(array $field): ?string
    {
        return Arr::get($field, 'rules');
    }


    protected function getFieldType($type): string
    {
        $type = is_array($type) ? $type['type'] : $type;

        if ($type === 'string' || $type === 'text' || $type === 'mediumText' || $type === 'json' || $type === 'longText') {
            return 'text';
        } else if ($type === 'integer' || $type === 'mediumInteger' || $type === 'bigInteger') {
            return 'int';
        } else if ($type === 'boolean' || $type === 'enum') {
            return 'bool';
        } else if (is_array($type)) {
            return 'relation';
        }

        return '';
    }

    protected function isBooleanField(array $field): bool
    {
        return 'bool' === $this->getFieldType($this->getNonRelationType($field));
    }

    protected function getFieldReferences(array $field): ?string
    {
        return Str::lower(Arr::get($field['type'], 'references'));
    }

    public function addFielsCustomProperty(array $field, string $key, $value, string $name, bool $force = false)
    {

        if ($force || !Arr::exists($field, $name)) {
            $field = Arr::prepend($field, $value, $name);
            $this->fields[$key] = $field;
        }

        return $field;
    }

    /**
     *
     * @param string|array $type
     * @return boolean
     */
    public function isTextField($type): bool
    {
        return 'text' == $this->getFieldType($type);
    }

    /**
     *
     * @param string|array $type
     * @return boolean
     */
    public function isIntegerField($type): bool
    {
        return 'int' == $this->getFieldType($type);
    }


    /**
     * @param string|array $type
     * @return bool
     */
    protected function isRelationField($type): bool
    {

        return is_array($type);
        // return is_array($type) && Arr::get($type, 'relation') ;
    }

    protected function getModelsFolder(): string
    {
        return ucfirst($this->getCrudConfiguration('folder', 'Models'));
    }


    protected function checkIfRelatedModelExists(string $model)
    {
        if (!class_exists($model)) {
            $this->triggerError("The related model [{$model}] does not exists. You need to create if first.");
        }

        return true;
    }

    protected function checkIfCrudHasAlreadyBeenDoneForModel(string $model): bool
    {
        $model_path = sprintf("%s\%s\%s.", $this->getNamespace(), $this->getCrudConfiguration('folder', 'Models'), ucfirst($model));

        if (class_exists($model_path)) {
            $this->triggerError("The model [{$model}] crud has already been done.");
        }

        return true;
    }


    protected function checkIfRelatedModelPropertyExists(array $field): bool
    {
        $table_name = $this->getRelatedModelTableName($field);

        return in_array($this->getRelatedModelProperty($field), Schema::getColumnListing($table_name));
    }

    protected function getRelatedModelTableName(array $field): string
    {
        $related_model = $this->getRelatedModel($field);
        return (new $related_model)->getTable();

        // if (Str::contains($related_model, '\\')) {
        //     $related_model = class_basename($related_model);
        // }

        // return Str::plural(Str::slug($related_model));
    }

    protected function getRelatedModel(array $field): string
    {
        return ucfirst(Arr::get($field['type'], 'related'));
    }

    protected function getRelationName(array $field): string
    {
        return Arr::get($field['type'], 'relation');
    }

    protected function getRelationRelatedForeignKey(array $field, ?string $key = null)
    {
        $value = Arr::get($field['type'], 'related_keys');

        return is_null($key) ? $value : Arr::get($value, $key);
    }



    protected function getRelationLocalForeignKey(array $field, ?string $key = null)
    {
        $value = Arr::get($field['type'], 'local_keys');

        return is_null($key) ? $value : Arr::get($value, $key);
    }


    protected function getIntermediateClassName(array $field): string
    {
        $class_name = Str::singular(Str::studly($this->getRelationIntermediateTable($field, true)));

        return sprintf("Create%sPivotTable", $class_name);
    }

    protected function getRelationIntermediateTable(array $field, bool $guest = false): ?string
    {
        $table_name = Arr::get($field['type'], 'intermediate_table');

        if (!$guest) {
            return $table_name;
        }

        return $this->guestIntermediataTableName($field);
    }



    protected function getRelationType(array $field): ?string
    {
        return Arr::get(Arr::get($field, 'type'), 'type');
    }

    protected function getNonRelationType(array $field)
    {
        return Arr::get($field, 'type');
    }

    protected function getRelatedModelProperty(array $field): ?string
    {
        return Arr::get($field['type'], 'property');
    }


    protected function getFieldConstraints(array $field)
    {
        return Arr::get($field, 'constraints');
    }

    protected function getFieldOnDelete(array $field): ?string
    {
        return Str::lower(Arr::get($field['type'], 'onDelete'));
    }

    protected function guestIntermediataTableName(array $field): string
    {
        $segments = [
            Str::snake($this->getRelatedModelWithoutNamespace($field)),
            Str::snake($this->model)
        ];

        sort($segments);

        return strtolower(implode('_', $segments));
    }

    protected function guestRelatedModelName(array $field): string
    {
        $related_model = $this->getRelatedModel($field);

        if ($related_model) {
            return $related_model;
        }

        $field_name = $this->getFieldName($field);

        if (Str::endsWith($field_name, '_id')) {
            $field_name = $this->getRelationModelWithoutId($field_name);
        }

        return sprintf("%s\%s\%s", $this->getNamespace(), $this->getModelsFolder(), ucfirst($field_name));
    }


    /**
     * @param string $name
     * @return string
     */
    protected function getRelationModelWithoutId(string $name): string
    {
        if (Str::endsWith($name, '_id')) {
            return Arr::first(explode('_', $name));
        }

        return $name;
    }

    protected function getRelatedModelWithoutNamespace(array $field): string
    {
        return $this->modelNameWithoutNamespace($this->getRelatedModel($field));
    }



    protected function modelNameWithoutNamespace(string $model): string
    {
        return class_basename($model);
    }



    protected function isPolymorphicField(array $field): bool
    {
        return ($this->getRelationType($field) === 'polymorphic') ||
            (Arr::exists($field, 'polymorphic') && Arr::get($field, 'polymorphic'));
    }

    protected function getPolymorphicModelId(array $field): ?string
    {
        return Str::lower(Arr::get($field, 'model_id'));
    }

    protected function getPolymorphicModelType(array $field): ?string
    {
        return Str::lower(Arr::get($field, 'model_type'));
    }

    protected function getMorphRelationableName(array $field): ?string
    {
        $value = Str::lower(Arr::get($field['type'], 'name'));

        return $value ?: $this->getFieldName($field) . 'able';
    }


    protected function runProcess(string $command)
    {
        $process = new Process(explode(' ', $command), null, null, null, 3600);

        $process->run(function ($type, $buffer) {
            // $this->getOutput()->write('> '.$buffer);
        });

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }
    }

    /**
     * @return string
     */
    protected function guestBreadcrumbFieldNane(): string
    {
        if ($this->breadcrumb) {
            return $this->breadcrumb;
        }

        foreach ($this->fields as $field) {
            if ($this->isTextField($field)) {
                return $this->getFieldName($field);
            }
        }
    }

    /**
     * @return boolean
     */
    protected function isTheadminTheme(): bool
    {
        return $this->isTheme('theadmin');
    }

    /**
     * @return boolean
     */
    protected function isThemeKitTheme(): bool
    {
        return $this->isTheme('themekit');
    }

    /**
     * @param string $theme
     * @return boolean
     */
    protected function isTheme(string $theme): bool
    {
        return $this->theme === $theme;
    }

    /**
     * @return string
     */
    protected function getGuard(): string
    {
        return config('administrable.guard', 'admin');
    }

    /**
     *
     * @return boolean
     */
    protected function checkIfPackageHasBeenInstalled(): bool
    {
        return $this->filesystem->exists(base_path('administrable.yaml'));
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param string $path
     * @param boolean $all
     * @return array
     */
    protected function getFilesFromDirectory(string $path, bool $all = true)
    {
        if (!$this->filesystem->exists($path)){
            return [];
        }

        return $all ? $this->filesystem->allFiles($path) : $this->filesystem->files($path);
    }


    // protected function getFileContent(string $path) :string
    // {
    //     if (!$this->filesystem->exists($path)) {
    //         return $this->filesystem->get($path);
    //     }

    // }

    protected function triggerError(string $error)
    {
        $this->error($error);
        exit();
    }

    protected function triggerSuccess(string $success)
    {
        $this->info($success);
    }

    protected function modelsExists(?string $model = null) :bool
    {
        if (!$model){
            $model = $this->model;
        }

        $data_map = $this->parseName($model);
        $path = app_path($data_map['{{modelsFolder}}'] . '/' . $data_map['{{singularClass}}']) . '.php';

        return $this->filesystem->exists($path);
    }




    protected function setGlobalConfigOption(array $options)
    {
        foreach ($options as $value) {
            $option = $this->getCrudConfiguration($value);

            if (is_null($option)){
                continue;
            }

            if (!is_bool($option) || is_null($option)) {
                throw new \Exception(
                    sprintf("The global [%s] option must be a boolean. Current value is [%s]", $value, $option)
                );
            }

            $this->$value = $option;
        }

    }



    protected function setConfigOption(): void
    {
        foreach (self::GLOBAL_OPTIONS as $option) {

            if (isset($this->fields[$option]) && is_array($this->fields[$option]) && in_array($option, self::RESERVED_WORDS)) {
                throw new \Exception(
                    "A field can not has [$option] for name. The [$option] word is reserved."
                );
            }



            if (isset($this->fields[$option]) && (is_bool($this->fields[$option]) || !empty($this->fields[$option]))) {
                if (array_key_exists($option, $this->fields)) {
                    if ($option === 'slug') {
                        $this->slug =  strtolower($this->fields['slug']);
                    } else {
                        $this->$option = $this->fields[$option];
                    }
                    $this->fields = Arr::except($this->fields, $option);
                }
            }
        }
    }

    protected function sanitizeFields()
    {
        foreach ($this->fields as $key =>  $field) {
            if (!is_array($field)) {
                $this->fields = Arr::except($this->fields, $key);
            }
        }
    }

    protected function setDefaultTypeAndRule()
    {
        foreach ($this->fields as $key => $field) {
            // We skip the actions to process them later
            if ($key === 'actions') {
                continue;
            }

            if (is_array($field)) {
                if (!Arr::exists($field ?? [], 'type')) {
                    $field['type'] = 'string';
                    $this->fields[$key] = $field;
                }

                if (!Arr::exists($field ?? [], 'rules')) {
                    $field['rules'] = '';
                    $this->fields[$key] = $field;
                }
            }
        }
    }

    protected function getCleanFields($config_fields)
    {
        if (!empty($config_fields)) {
            $this->fields = $config_fields;


            if (Arr::exists($this->fields, 'actions')) {
                $actions = Arr::get($this->fields, 'actions');

                if (!is_array($actions)) {
                    if (is_string($actions)) {
                        if (Str::contains($actions, '|')) {
                            $delimiter = '|';
                        } else if (Str::contains($actions, ',')) {
                            $delimiter = ',';
                        } else {
                            $delimiter = ',';
                        }

                        $actions = explode($delimiter, $actions);
                    } else {
                        $this->triggerError("The actions must be an array or string separated with [|] or [,]. Only one can be used.");
                    }
                }


                $this->actions = array_map(function ($action) {
                    if (!in_array($action, $this->ACTIONS)) {
                        $this->triggerError(
                            sprintf("[%s] action is not allowed. Allowed actions are [%s].", $action, join(',', $this->ACTIONS))
                        );
                    }

                    return trim($action);
                }, array_filter($actions));

                // We remove the actions from the list because it's already exists on the instance
                $this->fields  =  Arr::except($this->fields, 'actions');
            } else {
                $this->actions = $this->ACTIONS;
            }

            // on gere le cas du edit slug ici avnt de faire la configuration des options globaux
            // on stocke la configuration globale si elle a été définie
            // celui ci pourra ecraser sur un modele en particulier
            $this->setGlobalConfigOption(['clone', 'edit_slug', 'fillable']);

            // tester le truc de  si tableau et exception
            $this->setConfigOption();

            // Ajout du champ slug dans le formulaire
            // Il faut avoir un champ slug avant d'utiliser le edit_slug

            if ($this->edit_slug && !$this->slug) {
                $this->triggerError(
                    sprintf(
                        'You have to use edit_slug when the model is sluggable'
                    )
                );
            }

            $this->setDefaultTypeAndRule();

            $this->sanitizeFields();



            foreach ($this->fields as $key => $field) {
                if (!is_array($field)) {
                    continue;
                }
                // Adding the name key using the key if not provided
                $field = $this->addFielsCustomProperty($field, $key, $key, 'name');


                // allow req to be used for the required rule
                // be able to test if two rules are used in the same rule and thwow an exception
                // If the field is just req because it can also be in required dou the && of the condition
                $field_rule = $this->getFieldRules($field);
                if (!Str::contains($field_rule, 'required') && Str::contains($field_rule, 'req')) {
                    $this->fields[$key]['rules'] = Str::replaceFirst('req', 'required', $field_rule);
                }

                // Add length if passed in type
                $type = $field['type'];
                $delimiter = ':';


                if (!$this->isRelationField($field['type'])) {
                    if (Str::contains($type, $delimiter)) {

                        [$type, $length] = explode($delimiter, $field['type']);

                        if (!($this->isTextField($type) || $this->isIntegerField($type))) {
                            $this->triggerError(
                                sprintf(
                                    'The [%s] delimiter must only be used to specify text or integer field length. Occured in field [%s]',
                                    $delimiter,
                                    $field['name']
                                )
                            );
                        }

                        $field = $this->addFielsCustomProperty($field, $key, (int) $length, 'length');
                        $field = $this->addFielsCustomProperty($field, $key, $type, 'type', true);
                    }
                }

                if ($this->isPolymorphicField($field)) {


                    $poly_model_id = $this->getPolymorphicModelId($field);
                    $poly_model_type = $this->getPolymorphicModelType($field);

                    // add ---able_id et ....able_type fields
                    if (
                        !empty($poly_model_id) && !empty($poly_model_type)
                    ) {

                        if (!Str::endsWith($poly_model_id, 'able_id')) {
                            $this->triggerError(
                                sprintf(
                                    'The [%s] model_id must end with [able_id]',
                                    $poly_model_id
                                )
                            );
                        }

                        if (!Str::endsWith($poly_model_type, 'able_type')) {
                            $this->triggerError(
                                sprintf(
                                    'The [%s] model_type must end with [able_type]',
                                    $poly_model_type
                                )
                            );
                        }
                    } else {
                        $this->fields[$key]['model_id'] =  $this->getFieldName($field) . 'able_id';
                        $this->fields[$key]['model_type'] =  $this->getFieldName($field) . 'able_type';
                    }
                }


                if ($this->isRelationField($field['type'])) {

                    $type = $this->getRelationType($field);
                    if (!in_array($type, $this->RELATION_TYPES)) {
                        $this->triggerError(
                            sprintf(
                                'The [%s] relation type is not allowed. Allowed types are [%s]',
                                $type,
                                join(',', $this->RELATION_TYPES)
                            )
                        );
                    }

                    if ($constraint = $this->getFieldOnDelete($field)) {
                        if (!in_array($constraint, $this->RELATION_CONSTRAINTS)) {
                            $this->triggerError(
                                sprintf(
                                    'The [%s] relation constraint is not allowed. Allowed constraint are [%s]',
                                    $constraint,
                                    join(',', $this->RELATION_CONSTRAINTS)
                                )
                            );
                        }
                    }

                    if (!in_array($this->getRelationName($field), $this->RELATION_NAMES[$type])) {
                        $this->triggerError(
                            sprintf(
                                'The [%s] relation name is not allowed. Allowed names for the  [%s] type are [%s]',
                                $this->getRelationName($field),
                                $type,
                                join(',', $this->RELATION_NAMES[$type])
                            )
                        );
                    }

                    $related = $this->getRelatedModel($field);



                    if (empty($related)) {
                        $related = $this->guestRelatedModelName($field);

                        $this->fields[$key]['type']['related'] = $related;
                    } else {
                        // Add the full namespace to the related model
                        if (Str::contains($related, '\\')) {
                            // Put the first letter in uppercase of each word after a \ and combine them later
                            $related = join('\\', array_map(fn ($item) => ucfirst($item), explode('\\', $related)));
                        } else {
                            $related = sprintf("%s\%s\%s", $this->getNamespace(), $this->getModelsFolder(), ucfirst($related));
                        }

                        $this->fields[$key]['type']['related'] = $related;
                    }

                    // remove the | end in case the user forgot it
                    $this->fields[$key]['rules'] = rtrim($this->fields[$key]['rules'], '|');


                    $this->checkIfRelatedModelExists($related);

                    if (empty($this->getRelatedModelProperty($this->fields[$key]))) {
                        $this->triggerError(
                            sprintf(
                                'The [%s] relation field must have a property key who exists on the related table.',
                                $this->getFieldName($this->fields[$key]),
                            )
                        );
                    }

                    if (!$this->checkIfRelatedModelPropertyExists($this->fields[$key])) {
                        $this->triggerError(
                            sprintf(
                                'The [%s] related property does not exists on [%s] model.',
                                $this->getRelatedModelProperty($this->fields[$key]),
                                $related,
                            )
                        );
                    }


                    // Add exists rule
                    if (!$this->isPolymorphicField($this->fields[$key])) {

                        if (!Str::contains($field['rules'], 'exists')) {
                            if (empty($field['rules'])) {
                                $this->fields[$key]['rules'] = "exists:$related,id";
                            } else {
                                $this->fields[$key]['rules'] .= "|exists:$related,id";
                            }
                        } else {
                            $search = (string) Str::of($field['rules'])->after('exists:')->before(',');
                            if (!Str::contains($search, '\\')) {
                                $this->fields[$key]['rules'] = Str::replaceFirst($search, $related, $field['rules']);
                            }
                        }

                        // Add the onDelete if provided in the default yaml file it will be cascade
                        if ($onDelete = $this->getFieldOnDelete($field)) {
                            // validation of the onDelete field
                            if (!in_array($onDelete, $this->ONDELETERULES)) {
                                $this->triggerError(
                                    sprintf(
                                        'The [%s] onDelete constraint is not allowed. Allowed constraints are [%s]',
                                        $onDelete,
                                        join(',', $this->ONDELETERULES)
                                    )
                                );
                            }

                            if ($onDelete === $this->ONDELETERULES['setnull']) {
                                if (empty($this->fields[$key]['rules'])) {
                                    if (!Str::contains($this->fields[$key]['rules'], 'nullable')) {
                                        $this->fields[$key]['rules'] = 'nullable|' . $this->fields[$key]['rules'];
                                    }
                                } else {
                                    // a required field cannot be nullable
                                    if (Str::contains($field['rules'], 'required')) {
                                        $this->triggerError(
                                            sprintf(
                                                'The [%s] field can not be required and nullable. Please remove set null  constraint on the relation field or remove required rule.',
                                                $this->getFieldName($field),
                                            )
                                        );
                                    }
                                    // we cannot do. = because in redefining the variable
                                    if (!Str::contains($this->fields[$key]['rules'], 'nullable')) {
                                        $this->fields[$key]['rules'] = 'nullable|' . $this->fields[$key]['rules'];
                                    }
                                }
                            }

                            // if the value is set null is that the field is not nullable then we add it
                        } else {
                            $this->fields[$key]['type']['onDelete'] = 'cascade';
                        }
                        if (!$this->getFieldReferences($field)) {
                            $this->fields[$key]['type']['references'] = 'id';
                        }
                    }
                }

                if (!$this->isRelationField($field['type'])) {
                    if (!in_array($field['type'], $this->TYPES)) {
                        $this->triggerError(
                            sprintf("The [%s] field type is not available. Available types are [%s]", $field['type'], join(',', $this->TYPES))
                        );
                    }
                }


                // apply the constraints on the spot
                // standardize the constraints
                if ($constraints = Arr::get($field, 'constraints')) {
                    if (is_string($constraints)) {
                        $this->fields[$key]['constraints'] =  array_map(fn ($item) => Str::lower(trim($item)), array_filter(explode(',', $constraints)));
                    } else if (is_array($constraints)) {
                        $items = array_map(function ($item) {
                            if (is_string($item)) {
                                $item =  Str::lower(trim($item));
                            }

                            // The unique will be treated later not here
                            if (Str::contains($item, ':') && !Str::contains($item, 'unique')) {
                                [$constraint_name, $constraint_value] = array_filter(explode(':', $item));

                                // convert elements
                                if ('true' === $constraint_value) {
                                    $constraint_value = true;
                                } else if ('false' === $constraint_value) {
                                    $constraint_value = false;
                                } else if (is_numeric($constraint_value)) {
                                    $constraint_value = intval($constraint_value); // permet de le convertie en chiffre
                                }

                                // handle the case of the unique
                                $item = ['name' => $constraint_name, 'value' => $constraint_value];
                            }

                            return $item;
                        }, $constraints);

                        $this->fields[$key]['constraints'] =  $items;
                    }
                }


                // the nullable must be defined only once in the nullable: true | rule: nullable
                if (
                    (Arr::get($field, 'nullable') &&  Str::contains($field['rules'], 'nullable')) ||
                    (Arr::get($field, 'nullable') && in_array('nullable', Arr::get($this->fields[$key], 'constraints', []))) ||
                    (Str::contains($field['rules'], 'nullable') && in_array('nullable', Arr::get($this->fields[$key], 'constraints', [])))
                ) {
                    $this->triggerError(
                        sprintf(
                            'The  field [%s] nullable constraint must be defined one time',
                            $this->getFieldName($field)
                        )
                    );
                }

                /**
                 * Addition of nullable constraint
                 */
                if (in_array('nullable', Arr::get($this->fields[$key], 'constraints', []))) {
                    if (!Arr::exists($field, 'nullable')) {
                        $this->fields[$key]['nullable'] = true;
                    }

                    if (empty($this->fields[$key]['rules'])) {
                        $this->fields[$key]['rules'] = 'nullable';
                    } else {
                        $this->fields[$key]['rules'] = $this->fields[$key]['rules'] . '|nullable';
                    }
                } else if (Arr::exists($this->fields[$key], 'nullable') && $this->fields[$key]['nullable'] == true) {
                    if (!Str::contains($field['rules'], 'nullable')) {
                        if (empty($this->fields[$key]['rules'])) {
                            $this->fields[$key]['rules'] = 'nullable';
                        } else {
                            $this->fields[$key]['rules'] = $this->fields[$key]['rules'] . '|nullable';
                        }
                        $this->fields[$key]['constraints'][] = 'nullable';
                    }
                } else if (Str::contains($this->fields[$key]['rules'], 'nullable')) {
                    if (!Arr::exists($field, 'nullable')) {
                        // $field = $this->addFielsCustomProperty($field, $key, true, 'nullable', true);
                        $this->fields[$key]['nullable'] = true;
                        $this->fields[$key]['constraints'][] = 'nullable';
                    }
                }



                // Slug
                if ($this->slug && Arr::exists($field, 'slug')) {
                    $this->triggerError(
                        sprintf('The slug must be used on only one field and once. Ocurred on field [%s]. You have to remove the global slug with the [%s] value', $this->getFieldName($field), $this->slug)
                    );
                }

                if (!$this->slug && Arr::exists($field, 'slug') && $field['slug'] == true) {
                    $this->slug =  $this->getFieldName($field);
                }

                // Breadcrumb
                if ($this->breadcrumb && Arr::exists($field, 'breadcrumb')) {
                    $this->triggerError(
                        sprintf('The breadcrumb must be used on only one field and once. Ocurred on field [%s]. You have to remove the global breadcrumb with the [%s] value', $this->getFieldName($field), $this->breadcrumb)
                    );
                }

                if (!$this->breadcrumb && Arr::exists($field, 'breadcrumb') && $field['breadcrumb'] == true) {
                    $this->breadcrumb =  $this->getFieldName($field);
                }
            }

            return $this->fields;
        } else {
            $this->triggerError(
                sprintf("The model [%s] is not defined in the [%s] file.", $this->model, base_path('administrable.yaml'))
            );
        }

        // validate breadcrumb
        if (!Arr::get($this->fields, $this->breadcrumb)) {
            $this->triggerError(
                sprintf("The field [%s] used for the breadcrumb is not present in [%s] model's fields.", $this->breadcrumb, $this->model)
            );
        }
    }

    protected function getOptionsFilteredData($option) :array
    {
        /**
         * The filter allows you to remove empty elements from the array like ,
         */
        $fields = array_filter(explode(',', $option));

        // Sanitize data
        return array_map(fn ($field) => Str::lower(trim($field)), $fields);
    }


    protected function getRoutesStubsFolderPrefix() :string
    {
        $prefix = $this->route_controller_callable_syntax ?? config('administrable.route_controller_callable_syntax', true);

        return $prefix ? 'new' :  'old';
    }
}
