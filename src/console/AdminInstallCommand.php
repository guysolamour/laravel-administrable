<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Support\Arr;


use Illuminate\Support\Facades\Artisan;

class AdminInstallCommand extends BaseCommand
{
    protected  const TPL_PATH = __DIR__. '/../templates';

    /**
     * @var string
     */
    protected  $name = '';

    /**
     * @var boolean
     */
    protected  $exits = false;

    /**
     * @var boolean
     */
    protected  $override = false;


    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:install
                                {name=admin : Name of the guard.}
                                {--f|force : Whether to override existing files}
                                {--l|locale=fr : Locale to use default fr}
                            ';


    protected $description = 'Install admin package';





    /**
     *
     */
    public function handle()
    {

        $this->info('Initiating...');
        $this->name = $this->argument('name');
        $this->override = $this->option('force') ? true : false;


        Artisan::call('multi-auth:install',[
            'name' => $this->name,
            '--force' => $this->override
        ]);

        // faire un composer update pour installer honeypot et faire le test

        // Models
        $model_path = $this->info(PHP_EOL . 'Creating Model...');
        $this->loadModel();
        $this->info('Model created at ' . $model_path);


        // Factories
        $this->info(PHP_EOL . 'Creating Factory...');
        $factory_path = $this->loadFactory();
        $this->info('Factory created at ' . $factory_path);


        // Migrations
        $this->info(PHP_EOL . 'Creating Migrations...');
        $migrations_path = $this->loadMigrations();
        $this->info('Migrations created at ' . $migrations_path);


        // add variable in .env file
        $this->info(PHP_EOL . 'Adding env variables...');
        $env_path = $this->addEnvVariables();
        $this->info('Set env variables at ' . $env_path);


        // add app config keys
        $this->info(PHP_EOL . 'Adding config keys...');
        $env_path = $this->addAppConfigKeys();
        $this->info('App config set at ' . $env_path);


        // Run migrations
        $this->info(PHP_EOL . 'Migrate');
        Artisan::call('migrate');
        $this->info('Migrations done');


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


        // Traits
        $this->info(PHP_EOL . 'Creating Traits...');
        $traits_path = $this->loadTraits();
        $this->info('Traits created at ' . $traits_path);


        // Forms
        $this->info(PHP_EOL . 'Creating Forms...');
        $forms_path = $this->loadForms();
        $this->info('Forms created at ' . $forms_path);


        // lfm congfig
        $this->info(PHP_EOL . 'Creating Lfm config...');
        $config_path = $this->loadLfmConfig(self::TPL_PATH);
        $this->info('Forms created at ' . $config_path);


        // routes
        $this->info(PHP_EOL . 'Creating Routes...');
        $routes_path = $this->loadRoutes();
        $this->info('Routes created at ' . $routes_path);


        // routes and breadcrumbs
        $this->info(PHP_EOL . 'Creating Breadcrumb...');
        $breadcrumbs_path = $this->loadBreadcrumbs();
        $this->info('Breadcrumb created at ' . $breadcrumbs_path);


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


        // Assets
        $this->info(PHP_EOL . 'Publishing Assets...');
        Artisan::call('vendor:publish --tag=administrable-public');
        $this->info('Assets published at ' . public_path('vendor/adminlte'));


        // Seed Database
        $this->info(PHP_EOL . 'Seeding database...');
        $this->seedDatabase();
        $this->info('Database seeding completed successfully.');

    }




