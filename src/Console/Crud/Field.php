<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Guysolamour\Administrable\Console\CommandTrait;
use Guysolamour\Administrable\Console\Crud\Generate\RelationTrait;


class Field
{
    use RelationTrait;
    use CrudTrait;
    use CommandTrait;

    /** @var array */
    protected const RELATION_TYPES = [
        'simple'      => 'simple',
        'polymorphic' => 'polymorphic'
    ];

    /** @var array */
    protected const RELATION_CONSTRAINTS = [
        'cascade' => 'cascade',
        'setnull' => 'set null'
    ];

    /** @var array */
    protected const DEFAULT_FORM_ATTRIBUTES = [
        'id', 'class', 'type', 'pattern', 'readonly', 'disabled'
    ];

    /** @var array */
    protected const RELATION_NAMES = [
        'simple'      => ['o2o' => 'one to one', 'o2m' => 'one to many', 'm2m' => 'many to many',],
        'polymorphic' => ['o2o' => 'one to one', 'o2m' => 'one to many', 'm2m' => 'many to many'],
    ];

     /** @var string[] */
    protected const TYPES = [
        'string', 'text', 'mediumText', 'longText', 'json',
        'date', 'datetime',
        'boolean', 'enum',
        'decimal', 'float', 'double',
        'integer', 'mediumInterger', 'bigInteger',
        'ipAdress', 'image', 'relation',
        'polymorphic',
    ];

    /** @var string */
    private $name;

    /** @var array|string */
    private $type = 'string';

    /** @var string */
    private $default;

     /** @var array */
    private $rules = [];

    /** @var array */
    private $constraints = [];

    /** @var array */
    private $choices = [];

    /** @var array */
    private $form = [];

    /** @var string */
    private $cast;

     /** @var string */
    private $trans;

    /** @var bool */
    private $daterange = false;

    /** @var bool */
    private $datepicker = false;

    /** @var int */
    private $length;


    /** @var bool */
    private $nullable = false;

     /** @var bool */
    private $tinymce = false;

    /** @var array */
    private $polymorphic = [
        'state'      => false,
        'model_id'   => null,
        'model_type' => null,
        'morph_name' => null,
    ];

    /** @var array */
    private $relation = [
        'references'   => 'id',
        'type'         => null,
        'onDelete'     => 'cascade',
        'name'         => null,
        'related'      => null,
        'model_id'     => null,
        'model_type'   => null,
        'morph_name'   => null,
        'local_keys'   => [],
        'related_keys'   => [],
        'intermediate_table'   => null,
    ];

    /** @var bool */
    private $slug = false;

     /** @var bool */
    private $breadcrumb = false;

     /** @var Cruf */
    private $crud;




    public function __construct(array $field, Crud $crud)
    {
        $this->crud = $crud;

        $attributes = Arr::first($field);

        $this->setName($field);
        $this->setType($attributes);
        $this->setRules($attributes);
        $this->setDefault($attributes);
        $this->setNullable($attributes);
        $this->setDatepicker($attributes);
        $this->setDaterange($attributes);
        $this->setCast($attributes);
        $this->setLength(Arr::get($attributes, 'length'));

        $this->setSlug($attributes);
        $this->setBreadcrumb($attributes);
        $this->setTrans($attributes);
        $this->setForm($attributes);
        $this->setTinymce($attributes);
        $this->setChoices($attributes);
        $this->setPolymorphic($attributes);
        $this->setRelation($attributes);

        $this->validateType();

        $this->setConstraints($attributes);

    }

    // SETTERS
    private function setName(array $field) :void
    {
        $this->name = Str::lower(Arr::get(Arr::first($field), 'name', array_key_first($field)));
    }

    public function isPolymorphic() :bool
    {
        return $this->type === 'polymorphic';
    }

    private function setPolymorphicProperty(string $key, $value): void
    {
        $this->polymorphic[$key] = $value;
    }

