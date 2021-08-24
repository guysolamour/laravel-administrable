<?php

namespace Guysolamour\Administrable\Console;


use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;


abstract class BaseCommand extends Command
{
    use CommandTrait;


    protected function parseName(?string $name = null): array
    {
        if (!$name)
            $name = $this->guard;

        return [
            '{{namespace}}'           =>  $this->getAppNamespace(),
            '{{pluralCamel}}'         =>  Str::plural(Str::camel($name)),
            '{{pluralSlug}}'          =>  Str::plural(Str::slug($name)),
            '{{pluralSnake}}'         =>  Str::plural(Str::snake($name)),
            '{{pluralClass}}'         =>  Str::plural(Str::studly($name)),
            '{{singularCamel}}'       =>  Str::singular(Str::camel($name)),
            '{{singularSlug}}'        =>  Str::singular(Str::slug($name)),
            '{{singularSnake}}'       =>  Str::singular(Str::snake($name)),
            '{{singularClass}}'       =>  Str::singular(Str::studly($name)),
            '{{frontNamespace}}'      =>  Str::ucfirst(config('administrable.front_namespace')),
            '{{frontLowerNamespace}}' =>  Str::lower(config('administrable.front_namespace')),
            '{{backNamespace}}'       =>  Str::ucfirst(config('administrable.back_namespace')),
            '{{backLowerNamespace}}'  =>  Str::lower(config('administrable.back_namespace')),
            '{{modelsFolder}}'        =>  Str::ucfirst($this->models_folder_name),
            '{{administrableLogo}}'   =>  config('administrable.logo_url'),
            '{{theme}}'               =>  $this->theme,
        ];
    }

    public function getParsedName(?string $name = null): array
    {
        return $this->parseName($name);
    }

    protected function checkIfPackageHasBeenInstalled(): bool
    {
        return file_exists($this->getConfigurationYamlPath());
    }

    protected function getModelClass(string $model): string
    {
        return sprintf("%s\%s\%s", $this->getAppNamespace(), $this->getModelsFolderWithSubfolderNamespace($model, "\\"), ucfirst($model));
    }

    protected function getAllCrudConfigModels(): array
    {
        return array_filter($this->getCrudConfiguration('models', []), fn ($model) => is_array($model) && Arr::isAssoc($model));
    }


    protected function checkIfCrudHasAlreadyBeenDoneForModel(string $model): bool
    {
        return class_exists($this->getModelClass($model));
    }

    protected function getModelsFolderWithSubfolder(string $model, string $separator = '/'): string
    {
        $model = $this->getCrudModel($model);

        $subfolder = Str::ucfirst(Arr::get($model, 'subfolder', ''));

        if (empty($subfolder)) {
            return Str::ucfirst($this->getCrudGlobalConfiguration('folder', 'Models'));
        }

        return sprintf("%s%s%s", Str::ucfirst($this->getCrudGlobalConfiguration('folder', 'Models')), $separator, $subfolder);
    }


    protected function getModelsFolderWithSubfolderNamespace(string $model, string $separator = '/'): string
    {
        return $this->getModelsFolderWithSubfolder($model, $separator);
    }


    protected function getUnusedCrudConfigModels(): array
    {
        $models = [];

        foreach ($this->getAllCrudConfigModels() as $name => $model) {
            if ($this->checkIfCrudHasAlreadyBeenDoneForModel($name)) {
                continue;
            }

            array_push($models, $name);
        }

        return $models;
    }


    protected function getModel(): ?string
    {
        $model = Str::ucfirst($this->argument('model'));

        if (empty($model)) {
            $models = $this->getUnusedCrudConfigModels();

            if (empty($models)) {
                $this->triggerError("You must defined a model in configuration yaml file");
            }

            $model = $this->choice('Which model will be used ?', $models, array_key_first($models));
        }

        return $model;
    }

    protected function getMigrate(): bool
    {
        return $this->option('migrate') == 'true';
    }

    protected function runMigration(bool $migrate): void
    {
        if (!$migrate) {
            return;
        }

        $this->call('migrate');
    }

    protected function runProcess(string $command)
    {
        $process = new Process(explode(' ', $command), null, null, null, 3600);

        $process->run(function ($type, $buffer) {
            // $this->getOutput()->write('> '.$buffer);
        });

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }
    }
}