    protected function loadModel() :string
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularClass}}'];

        $models = $this->filesystem->files(self::TPL_PATH . '/models');

        $model_path =  app_path('Models');

        $this->compliedAndWriteFile(
            $models,
            $model_path
        );

        // Renommer du model et le déplacer à la racine du dossier app
        $this->filesystem->move(
            $model_path . '/Model.php',
            app_path($guard.'.php')
        );

       return $model_path;
    }

    protected function loadSeed()
    {

        $data_map = $this->parseName();

        $seeds = $this->filesystem->files(self::TPL_PATH . '/seeds');
        $seed_path = database_path('seeds');

        $this->compliedAndWriteFile(
            $seeds,
            $seed_path
        );

        $this->filesystem->move(
            $seed_path . '/Seeder.php',
            $seed_path . '/' . $data_map['{{pluralClass}}'] . 'TableSeeder.php'
        );

        // Register Seeders in DatabaseSeeder


        return $seed_path;
    }

    protected function registerSeed()
    {
        $data_map = $this->parseName();
        $database_seeder_path = database_path('seeds/DatabaseSeeder.php');
        $seeds = $this->filesystem->files(self::TPL_PATH . '/seeds');

        foreach ($seeds as $seed) {
            $name = $seed->getFileNameWithoutExtension();

            // ajout du guard
            if($name === 'Seeder'){
                $name = $data_map['{{pluralClass}}'] . 'TableSeeder';
            }
            $this->replaceAndWriteFile(
                $this->filesystem->get($database_seeder_path),
                $search = "  {\n",
                $search . '         $this->call(' . $name . '::class);' . PHP_EOL,
                $database_seeder_path
            );
        }

        return $database_seeder_path;
    }


    protected function loadFactory() :string
    {
        $data_map = $this->parseName();
        $guard = $data_map['{{singularClass}}'];

        $factory_stub = $this->filesystem->get(self::TPL_PATH . '/factories/factory.stub');

        $this->compliedAndWriteFile(
            $factory_stub,
            $factory_path = database_path('factories/' . $guard . 'Factory.php')
        );

        return $factory_path;
    }


    protected function loadMigrations()
    {
        $data_map = $this->parseName();
        $guard = $data_map['{{pluralSlug}}'];

        $migrations = $this->filesystem->files(self::TPL_PATH . '/migrations');
        $migrations_path =  database_path('migrations');

        $this->compliedAndWriteFile(
            $migrations,
            $migrations_path
        );

        // Remplacer la migration existante
        $this->filesystem->move(
            $migrations_path . '/provider.php',
            $this->filesystem->glob($migrations_path . '/*_create_' . $guard . '_table.php')[0]
        );

        return $migrations_path;
    }

    protected function loadControllers()
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularClass}}'];

        $controllers_path =  app_path('/Http/Controllers/');


        // Back controllers
        $controllers_stub = $this->filesystem->files(self::TPL_PATH . '/controllers/back');
        $this->compliedAndWriteFile(
            $controllers_stub,
            $controllers_path . $data_map["{{backLowerNamespace}}"]
        );

        // Renommage du controller par défaut et ajouter le guard pour ne pas le fixer sur admin
        $this->filesystem->move(
            $controllers_path . $data_map["{{backLowerNamespace}}"] . '/Controller.php',
            $controllers_path . $data_map["{{backLowerNamespace}}"] . '/' .$guard . 'Controller.php',
        );

        // Front controllers
        $controllers_stub = $this->filesystem->files(self::TPL_PATH . '/controllers/front');
        $this->compliedAndWriteFile(
            $controllers_stub,
            $controllers_path . $data_map["{{frontLowerNamespace}}"]
        );


        // Add redirectTo method to auth controllers
        $auth_controllers =  $this->filesystem->files($controllers_path . $guard . '/Auth');
        $search = 'protected $redirectTo = '. "'/{$data_map['{{singularSlug}}']}'" . ';';
        $replace = $this->filesystem->get(self::TPL_PATH . '/controllers/partials/redirectTo.stub');

        $this->replaceAndWriteFile(
            $auth_controllers,
            $search,
            $replace,
            $controllers_path . $data_map["{{backLowerNamespace}}"] . '/Auth'
        );


        // home controller
        $home_controller = $this->filesystem->get($controllers_path . $guard . '/HomeController.php');
        $search = 'protected $redirectTo = ' . "'/{$data_map['{{singularSlug}}']}/login'" . ';';

        $this->replaceAndWriteFile(
            $home_controller,
            $search,
            $replace,
            $controllers_path . $data_map["{{backLowerNamespace}}"] . '/HomeController.php',
        );


        // PseudoEmailLoginTrait;
        $login_controller_path = $controllers_path . $data_map["{{backLowerNamespace}}"] . '/Auth/LoginController.php';
        $login_controller = $this->filesystem->get($login_controller_path);
        $search = 'use AuthenticatesUsers;';
        $replace = $this->filesystem->get(self::TPL_PATH . '/controllers/partials/pseudoemaillogin.stub');
        $this->replaceAndWriteFile(
            $login_controller,
            $search,
            $search . PHP_EOL . PHP_EOL . $replace,
            $login_controller_path,
        );

        // RegisterController
        $register_controller_path = $controllers_path . $data_map["{{backLowerNamespace}}"] . '/Auth/RegisterController.php';
        $register_controller_stub = $this->filesystem->get(self::TPL_PATH . '/controllers/back/auth/RegisterController.stub');

        $this->compliedAndWriteFile(
            $register_controller_stub,
            $register_controller_path
        );

        $this->replaceAndWriteFile(
            $this->filesystem->get($register_controller_path),
            $search,
            $search . PHP_EOL . PHP_EOL . $register_controller_stub,
            $register_controller_path,
        );

        $this->filesystem->deleteDirectory($controllers_path  . $data_map['{{singularClass}}']);


        return $controllers_path;
    }

    protected function loadTraits()
    {

        $traits_path = app_path('/Traits');

        $traits_stub = $this->filesystem->files(self::TPL_PATH . '/traits');

        $this->compliedAndWriteFile(
            $traits_stub,
            $traits_path
        );

        return $traits_path;
    }

    protected function loadForms()
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularClass}}'];

        $form_path = app_path('Forms/');

        // Front forms;
        $forms_stub = $this->filesystem->files(self::TPL_PATH . '/forms/front');
        $this->compliedAndWriteFile(
            $forms_stub,
            $form_path . $data_map["{{frontLowerNamespace}}"]
        );

        // Back forms;
        $forms_stub = $this->filesystem->files(self::TPL_PATH . '/forms/back');
        $this->compliedAndWriteFile(
            $forms_stub,
            $form_path . $data_map["{{backLowerNamespace}}"]
        );

        // Renommer certains form afin d'ajouter le guard
        $this->filesystem->move(
            $form_path . $data_map["{{backLowerNamespace}}"] . '/CreateForm.php',
            $form_path . $data_map["{{backLowerNamespace}}"] . '/Create'. $guard .'Form.php',
        );
        $this->filesystem->move(
            $form_path . $data_map["{{backLowerNamespace}}"] . '/Form.php',
            $form_path . $data_map["{{backLowerNamespace}}"] . '/'. $guard .'Form.php',
        );
        $this->filesystem->move(
            $form_path . $data_map["{{backLowerNamespace}}"] . '/ResetPasswordForm.php',
            $form_path . $data_map["{{backLowerNamespace}}"] . '/Reset'. $guard . 'PasswordForm.php',
        );

        return $form_path;
    }
    /**
     * Load routes
     * @return string
     */
    protected function loadRoutes()
    {

        $data_map = $this->parseName();

        $routes_path = base_path('routes/web/');


        // Front routes;
        $route_stub = $this->filesystem->files(self::TPL_PATH . '/routes/web/front');
        $this->compliedAndWriteFile(
            $route_stub,
            $routes_path . $data_map["{{frontLowerNamespace}}"]
        );

        // Back routes;
        $route_stub = $this->filesystem->files(self::TPL_PATH . '/routes/web/back');
        $this->compliedAndWriteFile(
            $route_stub,
            $routes_path . $data_map["{{backLowerNamespace}}"]
        );


        // Change RouteServiceProvider
        $route_service_provider_stub = $this->filesystem->get(self::TPL_PATH . '/routes/RouteServiceProvider.stub');
        $complied = strtr($route_service_provider_stub, $data_map);

        $this->writeFile(
            $complied,
            app_path('Providers/RouteServiceProvider.php')
        );

        // Suppression des fichiers de routing de base
        $this->filesystem->delete([
            base_path('routes/web.php'),
            base_path('routes/admin.php'),
        ]);

        return $routes_path;
    }


    protected function loadBreadcrumbs()
    {
        $data_map = $this->parseName();
        $guard = $data_map['{{singularSlug}}'];

        // modification du fichier de configuration
        Artisan::call('vendor:publish --tag=breadcrumbs-config');

        $path = config_path('breadcrumbs.php');
        $config_file = $this->filesystem->get($path);

        $search = "base_path('routes/breadcrumbs.php'),";
        $replace = "glob(base_path('routes/breadcrumbs/*.php')),";

        $this->replaceAndWriteFile(
            $config_file,
            $search,
            $replace,
            $path,
        );

        // ajout du breadcrumb par défaut
        $path = '/routes/breadcrumbs/';

        $stub = $this->filesystem->get(self::TPL_PATH . $path .  'default.stub');

        $this->compliedAndWriteFile(
            $stub,
            $breadcrumbs_path = base_path($path) . $guard . '.php'
        );

        return $breadcrumbs_path;
    }

    protected function loadEmailsViews(){
        $data_map = $this->parseName();

        $views_path = resource_path('views/emails/');

        $views_stub = $this->filesystem->allFiles(self::TPL_PATH . '/views/emails/back');
        $this->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $data_map["{{backLowerNamespace}}"]
        );

        $views_stub = $this->filesystem->allFiles(self::TPL_PATH . '/views/emails/front');
        $this->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $data_map["{{frontLowerNamespace}}"]
        );

    }

    protected function loadViews()
    {
        $data_map = $this->parseName();

        $views_path = resource_path('views/');

        $views_stub = $this->filesystem->allFiles(self::TPL_PATH . '/views/back');
        $this->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $data_map["{{backLowerNamespace}}"]
        );

        $views_stub = $this->filesystem->allFiles(self::TPL_PATH . '/views/front');
        $this->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $data_map["{{frontLowerNamespace}}"]
        );

        $this->loadEmailsViews();


        // renommage du dossier avec le guard
        $this->filesystem->moveDirectory(
            $views_path . '/guard',
            $views_path . '/' . $data_map['{{pluralSlug}}']
        );

        // suppression des vues générées par le package Multi Auth
        $this->filesystem->deleteDirectory(resource_path('views/') . $data_map['{{singularSlug}}']);

        return $views_path;
    }
    protected function loadLfmConfig($template_path)
    {
        $data_map = $this->parseName();

        $files = array(
            [
                'stub' => $template_path . '/lfm/lfm.stub',
                'path' => config_path('lfm.php'),
            ],
            [
                'stub' => $template_path . '/lfm/LfmConfigHandler.stub',
                'path' => app_path('/Handlers/LfmConfigHandler.php'),
            ],
        );

        foreach ($files as $file) {
            $stub = file_get_contents($file['stub']);
            $complied = strtr($stub, $data_map);

            $dir = dirname($file['path']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($file['path'], $complied);
        }

    }


    protected function loadMiddleware()
    {

        $data_map = $this->parseName();

        $middleware_path = app_path('Http/Middleware');

        // Ajout de middleware RedirectIfNotSuper
        $redirect_middleware_stub = self::TPL_PATH . '/middleware/RedirectIfNotSuper.stub';
        $redirect_middleware = $this->filesystem->get($redirect_middleware_stub);

        $this->compliedAndWriteFile(
            $redirect_middleware,
            $middleware_path . '/RedirectIfNotSuper' . $data_map['{{singularClass}}'] . '.php'
        );

        // Change middleware redirectifNot{$guard) redirect
        $redirect_if_not = $middleware_path . "/RedirectIfNot{$data_map['{{singularClass}}']}.php";
        $provider = $this->filesystem->get($redirect_if_not);

        $search = "'{$data_map['{{singularSlug}}']}/login'";
        $replace = "config('administrable.auth_prefix_path').'/login'";

        $this->replaceAndWriteFile(
            $provider,
            $search,
            $replace,
            $redirect_if_not
        );

        return $middleware_path;

    }

    protected function registerRouteMiddleware()
    {

        $data_map = $this->parseName();
        $kernel_path = app_path('Http/Kernel.php');
        $kernel = $this->filesystem->get($kernel_path);

        $kernel_stub = $this->filesystem->get(self::TPL_PATH . '/middleware/Kernel.stub');
        $kernel_stub = strtr($kernel_stub, $data_map);


        $search = 'protected $routeMiddleware = [';

        $this->replaceAndWriteFile(
            $kernel,
            $search,
            $search . $kernel_stub,
            $kernel_path
        );

        return $kernel_path;

    }


    protected function addEnvVariables()
    {
        $env_path = base_path('.env');

        $env_stub = $this->filesystem->get(self::TPL_PATH . '/env/env.stub');
        $this->compliedAndWriteFile(
            $env_stub,
            $env_path
        );

        // generate a new key
        Artisan::call('key:generate');

        return $env_path;
    }

    protected function addAppConfigKeys()
    {
        $app_path = config_path('app.php');

        $app = $this->filesystem->get($app_path);

        $app_stub = $this->filesystem->get(self::TPL_PATH . '/env/app.stub');


        $this->replaceAndWriteFile(
            $app,
            $search = "'name' =>",
            $app_stub . '    ' .  $search,
            $app_path
        );

        return $app_path;

    }


    protected function loadLocale()
    {
        $locales_path = self::TPL_PATH . '/locales/' . $this->option('locale');

        if(!$this->filesystem->exists($locales_path)){
            return;
        }

        $locales_stub = $this->filesystem->allFiles($locales_path);
        $this->compliedAndWriteFileRecursively(
            $locales_stub,
            resource_path("lang")
        );

        $locale_json = self::TPL_PATH . '/locales/' . '/json/' .  $this->option('locale') . '.json';
        if($this->filesystem->exists($locale_json)){
            $locale_json_stub = $this->filesystem->get($locale_json);
            $this->compliedAndWriteFile(
                $locale_json_stub,
                resource_path("lang/") . $this->option('locale') . '.json'
            );
        }

        // change locale configuration in config file
        $config_path = config_path('app.php');

        $search = "'locale' => 'en'";
        $replace = "'locale' => '{$this->option('locale')}'";

        $this->replaceAndWriteFile(
            $this->filesystem->get($config_path),
            $search,
            $replace,
            $config_path
        );

        $search = "'faker_locale' => 'en_US'";
        $replace = "'faker_locale' => '". $this->option('locale') . '_' . strtoupper($this->option('locale'))."'";
        $this->replaceAndWriteFile(
            $this->filesystem->get($config_path),
            $search,
            $replace,
            $config_path
        );

        return $config_path;
    }

    public function seedDatabase()
    {
        // update Composer autoload for seeding
        $this->info(".........Running composer dump-autoload");
        $this->runProcess("composer dump-autoload -o");

        // Seed
        $data_map = $this->parseName();
        $seeds = $this->filesystem->files(self::TPL_PATH . '/seeds');

        foreach ($seeds as $seed) {
            $name = $seed->getFileNameWithoutExtension();

            // ajout du guard
            if ($name === 'Seeder') {
                $name = $data_map['{{pluralClass}}'] . 'TableSeeder';
            }

            $this->callSilent('db:seed', [
                '--class' => $name
            ]);
        }

        return app_path('seeds');

    }



    protected function loadNotifications() :string
    {

        // deplacer les notifs auth dans le dossier back
        $data_map = $this->parseName();
        $notification_path = app_path('Notifications/');

        // front
        $notification_stub = $this->filesystem->allFiles(self::TPL_PATH . '/notifications/front');
        $this->compliedAndWriteFile(
            $notification_stub,
            $notification_path . $data_map["{{frontNamespace}}"]
        );

        // back
        $notification_stub = $this->filesystem->allFiles(self::TPL_PATH . '/notifications/back');
        $this->compliedAndWriteFileRecursively(
            $notification_stub,
            $notification_path . $data_map["{{backNamespace}}"]
        );

        // suppression des notifs par défaut
        $this->filesystem->deleteDirectory($notification_path . $data_map['{{singularClass}}']);

        return $notification_path;
    }



    public function loadEmails() :string
    {
        $data_map = $this->parseName();
        // front
        $mail_path = app_path('Mail/');

        $mail_stub = $this->filesystem->allFiles(self::TPL_PATH . '/mail/front');
        $this->compliedAndWriteFile(
            $mail_stub,
            $mail_path . $data_map["{{frontNamespace}}"]
        );

        $mail_stub = $this->filesystem->allFiles(self::TPL_PATH . '/mail/back');
        $this->compliedAndWriteFile(
            $mail_stub,
            $mail_path . $data_map["{{backNamespace}}"]
        );

        return $mail_path;
    }

}