    private function getPolymorphicAttribute(string $key, $default = null)
    {
        return Arr::get($this->polymorphic, $key, $default);
    }

    public function getPolymorphicModelId() :?string
    {
        return "{$this->getPolymorphicMorphName()}_id";
    }

    public function getPolymorphicModelType() :?string
    {
        return "{$this->getPolymorphicMorphName()}_type";
    }

    public function getPolymorphicMorphName() :?string
    {
        return $this->getPolymorphicAttribute('morphname');
    }


    private function guestPolymorphicMorphName() :string
    {
        if (Str::endsWith($this->name, 'able')){
            return $this->name;
        }

        return "{$this->name}able";
    }

    private function setPolymorphicMorphname(array $attributes): void
    {
        $morphname = Arr::get($attributes, 'morphname');

        if (!$morphname) {
            $morphname = $this->guestPolymorphicMorphName();
        }

        $this->setPolymorphicProperty('morphname', $morphname);
    }

    private function setPolymorphic(array $attributes): void
    {
        if (!$this->isPolymorphic()) {
            return;
        }

        $this->setPolymorphicMorphname($attributes);
    }

    private function setRelationProperty(string $key, $value, bool $lower = true) :void
    {
        $this->relation[$key] = $lower && is_string($value) ? Str::lower($value) : $value;
    }


    private function setRelationType(array $attributes) :void
    {
        $type = Arr::get(Arr::get($attributes, 'type' ,[]), 'type');

        if (!$type) {
            $this->triggerError(
                sprintf(
                    'The [%s] relation must have a type. Allowed types are [%s]',
                    $this->name,
                    join(',', self::RELATION_TYPES)
                )
            );
        }
        if (!in_array($type, self::RELATION_TYPES)) {
            $this->triggerError(
                sprintf(
                    'The [%s] relation type is not allowed. Allowed types are [%s]',
                    $type,
                    join(',', self::RELATION_TYPES)
                )
            );
        }
        $this->setRelationProperty('type', $type);
    }


    private function setRelationOnDelete(array $attributes) :void
    {
        $onDelete = Arr::get(Arr::get($attributes, 'type', []), 'onDelete');

        if ($onDelete) {
            if (!in_array($onDelete, self::RELATION_CONSTRAINTS)) {
                $this->triggerError(
                    sprintf(
                        'The [%s] onDelete constraint is not allowed. Allowed constraints are [%s]',
                        $onDelete,
                        join(',', self::RELATION_CONSTRAINTS)
                    )
                );
            }

            $this->setRelationProperty('onDelete', $onDelete);
        }
    }


    private function setSlug(array $attributes) :void
    {
        $slug = Arr::get($attributes, 'slug');

        if (!$slug){
            return;
        }

        $this->slug = true;
    }

    private function setForm(array $attributes) :void
    {
        $form = Arr::get($attributes, 'form');

        if (!$form) {
            return;
        }

        if (!is_array($form)){
            $this->triggerError(
                sprintf(
                    'The [%s] field form must be an array.',
                    $this->name,
                )
            );
        }

        foreach ($form as $key =>  $value) {
            if (!in_array($key, self::DEFAULT_FORM_ATTRIBUTES)){
                $this->triggerError(
                    sprintf(
                        'The [%s] form attribute is not allowed. Allowed form attributes for the field [%s] are [%s]',
                        $key,
                        $this->name,
                        join(', ', self::DEFAULT_FORM_ATTRIBUTES)
                    )
                );
            }
        }


        $this->form = $form;
    }

    private function setTinymce(array $attributes) :void
    {
        $tinymce = Arr::get($attributes, 'tinymce');

        if (!$tinymce) {
            return;
        }

        if (!is_bool($tinymce)) {
            $this->triggerError(
                sprintf(
                    'The [%s] tinymce attribute must be a boolean (true or false).',
                    $this->name
                )
            );
        }

        $this->tinymce = $tinymce;
    }

