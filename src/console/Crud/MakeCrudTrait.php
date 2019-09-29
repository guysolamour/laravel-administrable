<?php
namespace Guysolamour\Administrable\Console\Crud;


use Illuminate\Container\Container;
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

    private function getFieldType(string $field) :string
    {
        if ($field === 'image') {
            return 'string';
        }
        return strtolower($field);
    }



    /**
     * @param string $type
     * @return string
     */
    private function getType(string $type) :string
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
        }else{
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
}
