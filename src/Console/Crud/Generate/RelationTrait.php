<?php

namespace Guysolamour\Administrable\Console\Crud\Generate;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\Crud\Field;


/**
 * @property Field $this
 * @static array RELATION_NAMES
 */
trait RelationTrait
{


    public function isPolymorphicRelation() :bool
    {
        return $this->getRelationType()   === self::RELATION_TYPES['polymorphic'];
    }

    public function isPolymorphicOneToOneRelation() :bool
    {
        return
            $this->isPolymorphicRelation() &&
            $this->getRelationName()   === self::RELATION_NAMES['polymorphic']['o2o'];
    }

    public function isPolymorphicOneToManyRelation() :bool
    {
        return
            $this->isPolymorphicRelation() &&
            $this->getRelationName()   === self::RELATION_NAMES['polymorphic']['o2m'];
    }

    public function isPolymorphicManyToManyRelation() :bool
    {
        return
            $this->isPolymorphicRelation() &&
            $this->getRelationName()   === self::RELATION_NAMES['polymorphic']['m2m'];
    }

    public function isSimpleRelation(): bool
    {
        /**
         * @var Field $this
         */
        return $this->getRelationType() === self::RELATION_TYPES['simple'];
    }

    public function isSimpleOneToOneRelation() :bool
    {
        /**
         * @var Field $this
         */
        return
            $this->isSimpleRelation() &&
            $this->getRelationName()  === self::RELATION_NAMES['simple']['o2o'];
    }

    public function isSimpleOneToManyRelation() :bool
    {
        return
            $this->isSimpleRelation() &&
            $this->getRelationName()   === self::RELATION_NAMES['simple']['o2m'];
    }

    public function isSimpleManyToOneRelation() :bool
    {
        /**
         * @var Field $this
         */
        return
            $this->isSimpleRelation() &&
            $this->getRelationName() === self::RELATION_NAMES['simple']['m2o'];
    }

    public function isSimpleManyToManyRelation() :bool
    {
        /**
         * @var Field $this
         */
        return
            $this->isSimpleRelation() &&
            $this->getRelationName() === self::RELATION_NAMES['simple']['m2m'];
    }

    public function getModelAndRelatedModelStubs(): array
    {
        $model_stub   = '';
        $related_stub = '';

        /**
         * @var Field $this
         */
        if ($this->isSimpleOneToOneRelation()) {
            $model_stub = $this->crud->filesystem->get($this->crud->getCrudTemplatePath('/models/relations/simple/onetoone/belongsTo.stub'));
            $related_stub = $this->crud->filesystem->get($this->crud->getCrudTemplatePath('/models/relations/simple/onetoone/hasOne.stub'));
        } else if ($this->isSimpleOneToManyRelation()) {
            $model_stub = $this->crud->filesystem->get($this->crud->getCrudTemplatePath('/models/relations/simple/onetomany/belongsTo.stub'));
            $related_stub = $this->crud->filesystem->get($this->crud->getCrudTemplatePath('/models/relations/simple/onetomany/hasMany.stub'));
        }
        else if ($this->isSimpleManyToManyRelation()) {

            $model_stub = $this->crud->filesystem->get($this->crud->getCrudTemplatePath('/models/relations/simple/manytomany/belongsToMany.stub'));
            $related_stub = $this->crud->filesystem->get($this->crud->getCrudTemplatePath('/models/relations/simple/manytomany/relatedBelongsToMany.stub'));
        } else if ($this->isPolymorphicOneToOneRelation()) {

            $model_stub = $this->crud->filesystem->get($this->crud->getCrudTemplatePath('/models/relations/polymorphic/onetoone/morphOne.stub'));
            $related_stub = '';
        } else if ($this->isPolymorphicOneToManyRelation()) {

            $model_stub = $this->crud->filesystem->get($this->crud->getCrudTemplatePath('/models/relations/polymorphic/onetomany/morphMany.stub'));
            $related_stub = '';
        } else if ($this->isPolymorphicManyToManyRelation()) {
            $model_stub = $this->crud->filesystem->get($this->crud->getCrudTemplatePath('/models/relations/polymorphic/manytomany/morphToMany.stub'));
            $related_stub = $this->crud->filesystem->get($this->crud->getCrudTemplatePath('/models/relations/polymorphic/manytomany/morphedByMany.stub'));
        }

        return [$model_stub, $related_stub];
    }

    public function getRelationRelatedModelPath() :string
    {
        /**
         * @var Field $this
         */

        $path = Str::after(str_replace('\\', '/', $this->getRelationRelatedModel()), $this->crud->getAppNamespace() . '/') . '.php';

        return app_path($path);
    }

