<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\CommandTrait;

trait MakeCrudTrait
{

    use CommandTrait;


    /**
     * Parse guard name
     * Get the guard name in different cases
     * @param string $name
     * @return array
     */
    protected function parseName(?string $name = null): array
    {
        if (!$name)
            $name = $this->guard;

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
            '{{guard}}'                =>  config('administrable.guard', 'admin')
        ];
    }

    /**
     * @param string $migration
     * @param array  $data_map
     * @param array  $fields_to_create
     * @param string|null $file_name
     * @param boolean $down
     * @return string
     */
    protected function generateMigrationFields(string $migration, array $data_map, array $fields_to_create = [], ?string $file_name = null, bool $down = false): string
    {

        $fields_to_create = empty($fields_to_create) ? $this->fields : $fields_to_create;

        $fields = "\n\n";
        $drop_fields = "\n\n";

        foreach ($fields_to_create as $field) {
            if ($this->isRelationField($this->getNonRelationType($field))) {

                if ($this->isSimpleRelation($field)) {
                    $fields .= <<<TEXT
                               \$table->foreignId('{$this->getFieldName($field)}'){$this->getFieldAttributes($field)}->constrained('{$this->getModelTableName($this->getRelatedModel($field))}','{$this->getFieldReferences($field)}')->onDelete('{$this->getFieldOnDelete($field)}');

                    TEXT;
                    $drop_fields .= <<<TEXT
                               \$table->dropColumn('{$this->getFieldName($field)}');

                    TEXT;
                }
            } else if ($this->isPolymorphicField($field)) {
                $fields .= <<<TEXT
                           \$table->morphs('{$this->getMorphRelationableName($field)}');

                TEXT;
                $drop_fields .= <<<TEXT
                            \$table->dropColumn('{$this->getMorphRelationableName($field)}');

                TEXT;
            } else {

                $fields .= <<<TEXT
                               \$table->{$this->getMigrationFieldType($field)}('{$this->getFieldName($field)}'{$this->getMigrationFieldLength($field)}){$this->getFieldAttributes($field)};

                    TEXT;
                $drop_fields .= <<<TEXT
                           \$table->dropColumn('{$this->getFieldName($field)}');

                TEXT;
            }
        }


        // add slug field and the linked field
        if ($this->slug && !$file_name) {
            $fields .= '           $table->string(' . "'slug'" . ')->unique();';
        }
        //

        //add timestamps
        if ($this->timestamps && !$file_name) {
            $fields .= "\n" . '           $table->timestamps();';
        }

        // Pivot tables must be created before migration due to foreign customers
        $this->createManyToManyRelationPivotTable();


        // compiled and write migration
        if ($file_name){
            $search = '// add up field here';
            $replace = $fields;
        }else {
            $search = '$table->id();';
            $replace = $search . $fields;
        }


        $signature = date('Y_m_d_His');

        if ($file_name) {
            $migration_path = database_path('migrations/' . $signature . '_' . $file_name . '_table.php');
        } else {
            $migration_path = database_path('migrations/' . $signature . '_create_' . $data_map['{{pluralSnake}}'] . '_table.php');
        }

        if ($down){
            $migration = str_replace('// add down field here', $drop_fields, $migration);
        }

        $this->writeFile(
            str_replace($search, $replace, $migration),
            $migration_path
        );

        return $migration_path;
    }

    /**
     * @param string $seeder
     * @param array $fields
     * @return string
     */
    protected function generateSeederFields(string $seeder, array $fields = []): string
    {
        $seed_fields = "\n";
        $fields = empty($fields) ? $this->fields : $fields;

        foreach ($fields as $field) {
            // allow to generate the slug in the seed by putting the variable $ slug in front
            if ($field['type'] === "string") {
                $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->text(50),';
            }

            if ($field['type'] === 'image') {
                $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->imageUrl,';
            }

            if ($field['type'] === 'text') {
                $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->realText(150),';
            }

            if ($field['type'] === 'integer' || $field['type'] === 'mediumInteger'  || $field['type'] === 'bigInteger') {
                $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->randomNumber(),';
            }

            if ($field['type'] === 'boolean') {
                $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->randomElement([true,false]),';
            }

            if ($this->isRelationField($this->getNonRelationType($field))) {
                if ($this->isSimpleRelation($field)) {
                    $seed_fields .= "\n" . "                '{$field['name']}'  => " . '$faker->randomElement(' . $this->getRelatedModel($field) . '::pluck(\'id\')' . '),';
                }
            }
        }

        // compiled and write migration
        $search = 'create([';
        return str_replace($search, $search . $seed_fields, $seeder);
    }

    /**
     * @param array $fields_to_create
     * @return string
     */
    protected function getFormFields(array $fields_to_create = []): string
    {
        $fields = "";

        $fields_to_create = empty($fields_to_create) ? $this->fields : $fields_to_create;

        foreach ($fields_to_create as $field) {
            if ($this->isRelationField($this->getNonRelationType($field))) {
                // polymorphic fields are ignored
                if (!$this->isPolymorphicField($field)) {
                    $fields .= <<<TEXT

                                ->add('{$this->getFieldName($field)}', '{$this->getFormType($field)}', [
                                    'class'  => \\{$this->getRelatedModel($field)}::class,
                                    'property' => '{$this->getRelatedModelProperty($field)}',
                                    'label'  => '{$this->getFieldLabel($field)}',
                                    {$this->getFormFieldRules($field)}
                                    // 'query_builder => function(\\{$this->getRelatedModel($field)} {$this->getRelatedModelFormVariable($field)}) {
                                        // return {$this->getRelatedModelFormVariable($field)};
                                    // }
                                ])
                    TEXT;
                }
                # code...
            } else {
                // polymorphic fields are ignored
                if (!$this->isPolymorphicField($field)) {
                    $fields .= <<<TEXT

                                    ->add('{$this->getFieldName($field)}', '{$this->getFormType($field)}', [
                                        'label'  => '{$this->getFieldLabel($field)}',
                                        {$this->getFormFieldChoices($field)}
                                        {$this->getFormFieldRules($field)}

                        TEXT;


                    if (Arr::get($field, 'tinymce')) {
                        $fields .= <<<TEXT
                                        'attr' => [
                                            'data-tinymce',
                                        ],
                        TEXT;
                    }

                    $fields .= '            ])';
                }
            }
        }



        // add slug field
        if ($this->slug && $this->edit_slug) {
            $data_map = $this->parseName($this->model);

            $table_name = $data_map['{{pluralSnake}}'];

            $fields .= <<<TEXT

                        ->add('slug', 'text', [
                            'label' => 'Slug',
                            'rules'  => [
                                'required',
                                \Illuminate\Validation\Rule::unique('{$table_name}')->ignore(\$this->getModel())
                            ],
                        ])

            TEXT;
        }

        return $fields;
    }

    /**
     * @param string $type
     * @return string
     */
    protected function getFormType(array $field): string
    {
        $type = $this->getNonRelationType($field);

        if (
            $type === 'string' || $type === 'decimal' || $type === 'double' ||
            $type === 'float'
        ) {
            return 'text';
        } elseif (
            $type === 'image' || $type === 'file'
        ) {
            return 'file';
        } elseif (
            $type === 'integer' || $type === 'mediumInteger'
        ) {
            return 'number';
        } elseif ($type === 'text' || $type === 'mediumText' || $type === 'longText') {
            return 'textarea';
        } elseif ($type === 'email') {
            return 'email';
        } elseif ($type === 'boolean' || $type === 'enum') {
            return 'select';
        } elseif ($type === 'date') {
            return 'date';
        } elseif ($type === 'datetime') {
            return 'datetime';
        } elseif ($this->isRelationField($this->getNonRelationType($field)) || $this->isPolymorphicField($field)) {
            return 'entity';
        } else {
            return 'text';
        }
    }

    /**
     * @param array $fields_to_create
     * @param array $data_map
     * @param string $fields
     * @param string $values
     * @return array
     */
    protected function getIndexViewFields(array $fields_to_create, array $data_map, string $fields = '', string $values = '') :array
    {
        $guard = $data_map['{{backLowerNamespace}}'];
        $var_name = $data_map['{{singularSlug}}'];

        foreach ($fields_to_create as $key => $field) {


            if ($this->isImageableField($key)) {
                continue;
            }

            // if the field is of type image we skip it because we do not want to display it on the index page
            if ($field['type'] == 'image') {
                continue;
            };



            if ($this->isRelationField($field['type'])) {
                $value = ucfirst(translate_model_field($this->getRelationModelWithoutId($field['name']), $field['trans'] ?? null));
                $fields = <<<TEXT
                    $fields
                                    <th>$value</th>
                    TEXT;
            } else {
                $value = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                $fields = <<<TEXT
                    $fields
                                    <th>$value</th>
                    TEXT;
            }


            if (!$this->isRelationField($field['type']) && $this->isTextField($this->getFieldType($field))) {

                if ($this->isTheadminTheme() && ($this->getFieldName($field) === $this->guestBreadcrumbFieldNane())) {
                    $values = <<<TEXT
                        $values
                                    <td>
                                        <a class="text-dark" data-provide="tooltip" title="Apercu rapide"
                                            href="#qv-{$data_map['{{pluralSlug}}']}-details-{{ \${$var_name}->id }}" data-toggle="quickview"
                                        >
                                            {{ Str::limit(\${$var_name}->{$this->getFieldName($field)},50) }}
                                        </a>
                                    </td>
                    TEXT;
                } else {
                    $values = <<<TEXT
                        $values
                                    <td>{{ Str::limit(\${$var_name}->{$this->getFieldName($field)},50) }}</td>
                    TEXT;
                }
            } else if ($this->isRelationField($field['type'])) {

                if ($this->isSimpleRelation($field)) {
                    if ($this->isSimpleOneToOneRelation($field) || $this->isSimpleOneToManyRelation($field)) {
                        $values = <<<TEXT
                        $values
                                        <td>
                                            <a href="{{ route( '{$guard}.{$this->getRelationModelWithoutId($this->getFieldName($field))}.show',\${$var_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))} ) }}" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </td>
                        TEXT;
                    } else {
                        $values = <<<TEXT
                        $values
                                        <td>
                                            <a href="{{ route( '{$guard}.{$this->getRelationModelWithoutId($this->getFieldName($field))}.show',\${$var_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0] ) }}" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0]->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </td>
                        TEXT;
                    }
                } else if ($this->isPolymorphicField($field)) {
                    if ($this->isPolymorphicOneToOneRelation($field)) {
                        $values = <<<TEXT
                        $values
                                        <td>
                                            <a href="Javascript:void(0)" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </td>
                        TEXT;
                    } else {
                        $values = <<<TEXT
                        $values
                                        <td>
                                            <a href="Javascript:void(0)" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0]->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </td>
                        TEXT;
                    }
                }
            }
        }

        return [$fields, $values];
    }


    /**
     * @param array $fields_to_create
     * @param array $data_map
     * @param boolean $withTimestamp
     * @return string
     */
    protected function getShowViewFields(array $fields_to_create = [], array $data_map = [], bool $withTimestamp = true): string
    {

        $fields_to_create = empty($fields_to_create) ? $this->fields : $fields_to_create;
        $data_map = empty($data_map) ? $this->data_map : $data_map;

        $field_name = $data_map['{{singularSlug}}'];


        $show_views = '';

        $guard = $data_map['{{backLowerNamespace}}'];


        foreach ($fields_to_create as $key =>  $field) {
            /**
             * If the field is of type imagemanager we skip it
             */
            if ($this->isImageableField($key)) {
                continue;
            } else if ($this->isRelationField($field['type'])) {

                if ($this->isSimpleRelation($field)) {
                    if ($this->isSimpleOneToOneRelation($field) || $this->isSimpleOneToManyRelation($field)) {
                        $model = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2">
                                            <b>$model: </b>
                                            <a href="{{ route('$guard.{$this->getRelationModelWithoutId($this->getFieldName($field))}.show', \${$field_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}) }}">
                                                {{ \${$field_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </p>
                        TEXT;
                    } else {
                        $model = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0]->{$this->getRelatedModelProperty($field)} }}</p>
                        TEXT;
                    }
                } else if ($this->isPolymorphicField($field)) {
                    if ($this->isPolymorphicOneToOneRelation($field)) {
                        $model = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}->{$this->getRelatedModelProperty($field)} }}</p>
                        TEXT;
                    } else {
                        $model = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0]->{$this->getRelatedModelProperty($field)} }}</p>
                        TEXT;
                    }
                }
            } else {
                $model = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                $show_views = <<<TEXT
                    $show_views
                            <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$this->getFieldName($field)} }}</p>
                TEXT;
            }
        }

        if ($withTimestamp) {
            $show_views = <<<TEXT
                $show_views
                            <p class="pb-2"><b>Date ajout: </b>{{ \${$field_name}->created_at->format('d/m/Y h:i') }}</p>
            TEXT;
            // $show_views .= '                <p class="pb-2"><b>Date ajout:</b> {{ $' . $field_name . '->created_at->format(\'d/m/Y h:i\') }}</p>' . "\n";
        }

        return $show_views;
    }

    /**
     * @param array $fields_to_create
     * @param array $data_map
     * @param boolean $withTimestamp
     * @return string
     */
    protected function getEditViewFields(array $fields_to_create = [], array $data_map = []): string
    {

        $fields_to_create = empty($fields_to_create) ? $this->fields : $fields_to_create;
        $data_map = empty($data_map) ? $this->data_map : $data_map;


        $edit_views = '';


        foreach ($fields_to_create as $key =>  $field) {
            /**
             * If the field is of type imagemanager we skip it
             */
            if ($this->isImageableField($key)) {
                continue;
            }

            $field_name = $this->getFieldName($field);

            $edit_views = <<<TEXT
                                $edit_views
                                <div class="col-md-6">
                                    {!! form_row(\$form->{$field_name}) !!}
                                </div>
            TEXT;

        }


        return $edit_views;
    }

    /**
     * @param $fields
     * @param $complied
     * @param $form_path
     */
    protected function registerFormFields($fields, $complied, $form_path): void
    {

        $this->createDirectoryIfNotExists($form_path, false);
        $search = '// add fields here';
        $form = str_replace($search, $search . $fields, $complied);
        // $this->filesystem->put($form_path, $form);
        $this->writeFile($form, $form_path);
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getFieldLabel(array $field): string
    {
        $trans = $this->translate_field($field);

        if (Str::endsWith($trans, '_id')) {
            $trans =  $this->getRelationModelWithoutId($trans);
        }

        return Str::ucfirst($trans);
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getRelatedModelFormVariable(array $field): string
    {
        return '$' .  Str::lower($this->getRelatedModelWithoutNamespace($field));
    }

    /**
     * @param array $field
     * @return void
     */
    protected function getFormFieldChoices(array $field)
    {
        $choices = Arr::get($field, 'choices', []);

        if (empty($choices)) {
            return '';
        }

        $choices_text = "'choices' => [";
        foreach ($choices as $key => $choice) {
            $choices_text .= <<<TEXT
             $key => '$choice',
            TEXT;
        }
        $choices_text .= '],';

        return $choices_text;
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getFormFieldRules(array $field): string
    {
        $rule = $this->getFieldRules($field);

        if ($length = Arr::get($field, 'length')) {
            $rule .= "|max:$length";
        }

        if (empty($rule)) {
            return '';
        }

        $rule = join(",", array_map(fn ($item) => "'$item'", explode('|', $rule)));



        if (in_array('unique', $this->getFieldConstraints($field) ?? [])) {
            $data_map = $this->parseName($this->model);

            $table_name = $data_map['{{pluralSnake}}'];

            return <<<TEXT
                'rules'  => [
                    $rule,
                    \Illuminate\Validation\Rule::unique('{$table_name}')->ignore($this->getModel())
                ],

            TEXT;
        }


        return "'rules' => [$rule,],";
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getModelForeignKey(array $field): string
    {
        $key = Arr::get($this->getRelationLocalForeignKey($field), 'foreign_key');

        if (!$key) {
            $key = $this->parseName($this->model)['{{singularSlug}}'] . '_id';
        }

        return $key;
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getRelatedModelForeignKey(array $field): string
    {
        $key = Arr::get($this->getRelationRelatedForeignKey($field), 'foreign_key');

        if (!$key) {
            $key = $this->parseRelationName($this->model, $this->getRelatedModel($field))['{{relatedSingularSlug}}'] . '_id';
        }

        return $key;
    }

    /**
     * @return void
     */
    protected function createManyToManyRelationPivotTable()
    {

        foreach ($this->fields as $field) {
            if ($this->isSimpleManyToManyRelation($field)) {
                $data_map = array_merge(
                    $this->parseRelationName($this->model, $this->getRelatedModel($field)),
                    ['{{intermediateTableName}}' => $this->getRelationIntermediateTable($field, true)],
                    ['{{intermediateClassName}}' => $this->getIntermediateClassName($field)],
                    ['{{onDelete}}' => $this->getFieldOnDelete($field)],
                    ['{{modelForeignKey}}' => $this->getModelForeignKey($field)],
                    ['{{relatedForeignKey}}' => $this->getRelatedModelForeignKey($field)],
                );

                $pivot_migration_stub = $this->TPL_PATH . '/migrations/pivot.stub';

                $pivot_migration = $this->compliedFile($pivot_migration_stub, true, $data_map);

                // Plus 5 generates the pivot table because of foreign keys
                $signature  = date('Y_m_d_') . (date('His') + 5);
                $pivot_migration_path = database_path('migrations/' . $signature . '_' . Str::snake($this->getIntermediateClassName($field)) . '.php');

                $this->writeFile(
                    $pivot_migration,
                    $pivot_migration_path
                );
            }
        }
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getMigrationFieldLength(array $field): string
    {
        if (!$this->isTextField($field['type'])){
            return '';
        }

        if ($length = Arr::get($field, 'length')) {
            return ", $length";
        }
        return '';


    }


    /**
     * @param array $field
     * @return string
     */
    protected function getFieldAttributes(array $field): string
    {
        $constraints = $this->getFieldConstraints($field);

        $attr = '';

        if ($default = Arr::get($field, 'default')) {
            if (is_bool($default)) {

                $attr .= sprintf("->default(%s)", $default ? 'true' : 'false');
            } else {
                $attr .= sprintf("->default('%s')", $default);
            }
        }


        if (empty($constraints)) {
            return $attr;
        }

        foreach ($constraints as $constraint) {
            if (is_array($constraint)) {
                if ($constraint['value'] === true) {
                    $value = 'true';
                } else if ($constraint['value'] === false) {
                    $value = 'false';
                } else if (is_string($constraint['value'])) {
                    $value = "'{$constraint['value']}'";
                } else {
                    $value = $constraint['value'];
                }
                $attr .= sprintf("->%s(%s)", $constraint['name'], $value);
            } else {
                $attr .=  "->$constraint()";
            }
        }


        return $attr;
    }

    /**
     * @param string $model
     * @return string
     */
    protected function getModelTableName(string  $model): string
    {
        // we recover the name of the model without namespace
        $model = explode('\\', $model);

        return  strtolower(Str::plural(Str::studly(end($model))));
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
     * @param string $key
     * @return boolean
     */
    protected function hasAction(string $key): bool
    {
        return in_array($key, $this->actions);
    }

    /**
     * @param array $field
     * @return void
     */
    protected function parseMorphsName(array $field)
    {
        return [
            '{{pluralMorphField}}'   =>  Str::plural(Str::slug($this->getRelatedModelWithoutNamespace($field))),
            '{{singularMorphField}}' =>  Str::singular(Str::slug($this->getRelatedModelWithoutNamespace($field))),
            '{{singularMorphClass}}' =>  Str::singular(Str::studly($this->getRelatedModelWithoutNamespace($field))),
            '{{singularMorphSlug}}'  =>  Str::singular(Str::slug($this->getRelatedModelWithoutNamespace($field))),
        ];
    }

    /**
     * @param string $model_name
     * @param string $related_full_name
     * @return array
     */
    protected function parseRelationName(string $model_name, string $related_full_name): array
    {

        // we recover the name of the model without the namespace
        $related = $this->modelNameWithoutNamespace($related_full_name);

        return [
            '{{modelPluralSlug}}'       => Str::plural(Str::slug($model_name)),
            '{{modelPluralClass}}'      => Str::plural(Str::studly($model_name)),
            '{{modelSingularClass}}'    => Str::studly($model_name),
            '{{modelSingularSlug}}'     => Str::singular(Str::slug($model_name)),
            '{{relatedSingularClass}}'  => Str::singular(Str::studly($related)),
            '{{relatedPluralSlug}}'     => Str::plural(Str::slug($related)),
            '{{relatedPluralClass}}'    => Str::plural(Str::studly($related)),
            '{{relatedSingularSlug}}'   => Str::singular(Str::slug($related)),
            // '{{relatedNamespace}}'  => $this->getRelatedNamespace($related_full_name),
        ];
    }

    /**
     * @param array $field
     * @return string
     */
    protected function getMigrationFieldType(array $field): string
    {
        $type = $this->getNonRelationType($field);
        // if the value is an array it is that we have a relation type field
        // no need to go further
        if ($this->isRelationField($type)) return '';

        if ($type === 'image') {
            return 'string';
        }
        return Str::lower($type);
    }

    /**
     * @param [type] $field
     * @return void
     */
    protected function getMorphFieldName($field)
    {
        return Str::plural(Str::slug($this->modelNameWithoutNamespace($this->getRelatedModel($field))));
    }

    /**
     * @param [type] $field
     * @return void
     */
    protected function getSingularMorphFieldName($field)
    {
        return Str::singular(Str::slug($this->modelNameWithoutNamespace($this->getRelatedModel($field))));
    }

    /**
     * @param [type] $field
     * @return boolean
     */
    protected function isImageFIeld($field): bool
    {
        return $field['name'] === 'image';
    }

    /**
     * @param array $field
     * @return string
     */
    protected function translate_field(array $field): string
    {
        return translate_model_field($field['name'], $field['trans'] ?? null);
    }

    /**
     * @param array $model
     * @return boolean
     */
    public function modelHasTinymceField(array $model = []): bool
    {
        if (empty($model)) {
            $model = $this->fields;
        }

        foreach ($model as $key =>  $field) {
            if (is_array($field) && Arr::exists($field, 'tinymce') && $field['tinymce'] == true) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $key
     * @return boolean
     */
    protected function hasCrudAction(string $key): bool
    {
        return in_array($key, $this->actions);
    }

    /**
     * @param array $field
     * @return boolean
     */
    protected function isSimpleRelation(array $field): bool
    {
        return $this->getRelationType($field) === $this->RELATION_TYPES['simple'];
    }

    /**
     * @param array $field
     * @return boolean
     */
    protected function isSimpleOneToOneRelation(array $field)
    {
        return
            $this->isSimpleRelation($field) &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['simple']['o2o'];
    }

    /**
     * @param array $field
     * @return boolean
     */
    protected function isSimpleOneToManyRelation(array $field)
    {
        return
            $this->isSimpleRelation($field) &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['simple']['o2m'];
    }

    /**
     * @param array $field
     * @return boolean
     */
    protected function isSimpleManyToOneRelation(array $field)
    {
        return
            $this->isSimpleRelation($field) &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['simple']['m2o'];
    }

    /**
     * @param array $field
     * @return boolean
     */
    protected function isSimpleManyToManyRelation(array $field)
    {
        return
            $this->isSimpleRelation($field) &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['simple']['m2m'];
    }

    /**
     * @param array $field
     * @return boolean
     */
    protected function isPolymorphicOneToOneRelation(array $field)
    {
        return
            $this->getRelationType($field) === $this->RELATION_TYPES['polymorphic'] &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['polymorphic']['o2o'];
    }

    /**
     *
     * @param array $field
     * @return boolean
     */
    protected function isPolymorphicOneToManyRelation(array $field)
    {
        return
            $this->getRelationType($field) === $this->RELATION_TYPES['polymorphic'] &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['polymorphic']['o2m'];
    }

    /**
     *
     * @param array $field
     * @return boolean
     */
    protected function isPolymorphicManyToOneRelation(array $field)
    {
        return
            $this->getRelationType($field) === $this->RELATION_TYPES['polymorphic'] &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['polymorphic']['m2o'];
    }

    /**
     * @param array $field
     * @param array $data_map
     * @return void
     */
    protected function getRelationRelatedModelPath(array $field, array $data_map)
    {
       return app_path($data_map['{{modelsFolder}}'] . '/' . $this->modelNameWithoutNamespace($this->getRelatedModel($field)) . '.php');
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getModelTranslation(string $name): string
    {
        if (empty($this->trans)) {
            return Str::plural(Str::studly($name));
        }

        return $this->trans;
    }

    /**
     * @param string $model
     * @param string $model_path
     * @param array $fields
     * @return void
     */
    protected function addRelations(string $model, string $model_path, array $fields = [])
    {
        $default_model = '';

        $fields = empty($fields) ? $this->fields : $fields;

        foreach ($fields as $field) {

            $related_model = '';

            if ($this->isRelationField($field['type'])) {
                // we recover the model
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
                    $related_path = $this->getRelationRelatedModelPath($field, $data_map);

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
                         * If the intermediate table is not defined, we guess it because to pass the foreign keys
                         * you need the name of the table
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

    protected function getCurrentModelTableName(?string $model = null): string
    {
        if (is_null($model)) {
            $model = $this->data_map['{{singularClass}}'] ?? $this->model;
        }

        $class = sprintf("%s\%s\%s", $this->data_map['{{namespace}}'], $this->data_map['{{modelsFolder}}'], ucfirst($model));

        return (new $class())->getTable();
    }

}
