<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Container\Container;
use Guysolamour\Administrable\Console\Crud\CreateCrudForm;
use Guysolamour\Administrable\Console\Crud\CreateCrudView;
use Guysolamour\Administrable\Console\Crud\CreateCrudModel;
use Guysolamour\Administrable\Console\Crud\CreateCrudRoute;
use Guysolamour\Administrable\Console\Crud\CreateCrudMigration;
use Guysolamour\Administrable\Console\Crud\CreateCrudController;

class MakeCrudCommand extends BaseCommand
{


    use CommandTrait;

    /**
     * @var string
     */
    protected $model = '';
    /**
     * @var array
     */
    protected $fields = [];
    /**
     * @var array
     */
    protected $tempFields = [];
    /**
     * @var array
     */
    protected $morphs = [];

    /**
     * @var bool
     */
    protected $timestamps = true;
    /**
     * @var bool
     */
    protected $seeder = true;
    /**
     * @var bool
     */
    protected $entity = false;
    /**
     * @var bool
     */
    protected $polymorphic;
    /**
     * @var string
     */
    protected $slug;
    /**
     * @var string
     */
    protected $icon = 'fa-folder';


    /**
     * @var string
     */
    protected $breadcrumb;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:make:crud
                             {model : Model name }
                             ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create, model, migration and all views';

