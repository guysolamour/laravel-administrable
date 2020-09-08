<?php

namespace Guysolamour\Administrable;

use Guysolamour\Administrable\Console\DeployCommand;
use Guysolamour\Administrable\Console\Crud\AddCrudCommand;
use Guysolamour\Administrable\Console\Crud\MakeCrudCommand;
use Guysolamour\Administrable\Console\Crud\RollbackCrudCommand;
use Guysolamour\Administrable\Console\Administrable\AdminInstallCommand;
use Guysolamour\Administrable\Console\Administrable\CreateAdministrableCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH       = __DIR__ . '/../config/administrable.php';
    const ASSETS_PATH       = __DIR__ . '/stubs/assets';
    const LOCALE_PATH       = __DIR__ . '/stubs/locales';
    const RESOURCES_PATH    = __DIR__ . '/stubs/resources';
    const IMAGEMANAGER_PATH = __DIR__ . '/stubs/imagemanager';
    const TINYMCE_PATH      = __DIR__ . '/stubs/tinymce';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('administrable.php'),
        ], 'administrable-config');

        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->loadHelperFile();


    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'administrable'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                AdminInstallCommand::class,
                MakeCrudCommand::class,
                AddCrudCommand::class,
                RollbackCrudCommand::class,
                CreateAdministrableCommand::class,
                DeployCommand::class,
            ]);
        }

        // $this->app->bind('admin', function () {
        //     return new Admin();
        // });
    }

    private function loadHelperFile()
    {
        require __DIR__ . '/helpers.php';
    }
}
