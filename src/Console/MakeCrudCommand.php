<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
     * @var bool
     */
    protected $edit_slug = false;
    /**
     * @var string
     */
    protected $icon = 'fa-folder';

    protected $imagemanager = '';

    /**
     * @var string[]
     */
    protected const GLOBAL_OPTIONS = ['slug', 'edit_slug', 'seeder', 'entity', 'polymorphic', 'timestamps', 'breadcrumb', 'imagemanager', 'trans', 'icon'];

    /**
     * @var string[]
     */
    protected const RESERVED_WORDS = ['slug', 'icon', 'edit_slug', 'breadcrumb', 'timestamps', 'seeder', 'trans'];



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
    protected $description = 'Create, model, migration and all views (crud)';

    /**
     *
     */
    public function handle()
    {
        $this->info('Initiating...');

        $progress = $this->output->createProgressBar(10);

        $this->model = $this->argument('model');


        $model = ucfirst($this->model);
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

                // We remove the actions from the list because already exists on the instance
                $this->fields  =  Arr::except($this->fields, 'actions');
            } else {
                $this->actions = $this->ACTIONS;
            }

            // on gere le cas du edit slug ici avnt de faire la configuration des options globaux
            // on stocke la configuration globale si elle a été définie
            // celui ci pourra ecraser sur un modele en particulier
            if ($edit_slug = $this->getCrudConfiguration('edit_slug')) {

                if (!is_bool($edit_slug)) {
                    throw new \Exception(
                        sprintf("The global edit_slug option must be a boolean. Current value is [%s]", $edit_slug)
                    );
                }
                $this->edit_slug = (bool) $edit_slug;
            }

            // tester le truc de icon si tableau et exception
            $this->setConfigOption();

            // Ajout du champ slug dans le formulaire
            // Il faut avoir un champ slug avant d'utiliser le edit_slug

            if($this->edit_slug && !$this->slug){
                throw new \Exception(
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

                    // add ---able_id et ....able_type fields
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

                        // Add the onDelete if provided in the default yaml file it will be cascade
                        if ($onDelete = $this->getFieldOnDelete($field)) {
                            // validation of the onDelete field
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
                                if (empty($this->fields[$key]['rules'])) {
                                    if (!Str::contains($this->fields[$key]['rules'], 'nullable')) {
                                        $this->fields[$key]['rules'] = 'nullable|' . $this->fields[$key]['rules'];
                                    }
                                } else {
                                    // a required field cannot be nullable
                                    if (Str::contains($field['rules'], 'required')) {
                                        throw new \Exception(
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
                        throw new \Exception(
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
                    throw new \Exception(
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
                    throw new \Exception(
                        sprintf('The slug must be used on only one field and once. Ocurred on field [%s]. You have to remove the global slug with the [%s] value', $this->getFieldName($field), $this->slug)
                    );
                }

                if (!$this->slug && Arr::exists($field, 'slug') && $field['slug'] == true) {
                    $this->slug =  $this->getFieldName($field);
                }

                // Breadcrumb
                if ($this->breadcrumb && Arr::exists($field, 'breadcrumb')) {
                    throw new \Exception(
                        sprintf('The breadcrumb must be used on only one field and once. Ocurred on field [%s]. You have to remove the global breadcrumb with the [%s] value', $this->getFieldName($field), $this->breadcrumb)
                    );
                }

                if (!$this->breadcrumb && Arr::exists($field, 'breadcrumb') && $field['breadcrumb'] == true) {
                    $this->breadcrumb =  $this->getFieldName($field);
                }
            }
        } else {
            throw new \Exception(
                sprintf("The model [%s] is not defined in the [%s] file.", $this->model, base_path('administrable.yaml'))
            );
        }


        // test to see if the parent_id exists and the forein exists do nothing
        // on the other hand if defined parent and foreign name take the field key to setter
        // and after putting it in the model over there

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
        // $this->call('migrate');
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
                $this->seeder,
                $this->edit_slug
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
        $this->runProcess("composer dump-autoload");

        $progress->finish();
    }





    private function removeFileExtension(string $file): string
    {
        return pathinfo($file, PATHINFO_FILENAME);
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



    private function setConfigOption(): void
    {

        foreach (self::GLOBAL_OPTIONS as $option) {

            if(isset($this->fields[$option]) && is_array($this->fields[$option]) && in_array($option, self::RESERVED_WORDS)){
                throw new \Exception(
                    "A field can not has [$option] for name. The [$option] word is reserved."
                );
            }

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

    private function sanitizeFields()
    {
        foreach ($this->fields as $key =>  $field) {
            if (!is_array($field)) {
                $this->fields = Arr::except($this->fields, $key);
            }
        }
    }

    private function setDefaultTypeAndRule()
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
}
