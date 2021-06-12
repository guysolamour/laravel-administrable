<?php

namespace Guysolamour\Administrable\Console\Administrable;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\BaseCommand;
use Guysolamour\Administrable\Console\Filesystem;


class AdminInstallCommand extends BaseCommand
{
    /** @var string */
    protected $guard;

    /** @var string */
    protected $models_folder_name = 'Models';

    /** @var bool */
    protected $migrate;

    /** @var string[] */
    protected  $presets = ['vue', 'react', 'bootstrap'];

    /** @var string */
    protected $preset = 'vue';

    /** @var string[] */
    protected  $themes = ['adminlte', 'theadmin', 'tabler', 'themekit'];

    /** @var bool */
    protected $route_controller_callable_syntax = true;

    /** @var string */
    protected $theme;

    /** @var Filesystem */
    protected $filesystem;

    /** @var array */
    protected $data_map;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:install
                                {guard? : Name of the guard }
                                {--p|preset=vue : Ui preset to use }
                                {--m|model=Models : Models folder name inside app directory }
                                {--s|seed : Seed database with fake data }
                                {--c|route_callable_syntax=true : Use route controller callable syntax }
                                {--r|migrate=true : Run migrations }
                                {--k|debug_packages : Add debug packages (debugbar, pretty routes ..) }
                                {--t|theme= : Theme to use }
                                {--l|locale=fr : Locale to use }
                            ';


    protected $description = 'Install administrable package';




    public function handle()
    {
        $this->info('Initiating...');

        $this->init();

        if ($this->checkIfPackageHasBeenInstalled()) {
            $this->triggerError("The installation has already been done, remove all generated files and run installation again!");
        }

        $this->callSilent('multi-auth:install', [
            'name'    => $this->guard,
        ]);


        $this->call("ui", [
            'type'   => $this->preset,
            '--auth' => true,
        ]);

        //  Administrable yaml file
        $this->displayTitle(PHP_EOL . 'Creating models administrable crud configuration yaml file');
        $path = $this->loadCrudConfiguration();
        $this->info('Administrable crud configuration yaml created at' . $path);

        // Helpers
        $this->displayTitle(PHP_EOL . 'Creating Helper...');
        $path = $this->loadHelpers();
        $this->info('Helper file created at' . $path);

        // Models
        $this->displayTitle(PHP_EOL . 'Creating Model...');
        $path = $this->loadModel();
        $this->info('Model created at ' . $path);

        // Settings
        $this->displayTitle(PHP_EOL . 'Creating Setting...');
        $path = $this->loadSetting();
        $this->info('Setting created at ' . $path);

        // Factories
        $this->displayTitle(PHP_EOL . 'Creating Factory...');
        $factory_path = $this->loadFactory();
        $this->info('Factory created at ' . $factory_path);

        // Traits
        $this->displayTitle(PHP_EOL . 'Creating Traits...');
        $traits_path = $this->loadTraits();
        $this->info('Traits created at ' . $traits_path);

        // Migrations
        $this->displayTitle(PHP_EOL . 'Creating Migrations...');
        $migrations_path = $this->loadMigrations();
        $this->info('Migrations created at ' . $migrations_path);

        // add variable in .env file
        $this->displayTitle(PHP_EOL . 'Adding env variables...');
        $env_path = $this->addEnvVariables();
        $this->info('Set env variables at ' . $env_path);

        // add app config keys
        $this->info(PHP_EOL . 'Adding config keys...');
        $env_path = $this->addAppConfigKeys();
        $this->info('App config set at ' . $env_path);

        // Run migrations
        if ($this->migrate) {
            $this->info(PHP_EOL . 'Migrate');
            $this->call('migrate');
        }

        // Seeds
        $this->info(PHP_EOL . 'Creating Seed...');
        $seed_path = $this->loadSeed();
        $this->info('Seed created at ' . $seed_path);

        // Registering seeder
        $this->info(PHP_EOL . 'Registering seeder...');
        $database_seeder_path = $this->registerSeed();
        $this->info('Seed registered in ' . $database_seeder_path);


        // Controllers
        $this->info(PHP_EOL . 'Creating Controllers...');
        $controllers_path = $this->loadControllers();
        $this->info('Controllers created at ' . $controllers_path);


        // Middleware
        $this->info(PHP_EOL . 'Creating Middleware...');
        $middleware_path = $this->loadMiddleware();
        $this->info('Middleware created at ' . $middleware_path);


         // Route Middleware
        $this->info(PHP_EOL . 'Registering route middleware...');
        $kernel_path = $this->registerRouteMiddleware();
        $this->info('Route middleware registered in ' . $kernel_path);


        // Policies
        $this->info(PHP_EOL . 'Creating Policies...');
        $policies_path = $this->loadPolicies();
        $this->info('Policies created at ' . $policies_path);


        // Forms
        $this->info(PHP_EOL . 'Creating Forms...');
        $forms_path = $this->loadForms();
        $this->info('Forms created at ' . $forms_path);


        // routes
        $this->info(PHP_EOL . 'Creating Routes...');
        $routes_path = $this->loadRoutes();
        $this->info('Routes created at ' . $routes_path);

        // Views
        $this->info(PHP_EOL . 'Creating Views...');
        $admin_views_path = $this->loadViews();
        $this->info('Views created at ' . $admin_views_path);

        // Locales
        $this->info(PHP_EOL . 'Adding locale...');
        $config_path = $this->loadLocale();
        $this->info('Locale added at ' . $config_path);

        // Emails
        $this->info(PHP_EOL . 'Adding email');
        $email_path = $this->loadEmails();
        $this->info('emails created ' . $email_path);

        // Notifications
        $this->info(PHP_EOL . 'Adding notification');
        $notification_path = $this->loadNotifications();
        $this->info('notifications created ' . $notification_path);

        // Utils packages
        $this->info(PHP_EOL . 'Load utils package');
        $this->loadUtilPackage();
        $this->info('Packages loaded successfuly');


        // Commands
        $this->info(PHP_EOL . 'Load commands');
        $kernel_path = $this->loadCommands();
        $this->info('Commands loaded successfuly at ' . $kernel_path);

        // Config
        $this->info(PHP_EOL . 'Load config');
        $config_path = $this->loadConfigs();
        $this->info('Config loaded successfuly at ' . $config_path);

        // Publish assets
        $this->info(PHP_EOL . 'Publishing Assets...');
        $this->publishAssets();
        $this->info('Assets published successfuly');

        // Composer dump-autoload
        $this->info("Running composer dump-autoload");
        $this->runProcess("composer dump-autoload --no-scripts");

        if ($this->option('debug_packages')) {
            // Add debugbar and IDE helper
            $this->info(PHP_EOL . 'Add debugbar, IDE helper some dev packages');
            $this->loadDebugbar();
        }

        // Seed Database
        if ($this->option('seed')) {
            if ($this->migrate) {
                $this->info(PHP_EOL . 'Seeding database...');
                $this->call('db:seed');
            } else {
                $this->info(PHP_EOL . 'Can not seed if migrate option is false. You have to run migrations and seed manually.');
            }
        }


    }

