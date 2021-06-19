<?php

namespace Guysolamour\Administrable\Console;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;



trait YamlTrait
{
    /**
     * @return array|null
     */
    public function parseConfigurationYamlFile()
    {
        return Yaml::parseFile($this->getConfigurationYamlPath());
    }

      /**
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function getCrudConfiguration(string $key, $default = null)
    {
        return  Arr::get($this->parseConfigurationYamlFile(), $key, $default);
    }


    public function getCrudModelsFolder() :string
    {
        return $this->getCrudConfiguration('folder', 'Models');
    }

    public function getModelsFolder(): string
    {
        return Str::ucfirst(config('administrable.models_folder'));
    }

    /**
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getCrudGlobalConfiguration(?string $key = null, $default = null)
    {
        $globals = $this->getCrudConfiguration('globals');

        if (is_null($key)) {
            return $globals;
        }

        return Arr::get($globals, $key, $default);
    }


}
