<?php

namespace Guysolamour\Admin;

use Guysolamour\Admin\Console\AdminInstallCommand;
use Guysolamour\Admin\Console\MakeCrudCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/admin.php';
    const ASSETS_PATH = __DIR__ . '/templates/views/assets';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('admin.php'),
        ], 'admin-config');

        $this->publishes([
            self::ASSETS_PATH => public_path('vendor/adminlte'),
        ], 'admin-public');


        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->loadHelpersFile();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'admin'
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
