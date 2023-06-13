<?php

namespace Guysolamour\Administrable;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Validator;
use Illuminate\Console\Scheduling\Schedule;
use Guysolamour\Administrable\Services\Flashy;
use Guysolamour\Administrable\View\Components\Filemanager;
use Guysolamour\Administrable\Jobs\RemoveOrphanTemporaryFiles;
use Guysolamour\Administrable\Console\Administrable\PaidCommand;
use Guysolamour\Administrable\Console\Storage\StorageDumpCommand;
use Guysolamour\Administrable\Console\Administrable\NotPaidCommand;
use Guysolamour\Administrable\Console\Extension\AddExtensionCommand;
use Guysolamour\Administrable\Console\Administrable\CreateGuardCommand;
use Guysolamour\Administrable\Console\Administrable\UpdateGuardCommand;
use Guysolamour\Administrable\Console\Administrable\AdminInstallCommand;
use Guysolamour\Administrable\Console\Administrable\GenerateFrontDashboardCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const PACKAGE_NAME = 'administrable';

    public function boot()
    {
        $this->app->bind('administrable-helper', fn () => new Helper);

        $this->app->singleton('flashy', function(){
            return $this->app->make(Flashy::class);
        });

        $this->scheduleCommands();

        $this->publishes([
            $this->packagePath('config/administrable.php') => config_path('administrable.php'),
        ], 'administrable-config');

        $this->loadViewsFrom($this->packagePath('/resources/views/front'), 'administrable');
        $this->loadViewsFrom($this->packagePath('/resources/views/back/' . config('administrable.theme')), 'administrable');
        $this->loadViewsFrom($this->packagePath('/resources/views/components'), 'administrable');
        $this->loadViewsFrom($this->packagePath('/resources/views/filemanager'), 'administrable');
        $this->loadViewsFrom($this->packagePath('/resources/views/emails'), 'administrable');

        // User dashboard
        if (static::checkIfUserDashboardWasGenerated()){
            $this->loadViewsFrom($this->packagePath('/resources/views/front/' . Str::lower(config('administrable.modules.user_dashboard.theme'))), 'administrable');
        }

        $this->loadHelperFile();

        $this->loadRoutesFrom($this->packagePath("/routes/back.php"));
        $this->loadRoutesFrom($this->packagePath("/routes/front.php"));
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'administrable');

        $this->loadMigrationsFrom([
            config('administrable.migrations_path'),
        ]);

        $this->publishes([
            $this->packagePath('/resources/views/back/' . config('administrable.theme') . '/back') => resource_path('views/vendor/administrable/' . Str::lower(config('administrable.back_namespace'))),
            $this->packagePath('/resources/views/components')  => resource_path('views/vendor/administrable/'. Str::lower(config('administrable.back_namespace')) .'/components'),
            $this->packagePath('/resources/views/components/filemanager') => resource_path('views/vendor/administrable/'. Str::lower(config('administrable.back_namespace')) .'/components/filemanager'),
            $this->packagePath('/resources/views/emails')      => resource_path('views/vendor/administrable/' . Str::lower(config('administrable.back_namespace')) .'/emails'),
        ], 'administrable-back-views');

        $this->publishes([
            $this->packagePath('/resources/views/front/front')       => resource_path('views/vendor/administrable/' . Str::lower(config('administrable.front_namespace'))),
        ], 'administrable-front-views');

        // User dashboard
        if (static::checkIfUserDashboardWasGenerated()) {
            $this->publishes([
                $this->packagePath('/resources/views/front/' . Str::lower(config('administrable.modules.user_dashboard.theme')) . '/front') => resource_path('views/vendor/administrable/' . Str::lower(config('administrable.front_namespace'))),
            ], 'administrable-dashboard-views');
        }

        $this->publishes([
            $this->packagePath('resources/lang') => resource_path('lang/vendor/administrable'),
        ], 'administrable-lang');

        // View component aliases
        Blade::include('administrable::front.comments.comments', 'comments');
        Blade::include('administrable::front.flashy.message', 'flashy');
        Blade::include('administrable::filemanager.image', 'imagemanager');
        Blade::include('administrable::filemanager.front', 'imagemanagerfront');
        Blade::include('administrable::filemanager.back', 'imagemanagerback');
        Blade::include('administrable::filemanager.button', 'filemanagerButton');
        Blade::include('administrable::filemanager.show', 'filemanagerShow');
        Blade::include('administrable::filemanager.guardavatar', 'guardavatar');

        // View aliases
        Blade::include('administrable::helpers.includeback', 'includeback');
        Blade::include('administrable::helpers.includefront', 'includefront');
        Blade::include('administrable::helpers.deleteall', 'deleteall');
        Blade::include('administrable::helpers.dropzone', 'dropzone');


        $this->loadPolicies([
            config('administrable.modules.comment.model') => config('administrable.modules.comment.front.policy'),
        ]);

        Blade::component('administrable-filemanager', Filemanager::class);

        $this->loadGuardGates();

        $this->loadValidationRules();
    }


    private function scheduleCommands() :void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            if (config('administrable.schedule.command.backup')){
                $schedule->command('backup:clean')->weekly();
                $schedule->command('backup:run --only-db')->weekly();
            }

            if (config('administrable.schedule.command.storage')){
                $schedule->command('administrable:storage:dump -s')->weekly();
            }

            if (config('administrable.schedule.command.telescope')){
                $schedule->command('telescope:prune')->daily();
            }

            $schedule->job(new RemoveOrphanTemporaryFiles)->daily();
            $schedule->command('cache:prune-stale-tags')->hourly();
        });
    }

    private function loadValidationRules() :void
    {
        Validator::extend('route_exists', fn ($attribute, $value, $parameters, $validator) => Route::has($value));

        Validator::replacer('route_exists', fn ($message, $attribute, $rule, $parameters) => str_replace('Route', 'route', $message));
    }

    private function loadPolicies(array $policies) :void
    {
        foreach ($policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    private function loadGuardGates() :void
    {
        Gate::define('update-' . config('administrable.guard') .'-password', [config('administrable.modules.auth.back.policy'), 'updatePassword']);
        Gate::define('change-' . config('administrable.guard') .'-avatar', [config('administrable.modules.auth.back.policy'), 'changeAvatar']);
        Gate::define('update-' . config('administrable.guard'), [config('administrable.modules.auth.back.policy'), 'update']);
        Gate::define('delete-' . config('administrable.guard'), [config('administrable.modules.auth.back.policy'), 'delete']);
        Gate::define('create-' . config('administrable.guard'), [config('administrable.modules.auth.back.policy'), 'create']);
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
                CreateGuardCommand::class,
                UpdateGuardCommand::class,
                StorageDumpCommand::class,
                NotPaidCommand::class,
                PaidCommand::class,
                AddExtensionCommand::class,
                GenerateFrontDashboardCommand::class,
            ]);
        }

        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('AdminModule', Module::class);
        });
    }

    private function loadHelperFile(): void
    {
        require_once $this->srcPath('/Helpers/helpers.php');
    }

    private function packagePath(string $path = ''): string
    {
        return  __DIR__ . '/../' . $path;
    }

    private function srcPath(string $path = ''): string
    {
        return  __DIR__ . $path;
    }

    public static function checkIfUserDashboardWasGenerated() :bool
    {
        return file_exists(public_path("vendor/") . Str::lower(config('administrable.modules.user_dashboard.theme')));
    }
}

