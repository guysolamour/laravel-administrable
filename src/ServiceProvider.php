<?php

namespace Guysolamour\Admin;

use Guysolamour\Admin\Console\AdminInstallCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/admin.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('admin.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'admin'
        );

        if ($this->app->runningInConsole()) {
            $this->commands([
                AdminInstallCommand::class
            ]);
        }

        $this->app->bind('admin', function () {
            return new Admin();
        });
    }
}