    private function setChoices(array $attributes) :void
    {
        $choices = Arr::get($attributes, 'choices');

        if (!$choices) {
            return;
        }

        $this->choices = $choices;
    }

    private function setTrans(array $attributes)
    {
        $trans = Arr::get($attributes, 'trans');

        if (!$trans) {
            return;
        }

        $this->trans = $trans;
    }

    private function setBreadcrumb(array $attributes) :void
    {
        $breadcrumb = Arr::get($attributes, 'breadcrumb');

        if (!$breadcrumb){
            return;
        }

        $this->breadcrumb = true;
    }

    private function addRulesForOnDeleteRelation() :void
    {
        $onDelete = $this->getRelationOnDelete();


        if ($onDelete === self::RELATION_CONSTRAINTS['setnull']) {
            if ($this->hasRules('nullable') && $this->hasRules('required')) {
                $this->triggerError(
                    sprintf(
                        'The [%s] field can not be required and nullable. Please remove set null  constraint on the relation field or remove required rule.',
                        $this->name,
                    )
                );
            }

            if (!$this->hasRules('nullable')) {
                $this->addRules('nullable');
            }
        }
    }

    private function setRelationOnName(array $attributes) :void
    {
        $relation = Arr::get(Arr::get($attributes, 'type', []), 'relation');
        $type = $this->getRelationType();

        if (!in_array($relation, self::RELATION_NAMES[$type])) {
            $this->triggerError(
                sprintf(
                    'The [%s] relation name is not allowed. Allowed names for the  [%s] type are [%s]',
                    $relation,
                    $type,
                    join(',', self::RELATION_NAMES[$type])
                )
            );
        }

        $this->setRelationProperty('name', $relation);
    }


    private function setRelationRelatedModel(array $attributes) :void
    {
        $related = Arr::get(Arr::get($attributes, 'type', []), 'related');

        if (!$related) {
            $related = $this->guestRelationRelatedModelName();
        } else {
            // Add the full namespace to the related model
            if (Str::contains($related, '\\')) {
                // Put the first letter in uppercase of each word after a \ and combine them later
                $related = join('\\', array_map(fn ($item) => ucfirst($item), explode('\\', $related)));
            } else {
                $related = $this->setRelationRelatedModelNamespace($related);
            }
        }

        if (!class_exists($related)) {
            $this->triggerError("The related model [{$related}] does not exists, you need to create if first. If the model has a subfolder, you have to give the full namespace related class.");
        }


        $this->setRelationProperty('related', $related, false);
    }


    private function setRelationLocalForeignKey(array $attributes) :void
    {
        $local_keys = Arr::get(Arr::get($attributes, 'type', []), 'local_keys');

        if (is_null($local_keys)){
            return;
        }

        $this->setRelationProperty('local_keys', $local_keys, false);
    }

    private function setRelationRelatedForeignKey(array $attributes) :void
    {
        $related_keys = Arr::get(Arr::get($attributes, 'type', []), 'related_keys');

        if (is_null($related_keys)){
            return;
        }

        $this->setRelationProperty('related_keys', $related_keys, false);
    }

    private function setRelationIntermediateTable(array $attributes) :void
    {
        $intermediate_table = Arr::get(Arr::get($attributes, 'type', []), 'intermediate_table');

        if (is_null($intermediate_table)) {
            return;
        }

        $this->setRelationProperty('intermediate_table', $intermediate_table);
    }

    private function setRelationExistsValidationRule() :void
    {
        if ($this->checkIfExistsRuleIsDefined() || $this->isPolymorphicRelation()){
            return;
        }
        $this->rules[] = "exists:{$this->crud->getTableName()},{$this->getRelationReferences()}";
    }


