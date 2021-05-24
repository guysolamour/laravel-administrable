<?php

namespace Guysolamour\Administrable\Console\Crud\Generate;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\Crud\Field;


class GenerateForm extends BaseGenerate
{
    public function run()
    {
        if (!$this->crud->generateFieldIsAllowedFor($this)) {
            return [ false, 'Skip creating form' ];
        }

        if (!$this->crud->hasActions('create', 'edit')){
            return;
        }


        $form_stub = $this->crud->getCrudTemplatePath('/forms/form.stub');

        $form  = $this->crud->filesystem->compliedFile($form_stub, true, $this->data_map);

        $fields = $this->getFormFields();

        $path = $this->writeForm($fields, $form);

        return [$form, $path];
    }

    public function getParsedName(?string $name = null): array
    {
        return array_merge(
            $this->crud->getParsedName($name),
            $this->getRoutesParsedName(),
             [
        ]);
    }

    protected function getFormFields(): string
    {
        $fields = "";

        foreach ($this->crud->getFields() as $field) {
            if ($field->isRelation()) {

                if ($field->isSimpleManyToManyRelation() || $field->isPolymorphicRelation()) {
                    continue;
                }

                $fields .= $this->getRelationForm($field);
            } else {

                if ($field->isBoolean()) {
                    $fields .= $this->getBooleanFormField($field);
                } else {
                    $fields .= $this->getField($field);
                }
            }
        }

        if ($this->crud->getSlug() && $this->crud->getEditSlug()) {
            $fields .= $this->getFormFieldSlug();
        }

        return $fields;
    }

    protected function getFormFieldSlug(): string
    {
        return <<<TEXT

                        ->add('slug', 'text', [
                            'label'  => 'Slug',
                            'rules'  => [
                                'required',
                                \Illuminate\Validation\Rule::unique('{$this->crud->getTableName()}')->ignore(\$this->getModel())
                            ],
                        ])

            TEXT;
    }

    protected function writeForm(string $fields, string $form): string
    {
        $path = $this->getPath();
        $search = '// add fields here';

        $form = str_replace($search, $search . PHP_EOL . $fields, $form);

        $this->crud->filesystem->writeFile($path, $form);

        return $path;
    }

    protected function getRelationForm(Field $field): string
    {
        return <<<TEXT

                                ->add('{$field->getName()}', '{$this->getFormType($field)}', [
                                    'class'  => \\{$field->getRelationRelatedModel()}::class,
                                    'property' => '{$field->getRelationRelatedModelProperty()}',
                                    'label'  => '{$this->getFieldLabel($field)}',
                                    {$this->getFormFieldRules($field)}
                                    // 'query_builder => function(\\{$field->getRelationRelatedModel()} {$this->getRelatedModelFormVariable($field)}) {
                                        // return {$this->getRelatedModelFormVariable($field)};
                                    // }
                                ])
                    TEXT;
    }

    protected function getBooleanFormField(Field $field): string
    {
        $choices = $this->getFormFieldChoices($field);

        if (!$choices){
            $choices = "'choices' => ['1' => '" . __('Yes') . "', '0' => '" . __('No') . "'],";
        }

        return  <<<TEXT

                                        ->add('{$field->getName()}', 'select', [
                                            'label'   => '{$this->getFieldLabel($field)}',
                                            {$choices}
                                            'rules'   => 'required|in:0,1',
                                        ])
                            TEXT;
    }

    protected function getFormFieldChoices(Field $field) :string
    {
        $choices = $field->getChoices();

        if (empty($choices)) {
            return '';
        }

        $choices_text = "'choices' => [";
        foreach ($choices as $key => $choice) {
            $choice = (is_int($choice) || is_bool($choice)) ? $choice : "'{$choice}'";
            $choices_text .= <<<TEXT
             '{$key}' => $choice,
            TEXT;
        }
        $choices_text .= '],';

        return $choices_text;
    }

