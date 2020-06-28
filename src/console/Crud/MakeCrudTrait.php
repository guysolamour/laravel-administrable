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
            '{{modelsFolder}}'         =>  $this->getCrudConfiguration('folder','Models'),
            '{{administrableLogo}}'    =>  asset(config('administrable.logo_url')),
            '{{theme}}'                =>  $this->theme,
            '{{guard}}'                =>  config('administrable.guard','admin')
        ];
    }

    protected function hasAction(string $key): bool
    {
        return in_array($key, $this->actions);
    }



    protected function parseMorphsName(array $field)
    {
        return [
            '{{pluralMorphField}}'   =>  Str::plural(Str::slug($this->getRelatedModelWithoutNamespace($field))),
            '{{singularMorphField}}' =>  Str::singular(Str::slug($this->getRelatedModelWithoutNamespace($field))),
            '{{singularMorphClass}}' =>  Str::singular(Str::studly($this->getRelatedModelWithoutNamespace($field))),
            '{{singularMorphSlug}}'  =>  Str::singular(Str::slug($this->getRelatedModelWithoutNamespace($field))),
        ];
    }

    protected function parseRelationName(string $model_name, string $related_full_name): array
    {

        // on recupere le nom du modele sans le namespace
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

    protected function getMigrationFieldType(array $field) :string
    {
        $type = $this->getNonRelationType($field);
        // si la valeur est un tableau c'est que nous avons un champ de type relation
        // pas la paine d'aller plus loin
        if ($this->isRelationField($type)) return '';

        if ($type === 'image') {
            return 'string';
        }
        return Str::lower($type);
    }





    protected function getMorphFieldName($field)
    {
        return Str::plural(Str::slug($this->modelNameWithoutNamespace($this->getRelatedModel($field))));
    }


    protected function getSingularMorphFieldName($field)
    {
        return Str::singular(Str::slug($this->modelNameWithoutNamespace($this->getRelatedModel($field))));
    }



    protected function isImageFIeld($field): bool
    {
        return $field['name'] === 'image';
    }




    protected function translate_field(array $field) :string
    {
        return translate_model_field($field['name'], $field['trans'] ?? null);
    }


    public function modelHasTinymceField(array $model = []) :bool
    {
        if (empty($model)){
            $model = $this->fields;
        }

        foreach ($model as $key =>  $field) {
            if (is_array($field) && Arr::exists($field, 'tinymce') && $field['tinymce'] == true){
                return true;
            }
        }

        return false;
    }


    protected function hasCrudAction(string $key) :bool
    {
        return in_array($key, $this->actions);
    }



    protected function isSimpleRelation(array $field) :bool
    {
        return $this->getRelationType($field) === $this->RELATION_TYPES['simple'];
    }

    protected function isSimpleOneToOneRelation(array $field)
    {
        return
            $this->isSimpleRelation($field) &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['simple']['o2o'];
    }

    protected function isSimpleOneToManyRelation(array $field)
    {
        return
            $this->isSimpleRelation($field) &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['simple']['o2m'];
    }

    protected function isSimpleManyToOneRelation(array $field)
    {
        return
            $this->isSimpleRelation($field) &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['simple']['m2o'];
    }

    protected function isSimpleManyToManyRelation(array $field)
    {
        return
            $this->isSimpleRelation($field) &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['simple']['m2m'];
    }

    protected function isPolymorphicOneToOneRelation(array $field)
    {
        return
            $this->getRelationType($field) === $this->RELATION_TYPES['polymorphic'] &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['polymorphic']['o2o'];
    }

    protected function isPolymorphicOneToManyRelation(array $field)
    {
        return
            $this->getRelationType($field) === $this->RELATION_TYPES['polymorphic'] &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['polymorphic']['o2m'];
    }

    protected function isPolymorphicManyToOneRelation(array $field)
    {
        return
            $this->getRelationType($field) === $this->RELATION_TYPES['polymorphic'] &&
            $this->getRelationName($field)   === $this->RELATION_NAMES['polymorphic']['m2o'];
    }

}