    private function setRelation(array $attributes) :void
    {
        $type = Arr::get($attributes, 'type');

        if (!is_array($type)){
            return;
        }

        $this->setType('relation');

        $this->setRelationType($attributes);

        $this->setRelationOnDelete($attributes);
        $this->addRulesForOnDeleteRelation($attributes);

        $this->setRelationOnName($attributes);

        $this->setRelationRelatedModel($attributes);

        $this->setRelationRelatedModelProperty($attributes);
        $this->setRelationRelatedModelReferences($attributes);
        $this->setRelationPolymorphicAttributes($attributes);

        $this->setRelationLocalForeignKey($attributes);
        $this->setRelationRelatedForeignKey($attributes);
        $this->setRelationIntermediateTable($attributes);

        $this->setRelationExistsValidationRule();
    }

    private function setDatepicker(array $attributes) :void
    {
        /**
         * @var bool
         */
        $datepicker = Arr::get($attributes, 'datepicker', false);


        if (!is_bool($datepicker)){
            $this->triggerError(
                sprintf(
                    'The [%s] datepicker attribute must be a boolean (true or false).',
                    $this->name
                )
            );
        }

        if ($datepicker && !$this->isDatetime()) {
            $this->triggerError(
                sprintf(
                    'only datetime fields can have [datepicker]  attributes. The [%s] field is not a datetime field',
                    $this->name
                )
            );
        }

        if ($datepicker && $this->isDaterange()){
            $this->triggerError(
                sprintf(
                    'The [%s] field can not have [datepicker] and [daterange] attributes.',
                    $this->name
                )
            );
        }

        if ($datepicker) {
            $this->datepicker = $datepicker;
        }
    }


    private function setDaterange(array $attributes) :void
    {
        $daterange = Arr::get($attributes, 'daterange', false);

        if (!$daterange){
            return;
        }

        if ($daterange && !$this->isDatetime()) {
            $this->triggerError(
                sprintf(
                    'only datetime fields can have  [daterange] attributes. The [%s] field is not a datetime field',
                    $this->name
                )
            );
        }

        if ($daterange && $this->isDatepicker()){
            $this->triggerError(
                sprintf(
                    'The [%s] field can not have [daterange] and [datepicker] attributes.',
                    $this->name
                )
            );
        }

        $this->daterange = $daterange;
    }

    private function setConstraints(array $attributes) :void
    {
        $constraints = Arr::get($attributes, 'constraints');

        if (!$constraints){
            return;
        }

        if (is_string($constraints)){
            $separator = $this->crud->getAttributeSeparator($constraints);

            $constraints = array_map(function($constraint){
                return Str::lower(trim($constraint));
            }, array_filter(explode($separator, $constraints)));
        }

        $constraints = $this->parseContraintsValues($constraints);

        /**
         * Si le champ est nullable et a une contrainte nullable
         */
        if ($this->hasRules('nullable') && in_array('nullable', $constraints)){
            $this->triggerError(
                sprintf(
                    'The  field [%s] nullable constraint must be defined one time',
                    $this->name
                )
            );
        }


        /**
         * Addition of nullable constraint
         */
        if (in_array('nullable', $constraints)) {

            if (!$this->nullable){
                $this->nullable = true;
            }

            if (!$this->hasRules('nullable')){
                $this->appendRule('nullable');
            }
        }


        $this->constraints = $constraints;
    }


    private function setNullable(array $attributes) :void
    {
        $nullable = Arr::get($attributes, 'nullable');

        if ($nullable && !is_bool($nullable)){
            $this->triggerError(
                sprintf(
                    'The [%s] field  nullable attribute must be a boolean (true or false).',
                    $this->name
                )
            );
        }

        if ($nullable && !$this->hasRules('nullable')){
            $this->appendRule('nullable');
        }

        if ($this->hasRules('nullable') && !$this->hasConstraint('nullable')){
            $this->appendConstraint('nullable');
        }

        if ($this->hasRules('nullable')){
            $nullable = true;
        }


        if ($nullable){
            $this->nullable = $nullable;
        }
    }

