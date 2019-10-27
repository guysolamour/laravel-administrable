<?php
namespace Guysolamour\Administrable\Console\Crud;


use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait MakeCrudTrait
{

    public   $TPL_PATH = __DIR__ . '/../../templates/crud';

    protected function parseName(string $name)
    {
        return $parsed = array(
            '{{namespace}}' => $this->getNamespace(),
            '{{pluralCamel}}' => Str::plural(Str::camel($name)),
            '{{pluralSlug}}' => Str::plural(Str::slug($name)),
            '{{pluralSnake}}' => Str::plural(Str::snake($name)),
            '{{pluralClass}}' => Str::plural(Str::studly($name)),
            '{{singularCamel}}' => Str::singular(Str::camel($name)),
            '{{singularSlug}}' => Str::singular(Str::slug($name)),
            '{{singularSnake}}' => Str::singular(Str::snake($name)),
            '{{singularClass}}' => Str::singular(Str::studly($name)),
        );
    }

    private function getRelatedModelWithoutNamespace(array $field) :string
    {
        return $this->modelNameWithoutNamespace($this->getRelatedModel($field));
    }

    private function parseMorphsName(array $field)
    {
        return [
            '{{pluralMorphField}}' => Str::plural(Str::slug($this->getRelatedModelWithoutNamespace($field))),
            '{{singularMorphField}}' => Str::singular(Str::slug($this->getRelatedModelWithoutNamespace($field))),
            '{{singularMorphClass}}' => Str::singular(Str::studly($this->getRelatedModelWithoutNamespace($field))),
            '{{singularMorphSlug}}' => Str::singular(Str::slug($this->getRelatedModelWithoutNamespace($field))),
        ];
    }

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

    /**
     * @param  string $path
     */
    private function createDirIfNotExists(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function getFieldType($field)
    {
        // si la valeur est un tableau c'est que nous avons un champ de type relation
        // pas la paine d'aller plus loin
        if ($this->isRelationField($field)) return '';

        if ($field === 'image') {
            return 'string';
        }
        return strtolower($field);
    }


    /**
     * @param string|array $type
     * @return bool
     */
    private function isRelationField($type) :bool
    {

        return is_array($type);
    }


    private function getMorphFieldName ($field)
    {
        return Str::plural(Str::slug($this->modelNameWithoutNamespace($this->getRelatedModel($field))));
    }


    private function getSingularMorphFieldName ($field)
    {
        return Str::singular(Str::slug($this->modelNameWithoutNamespace($this->getRelatedModel($field))));
    }


    private function isMorphsFIeld($field) :bool
    {
        return $field['name'] === 'morphs';
    }
    /**
     * @param string $type
     * @return string
     */
    private function getType($type) :string
    {

        if (
            $type === 'string' || $type === 'decimal' || $type === 'double' ||
            $type === 'float')
        {
            return 'text';
        }elseif (
           $type === 'image')
        {
          return 'file';
        }
        elseif (
            $type === 'integer' || $type === 'mediumInteger')
        {
            return 'number';
        } elseif ($type === 'text' || $type === 'mediumText' || $type === 'longText') {
            return 'textarea';
        } elseif ($type === 'email') {
            return 'email';
        } elseif ($type === 'boolean' || $type === 'enum') {
            return 'checkbox';
        } elseif ($type === 'date') {
            return 'date';
        } elseif ($type === 'datetime') {
            return 'datetime';
        }elseif ($this->isRelationField($type)){
            return 'entity';
        }
        else{
            return 'text';
        }
    }


    private function writeFile($path,$content) :bool {
        if (!file_exists($path)) {
            file_put_contents($path, $content);
            return true;
        }

        return false;
    }

    private function modelNameWithoutNamespace(string $model) :string
    {
        $parts = explode('\\', $model);
        return end($parts);
    }

    private function getRelatedModel(array $field) :string
    {
        return $field['type']['relation']['model'];
    }

    private function isImagesMorphRelation($field) :bool
    {
        return $this->getRelatedModel($field) == 'App\\Models\\Image';
    }

    private function getRelatedModelProperty(array $field) :string
    {
        return $field['type']['relation']['property'];
    }

    /**
     * @param string $name
     * @return string
     */
    private function getRelationModelWithoutId(string $name) :string
    {
        return Arr::first(explode('_', $name));
    }


}
