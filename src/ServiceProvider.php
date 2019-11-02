<?php

namespace Guysolamour\Administrable;

use Guysolamour\Administrable\Console\AdminInstallCommand;
use Guysolamour\Administrable\Console\MakeCrudCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/administrable.php';
    const ASSETS_PATH = __DIR__ . '/templates/views/assets';
    const LOCALE_PATH = __DIR__ . '/templates/locales';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('administrable.php'),
        ], 'administrable-config');

        $this->publishes([
            self::ASSETS_PATH => public_path('vendor/adminlte'),
        ], 'administrable-public');

        $this->publishes([
            self::LOCALE_PATH => resource_path('lang'),
        ], 'administrable-locale');


        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->loadHelpersFile();
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
            ]);
        }

        $this->app->bind('admin', function () {
            return new Admin();
        });
    }

    private function loadHelpersFile()
    {
        foreach (glob(__DIR__ .'/Helpers' . '/*.php') as $file) {
            require_once $file;
        }
    }
}