    private function setCast(array $attributes) :void
    {
        $cast = Arr::get($attributes, 'cast');

        if (!$cast) {
            if ($this->isDaterange() || $this->isDatepicker()) {
                $cast = 'DaterangepickerCast::class';
            }
            else if ($this->isDatetime()) {
                $cast = 'datetime';
            }
            elseif ($this->isBoolean()) {
                $cast = 'boolean';
            }
            elseif ($this->isInteger()) {
                $cast = 'integer';
            }
        }

        if ($cast) {
            $this->cast = $cast;
        }
    }


    private function setDefault(array $attributes) :void
    {
        $default = Arr::get($attributes, 'default');

        if (!$default) {
            return;
        }

        $this->default = $default;
    }

    private function setRules(array $attributes) :void
    {
        $rules = Arr::get($attributes, 'rules');

        if (!$rules) {
            return;
        }

        // remove the | end in case the user forgot it
        $rules = rtrim($rules, '|');

        $rules = array_filter(explode('|', $rules));

        $rules = array_map(function($rule){
            if ($rule === 'req') {
                $rule = 'required';
            }
            return $rule;
        }, $rules);


        $this->rules =  $rules;
    }


    private function setLength(?int $length = null) :void
    {
        if (!$length){
            return;
        }

        $this->length = $length;
    }


    /**
     * @param  array|string $type
     * @return void
     */
    private function setType($type) :void
    {
        $type = is_array($type) ? Arr::get($type, 'type') : $type;

        if (!$type) {
            return;
        }


        if (!$this->isRelation() && (is_string($type) && Str::contains($type, ':'))){

            [$type, $length] = explode(':', $type);

            $this->setType($type);

            if (!$this->isText() && !$this->isInteger()) {
                $this->triggerError(
                    sprintf(
                        'The [%s] delimiter must only be used to specify text or integer field length. Occured in field [%s]',
                        ":",
                        $this->name
                    )
                );
            }

            $this->setLength($length);
        }

        if (!$this->isDatetime() && ($this->isDaterange() || $this->isDatepicker())) {
            $this->triggerError(
                sprintf(
                    'only datetime fields can have [datepicker] or [daterange] attributes. The [%s] field is not a datetime field',
                    $this->name
                )
            );
        }


        $this->type = $type;
    }

    private function validateType() :void
    {
        if (!$this->hasType($this->type)) {
            $this->triggerError(
                sprintf("The [%s] field type is not available. Available types are [%s]", $this->type, join(',', self::TYPES))
            );
        }
    }

    private function setRelationPolymorphicAttributes(array $attributes) :void
    {
        if (!$this->isPolymorphicRelation()){
            return;
        }

        $this->setRelationPolymorphicMorphName($attributes);
        $this->setRelationPolymorphicModelId($attributes);
        $this->setRelationPolymorphicModelType($attributes);
    }


    private function setRelationPolymorphicMorphName(array $attributes) :void
    {
        $morph_name = Arr::get(Arr::get($attributes, 'type', []), 'morphname');

        if (!$morph_name) {
            $morph_name = "{$this->name}able";
        }

        $this->setRelationProperty('morph_name', $morph_name, false);
    }


    private function setRelationPolymorphicModelId(array $attributes) :void
    {
        $this->setRelationProperty('model_id', "{$this->getPolymorphicRelationMorphName()}_id", false);
    }


    private function setRelationPolymorphicModelType(array $attributes) :void
    {
        $this->setRelationProperty('model_type', "{$this->getPolymorphicRelationMorphName()}_type", false);
    }

    private function setRelationRelatedModelReferences(array $attributes) :void
    {
        $references = Arr::get(Arr::get($attributes, 'type', []), 'references');

        if (is_null($references)){
            return;
        }

        $this->setRelationProperty('references', $references);
    }


