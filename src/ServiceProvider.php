<?php

namespace Guysolamour\Administrable;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Validator;
use Illuminate\Console\Scheduling\Schedule;
use Guysolamour\Administrable\Console\DeployCommand;
use Guysolamour\Administrable\Console\Crud\MakeCrudCommand;
use Guysolamour\Administrable\Console\Crud\AppendCrudCommand;
use Guysolamour\Administrable\Jobs\PublishProgrammaticalyPost;
use Guysolamour\Administrable\Console\Crud\RollbackCrudCommand;
use Guysolamour\Administrable\Console\Storage\StorageDumpCommand;
use Guysolamour\Administrable\Console\Administrable\NotPaidCommand;
use Guysolamour\Administrable\Console\Administrable\AdminInstallCommand;
use Guysolamour\Administrable\Console\Extension\Add\AddExtensionCommand;
use Guysolamour\Administrable\Console\Administrable\CreateAdministrableCommand;
use Guysolamour\Administrable\Console\Administrable\UpdateGuardCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        $this->app->bind('administrable-helper', fn () => new Helper);

        $this->scheduleCommands();

        $this->publishes([
            $this->packagePath('config/administrable.php') => config_path('administrable.php'),
        ], 'administrable-config');

        $this->loadViewsFrom($this->packagePath('/resources/views/front'), 'administrable');
        $this->loadViewsFrom($this->packagePath('/resources/views/back/' . config('administrable.theme')), 'administrable');
        $this->loadViewsFrom($this->packagePath('/resources/views/components'), 'administrable');
        $this->loadViewsFrom($this->packagePath('/resources/views/emails'), 'administrable');

        $this->loadHelperFile();

        $this->loadRoutesFrom($this->packagePath("/routes/back.php"));
        $this->loadRoutesFrom($this->packagePath("/routes/front.php"));
        $this->loadTranslationsFrom($this->packagePath('resources/lang'), 'administrable');

        $this->loadMigrationsFrom([
            config('administrable.migrations_path'),
        ]);

        $this->publishes([
            $this->packagePath('/resources/views/back/' . config('administrable.theme') . '/back') => resource_path('views/vendor/administrable/' . strtolower(config('administrable.back_namespace'))),
            $this->packagePath('/resources/views/front') => resource_path('views/vendor/administrable'),
            $this->packagePath('/resources/views/components') => resource_path('views/vendor/administrable/components'),
            $this->packagePath('/resources/views/emails') => resource_path('views/vendor/administrable/emails'),
        ], 'administrable-views');

        $this->publishes([
            $this->packagePath('resources/lang') => resource_path('lang/vendor/administrable'),
        ], 'administrable-lang');

        Blade::include('administrable::front.comments.comments', 'comments');

        $this->loadPolicies([
            config('administrable.modules.comment.model') => config('administrable.modules.comment.front.policy'),
        ]);

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

            if (Extension::state('blog')) {
                $schedule->job(new PublishProgrammaticalyPost)->hourly();
            }

        });
    }

    private function loadValidationRules() :void
    {
        Validator::extend('route_exists', function ($attribute, $value, $parameters, $validator) {
            return Route::has($value);
        });

        Validator::replacer('route_exists', function ($message, $attribute, $rule, $parameters) {
            return str_replace('Route', 'route', $message);
        });
    }

    private function loadPolicies(array $policies) :void
    {
        foreach($policies as $model => $policy){
            Gate::policy($model, $policy);
        }
    }

    private function loadGuardGates() :void
    {
        Gate::define('update-' . config('administrable.guard') .'-password', [config('administrable.modules.auth.back.policy'), 'updatePassword']);
        Gate::define('change-' . config('administrable.guard') .'-avatar', [config('administrable.modules.auth.back.policy'), 'changeAvatar']);
        Gate::define('update-' . config('administrable.guard') , [config('administrable.modules.auth.back.policy'), 'update']);
        Gate::define('delete-' . config('administrable.guard') , [config('administrable.modules.auth.back.policy'), 'delete']);
        Gate::define('create-' . config('administrable.guard') , [config('administrable.modules.auth.back.policy'), 'create']);
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
                UpdateGuardCommand::class,
                DeployCommand::class,
                StorageDumpCommand::class,
                NotPaidCommand::class,
                AddExtensionCommand::class,
            ]);
        }

        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('AdminModule', Module::class);
            $loader->alias('AdminExtension', Extension::class);
        });

    }

    private function loadHelperFile(): void
    {
        require $this->srcPath('/Helpers/helpers.php');
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

