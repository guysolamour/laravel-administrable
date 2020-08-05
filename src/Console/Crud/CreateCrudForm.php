<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class CreateCrudForm
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
     * @var array
     */
    private $actions;
    /**
     * @var string
     */
    private $breadcrumb;
    /**
     * @var string
     */
    private $theme;
    /**
     * @var string
     */
    private $slug;
    /**
     * @var bool
     */
    private $timestamps;
    /**
     * @var bool
     */
    private $entity;
    /**
     * @var bool
     */
    private $seeder;
    /**
     * @var bool
     */
    private $edit_slug;


    /**
     * @param string $model
     * @param array $fields
     * @param array $actions
     * @param string|null $breadcrumb
     * @param string $theme
     * @param string|null $slug
     * @param boolean $timestamps
     * @param boolean $entity
     * @param boolean $seeder
     */
    public function __construct(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug, bool $timestamps, bool $entity, bool $seeder, bool $edit_slug)
    {
        $this->model       = $model;
        $this->fields      = $fields;
        $this->actions     = $actions;
        $this->slug        = $slug;
        $this->breadcrumb  = $breadcrumb;
        $this->theme       = $theme;
        $this->slug        = $slug;
        $this->timestamps  = $timestamps;
        $this->entity      = $entity;
        $this->seeder      = $seeder;
        $this->edit_slug   = $edit_slug;

        $this->filesystem   = new Filesystem;
    }

    /**
     * @param string $model
     * @param array  $fields
     * @param array  $actions
     * @param string|null $breadcrumb
     * @param string $theme
     * @param string|null $slug
     * @param boolean $timestamps
     * @param boolean $entity
     * @param boolean $seeder
     * @param boolean $edit_slug
     * @return void
     */
    public static function generate(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug, bool $timestamps, bool $entity, bool $seeder, bool $edit_slug)
    {
        return (new CreateCrudForm($model, $fields, $actions, $breadcrumb, $theme, $slug, $timestamps, $entity, $seeder, $edit_slug))
            ->loadForm();
    }

    /**
     * @return string
     */
    private function loadForm(): string
    {
        if ($this->hasAction('create') || $this->hasAction('edit')) {
            $data_map = $this->parseName($this->model);
            $form_name = $data_map['{{singularClass}}'];

            $form_path = app_path("/Forms/" . $data_map['{{modelsFolder}}']);
            $form_stub = $this->TPL_PATH . '/forms/form.stub';
            $form_path = $form_path . "/{$form_name}Form.php";

            $complied = $this->compliedFile($form_stub, true, $data_map);

            // add fields
            $fields = $this->getFormFields();

            [$form_path, $complied] = $this->loadAndRegisterFormStub($form_name, $data_map);

            $this->registerFields($fields, $complied, $form_path);


            return $form_path;
        }

        return '';
    }



    /**
     * @return string
     */
    private function getFormFields(): string
    {
        $fields = "";

        foreach ($this->fields as $field) {
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


    protected function getFieldLabel(array $field): string
    {
        $trans = $this->translate_field($field);

        if (Str::endsWith($trans, '_id')) {
            $trans =  $this->getRelationModelWithoutId($trans);
        }

        return Str::ucfirst($trans);
    }

    protected function getRelatedModelFormVariable(array $field): string
    {
        return '$' .  Str::lower($this->getRelatedModelWithoutNamespace($field));
    }

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
     * @param $fields
     * @param $complied
     * @param $form_path
     */
    private function registerFields($fields, $complied, $form_path): void
    {

        $this->createDirectoryIfNotExists($form_path, false);
        $search = '$this' . "\n";
        $form = str_replace($search, $search . $fields, $complied);
        // $this->filesystem->put($form_path, $form);
        $this->writeFile($form, $form_path);
    }

    /**
     * @param $form_name
     * @param $data_map
     * @return array
     */
    private function loadAndRegisterFormStub($form_name, $data_map): array
    {
        $form_path = app_path('Forms/' . $data_map['{{backNamespace}}']);

        $form_stub = $this->TPL_PATH . '/forms/form.stub';
        $form_path = $form_path . "/{$form_name}Form.php";

        $complied = $this->compliedFile($form_stub,  true, $data_map);

        return array($form_path, $complied);
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
}