    private function setRelationRelatedModelProperty(array $attributes) :void
    {
        $property = Arr::get(Arr::get($attributes, 'type', []), 'property');

        if (!$property){
            $this->triggerError(
                sprintf(
                    'The [%s] relation field must have a property key who exists on the related table.',
                    $this->name,
                )
            );
        }


        $this->setRelationProperty('property', $property);
    }

    private function setRelationRelatedModelNamespace(string $name) :string
    {
        if ($this->crud->hasSubfolder()) {
            $name =  sprintf("%s\%s", Str::ucfirst($this->crud->getSubFolder()), Str::ucfirst($name));
        }
        return sprintf("%s\%s\%s", $this->crud->getAppNamespace(), $this->crud->getModelsFolder(), Str::ucfirst($name));
    }

    // METHODS

    /**
     * @return mixed
     */
    private function checkIfExistsRuleIsDefined()
    {
        return  Arr::first($this->rules, fn ($value, $key) => Str::contains($value, 'exists'));
    }

    private function parseContraintsValues(array $constraints): array
    {
        return array_map(function ($constraint) {
            // on retire les éventuels espace
            $constraint = is_string($constraint) ? Str::lower(trim($constraint)) : $constraint;

            // The unique will be treated later not here
            if (Str::contains($constraint, ':') && !Str::contains($constraint, 'unique')) {
                [$constraint_name, $constraint_value] = array_filter(explode(':', $constraint));

                // convert elements
                if ('true' === $constraint_value) {
                    $constraint_value = true;
                } else if ('false' === $constraint_value) {
                    $constraint_value = false;
                } else if (is_numeric($constraint_value)) {
                    $constraint_value = intval($constraint_value); // permet de le convertie en chiffre
                }

                // handle the case of the unique
                $constraint = ['name' => $constraint_name, 'value' => $constraint_value];
            }

            return $constraint;
        }, $constraints);
    }

    public function isBoolean() :bool
    {
        return 'bool' === $this->getCrudType();
    }

    public function isImageType() :bool
    {
        return $this->type === 'image' || $this->type === 'file';
    }

    public function isText() :bool
    {
        return 'text' === $this->getCrudType();
    }

    public function isDatetime() :bool
    {
        return 'date' === $this->getCrudType();
    }

    public function isInteger() :bool
    {
        return 'int' === $this->getCrudType();
    }


    public function isRelation() :bool
    {
        return $this->type === 'relation';
    }

    private function appendSimpleManyToManyAndPolymorphicRelationFields(array $columns) :array
    {
        if ($this->isSimpleManyToManyRelation() || $this->isPolymorphicRelation()) {
            $method = Str::beforeLast($name = $this->getName(), '_id');

            if (method_exists($this->crud->getModelInstance(), $method)) {
                array_push($columns, $name);
            }
        }

        return $columns;
    }

    public function isAppend() :bool
    {
        $columns = $this->appendSimpleManyToManyAndPolymorphicRelationFields($this->crud->getModelAttributes());

        return in_array($this->name, $columns);
    }


    public function getRelationType(): ?string
    {
        return $this->getRelationAttribute('type');
    }


    public function getRelatedModel(): string
    {
        return Str::ucfirst($this->getRelationAttribute('related'));
    }


    public function getRelationName(): ?string
    {
        return $this->getRelationAttribute('name');
    }


    public function getRelationRelatedModelProperty(): ?string
    {
        return $this->getRelationAttribute('property');
    }


    public function getRelationOnDelete(): ?string
    {
        return $this->getRelationAttribute('onDelete');
    }

    public function getRelationReferences(): ?string
    {
        return $this->getRelationAttribute('references');
    }


