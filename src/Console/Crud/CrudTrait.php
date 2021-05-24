<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\YamlTrait;

/**
 * @property Crud $crud
 */
trait CrudTrait
{
    use YamlTrait;

    public function getCrudTemplatePath(string $path = '') :string
    {
        $path = Str::start($path, '/');

        return $this->getTemplatePath('/crud' . $path);
    }


    public function getCrudType(): string
    {
        $type = $this->type;

        if ($type === 'string' || $type === 'text' || $type === 'mediumText' || $type === 'json' || $type === 'longText') {
            return 'text';
        } else if ($type === 'integer' || $type === 'mediumInteger' || $type === 'bigInteger') {
            return 'int';
        } else if ($type === 'boolean' || $type === 'enum') {
            return 'bool';
        } else if ($type === 'datetime') {
            return 'date';
        } else if (is_array($type)) {
            return 'relation';
        }

        return $type;
    }

    /**
     *
     * @param string|array $data
     * @param string $separator
     * @return array
     */
    public function parseCrudParameter($data, string $separator = '|') :array
    {
        if (!is_string($data) && !is_array($data)){
            $this->triggerError("The data must be a string or an array");
        }

        if (is_string($data)){
            $data = explode($separator, $data);
        }

        return array_map(fn($item) => trim($item), array_filter($data));
    }

    public function getCrudModel(string $model): array
    {
        $data = Arr::get($this->parseConfigurationYamlFile(), 'models');

        $crud_model = Arr::get($data, Str::ucfirst($model));

        if (!$crud_model) {
            throw new \Exception(
                sprintf("The model [%s] is not defined in the [%s] file.", $model, $this->getConfigurationYamlPath())
            );
        }

        return $crud_model;
    }

   
}
