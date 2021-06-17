<?php

namespace Guysolamour\Administrable;

use Guysolamour\Administrable\Console\DeployCommand;
use Guysolamour\Administrable\Console\Crud\MakeCrudCommand;
use Guysolamour\Administrable\Console\Crud\AppendCrudCommand;
use Guysolamour\Administrable\Console\Crud\RollbackCrudCommand;
use Guysolamour\Administrable\Console\Storage\StorageDumpCommand;
use Guysolamour\Administrable\Console\Administrable\NotPaidCommand;
use Guysolamour\Administrable\Console\Administrable\AdminInstallCommand;
use Guysolamour\Administrable\Console\Extension\Add\AddExtensionCommand;
use Guysolamour\Administrable\Console\Administrable\CreateAdministrableCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            $this->packagePath('config/administrable.php') => config_path('administrable.php'),
        ], 'administrable-config');

        $this->loadViewsFrom($this->srcPath('/resources/views'), 'administrable');

        $this->publishes([
            $this->srcPath('/resources/views') => resource_path('views/vendor/administrable'),
        ], 'administrable-views');

        $this->loadRoutesFrom($this->srcPath('/routes/web.php'));

        $this->loadMigrationsFrom(config('administrable.migrations_path'));

        $this->loadHelperFile();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            $this->packagePath('config/administrable.php'),
            'administrable'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                AdminInstallCommand::class,
                MakeCrudCommand::class,
                AppendCrudCommand::class,
                RollbackCrudCommand::class,
                CreateAdministrableCommand::class,
                DeployCommand::class,
                StorageDumpCommand::class,
                NotPaidCommand::class,
                AddExtensionCommand::class,
            ]);
        }
    }

    private function loadHelperFile(): void
    {
        require $this->srcPath('/helpers.php');
    }

    private function packagePath(string $path = ''): string
    {
        return  __DIR__ . '/../' . $path;
    }

    private function srcPath(string $path = ''): string
    {
        return  __DIR__ . $path;
    }
}