    /**
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function getRelationAttribute(string $key, $default = null)
    {
        return Arr::get($this->relation, $key, $default);
    }



    private function guestRelationRelatedModelName(): string
    {
        $field_name = $this->name;

        if (Str::endsWith($field_name, '_id')) {
            $field_name = $this->getRelationModelWithoutId($field_name);
        }


        return $this->setRelationRelatedModelNamespace($field_name);
    }


    public function getRelationModelWithoutId() :string
    {
        if (Str::endsWith($this->name, '_id')) {
            return Arr::first(explode('_', $this->name));
        }

        return $this->name;
    }


    private function checkIfRelationRelatedModelPropertyExists(string $property): bool
    {
        $table_name = $this->getRelationRelatedModelTableName();

        return in_array($property, Schema::getColumnListing($table_name));
    }


    public function getModelInstance() : \Illuminate\Database\Eloquent\Model
    {
        $related_model = Str::ucfirst($this->getRelationAttribute('related'));

        return (new $related_model);
    }

	private function hasRules(string $rule) :bool
	{
		return in_array($rule, $this->rules);
	}

    private function appendRule(string ...$rules) :void
    {
        foreach($rules as $rule){
            $this->rules[] = $rule;
        }
    }

    private function appendConstraint(string ...$constraints) :void
    {
        foreach($constraints as $constraint){
            $this->constraints[] = $constraint;
        }
    }


	private function addRules(string ...$rules) :void
	{
		foreach ($rules as $rule ) {
            array_push($this->rules, $rule);
        }
	}

	private function hasType(string $rule) :bool
	{
        return in_array($rule, self::TYPES);
	}

	private function hasConstraint(string $constraint) :bool
	{
        return in_array($constraint, $this->constraints);
	}


    public function getName() :string
    {
        return $this->name;
    }


    public function getSlug() :string
    {
        return $this->slug;
    }

    public function getBreadcrumb() :bool
    {
        return $this->breadcrumb;
    }

    public function getPolymorphicRelationModelId(): ?string
    {
        return Str::lower($this->getRelationAttribute('model_id'));
    }

    public function getPolymorphicRelationModelType(): ?string
    {
        return Str::lower($this->getRelationAttribute('model_type'));
    }
    public function getPolymorphicRelationMorphName(): ?string
    {
        return Str::lower($this->getRelationAttribute('morph_name'));
    }

    public function getDaterangeStartFieldName(): string
    {
        return $this->name . '_' .  config('administrable.daterange.start', 'start_at');
    }

    public function getDaterangeEndFieldName(): string
    {
        return $this->name . '_' .  config('administrable.daterange.end', 'end_at');
    }

    private function parseDateRangeTrans() :array
    {
        $trans = $this->trans;

        if (!Str::contains($trans, '|')) {
            return Arr::wrap($trans);
        }

        return array_map(fn ($part) => trim($part), array_filter(explode('|', $trans)));

    }

    public function getDateRangeStartTrans(): ?string
    {
        return Arr::first($this->parseDateRangeTrans());
    }


    public function getDateRangeEndTrans(): ?string
    {
        return Arr::last($this->parseDateRangeTrans());
    }

    public function isDaterange() :bool
    {
        return $this->daterange;
    }

    public function isDatepicker() :bool
    {
        return $this->datepicker;
    }

    public function getCast() :?string
    {
        return $this->cast;
    }

    public function getConstraints() :array
    {
        return $this->constraints;
    }

    public function hasConstraints(string $key) :bool
    {
        return in_array($key, $this->constraints);
    }

    /**
     * @return  array|string
     */
    public function getType()
    {
        return $this->type;
    }

    public function getLength() :?int
    {
        return $this->length;
    }

    public function getDefault() :?string
    {
        return $this->default;
    }


    public function getTrans() :?string
    {
        return $this->trans;
    }


    public function getForm() :array
    {
        return $this->form;
    }

    public function getRules() :array
    {
        return $this->rules;
    }

    public function getTinymce() :bool
    {
        return $this->tinymce;
    }
    public function getNullable() :bool
    {
        return $this->nullable;
    }

    public function getChoices() :array
    {
        return $this->choices;
    }
}