    protected function getFormFieldTinymce(Field $field): string
    {
        $tinymce = "";

        if ($field->getTinymce()) {
            return "'data-tinymce',";
        }

        return $tinymce;
    }

    protected function getFormFieldAttributes(Field $field): string
    {
        $attributes = "";

        if ($form = $field->getForm()) {

            if ($id = Arr::get($form, 'id')) {
                $attributes .= "'id' => '{$id}',\n";
            }

            if ($class = Arr::get($form, 'class')) {
                $attributes .= "                    'class' => '{$class}',\n";
            }

            if ($pattern = Arr::get($form, 'pattern')) {
                $attributes .= "                    'pattern' => '{$pattern}',\n";
            }

            if (Arr::get($form, 'readonly')) {
                $attributes .= "                    'readonly',\n";
            }

            if (Arr::get($form, 'disabled')) {
                $attributes .= "                    'disabled',\n";
            }
        }

        return $attributes;
    }

    protected function getField(Field $field): string
    {
        $fields = <<<TEXT

                                        ->add('{$field->getName()}', '{$this->getFormType($field)}', [
                                            'label'  => '{$this->getFieldLabel($field)}',
                                            {$this->getFormFieldChoices($field)}
                                            {$this->getFormFieldRules($field)}
                            TEXT;

        $fields = <<<TEXT
            $fields
                        'attr' => [
                            {$this->getFormFieldTinymce($field)}
                            {$this->getFormFieldAttributes($field)}
                        ],

        TEXT;



        $fields .= '
            ])';

        return $fields;
    }

    protected function getRelatedModelFormVariable(Field $field): string
    {
        return '$' .  Str::lower($field->getRelationRelatedModelWithoutNamespace());
    }

    protected function getFormFieldRules(Field $field): string
    {
        $rules = $field->getRules();

        if ($length = $field->getLength()) {
            $rules[]  = "max:$length";
        }

        if (empty($rules)) {
            return '';
        }

        $rules = join(",", array_map(fn ($item) => "'$item'", $rules));

        if ($field->hasConstraints('unique')) {

            return <<<TEXT
                'rules'  => [
                    $rules,
                    \Illuminate\Validation\Rule::unique('{$this->crud->getTableName()}')->ignore($this->getModel())
                ],

            TEXT;
        }

        return "'rules' => [$rules,],";
    }

    protected function getFormType(Field $field): string
    {
        $type = $field->getType();

        $attrs = $field->getForm();

        if ($attrs && $form_type = Arr::get($attrs, 'type')) {
            return $form_type;
        }

        if (
            $type === 'string' || $type === 'decimal' || $type === 'double' ||
            $type === 'float' || $type === 'json'
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
        } elseif ($field->isDaterange() || $field->isDatepicker()) {
            return 'text';
        } elseif ($type === 'date' || $type === 'datetime') {

            return 'date';
        } elseif ($field->isRelation() || $field->isPolymorphicRelation()) {
            return 'entity';
        } else {
            return 'text';
        }
    }

    protected function translate_field(Field $field): string
    {
        return translate_model_field($field->getName(), $field->getTrans() ?? null);
    }

    protected function getFieldLabel(Field $field): string
    {
        $trans = $this->translate_field($field);

        if (Str::endsWith($trans, '_id')) {
            $trans =  $this->getRelationModelWithoutId($trans);
        }

        return Str::ucfirst($trans);
    }

    protected function getRelationModelWithoutId(string $name): string
    {
        return Str::beforeLast($name, '_id');
    }

    protected function getName() :string
    {
        return $this->data_map['{{singularClass}}'];
    }

	protected function getPath() :string
	{
        $form_path = app_path("Forms/{$this->data_map['{{backNamespace}}']}/");

        if ($subfolder = $this->crud->getSubFolder()){
            $form_path .= Str::ucfirst($subfolder) . '/';
        }

        return $form_path . $this->getName() . 'Form.php';
	}
}