    private function loadDebugbar() :void
    {
        $composer_path = base_path('composer.json');

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($composer_path),
            $search = '"require": {',
            <<<TEXT
            $search
                    "simonschaufi/laravel-dkim": "^1.0",
            TEXT,
            $composer_path
        );

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($composer_path),
            $search = '"require-dev": {',
            <<<TEXT
            $search
                    "garygreen/pretty-routes": "^1.0",
                    "barryvdh/laravel-debugbar": "^3.3",
                    "barryvdh/laravel-ide-helper": "^2.7",
                    "sven/artisan-view": "^3.3",
            TEXT,
            $composer_path
        );

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($composer_path),
            $search = '"scripts": {',
            <<<TEXT
            {$search}
                    "post-update-cmd": [
                        "Illuminate\\\Foundation\\\ComposerScripts::postUpdate",
                        "@php artisan ide-helper:generate --memory --helpers",
                        "@php artisan ide-helper:meta",
                        "@php artisan ide-helper:models --write"
                    ],
            TEXT,
            $composer_path
        );

        $this->call('clear-compiled');

        // composer install
        $this->runProcess("composer update --no-scripts");
    }

    private function publishAssets() :void
    {
        // Make copies
        $this->filesystem->copyDirectory(
            $this->getTemplatePath() . "/assets/{$this->theme}",
            public_path("vendor/{$this->theme}"),
        );

        $this->filesystem->copyDirectory(
            $this->getTemplatePath() . "/resources",
            public_path(),
        );

        $this->filesystem->copyDirectory(
            $this->getTemplatePath() . "/imagemanager",
            public_path('vendor/imagemanager'),
        );

        $this->filesystem->copyDirectory(
            $this->getTemplatePath() . "/tinymce",
            public_path('vendor/tinymce'),
        );
    }

    private function loadConfigs() :string
    {
        $config_path = config_path();
        $config_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . '/config', false);

        $this->filesystem->compliedAndWriteFile(
            $config_stub,
            $config_path
        );

        $auth_config_path = config_path('auth.php');

        $this->filesystem->replaceAndWriteFile(
        $this->filesystem->get($auth_config_path),
        $this->getAppNamespace() . "\Models",
        $this->getAppNamespace() . "\\" . $this->models_folder_name,
        $auth_config_path
        );

        return $config_path;
    }



    private function loadCommands() :string
    {
        $kernel_path = app_path('Console/Kernel.php');
        $kernel = $this->filesystem->get($kernel_path);
        $commands_stub = $this->filesystem->get($this->getTemplatePath() . '/commands/kernel.stub');

        $search = 'protected function schedule(Schedule $schedule)' . PHP_EOL .   '    {';
        $this->filesystem->replaceAndWriteFile(
            $kernel,
            $search,
            $search . PHP_EOL . $commands_stub,
            $kernel_path
        );

        return $kernel_path;
    }

    private function loadProviders() :string
    {
        $provider_path = app_path('/Providers');

        $provider_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . '/providers', false);

        $this->filesystem->compliedAndWriteFile(
            $provider_stub,
            $provider_path
        );

        $blade_sp = 'BladeServiceProvider';
        $blade_sp_path = $provider_path . '/' . $blade_sp . '.php';

        $this->call('cmd:make:provider', [
            'name'       => $blade_sp,
            '--register' => true
        ]);
        $search = <<<TEXT
            public function boot()
            {
        TEXT;

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($blade_sp_path),
            $search,
            <<<TEXT
            $search
                   Blade::include('{$this->data_map['{{frontLowerNamespace}}']}.comments.comments', 'comments');
                   Blade::include('{$this->data_map['{{backLowerNamespace}}']}.partials._seoform', 'seoForm');
                   Blade::include('{$this->data_map['{{frontLowerNamespace}}']}.partials._seotags', 'seoTags');
                   Blade::include('{$this->data_map['{{backLowerNamespace}}']}.partials._daterangepicker', 'daterangepicker');
                   Blade::include('{$this->data_map['{{backLowerNamespace}}']}.partials._select2', 'select2');
                   Blade::include('{$this->data_map['{{backLowerNamespace}}']}.partials._tinymce', 'tinymce');
                   Blade::include('{$this->data_map['{{backLowerNamespace}}']}.media._imagemanager', 'imagemanager');

            TEXT,
            $blade_sp_path
        );

        $search = 'use Illuminate\Support\ServiceProvider;';

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($blade_sp_path),
            $search,
            <<<TEXT
            use Illuminate\Support\Facades\Blade;
            $search
            TEXT,
            $blade_sp_path
        );

        return $provider_path;
    }

    private function loadUtilPackage() :void
    {

        // Telescope
        $this->callSilent('telescope:install');
        $this->callSilent('migrate');

        $this->loadProviders();

        // Backup
        $config_filesystems_path = config_path('filesystems.php');
        $config_filesystem = $this->filesystem->get($config_filesystems_path);

        $this->filesystem->replaceAndWriteFile(
            $config_filesystem,
            $search = "'disks' => [\n",
            $search . PHP_EOL . $this->filesystem->get($this->getTemplatePath() . '/config/partials/filesystem.stub'),
            $config_filesystems_path
        );
    }

    private function loadNotifications() :string
    {
        $notification_path = app_path('Notifications/');

        // front
        $notification_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . "/notifications/{$this->data_map['{{frontLowerNamespace}}']}");
        $this->filesystem->compliedAndWriteFile(
            $notification_stub,
            $notification_path . $this->data_map["{{frontNamespace}}"]
        );

        // back
        $notification_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . "/notifications/{$this->data_map['{{backLowerNamespace}}']}");
        $this->filesystem->compliedAndWriteFileRecursively(
            $notification_stub,
            $notification_path . $this->data_map["{{backNamespace}}"]
        );

        // deletion of default notifications
        $this->filesystem->deleteDirectory($notification_path . $this->data_map['{{singularClass}}']);

        return $notification_path;
    }

    private function loadEmails(): string
    {
        $mail_path = app_path('Mail/');

        // Front
        $mail_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . "/mail/{$this->data_map['{{frontLowerNamespace}}']}");
        $this->filesystem->compliedAndWriteFile(
            $mail_stub,
            $mail_path . $this->data_map["{{frontNamespace}}"]
        );

        // Back
        $mail_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . "/mail/{$this->data_map['{{backLowerNamespace}}']}");
        $this->filesystem->compliedAndWriteFile(
            $mail_stub,
            $mail_path . $this->data_map["{{backNamespace}}"]
        );

        return $mail_path;
    }


    private function loadLocale()
    {
        $locales_path = $this->getTemplatePath() . '/locales/' . $this->option('locale');

        if ($this->filesystem->exists(resource_path("lang/{$this->option('locale')}"))) {
            return;
        }

        $locales_stub = $this->filesystem->getFilesFromDirectory($locales_path, true);
        $this->filesystem->compliedAndWriteFileRecursively(
            $locales_stub,
            resource_path("lang")
        );

        $locale_json = $this->getTemplatePath() . '/locales/' . '/json/' .  $this->option('locale') . '.json';
        $locale_path = resource_path("lang/") . $this->option('locale') . '.json';
        if ($this->filesystem->exists($locale_path)) {
            return;
        }

        $locale_json_stub = $this->filesystem->get($locale_json);
        $this->filesystem->compliedAndWriteFile(
            $locale_json_stub,
            $locale_path
        );

        // change locale configuration in config file
        $config_path = config_path('app.php');

        $search = "'locale' => 'en'";
        $replace = "'locale' => '{$this->option('locale')}'";

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($config_path),
            $search,
            $replace,
            $config_path
        );

        $search = "'faker_locale' => 'en_US'";
        $replace = "'faker_locale' => '" . $this->option('locale') . '_' . strtoupper($this->option('locale')) . "'";
        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($config_path),
            $search,
            $replace,
            $config_path
        );

        return $config_path;
    }

    private function loadViews() :string
    {
        $views_path = resource_path('views/');

        $views_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath("/views/back/{$this->theme}"));

        $this->filesystem->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $this->data_map["{{backLowerNamespace}}"]
        );

        // renaming of the file with the guard
        $this->filesystem->moveDirectory(
            $views_path . '/' . $this->data_map["{{backLowerNamespace}}"] . '/guard',
            $views_path . '/' . $this->data_map["{{backLowerNamespace}}"] . '/' .  $this->data_map['{{pluralSlug}}']
        );

        $views_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . '/views/front');

        $this->filesystem->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $this->data_map["{{frontLowerNamespace}}"]
        );

        $views_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . '/views/vendor');
        $this->filesystem->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . '/vendor'
        );

        // Management of links (aside) in administration
        $aside_path = resource_path("views/{$this->data_map["{{backLowerNamespace}}"]}/partials/_sidebar.blade.php");

        $links_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . "/views/back/{$this->theme}/stubs/sidebar");

        $search = "{{-- insert sidebar links here --}}";

        foreach ($links_stub as $link) {
            $this->filesystem->replaceAndWriteFile(
                $this->filesystem->get($aside_path),
                $search,
                $this->filesystem->compliedFile($link) . PHP_EOL . PHP_EOL . $search,
                $aside_path
            );
        }


        // Notifications link in header
        if ($this->isAdminLteTheme() || $this->isTablerTheme()){
            $header_path = resource_path("views/{$this->data_map["{{backLowerNamespace}}"]}/partials/_header.blade.php");
            $stub = $this->filesystem->get($this->getTemplatePath() . "/views/back/{$this->theme}/stubs/header/notificationLink.blade.stub");

            $search = "{{-- Insert Notification Link --}}";

            $this->filesystem->replaceAndWriteFile(
                $this->filesystem->get($header_path),
                $search,
                $this->filesystem->compliedFile($stub, false),
                $header_path
            );
        }

        $this->loadEmailsViews();

        $this->loadErrorsViews();

        // deleting of views generated by the Multi Auth package
        $this->filesystem->deleteDirectory(resource_path('views/') . $this->data_map['{{singularSlug}}']);

        // Layouts
        $this->filesystem->deleteDirectory(resource_path('views/layouts'));
        $this->filesystem->deleteDirectory(resource_path('views/auth'));

        // Default welcome page
        $this->filesystem->delete(resource_path('views/welcome.blade.php'));

        // Default home page
        $this->filesystem->delete(resource_path('views/home.blade.php'));

        return $views_path;
    }

    private function loadEmailsViews() :void
    {
        $views_path = resource_path('views/emails/');

        $views_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . '/views/emails/back');


        $this->filesystem->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $this->data_map["{{backLowerNamespace}}"]
        );

        $views_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . '/views/emails/front');

        $this->filesystem->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $this->data_map["{{frontLowerNamespace}}"]
        );
    }

    protected function loadErrorsViews() :void
    {
        $views_path = resource_path('views/errors/');

        $views_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . "/views/errors/{$this->theme}");

        $this->filesystem->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path
        );
    }

    private function loadRoutes() :string
    {
        $routes_path = base_path('routes/web/');
        $prefix = $this->getRoutesStubsFolderPrefix();

        // Front routes;
        $route_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath("/routes/web/front/${prefix}"), false);


        $this->filesystem->compliedAndWriteFile(
            $route_stub,
            $routes_path . $this->data_map["{{frontLowerNamespace}}"]
        );

        // Back routes;
        $route_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath("/routes/web/back/${prefix}"), false);


        $this->filesystem->compliedAndWriteFile(
            $route_stub,
            $routes_path . $this->data_map["{{backLowerNamespace}}"]
        );


        // Change RouteServiceProvider
        $complied = $this->filesystem->compliedFile($this->getTemplatePath('/routes/RouteServiceProvider.stub'));

        if (!$this->route_controller_callable_syntax) {
            $complied = str_replace('// protected $namespace', 'protected $namespace', $complied);
        }

        $this->filesystem->writeFile(
            app_path('Providers/RouteServiceProvider.php'),
            $complied,
        );


        // Delete basic routing files
        $this->filesystem->delete([
            base_path('routes/web.php'),
            base_path("routes/{$this->guard}.php"),
        ]);

        $this->filesystem->compliedAndWriteFile(
            $this->filesystem->get($this->getTemplatePath('/routes/web/api.stub')),
            base_path('routes/api.php')
        );

        $this->filesystem->compliedAndWriteFile(
            $this->filesystem->get($this->getTemplatePath('/routes/web/channels.stub')),
            base_path('routes/channels.php')
        );

        return $routes_path;
    }

    private function loadForms() :string
    {

        $guard = $this->data_map['{{singularClass}}'];

        $form_path = app_path('Forms/');

        // Front forms;
        $forms_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/forms/front'), false);


        $this->filesystem->compliedAndWriteFile(
            $forms_stub,
            $form_path . $this->data_map["{{frontNamespace}}"]
        );

        // Back forms;
        $forms_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/forms/back'), false);

        $this->filesystem->compliedAndWriteFile(
            $forms_stub,
            $form_path . $this->data_map["{{backNamespace}}"]
        );

        // Rename some form to add the guard
        $this->filesystem->move(
            $form_path . $this->data_map["{{backNamespace}}"] . '/CreateForm.php',
            $form_path . $this->data_map["{{backNamespace}}"] . '/Create' . $guard . 'Form.php',
        );
        $this->filesystem->move(
            $form_path . $this->data_map["{{backNamespace}}"] . '/GuardForm.php',
            $form_path . $this->data_map["{{backNamespace}}"] . '/' . $guard . 'Form.php',
        );
        $this->filesystem->move(
            $form_path . $this->data_map["{{backNamespace}}"] . '/ResetPasswordForm.php',
            $form_path . $this->data_map["{{backNamespace}}"] . '/Reset' . $guard . 'PasswordForm.php',
        );

        return $form_path;
    }

    private function loadPolicies() :string
    {

        $policies_path = app_path('/Policies');

        $policies_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/policies'), false);

        $this->filesystem->compliedAndWriteFile(
            $policies_stub,
            $policies_path
        );

        return $policies_path;
    }

    private function registerRouteMiddleware() :string
    {
        $kernel_path = app_path('Http/Kernel.php');
        $kernel = $this->filesystem->get($kernel_path);

        $kernel_stub = $this->filesystem->compliedFile($this->getTemplatePath('/middleware/Kernel.stub'));

        $search = 'protected $routeMiddleware = [';


        $kernel = str_replace($search, $search . $kernel_stub,  $kernel);


        $search = 'protected $middleware = [';
        $namespace = $this->data_map['{{namespace}}'];

        $this->filesystem->replaceAndWriteFile(
            $kernel,
            $search,
            <<<HTML
            {$search}
                    \\$namespace\Http\Middleware\RedirectIfNotPaid::class,
            HTML,
            $kernel_path
        );

        return $kernel_path;
    }


    private function loadMiddleware() :string
    {

        $middleware_path = app_path('Http/Middleware');

        // Addition of RedirectIfNotSuper middleware
        $redirect_middleware_stub = $this->getTemplatePath('/middleware/RedirectIfNotSuper.stub');
        $redirect_middleware = $this->filesystem->compliedFile($redirect_middleware_stub);

        $this->filesystem->compliedAndWriteFile(
            $redirect_middleware,
            $middleware_path . '/RedirectIfNotSuper' . $this->data_map['{{singularClass}}'] . '.php'
        );

        // Addition of RedirectIfNotSuper middleware
        $redirect_middleware_stub = $this->getTemplatePath('/middleware/RedirectIfNotPaid.stub');
        $redirect_middleware = $this->filesystem->compliedFile($redirect_middleware_stub);

        $this->filesystem->compliedAndWriteFile(
            $redirect_middleware,
            $middleware_path . '/RedirectIfNotPaid.php'
        );

        // Addition of RedirectIfNotSuper middleware
        $redirect_authenticated_middleware_stub = $this->getTemplatePath('/middleware/RedirectIfAuthenticated.stub');
        $redirect_authenticated_middleware = $this->filesystem->compliedFile($redirect_authenticated_middleware_stub);

        $this->filesystem->compliedAndWriteFile(
            $redirect_authenticated_middleware,
            $middleware_path . '/RedirectIfAuthenticated.php'
        );

        // Change middleware redirectifNot{$guard) redirect
        $redirect_if_not = $middleware_path . "/RedirectIfNot{$this->data_map['{{singularClass}}']}.php";
        $provider = $this->filesystem->get($redirect_if_not);

        $search = "'{$this->data_map['{{singularSlug}}']}/login'";
        $replace = "config('administrable.auth_prefix_path').'/login'";

        $this->filesystem->replaceAndWriteFile(
            $provider,
            $search,
            $replace,
            $redirect_if_not
        );

        $redirect_if_not = $middleware_path . "/RedirectIf{$this->data_map['{{singularClass}}']}.php";
        $provider = $this->filesystem->get($redirect_if_not);

        $search = $this->data_map['{{singularSlug}}'] . '.home';
        $replace = $this->data_map['{{singularSlug}}'] . '.dashboard';

        $this->filesystem->replaceAndWriteFile(
            $provider,
            $search,
            $replace,
            $redirect_if_not
        );

        return $middleware_path;
    }


    private function loadControllers() :string
    {
        $guard = $this->data_map['{{singularClass}}'];

        $controllers_path =  app_path('/Http/Controllers/');

        // Front controllers
        $controllers_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/controllers/front'));

        $this->filesystem->compliedAndWriteFileRecursively(
            $controllers_stub,
            $controllers_path . $this->data_map["{{frontNamespace}}"]
        );

        // Back controllers
        $controllers_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/controllers/back'));

        if ($this->isTheAdminTheme()) {
            $theadmin_controllers = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('controllers/' . $this->theme));
            $controllers_stub = array_merge($controllers_stub, $theadmin_controllers);
        }


        $this->filesystem->compliedAndWriteFileRecursively(
            $controllers_stub,
            $controllers_path . $this->data_map["{{backNamespace}}"]
        );

        // Rename the default controller and add the guard so as not to fix it on admin
        $this->filesystem->move(
            $controllers_path . $this->data_map["{{backNamespace}}"] . '/GuardController.php',
            $controllers_path . $this->data_map["{{backNamespace}}"] . '/' . $guard . 'Controller.php',
        );

        // Delete default HomeController
        $this->filesystem->delete([
            $controllers_path . 'HomeController.php',
        ]);

        $this->filesystem->deleteDirectory($controllers_path  . $this->data_map['{{singularClass}}']);
        $this->filesystem->deleteDirectory($controllers_path  . 'Auth');


        return $controllers_path;
    }

    private function registerSeed() :string
    {
        $database_seeder_path = database_path('seeders/DatabaseSeeder.php');
        $seeds = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/seeds') , false);


        // The array_reverse function allows to seeder the category before the
        foreach ($seeds as $seed) {
            $name = $seed->getFileNameWithoutExtension();

            // added guard
            if ($name === 'Seeders') {
                $name = $this->data_map['{{pluralClass}}'] . 'TableSeeder';
            }

            $this->filesystem->replaceAndWriteFile(
                $this->filesystem->get($database_seeder_path),
                $search = "  {\n",
                $search . '         $this->call(' . $name . '::class);' . PHP_EOL,
                $database_seeder_path
            );
        }

        return $database_seeder_path;
    }

    private function loadSeed() :string
    {

        $seeds = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/seeds'), false);

        $seed_path = database_path('seeders');


        $this->filesystem->compliedAndWriteFile(
            $seeds,
            $seed_path
        );

        $this->filesystem->move(
            $seed_path . '/Seeders.php',
            $seed_path . '/' . $this->data_map['{{pluralClass}}'] . 'TableSeeder.php'
        );


        return $seed_path;
    }

    private function addAppConfigKeys() :string
    {
        $app_path = config_path('app.php');

        $app = $this->filesystem->get($app_path);

        $app_stub = $this->filesystem->get($this->getTemplatePath('/env/app.stub'));


        $this->filesystem->replaceAndWriteFile(
            $app,
            $search = "'name' =>",
            $app_stub . '    ' .  $search,
            $app_path
        );

        return $app_path;
    }

    private function addEnvVariables() :string
    {
        $env_path = base_path('.env');

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            "APP_NAME=Laravel",
            'APP_NAME=Administrable',
            $env_path
        );

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            "APP_URL=http://localhost",
            'APP_URL=http://localhost:8000',
            $env_path
        );

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            $search = "APP_ENV",
            <<<TEXT
            APP_FIRST_NAME={$this->guard}
            APP_LAST_NAME={$this->guard}
            APP_SHORT_NAME=lvl
            {$search}
            TEXT,
            $env_path
        );


        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            $search = "MAIL_MAILER",
            <<<TEXT
            FTP_HOST=
            FTP_USERNAME=
            FTP_PASSWORD=


            MODEL_CACHE_ENABLED=false
            COOKIE_CONSENT_ENABLED=true


            MAIL_DKIM_SELECTOR=dkim
            MAIL_DKIM_DOMAIN=
            MAIL_DKIM_PASSPHRASE=''
            MAIL_DKIM_ALGORITHM=rsa-sha256
            MAIL_DKIM_IDENTITY=null


            {$search}
            TEXT,
            $env_path
        );

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            "MAIL_HOST=smtp.mailtrap.io",
            'MAIL_HOST=127.0.0.1',
            $env_path
        );

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            "MAIL_PORT=2525",
            'MAIL_PORT=1030',
            $env_path
        );

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            "MAIL_FROM_ADDRESS=null",
            "MAIL_FROM_ADDRESS={$this->guard}@administrable.com",
            $env_path
        );

        // generate a new key
        $this->call('key:generate');

        return $env_path;
    }

    private function loadMigrations() :string
    {
        // $data_map = $this->parseName();
        $guard = $this->data_map['{{pluralSlug}}'];

        $migrations = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/migrations'), false);

        $migrations_path =  database_path('migrations');

        // removing default user migration
        $this->filesystem->delete([
            Arr::first($this->filesystem->glob($migrations_path . '/*_create_users_table.php'))
        ]);


        $this->filesystem->compliedAndWriteFile(
            $migrations,
            $migrations_path
        );

        // Remove existing guard migrations
        $guard_migration = Arr::first($this->filesystem->glob($migrations_path . '/*_create_' . $guard . '_table.php'));

        $this->filesystem->delete([$guard_migration]);

        // add guard migrations
        $this->filesystem->move(
            $migrations_path . '/provider.php',
            $migrations_path . '/2014_07_24_092010_create_' . $guard . '_table.php',
        );


        $guard_reset_password_migration = Arr::first($this->filesystem->glob($migrations_path . '/*_create_' . $this->data_map['{{singularSlug}}'] . '_password_resets_table.php'));

        $this->filesystem->move(
            $guard_reset_password_migration,
            $migrations_path . '/2014_07_25_092010_create_' . $this->data_map['{{singularSlug}}'] . '_password_resets_table.php',
        );

        // load setting migrations
        $this->call('vendor:publish', [
            '--provider' => 'Spatie\LaravelSettings\LaravelSettingsServiceProvider',
            '--tag'      => 'migrations',
        ]);

        // load media library migrations
        $this->call('vendor:publish', [
            '--provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider',
            '--tag'      => 'migrations',
        ]);

        // load configuration settings
        $setting_migrations_stubs = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/migrations/settings'), false);
        $signature = now();
        $settings_path = database_path('settings');
        $this->filesystem->createDirectoryIfNotExists($settings_path);

        foreach ($setting_migrations_stubs as $setting_stub) {
            $signature = $signature->addMinutes(rand(5, 60));
            $filename_prefix = $signature->format('Y_m_d_His') . '_';

            $this->filesystem->writeFile(
                $settings_path . '/' . $filename_prefix . $setting_stub->getFilenameWithoutExtension() . '.php',
                $this->filesystem->compliedFile($setting_stub, true, $this->data_map)
            );
        }

        // add notifications table migration
        $this->callSilent('notifications:table');


        return $migrations_path;
    }

    protected function loadTraits() :string
    {
        $traits_path = app_path('/Traits');

        $traits_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/traits'), false);

        $this->filesystem->compliedAndWriteFile(
            $traits_stub,
            $traits_path
        );

        return $traits_path;
    }

    private function loadFactory(): string
    {
        $factory_stub = $this->filesystem->complied($this->getTemplatePath('factories/factory.stub'));


        $this->filesystem->compliedAndWriteFile(
            $factory_stub,
            $factory_path = database_path('factories/' . $this->data_map['{{singularClass}}'] . 'Factory.php')
        );

        $path = database_path('factories/UserFactory.php');
        $user_factory = $this->filesystem->get($path);

        $search = '\'name\' => $this->faker->name,';
        $replace = '            \'pseudo\' => $this->faker->userName,';

        $this->filesystem->replaceAndWriteFile(
            $user_factory,
            $search,
            $search . PHP_EOL . $replace,
            $path,
        );

        return $factory_path;
    }

    private  function loadSetting() :string
    {
        $settings = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('settings'), false);

        $setting_path = app_path('Settings');

        $this->filesystem->compliedAndWriteFile(
            $settings,
            $setting_path
        );

        return $setting_path;
    }

    private function loadModel(): string
    {
        $models = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/models'), false);

        $model_path =  app_path($this->models_folder_name);

        if ($this->filesystem->exists($model_path)) {
            $this->filesystem->deleteDirectory($model_path);
        }

        $this->filesystem->compliedAndWriteFile(
            $models,
            $model_path
        );

        $this->filesystem->renameFile($model_path , '/Model.php',$this->data_map['{{singularClass}}'] . '.php');

        return $model_path;
    }

    private function loadHelpers() :string
    {
        $this->call('cmd:make:helper', [
            'name' => 'helpers',
        ]);

        $helper_path = app_path('Helpers');


        $helper_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/helpers'));

        $this->filesystem->compliedAndWriteFile(
            $helper_stub,
            $helper_path
        );

        return $helper_path;
    }


    private function updateConfigFile() :void
    {
        $this->updateThemeConfig();
        $this->updateGuardConfig();
        $this->updateControllerCallbackSyntaxConfig();
    }

    private function updateThemeConfig() :void
    {
        $config_path = $this->getConfigFilePath();

        $theme = config('administrable.theme');
        if ($theme !== $this->theme) {
            $this->filesystem->replaceAndWriteFile(
                $this->filesystem->get($config_path),
                "'theme' => '{$theme}',",
                "'theme' => '{$this->theme}',",
                $config_path
            );
        }
    }

    private function updateGuardConfig() :void
    {
        $config_path = $this->getConfigFilePath();

        $guard = config('administrable.guard');
        if ($guard !== $this->guard) {
            $this->replaceAndWriteFile(
                $this->filesystem->get($config_path),
                "'guard' => '{$guard}',",
                "'guard' => '{$this->guard}',",
                $config_path
            );
        }
    }

    private function updateControllerCallbackSyntaxConfig() :void
    {
        $config_path = $this->getConfigFilePath();

        if (!in_array($this->option('route_callable_syntax'), ['true', 'false'])) {
            $this->triggerError("The route callback syntax must be [true] or [false] instead of [{$this->option('route_callable_syntax')}]");
        }

        if ($this->option('route_callable_syntax') === 'false') {
            $this->filesystem->replaceAndWriteFile(
                $this->filesystem->get($config_path),
                "'route_controller_callable_syntax' => true,",
                "'route_controller_callable_syntax' => false,",
                $config_path
            );

            $this->route_controller_callable_syntax = false;
        }
    }

    private function getConfigFilePath() :string
    {
        if ($this->filesystem->exists(config_path('administrable.php'))) {
            $config_path = config_path('administrable.php');
        } else {
            $config_path = $this->getPackagePath('/config/administrable.php');
        }

        return $config_path;
    }

    private function loadCrudConfiguration() :string
    {
        $path = $this->getConfigurationYamlPath();

        $stub = $this->getTemplatePath('/crud/configuration/administrable.stub');

        $complied = $this->filesystem->compliedFile($stub);

        $this->filesystem->writeFile(
            $path,
            $complied
        );

        $this->updateConfigFile();

        return $path;
    }

    private function init() :void
    {
        $this->setGuard();
        $this->setModelsFolderName();
        $this->setMigrate();
        $this->setPreset();
        $this->setTheme();

        $this->data_map = $this->getParsedName();
        $this->filesystem = new Filesystem($this->data_map);
    }

    private function setGuard(): void
    {
        $guard = config('administrable.guard');

        if ($this->argument('guard')) {
            $guard = Str::lower($this->argument('guard'));
        }

        $this->guard = $guard; ;
    }

    private function setPreset(): void
    {
        $preset = Str::lower($this->option('preset'));

        if (!in_array($preset, $this->presets)) {
            $this->triggerError(sprintf('The {%s} preset is not available. Available presets are {%s}', $preset, join(',', $this->presets)));
        }

        $this->preset = $preset;
    }

    private function setModelsFolderName() :void
    {
        $model_folder = Str::ucfirst($this->option('model'));

        if (!$model_folder){
            return;
        }

        $this->models_folder_name = $model_folder;
    }

    private function setMigrate() :void
    {
        $this->migrate = $this->option('migrate') === 'true';
    }

	private function setTheme() :void
	{
        $theme = $this->option('theme') ? Str::lower($this->option('theme')) : Str::lower(config('administrable.theme', 'theadmin'));

        if (!in_array($theme, $this->themes)) {
            $this->triggerError(sprintf('The {%s} theme is not available. Available theme are {%s}', $theme, join(',', $this->themes)));
        }

        $this->theme = $theme;
	}




}