    /**
     *
     */
    public function handle()
    {
        $this->info('Initiating...');

        $progress = $this->output->createProgressBar(10);

        $this->model = $this->argument('model');


        $model = ucfirst($this->model);
        $models_config_file_path = base_path('administrable.yaml');
        $config_fields = $this->getCrudConfiguration($model);



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
                        throw new \Exception(
                            "The actions must be an array or string separated with [|] or [,]. Only one can be used."
                        );
                    }
                }


                $this->actions = array_map(function ($action) {
                    if (!in_array($action, $this->ACTIONS)) {
                        throw new \Exception(
                            sprintf("[%s] action is not allowed. Allowed actions are [%s].", $action, join(',', $this->ACTIONS))
                        );
                    }

                    return trim($action);
                }, array_filter($actions));

                // On retire les actions dans la liste car déjà dans l'instance
                $this->fields  =  Arr::except($this->fields, 'actions');
            } else {
                $this->actions = $this->ACTIONS;
            }


            $this->setConfigOption([
                'slug', 'seeder', 'entity', 'polymorphic', 'timestamps', 'breadcrumb', 'imagemanager', 'trans',
                'icon'
            ]);
            $this->setDefaultTypeAndRule();


            foreach ($this->fields as $key => $field) {
                if (!is_array($field)) {
                    continue;
                }
                // Ajout de la clé name en utilisant la clé si pas fourni
                $field = $this->addFielsCustomProperty($field, $key, $key, 'name');


                // permettre l 'utilisation de req pour la règle required
                // pouvoir tester si deux regles sont utilisés dans la meme rule et thwow une exception
                // Si le champ est juste req car il peut aussi se trouver dans required dou le && de la condition
                $field_rule = $this->getFieldRules($field);
                if (!Str::contains($field_rule, 'required') && Str::contains($field_rule, 'req')) {
                    $this->fields[$key]['rules'] = Str::replaceFirst('req', 'required', $field_rule);
                }

                // Ajouter la longueur si passé dans le type
                $type = $field['type'];
                $delimiter = ':';


                if (!$this->isRelationField($field['type'])) {
                    if (Str::contains($type, $delimiter)) {

                        [$type, $length] = explode($delimiter, $field['type']);

                        if (!($this->isTextField($type) || $this->isIntegerField($type))) {
                            throw new \Exception(
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

                    // ajout des champs ---able_id et ....able_type
                    if (
                        !empty($poly_model_id) && !empty($poly_model_type)
                    ) {

                        if (!Str::endsWith($poly_model_id, 'able_id')) {
                            throw new \Exception(
                                sprintf(
                                    'The [%s] model_id must end with [able_id]',
                                    $poly_model_id
                                )
                            );
                        }

                        if (!Str::endsWith($poly_model_type, 'able_type')) {
                            throw new \Exception(
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
                    // dd($type, $this->RELATION_TYPES);
                    if (!in_array($type, $this->RELATION_TYPES)) {
                        throw new \Exception(
                            sprintf(
                                'The [%s] relation type is not allowed. Allowed types are [%s]',
                                $type,
                                join(',', $this->RELATION_TYPES)
                            )
                        );
                    }

                    if ($constraint = $this->getFieldOnDelete($field)) {
                        if (!in_array($constraint, $this->RELATION_CONSTRAINTS)) {
                            throw new \Exception(
                                sprintf(
                                    'The [%s] relation constraint is not allowed. Allowed constraint are [%s]',
                                    $constraint,
                                    join(',', $this->RELATION_CONSTRAINTS)
                                )
                            );
                        }
                    }

                    if (!in_array($this->getRelationName($field), $this->RELATION_NAMES[$type])) {
                        throw new \Exception(
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
                        // Ajouter le namespace complet au related model
                        if (Str::contains($related, '\\')) {
                            // Mettre la premiere lettre en Majuscule de chaque mot après un \ et les combiner plus tard
                            $related = join('\\', array_map(fn ($item) => ucfirst($item), explode('\\', $related)));
                        } else {
                            $related = sprintf("%s\%s\%s", $this->getNamespace(), $this->getModelsFolder(), ucfirst($related));
                        }

                        $this->fields[$key]['type']['related'] = $related;
                    }

                    // retirer le | de fin au cas ou le user l'a oublié
                    $this->fields[$key]['rules'] = rtrim($this->fields[$key]['rules'], '|');


                    $this->checkIfRelatedModelExists($related);

                    if (!$this->getRelatedModelProperty($field)) {
                        throw new \Exception(
                            sprintf(
                                'The [%s] relation field must have a property key who exists on the related table.',
                                $this->getFieldName($field),
                            )
                        );
                    }

                    if (!$this->checkIfRelatedModelPropertyExists($field)) {
                        throw new \Exception(
                            sprintf(
                                'The [%s] related property does not exists on [%s] model.',
                                $this->getRelatedModelProperty($field),
                                $related,
                            )
                        );
                    }


                    // Add exists rule
                    if (!$this->isPolymorphicField($field)) {

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

                        // Ajouter le onDelete si fournis dans le fichier yaml par défaut ce sera cascade
                        if ($onDelete = $this->getFieldOnDelete($field)) {
                            // validation du champ onDelete
                            if (!in_array($onDelete, $this->ONDELETERULES)) {
                                throw new \Exception(
                                    sprintf(
                                        'The [%s] onDelete constraint is not allowed. Allowed constraints are [%s]',
                                        $onDelete,
                                        join(',', $this->ONDELETERULES)
                                    )
                                );
                            }

                            if ($onDelete === $this->ONDELETERULES['setnull']) {
                                // $field = $this->addFielsCustomProperty($field, $key, true, 'nullable', true);
                                // dd($field['rules'], empty($this->fields[$key]['rules']));
                                if (empty($this->fields[$key]['rules'])) {
                                    // dd(' vide', $field, $this->fields[$key]['rules'] );
                                    // $this->fields[$key]['rules'] .= '|nullable';
                                    if (!Str::contains($this->fields[$key]['rules'], 'nullable')) {
                                        $this->fields[$key]['rules'] = 'nullable|' . $this->fields[$key]['rules'];
                                    }
                                } else {
                                    // dd('pas vide');
                                    // un champ required ne peut pas être nullable
                                    if (Str::contains($field['rules'], 'required')) {
                                        throw new \Exception(
                                            sprintf(
                                                'The [%s] field can not be required and nullable. Please remove set null  constraint on the relation field or remove required rule.',
                                                $this->getFieldName($field),
                                            )
                                        );
                                    }
                                    // on ne peut pas faire .= parceque in redefini la variable
                                    // dd('nullable|' . $this->fields[$key]['rules']);
                                    if (!Str::contains($this->fields[$key]['rules'], 'nullable')) {
                                        $this->fields[$key]['rules'] = 'nullable|' . $this->fields[$key]['rules'];
                                    }
                                    // $this->fields[$key]['rules'] = $this->fields[$key]['rules'] . '|nullable';
                                }
                            }

                            // si la valeur est set null est que le champ n'est pas nullable alors on l'ajoute
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
                        throw new \Exception(
                            sprintf("The [%s] field type is not available. Available types are [%s]", $field['type'], join(',', $this->TYPES))
                        );
                    }
                }



                // appliquer les constraintes sur le champ
                // uniformiser les constraintes
                if ($constraints = Arr::get($field, 'constraints')) {
                    if (is_string($constraints)) {
                        // $items = array_map(function($item){
                        // }, array_filter(explode(',', $constraints)));
                        $this->fields[$key]['constraints'] =  array_map(fn ($item) => Str::lower(trim($item)), array_filter(explode(',', $constraints)));
                    } else if (is_array($constraints)) {
                        $items = array_map(function ($item) {
                            if (is_string($item)) {
                                $item =  Str::lower(trim($item));
                            }

                            // Le unique sera traité plus tard pas ici
                            if (Str::contains($item, ':') && !Str::contains($item, 'unique')) {
                                [$constraint_name, $constraint_value] = array_filter(explode(':', $item));

                                // convertir les élements
                                if ('true' === $constraint_value) {
                                    $constraint_value = true;
                                } else if ('false' === $constraint_value) {
                                    $constraint_value = false;
                                } else if (is_numeric($constraint_value)) {
                                    $constraint_value = intval($constraint_value); // permet de le convertie en chiffre
                                }

                                // gerer le cas du unique
                                $item = ['name' => $constraint_name, 'value' => $constraint_value];
                            }

                            return $item;
                        }, $constraints);

                        $this->fields[$key]['constraints'] =  $items;
                    }
                }


                // le nullable doit etre defini qu'une seule fois soit dans le nullable:true | rule:nullable
                if (
                    (Arr::get($field, 'nullable') &&  Str::contains($field['rules'], 'nullable')) ||
                    (Arr::get($field, 'nullable') && in_array('nullable', Arr::get($this->fields[$key], 'constraints', []))) ||
                    (Str::contains($field['rules'], 'nullable') && in_array('nullable', Arr::get($this->fields[$key], 'constraints', [])))
                ) {
                    throw new \Exception(
                        sprintf(
                            'The  field [%s] nullable constraint must be defined one time',
                            $this->getFieldName($field)
                        )
                    );
                }

                /**
                 * Ajout de la contrainte nullable
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
                    throw new \Exception(
                        sprintf('Le slug ne peut être utilisé que sur un champ et peut etre utilisé qu\'une seule fois. \ocuured sur le champ [%s] ou retirer celui uitlisé globalemet avec la value [%s]', $this->getFieldName($field), $this->slug)
                    );
                }

                if (!$this->slug && Arr::exists($field, 'slug') && $field['slug'] == true) {
                    $this->slug =  $this->getFieldName($field);
                }

                // Breadcrumb
                if ($this->breadcrumb && Arr::exists($field, 'breadcrumb')) {
                    throw new \Exception(
                        sprintf('Le breadcrumb ne peut être utilisé que sur un champ et peut etre utilisé qu\'une seule fois. \ocuured sur le champ [%s] ou retirer celui uitlisé globalemet avec la value [%s]', $this->getFieldName($field), $this->breadcrumb)
                    );
                }

                if (!$this->breadcrumb && Arr::exists($field, 'breadcrumb') && $field['breadcrumb'] == true) {
                    $this->breadcrumb =  $this->getFieldName($field);
                }
            }
        } else {
            $this->fields = $this->getFields();
            $this->filesystem->append($models_config_file_path, Yaml::dump([$model => $this->fields], 2));
        }

        // if (!Arr::exists($this->fields, 'timestamps')) {
        //    $this->timestamps = true;
        // }

        // tester pour voir si le parent_id existe et que le forein existe ne rien faire
        // par contre si parent defini et nom foreign prendre la clé du champ pour setter
        // et apres le mettre dans le modele la bas

        $this->theme = config('administrable.theme');

        // validate breadcrumb
        if (!Arr::get($this->fields, $this->breadcrumb)) {
            throw new \Exception(
                sprintf("The field [%s] used for the breadcrumb is not present in [%s] model's fields.", $this->breadcrumb, $this->model)
            );
        }


        // Models
        $this->info(PHP_EOL . 'Creating Model...');
        [$result, $model_path] = CreateCrudModel::generate(
            $this->model,
            $this->fields,
            $this->actions,
            $this->breadcrumb,
            $this->theme,
            $this->slug,
            $this->timestamps
        );
        $this->displayResult($result, $model_path);
        $progress->advance();


        // Migrations and seeds
        $this->info(PHP_EOL . 'Creating Migration...');
        [$migration_path, $seeder_path] = CreateCrudMigration::generate(
            $this->model,
            $this->fields,
            $this->actions,
            $this->breadcrumb,
            $this->theme,
            $this->slug,
            $this->timestamps,
            $this->entity,
            $this->seeder
        );
        $this->info('Migration file created at ' . $migration_path);
        $this->info('Seeder file created at ' . $seeder_path);
        $progress->advance();

        // Migrate
        $this->info(PHP_EOL . 'Migrate...');
        $this->call('migrate');
        $progress->advance();



        if (!$this->entity) {
            // Forms
            $this->info(PHP_EOL . 'Forms...');
            $form_path = CreateCrudForm::generate(
                $this->model,
                $this->fields,
                $this->actions,
                $this->breadcrumb,
                $this->theme,
                $this->slug,
                $this->timestamps,
                $this->entity,
                $this->seeder
            );
            $this->info('Form created at ' . $form_path);
            $progress->advance();



            // Controllers
            $this->info(PHP_EOL . 'Controllers...');
            $controller_path = CreateCrudController::generate(
                $this->model,
                $this->fields,
                $this->actions,
                $this->breadcrumb,
                $this->theme,
                $this->slug,
                $this->timestamps,
                $this->entity
            );
            $this->info('Controller created at ' . $controller_path);
            $progress->advance();



            // Routes
            $this->info(PHP_EOL . 'Routes...');
            $route_path = CreateCrudRoute::generate(
                $this->model,
                $this->fields,
                $this->actions,
                $this->breadcrumb,
                $this->theme,
                $this->slug,
                $this->timestamps,
                $this->entity
            );
            $this->info('Routes created at ' . $route_path);
            $progress->advance();


            //  Views and registered link to left sidebar
            $this->info(PHP_EOL . 'Views...');
            $view_path = CreateCrudView::generate(
                $this->model,
                $this->fields,
                $this->actions,
                $this->breadcrumb,
                $this->theme,
                $this->slug,
                $this->timestamps,
                $this->imagemanager,
                $this->icon
            );
            $this->info('Views created at ' . $view_path);
            $progress->advance();
        }


        // update composer autoload for seeding
        $this->runProcess("composer dump-autoload -o");

        $progress->finish();
    }




    /**
     * @return array
     */
    private function getFields(): array
    {
        $field = $this->ask('Field');
        $type = $this->anticipate('Type', $this->TYPES);

        if ($type === 'relation') {
            $relation_type = $this->choice('Which type of relation is it ?', self::RELATION_TYPES, 1);
            $relation_property = $this->ask('What property will be used to access relation ?');
            $relation_model = $this->anticipate('Which model is associated to ?', $this->getAllAppModels());



            $relation_model_with_namespace = $this->getAllAppModels(true)[$relation_model];
            $rules = '';
            $this->tempFields[$field] = [
                'name' => $field,
                'type' =>
                [$type => ['name' => $relation_type, 'model' => $relation_model_with_namespace, 'property' => $relation_property]],
                'rules' => $rules,
                //'nullable' => $nullable,

            ];

            if ($this->confirm('This relation is guest ?')) {
                $guest = $this->ask('Guest fields');

                while (empty($guest)) {
                    $guest = $this->ask('Guest fields can not be empty');
                }
                $this->tempFields[$field]['guest'] = explode(',', $guest);
            }
        } else {
            $headers = ['Name', 'Name', 'Name', 'Name'];

            $rules = [
                ['boolean', 'between:min,max', 'confirmed', 'date'],
                ['dimensions:mwidth,mheight', 'email', 'exists:table,column', 'image'],
                ['integer', 'ip', 'max:value', 'min:value', 'nullable'],
                ['required', 'unique:table,column', 'string', 'size:value'],
            ];

            $this->table($headers, $rules);
            $rules = $this->ask('Rules');

            $this->tempFields[$field] = [
                'name' => $field, 'type' => $type, 'rules' => $rules,
            ];
        }

        if ($this->confirm('This field is nulable ?')) {
            $this->tempFields[$field]['nullable'] = true;
        }

        if ($this->confirm('This field has a default value ?')) {
            $default = $this->ask('Default value');

            while (empty($default)) {
                $default = $this->ask('default value can not be empty');
            }
            $this->tempFields[$field]['default'] = $default;
        }

        if ($this->confirm('This field will be translated ?')) {
            $trans = $this->ask('Give the translated value');

            while (empty($trans)) {
                $trans = $this->ask('Translated value can not be empty');
            }
            $this->tempFields[$field]['trans'] = $trans;
        }


        if ($this->confirm('Add another field ?')) {
            $this->getFields();
        }

        return $this->tempFields;
    }

    private function removeFileExtension(string $file): string
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }

    private function getAllAppModels(bool $withNamespace = false): array
    {
        // get all models in app folder
        $results = glob(app_path() . '/*.php');

        $out = [];
        foreach ($results as $file) {
            $parts = explode('/', $file);

            if ($withNamespace) {
                $model = Container::getInstance()->getNamespace() . end($parts);
            } else {
                $model = end($parts);
            }
            $out[$this->removeFileExtension(end($parts))] = $this->removeFileExtension($model);
        }

        // get all models in app/models folder
        $path = app_path() . "/Models";
        $results = scandir($path);

        foreach ($results as $result) {
            if ($result === '.' || $result === '..' || $result === 'BaseModel.php') continue;

            if ($withNamespace) {
                $model = Container::getInstance()->getNamespace() . 'Models\\' . $result;
            } else {
                $model = $result;
            }
            $out[$this->removeFileExtension($result)] = $this->removeFileExtension($model);
        }

        return $out;
    }


    private function displayResult(bool $result, string $path): void
    {
        if ($result) {
            $this->info('File created at ' . $path);
        } else {
            if (!$this->polymorphic)
                $this->info('File ' . $path . ' already exists');
        }
    }


    /**
     * @param array $options
     */
    private function setConfigOption(array $options): void
    {
        foreach ($options as $option) {
            if (isset($this->fields[$option]) && (is_bool($this->fields[$option]) || !empty($this->fields[$option]))) {
                // is option model (generate only model and migration)
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

    private function setDefaultTypeAndRule()
    {
        foreach ($this->fields as $key => $field) {
            // On saute les actions pour les traiter plus tard
            if ($key === 'actions') {
                continue;
            }

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