    public function getRelationIntermediateTable(bool $guest = false): ?string
    {
        $table_name = $this->getRelationAttribute('intermediate_table');

        if (!$table_name && $guest){
            $table_name = $this->guestIntermediateTableName();
        }

        return $table_name;
    }

    public function getSimpleRelationIntermediateTable() :?string
    {
        return $this->getRelationIntermediateTable(true);
    }
    
    public function getPolymorphicRelationIntermediateTable() :?string
    {
        return Str::plural($this->getPolymorphicRelationMorphName());
    }

    public function guestIntermediateTableName(): string
    {
        /**
         * @var Field $this
         */
        $segments = [
            Str::snake($this->getRelatedModelWithoutNamespace()),
            Str::snake($this->crud->getModel())
        ];

        sort($segments);

        return strtolower(implode('_', $segments));
    }

    public function getRelationRelatedModelTableName(): string
    {
        return $this->getModelInstance()->getTable();
    }

    public function getRelationRelatedModel(): ?string
    {
        return $this->getRelationAttribute('related');
    }

    public function getRelationRelatedModelWithoutNamespace(): ?string
    {
        return Str::afterLast($this->getRelationAttribute('related'), '\\');
    }

    public function getRelationRelatedModelSubfolder() :?string
    {
        $related_model =  Str::after($this->getRelationRelatedModel(), $this->crud->getModelsFolder() . '\\');

        if (!Str::contains($related_model, '\\')){
            return null;
        }

       return Str::beforeLast($related_model, '\\');
    }

    public function checkIfRelationRelatedModelIsInASubfolder() :bool
    {
        return $this->getRelationRelatedModelSubfolder !== null;
    }

    public function getRelationRelatedModelRoute(string $action): string
    {
        /**
         * @var Field $this
         */
        $route = $this->crud->getParsedName()['{{backLowerNamespace}}'] . '.';

        if ($subfolder = $this->getRelationRelatedModelSubfolder()) {
            $route .=$subfolder . '.';
        }

        $route .= $this->getRelationRelatedModelWithoutNamespace() . '.' . $action;

        return Str::lower($route);
    }



    /**
     * getRelationType
     *
     * @return array
     */
    public function getRelationRelatedKeys(): array
    {
        return $this->getRelationAttribute('related_keys');
    }

    /**
     * getRelationType
     *
     * @return array
     */
    public function getRelationLocalKeys(): array
    {
        return $this->getRelationAttribute('local_keys');
    }

    public function modelNameWithoutNamespace(string $model): string
    {
        return class_basename($model);
    }

    public function getRelatedModelWithoutNamespace(): string
    {
        /**
         * @var Field $this
         */
        return $this->modelNameWithoutNamespace($this->getRelatedModel());
    }

    public function getMorphRelationableName(): ?string
    {
        /**
         * @var Field|GenerateModel $this
         */
        $value = Str::lower($this->getPolymorphicAttribute('morphname'));

        return $value ?: $this->name . 'able';
    }

    /**
     * @param string|null $key
     * @return void
     */
    public function getRelationLocalForeignKey(?string $key = null)
    {
        $value = $this->getRelationLocalKeys();

        return is_null($key) ? $value : Arr::get($value, $key);
    }

    /**
     * @param array $field
     * @param string|null $key
     * @return mixed
     */
    public function getRelationRelatedForeignKey(?string $key = null)
    {
        /**
         * @var Field $this
         */
        $value = $this->getRelationRelatedKeys();
        // Arr::get($field['type'], 'related_keys');

        return is_null($key) ? $value : Arr::get($value, $key);
    }



    /**
     * @return array
     */
    public function parseRelationName(): array
    {
        /**
         * @var Field $this
         */
        $model_name = $this->crud->getModel();
        // we recover the name of the model without the namespace
        $related = $this->modelNameWithoutNamespace($this->getRelatedModel());

        return [
            '{{modelPluralSlug}}'       => Str::plural(Str::slug($model_name)),
            '{{modelPluralClass}}'      => Str::plural(Str::studly($model_name)),
            '{{modelSingularClass}}'    => Str::studly($model_name),
            '{{modelSingularSlug}}'     => Str::singular(Str::slug($model_name)),
            '{{relatedSingularClass}}'  => Str::singular(Str::studly($related)),
            '{{relatedPluralSlug}}'     => Str::plural(Str::slug($related)),
            '{{relatedPluralClass}}'    => Str::plural(Str::studly($related)),
            '{{relatedSingularSlug}}'   => Str::singular(Str::slug($related)),
        ];
    }
}
