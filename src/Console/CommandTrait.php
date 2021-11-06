<?php

namespace Guysolamour\Administrable\Console;


use RuntimeException;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;


trait CommandTrait
{

    public function getAppNamespace(): string
    {
        return get_app_namespace();
    }

    public function getTemplatePath(string $path = ''): string
    {
        $path = Str::start($path, '/');

        return $this->getPackagePath('/stubs' . $path);
    }

    public function getBasePath(string $path = ''): string
    {
        $path = Str::start($path, '/');

        return get_template_path() . $path;
    }

    public function getPackagePath(string $path = ''): string
    {
        $path = Str::start($path, '/');

        return dirname((string) get_template_path()) . $path;
    }

    public function triggerError(string $error)
    {
        throw new \Exception($error);
    }

    public function getConfigurationYamlPath(): string
    {
        return base_path('administrable.yaml');
    }
    

    public function displayTitle(string $title): void
    {
        $this->info(PHP_EOL . "<========================================   {$title}   =========================================>" .  PHP_EOL);
    }

    public function displayResult(?bool $result, ?string $path): void
    {
        if ($result) {
            $this->info(PHP_EOL . '<------ File created at ' . $path . '------>' . PHP_EOL);
        } else {
            $this->info(PHP_EOL . "<========================================   {$path}   =========================================>" . PHP_EOL);
        }
    }

    public function isTheadminTheme(): bool
    {
        return $this->isTheme('theadmin');
    }

    public function isThemeKitTheme(): bool
    {
        return $this->isTheme('themekit');
    }

    public function isTheme(string $theme): bool
    {
        return $this->getTheme() === Str::lower($theme);
    }

    public function getTheme(): string
    {
        return config('administrable.theme', 'themekit');
    }

    public function isAdminLteTheme(): bool
    {
        return $this->isTheme('adminlte');
    }

    public function isTablerTheme(): bool
    {
        return $this->isTheme('tabler');
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
