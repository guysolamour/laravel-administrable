<?php

namespace Guysolamour\Administrable\Console;

use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Artisan;


class AdminInstallCommand extends BaseCommand
{
    protected  const TPL_PATH = __DIR__. '/../stubs';

    /**
     * @var string
     */
    protected  $name = '';


    /**
     * @var array
     */
    protected $crud_models = ['Post','Testimonial','Mailbox'];


    /**
     * Les générations à effectuer par défaut
     * @var array
     */
    protected const DEFAULTS = [
        'models'          => ['BaseModel', 'Configuration', 'Media', 'User', 'Model'],
        'migrations'      => ['User','Administrable','Media','Provider'],
        'seeds'           => ['Configuration','Seeder'],
        'controllers'     => [
            'front'       => ['ConfirmPassword', 'ForgotPassword', 'Login', 'Register', 'ResetPassword', 'Verification','Home','Page','Redirect'],
            'back'        => ['ConfirmPassword','ForgotPassword','Login','Register','ResetPassword','Verification','Configuration','Home','Media','Guard'],
        ],
        'forms' => [
            'front' => [],
            'back'  => ['Configuration','Create','Guard','ResetPassword']
        ],
        'routes' => [
            'front' => ['Auth','Default','Social'],
            'back'  => ['Auth','Configuration','Media','Other','Profile']
        ],
        'breadcrumbs' => [
            'front' => [],
            'back'  => ['Guard']
        ],
        'views' => [
            'front' => ['Auth','Dashboard','Home','Layouts','Legalmention','Partials'],
            'back'  => ['Auth','Configuration','Dashboard','Guard','Layouts','Media','Partials']
        ],
        'emails' => [
            'front' => [],
            'back'  => []
        ],
        'mails' => [
            'front' => [],
            'back'  => []
        ],
        'notifications' => [
            'front' => [],
            'back'  => ['ResetPassword', 'VerifyEmail']
        ],

    ];

    /**
     * @var array
     */
    protected  $presets = ['vue','react', 'bootstrap'];

    /**
     * @var string
     */
    protected $preset = 'vue';

    /**
     * @var array
     */
    protected  $themes = ['adminlte', 'theadmin', 'cooladmin','tabler', 'themekit'];

    /**
     * @var string
     */
    protected $theme = '';


    /**
     * @var string
     */
    protected $models_folder_name = 'Models';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:install
                                {name=admin : Name of the guard }
                                {--g|generate=Mailbox,Testimonial,Post : Default models crud to generate }
                                {--p|preset=vue : Ui preset to use }
                                {--m|model=Models : Models folder name inside app directory }
                                {--s|seed : Seed database with fake data }
                                {--d|create_db : Create database with default connection }
                                {--t|theme= : Theme to use }
                                {--l|locale=fr : Locale to use default fr }
                            ';


    protected $description = 'Install admin package';


    protected function init()
    {

        if ($this->filesystem->exists(base_path('administrable.yaml'))) {
            throw new \Exception("The installation has already been done, remove all generated files and run installation again!");
        }

        $this->name = strtolower($this->argument('name'));

        $this->models_folder_name = ucfirst($this->option('model'));

        /**
         * Le filter permet de retirer les élémenst vides du tableau comme des , simples
         */
        $models = array_filter(explode(',', $this->option('generate')));

        // Sanitize data
        $models = array_map(fn($model) => ucfirst(trim($model)),$models);

        /**
         * Validation
         */
        foreach ($models as $model) {
            if (!in_array(ucfirst($model), $this->crud_models)) {
                throw new \Exception(sprintf('Le modèle {%s} n\'est pas disponible. Les modèles disponible sont {%s}', $model, join(',', $this->crud_models)));
            }
        }


        $this->crud_models = $models;


        $preset = strtolower($this->option('preset'));

        if (!in_array($preset, $this->presets)) {
            throw new \Exception(sprintf('Le preset {%s} n\'est pas disponible. Les presets disponiblent sont {%s}', $preset, join(',', $this->presets)));
        }

        $this->preset = $preset;


        $theme = $this->option('theme') ? strtolower($this->option('theme')) : strtolower(config('administrable.theme','theadmin'));


        if(! in_array($theme, $this->themes)){
            throw new \Exception(sprintf('Le thème {%s} n\'est pas disponible. Les thèmes disponiblent sont {%s}', $theme, join(',', $this->themes)));
        }


        $this->theme = $theme;

    }

    /**
     *
     */
    public function handle()
    {
        $this->info('Initiating...');

        $this->init();

        // Passer des options pour generer les articles, mentions legales, temoignages en option

        $this->callSilent('multi-auth:install', [
            'name'    => $this->name,
        ]);

        // Gerer l'authentification
        $this->call("ui", [
            'type'   => $this->preset,
            '--auth' => true,
        ]);


        //  Administrable yaml file
        $administrable_path = $this->info(PHP_EOL . 'Creating models administrable crud configuration yaml file');
        $this->loadCrudConfiguration();
        $this->info('Administrable crud configuration yaml ' . $administrable_path);


        // Helpers
        $helper_path = $this->info(PHP_EOL . 'Creating Helper...');
        $this->loadHelpers();
        $this->info('Helper created at ' . $helper_path);


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
        $this->call('migrate');


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


        // Move User|Guard={admin} To Models Directory
        $this->info("Moving User.php and ". ucfirst($this->name) . ".php to app/{$this->models_folder_name} folder");
        $this->moveDefaultModelsToNewModelsDirectory();


        // Publish assets
        $this->info(PHP_EOL . 'Publishing Assets...');
        $this->publishAssets();
        $this->info('Assets published successfuly');

        // Composer dump-autoload
        $this->info("Running composer dump-autoload");
        $this->runProcess("composer dump-autoload -o");


        // Add debugbar and IDE helper
        $this->info(PHP_EOL . 'Add debugbar, IDE helper some dev packages');
        // $this->loadDebugbar();


        // Seed Database
        if($this->option('seed')){
            $this->info(PHP_EOL . 'Seeding database...');
            $this->call('db:seed');
        }

    }


    protected function publishAssets()
    {
        // Faire les copies
        $this->filesystem->copyDirectory(
            self::TPL_PATH . "/assets/{$this->theme}",
            public_path("vendor/{$this->theme}"),
        );

        $this->filesystem->copyDirectory(
            self::TPL_PATH . "/resources",
            public_path(),
        );

        $this->filesystem->copyDirectory(
            self::TPL_PATH . "/imagemanager",
            public_path('vendor/imagemanager'),
        );

        $this->filesystem->copyDirectory(
            self::TPL_PATH . "/tinymce",
            public_path('vendor/tinymce'),
        );


    }


    protected function loadCrudConfiguration()
    {
        $path = base_path('administrable.yaml');

        $helper_stub = $this->filesystem->get(self::TPL_PATH . '/crud/configuration/administrable.stub');

        $this->writeFile(
            $this->compliedFile($helper_stub, false),
            $path
        );

        return $path;
    }


    protected function loadHelpers()
    {
        // Helper
        $this->call('make:helper', [
            'name' => 'helpers'
        ]);

        $helper_path = app_path('Helpers');

        // Front
        $helper_stub = $this->filesystem->allFiles(self::TPL_PATH . '/helpers');
        $this->compliedAndWriteFileRecursively(
            $helper_stub,
            $helper_path
        );

        return $helper_path;
    }


    protected function loadModel() :string
    {
        // On ajoute le modele Category si le Post est dans la liste
        if (in_array('Post', $this->crud_models)) {
            $this->crud_models[] = 'Category';
        }

        $models_to_create = [...$this->crud_models, ...self::DEFAULTS['models']];

        $data_map = $this->parseName();

        $guard = $data_map['{{singularClass}}'];

        $models = $this->filesystem->files(self::TPL_PATH . '/models');

        /**
         * Remove uncreate models in the list
         */

        $models = array_filter($models,fn($model) => in_array($model->getFilenameWithoutExtension(), $models_to_create));


        $model_path =  app_path($this->models_folder_name);


        $this->compliedAndWriteFile(
            $models,
            $model_path
        );

        // Renommer du model et le déplacer à la racine du dossier app
        $this->filesystem->move(
            $model_path . '/Model.php',
            app_path($guard.'.php')
        );


        // Renommer du model user et le déplacer à la racine du dossier app
        $this->filesystem->move(
            $model_path . '/User.php',
            app_path('User.php')
        );

        return $model_path;
    }

    /**
     * Move User|Guard={admin} To Models Directory
     */
    protected function moveDefaultModelsToNewModelsDirectory()
    {

        $data_map = $this->parseName();
        $guard = $data_map['{{singularClass}}'];

        foreach (['User', $guard] as $model ) {
            if ($this->filesystem->exists($userPath = app_path("$model.php"))) {
                $this->filesystem->move($userPath, $targetPath = app_path("{$this->models_folder_name}/$model.php"));
                $this->filesystem->put(
                    $targetPath,
                    strtr($this->filesystem->get($targetPath), [
                        "{$this->getNamespace()};" => "{$this->getNamespace()}\\{$this->models_folder_name};",
                    ])
                );

                $this->changeNamespaceEverywhereItUses($model);
            }
        }
    }


    /**
     * Change User|Guard={admin} Namespace Everywhere It Uses
     *
     */
    protected function changeNamespaceEverywhereItUses(string $model)
    {
        $this->info("Changing $model uses and imports from {$this->getNamespace()}\\$model to {$this->getNamespace()}\\{$this->models_folder_name}\\$model");

        $files = Finder::create()
            ->in(base_path())
            ->contains("{$this->getNamespace()}\\$model")
            ->exclude('vendor')
            ->name('*.php');

        foreach ($files as $file) {
            $path = $file->getRealPath();
            if ($this->filesystem->exists($path)) {
                $this->filesystem->put($path, strtr($this->filesystem->get($path), [
                    "{$this->getNamespace()}\\$model" => "{$this->getNamespace()}\\{$this->models_folder_name}\\$model",
                ]));
            }
        }
    }



    protected function loadSeed()
    {
        $data_map = $this->parseName();

        $seeds = $this->filesystem->files(self::TPL_PATH . '/seeds');
        $seed_path = database_path('seeds');


        $seeds = $this->filterSeeds($seeds);


        $this->compliedAndWriteFile(
            $seeds,
            $seed_path
        );

        $this->filesystem->move(
            $seed_path . '/Seeders.php',
            $seed_path . '/' . $data_map['{{pluralClass}}'] . 'TableSeeder.php'
        );

        // Register Seeders in DatabaseSeeder


        return $seed_path;
    }

    private function filterSeeds($seeds) :array
    {
        $seeds_to_create = array_merge(
            $this->crud_models,
            self::DEFAULTS['seeds']
        );
        return array_filter($seeds, function ($seed) use ($seeds_to_create) {

            $name = (string) Str::of($seed->getFilenameWithoutExtension())
                ->before('Table')
                ->singular()
                ->ucfirst();

            return in_array($name, $seeds_to_create);
        });

    }

    protected function registerSeed()
    {

        $data_map = $this->parseName();
        $database_seeder_path = database_path('seeds/DatabaseSeeder.php');
        $seeds = $this->filesystem->files(self::TPL_PATH . '/seeds');

        $seeds = $this->filterSeeds($seeds);


        // La fonction array_reverse permet de seeder la categorie avant les
        foreach ($seeds as $seed) {
            $name = $seed->getFileNameWithoutExtension();

            // ajout du guard
            if($name === 'Seeders'){
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

        $path = database_path('factories/UserFactory.php');
        $user_factory = $this->filesystem->get($path);

        $search = '\'name\' => $faker->name,';
        $replace = '        \'pseudo\' => $faker->userName,';

        $this->replaceAndWriteFile(
            $user_factory,
            $search,
            $search . PHP_EOL . $replace,
            $path,
        );

        return $factory_path;
    }


    protected function loadMigrations()
    {
        $data_map = $this->parseName();
        $guard = $data_map['{{pluralSlug}}'];

        $migrations = $this->filesystem->files(self::TPL_PATH . '/migrations');

        $migrations_to_create = array_merge(self::DEFAULTS['migrations'], $this->crud_models);


        $migrations = array_filter($migrations, function($migration) use($migrations_to_create) {
            /**
             * On recupere le nom de la migration grace au model
             */
            $name = (string) Str::of($migration->getFilenameWithoutExtension())
                ->after('create_')
                ->before('_table')
                ->singular()
                ->ucfirst()
                ;

                if ($name == 'Medium') {
                    $name = Str::plural($name);
                }
            return in_array($name, $migrations_to_create);
        });

        $migrations_path =  database_path('migrations');

        // suppression de la migration user par défaut
        $this->filesystem->delete([
            $this->filesystem->glob($migrations_path . '/*_create_users_table.php')[0]
        ]);


        $this->compliedAndWriteFile(
            $migrations,
            $migrations_path
        );

        // Remplacer la migration existante
        $this->filesystem->move(
            $migrations_path . '/provider.php',
            $this->filesystem->glob($migrations_path . '/*_create_' . $guard . '_table.php')[0]
        );

        // Remplacer la migration des users

        return $migrations_path;
    }



    protected function loadControllers()
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularClass}}'];

        $controllers_path =  app_path('/Http/Controllers/');

        $controllers_to_create = array_merge(self::DEFAULTS['controllers']['front'], $this->crud_models);
        // remplacer Mailbox par contact
        if(in_array('Mailbox', $this->crud_models)) {
            $controllers_to_create[] = 'Contact';
        }
        // Front controllers
        $controllers_stub = $this->filesystem->allFiles(self::TPL_PATH . '/controllers/front');

        $controllers_stub = array_filter($controllers_stub, function ($controller) use ($controllers_to_create) {
            $name = (string) Str::of($controller->getFilenameWithoutExtension())->before('Controller');

            return in_array($name, $controllers_to_create);
        });

        $this->compliedAndWriteFileRecursively(
            $controllers_stub,
            $controllers_path . $data_map["{{frontNamespace}}"]
        );

        $controllers_to_create = [...self::DEFAULTS['controllers']['back'], ...$this->crud_models];
        // Back controllers
        $controllers_stub = $this->filesystem->allFiles(self::TPL_PATH . '/controllers/back');
        $controllers_stub = array_filter($controllers_stub, function ($controller) use ($controllers_to_create) {
            $name = (string) Str::of($controller->getFilenameWithoutExtension())->before('Controller');
            return in_array($name, $controllers_to_create);
        });


        $this->compliedAndWriteFileRecursively(
            $controllers_stub,
            $controllers_path . $data_map["{{backNamespace}}"]
        );

        // Renommage du controller par défaut et ajouter le guard pour ne pas le fixer sur admin
        $this->filesystem->move(
            $controllers_path . $data_map["{{backNamespace}}"] . '/GuardController.php',
            $controllers_path . $data_map["{{backNamespace}}"] . '/' .$guard . 'Controller.php',
        );

        // Delete default HomeController
        $this->filesystem->delete([
            $controllers_path . 'HomeController.php',
        ]);

        $this->filesystem->deleteDirectory($controllers_path  . $data_map['{{singularClass}}']);
        $this->filesystem->deleteDirectory($controllers_path  . 'Auth');


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

        $forms_to_create = array_merge(self::DEFAULTS['forms']['front'], $this->crud_models);
        // remplacer Mailbox par contact
        if (in_array('Mailbox', $this->crud_models)) {
            $forms_to_create[] = 'Contact';
        }

        $forms_stub = array_filter($forms_stub, function ($form) use ($forms_to_create) {
            $name = (string) Str::of($form->getFilenameWithoutExtension())->before('Form');
            return in_array($name, $forms_to_create);
        });


        $this->compliedAndWriteFile(
            $forms_stub,
            $form_path . $data_map["{{frontNamespace}}"]
        );

        // Back forms;
        $forms_stub = $this->filesystem->files(self::TPL_PATH . '/forms/back');

        $forms_to_create = array_merge(self::DEFAULTS['forms']['back'], $this->crud_models);

        $forms_stub = array_filter($forms_stub, function ($form) use ($forms_to_create) {
            $name = (string) Str::of($form->getFilenameWithoutExtension())->before('Form');
            return in_array($name, $forms_to_create);
        });

        $this->compliedAndWriteFile(
            $forms_stub,
            $form_path . $data_map["{{backNamespace}}"]
        );

        // Renommer certains form afin d'ajouter le guard
        $this->filesystem->move(
            $form_path . $data_map["{{backNamespace}}"] . '/CreateForm.php',
            $form_path . $data_map["{{backNamespace}}"] . '/Create'. $guard .'Form.php',
        );
        $this->filesystem->move(
            $form_path . $data_map["{{backNamespace}}"] . '/GuardForm.php',
            $form_path . $data_map["{{backNamespace}}"] . '/'. $guard .'Form.php',
        );
        $this->filesystem->move(
            $form_path . $data_map["{{backNamespace}}"] . '/ResetPasswordForm.php',
            $form_path . $data_map["{{backNamespace}}"] . '/Reset'. $guard . 'PasswordForm.php',
        );

        return $form_path;
    }


    protected function loadRoutes()
    {

        $data_map = $this->parseName();

        $routes_path = base_path('routes/web/');

        // Front routes;
        $route_stub = $this->filesystem->files(self::TPL_PATH . '/routes/web/front');

        $routes_to_create = array_merge(self::DEFAULTS['routes']['front'], $this->crud_models);
        // remplacer Mailbox par contact
        if (in_array('Mailbox', $this->crud_models)) {
            $routes_to_create[] = 'Contact';
        }

        $route_stub = array_filter($route_stub, function ($form) use ($routes_to_create) {
            return in_array(ucfirst($form->getFilenameWithoutExtension()), $routes_to_create);
        });


        $this->compliedAndWriteFile(
            $route_stub,
            $routes_path . $data_map["{{frontLowerNamespace}}"]
        );

        // Back routes;
        $route_stub = $this->filesystem->files(self::TPL_PATH . '/routes/web/back');

        $routes_to_create = array_merge(self::DEFAULTS['routes']['back'], $this->crud_models);


        $route_stub = array_filter($route_stub, function ($form) use ($routes_to_create) {
            return in_array(ucfirst($form->getFilenameWithoutExtension()), $routes_to_create);
        });

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
            base_path("routes/{$this->name}.php"),
        ]);

        $this->compliedAndWriteFile(
            $this->filesystem->get(self::TPL_PATH . '/routes/web/api.stub'),
            base_path('routes/api.php')
        );

        $this->compliedAndWriteFile(
            $this->filesystem->get(self::TPL_PATH . '/routes/web/channels.stub'),
            base_path('routes/channels.php')
        );

        return $routes_path;
    }



    protected function loadEmailsViews(){
        $data_map = $this->parseName();

        $views_path = resource_path('views/emails/');

        $views_stub = $this->filesystem->allFiles(self::TPL_PATH . '/views/emails/back');
        $emails_to_create = [...self::DEFAULTS['emails']['back'],...$this->crud_models];


        if(in_array('Mailbox', $emails_to_create)){
            $emails_to_create[] = 'Contact';
        }

        $views_stub = (array_filter(
            $views_stub,
            fn ($view) => in_array(ucfirst(Str::before($view->getFilenameWithoutExtension(), '.')), $emails_to_create)
        ));

        $this->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $data_map["{{backLowerNamespace}}"]
        );


        $views_stub = $this->filesystem->allFiles(self::TPL_PATH . '/views/emails/front');
        $emails_to_create = [...self::DEFAULTS['emails']['front'], ...$this->crud_models];

        if (in_array('Mailbox', $emails_to_create)) {
            $emails_to_create[] = 'Contact';
            $emails_to_create[] = 'Sendmessagecopy';
        }

        $views_stub = (array_filter(
            $views_stub,
            fn ($view) => in_array(ucfirst(Str::before($view->getFilenameWithoutExtension(), '.')), $emails_to_create)
        ));

        $this->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $data_map["{{frontLowerNamespace}}"]
        );

    }

    protected function loadErrorsViews(){

        $views_path = resource_path('views/errors/');

        $views_stub = $this->filesystem->allFiles(self::TPL_PATH . "/views/errors/{$this->theme}");

        $this->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path
        );

    }

    protected function loadViews()
    {
        $data_map = $this->parseName();

        $views_path = resource_path('views/');

        // Mettre les models au pluriel pour les Views
        $crud_models = array_map(fn($item) => Str::plural($item),$this->crud_models);


        $views_stub = $this->filesystem->allFiles(self::TPL_PATH . "/views/back/{$this->theme}");
        $views_to_create = [...self::DEFAULTS['views']['back'], ...$crud_models];



        $views_stub = array_filter($views_stub, fn($view) => in_array(ucfirst(Str::before($view->getRelativePathname(), '/')), $views_to_create));

        $this->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $data_map["{{backLowerNamespace}}"]
        );

        // renommage du dossier avec le guard
        $this->filesystem->moveDirectory(
            $views_path . '/' . $data_map["{{backLowerNamespace}}"] .'/guard',
            $views_path . '/' . $data_map["{{backLowerNamespace}}"] . '/' .  $data_map['{{pluralSlug}}']
        );


        $views_stub = $this->filesystem->allFiles(self::TPL_PATH . '/views/front');
        $views_to_create = [...self::DEFAULTS['views']['front'], ...$crud_models];

        if (in_array('Mailboxes', $views_to_create)) {
            $views_to_create[] = 'Contact';
        }

        $views_stub = array_filter($views_stub, fn ($view) => in_array(ucfirst(Str::before($view->getRelativePathname(), '/')), $views_to_create));


        $this->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $data_map["{{frontLowerNamespace}}"]
        );

        $views_stub = $this->filesystem->allFiles(self::TPL_PATH . '/views/vendor');
        $this->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . '/vendor'
        );


        // Gestion des liens (aside) en administration
        $aside_path = resource_path("views/{$data_map["{{backLowerNamespace}}"]}/partials/_sidebar.blade.php");


        $links_stub = $this->filesystem->files(self::TPL_PATH . "/views/back/{$this->theme}/stubs");

        $links_stub = array_filter($links_stub, function ($link) {
            $name = ucfirst(Str::before($link->getFileNameWithoutExtension(), 'Link'));
            return in_array($name, $this->crud_models);
        });
        $search = "{{-- insert sidebar links here --}}";

        foreach ($links_stub as $link) {
            $this->replaceAndWriteFile(
                $this->filesystem->get($aside_path),
                $search,
                $this->compliedFile($link) . PHP_EOL . PHP_EOL . $search,
                $aside_path
            );
        }


        // Gestion du lien dans le header
        if (in_array('Mailbox', $this->crud_models)) {
            $header_path = resource_path("views/{$data_map["{{backLowerNamespace}}"]}/partials/_header.blade.php");
            $stub = $this->filesystem->get(self::TPL_PATH . "/views/back/{$this->theme}/stubs/headerLink.blade.stub");

            $search = "{{-- Insert Mailbox Link --}}";

            $this->replaceAndWriteFile(
                $this->filesystem->get($header_path),
                $search,
                $this->compliedFile($stub, false),
                $header_path
            );
        }



        $this->loadEmailsViews();


        $this->loadErrorsViews();


        // suppression des vues générées par le package Multi Auth
        $this->filesystem->deleteDirectory(resource_path('views/') . $data_map['{{singularSlug}}']);

        // Layouts
        $this->filesystem->deleteDirectory(resource_path('views/layouts'));
        $this->filesystem->deleteDirectory(resource_path('views/auth'));

        // Default welcome page
        $this->filesystem->delete(resource_path('views/welcome.blade.php'));

        // Default home page
        $this->filesystem->delete(resource_path('views/home.blade.php'));

        return $views_path;
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

        // Ajout de middleware RedirectIfNotSuper
        $redirect_authenticated_middleware_stub = self::TPL_PATH . '/middleware/RedirectIfAuthenticated.stub';
        $redirect_authenticated_middleware = $this->filesystem->get($redirect_authenticated_middleware_stub);

        $this->compliedAndWriteFile(
            $redirect_authenticated_middleware,
            $middleware_path . '/RedirectIfAuthenticated.php'
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

        $redirect_if_not = $middleware_path . "/RedirectIf{$data_map['{{singularClass}}']}.php";
        $provider = $this->filesystem->get($redirect_if_not);

        $search = $data_map['{{singularSlug}}'] . '.home';
        $replace = $data_map['{{singularSlug}}'] . '.dashboard';

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
        // $data_map = $this->parseName();
        $env_path = base_path('.env');
        //$env = $this->filesystem->get($env_path);



        $this->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            "APP_NAME=Laravel",
            'APP_NAME=Administrable',
            $env_path
        );

        $this->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            $search = "APP_ENV",
            <<<TEXT
            APP_FIRST_NAME={$this->name}
            APP_LAST_NAME={$this->name}
            APP_SHORT_NAME=lvl
            {$search}
            TEXT,
            $env_path
        );


        $this->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            $search = "MAIL_MAILER",
            <<<TEXT
            FTP_HOST=
            FTP_USERNAME=
            FTP_PASSWORD=


            MODEL_CACHE_ENABLED=false
            COOKIE_CONSENT_ENABLED=true


            {$search}
            TEXT,
            $env_path
        );

        $this->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            "MAIL_HOST=smtp.mailtrap.io",
            'MAIL_HOST=127.0.0.1',
            $env_path
        );

        $this->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            "MAIL_PORT=2525",
            'MAIL_PORT=1030',
            $env_path
        );

        $this->replaceAndWriteFile(
            $this->filesystem->get($env_path),
            "MAIL_FROM_ADDRESS=null",
            "MAIL_FROM_ADDRESS={$this->name}@administrable.com",
            $env_path
        );

        // generate a new key
        $this->call('key:generate');

        // Create database
        if($this->option('create_db')){
            $this->call('commands:db:create',[
                '--connection' => config('database.default')
            ]);
        }

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
        // $this->info("Running composer dump-autoload");
        // $this->runProcess("composer dump-autoload -o");

        // Seed
        $data_map = $this->parseName();
        $seeds = $this->filesystem->files(self::TPL_PATH . '/seeds');

        foreach ($seeds as $seed) {
            $name = $seed->getFileNameWithoutExtension();

            // ajout du guard
            if ($name === 'Seeder') {
                $name = $data_map['{{pluralClass}}'] . 'TableSeeder';
            }

            $this->call('db:seed', [
                '--class' => $name
            ]);
        }

        return app_path('seeds');

    }


    protected function getNotificationsStubs(string $type)
    {
        $notification_stub = $this->filesystem->allFiles(self::TPL_PATH . "/notifications/$type");

        $notifications_to_create = [...self::DEFAULTS['notifications'][$type], ...$this->crud_models];

        if (in_array('Mailbox', $notifications_to_create)) {
            $notifications_to_create[] = 'Contact';
        }


        $notification_stub = array_filter($notification_stub, function ($view) use ($notifications_to_create) {

            if (Str::contains($view->getRelativePathname(), 'Notification')) {
                return in_array(
                    ucfirst(Str::before($view->getRelativePathname(), 'Notification')),
                    $notifications_to_create
                );
            }
            return true;
        });

        return $notification_stub;

    }

    protected function loadNotifications()
    {

        // deplacer les notifs auth dans le dossier back
        $data_map = $this->parseName();
        $notification_path = app_path('Notifications/');

        // front
        $this->compliedAndWriteFile (
            $this->getNotificationsStubs($data_map["{{frontLowerNamespace}}"]),
            $notification_path . $data_map["{{frontNamespace}}"]
        );

        // back
        $this->compliedAndWriteFileRecursively (
            $this->getNotificationsStubs($data_map["{{backLowerNamespace}}"]),
            $notification_path . $data_map["{{backNamespace}}"]
        );

        // suppression des notifs par défaut
        $this->filesystem->deleteDirectory($notification_path . $data_map['{{singularClass}}']);

        return $notification_path;
    }


    protected function loadCommands(){
        $kernel_path = app_path('Console/Kernel.php');
        $kernel = $this->filesystem->get($kernel_path);
        $commands_stub = $this->filesystem->get(self::TPL_PATH . '/commands/kernel.stub');

        $search = 'protected function schedule(Schedule $schedule)' . PHP_EOL .   '    {';
        $this->replaceAndWriteFile(
            $kernel,
            $search,
            $search . PHP_EOL . $commands_stub,
            $kernel_path
        );

        return $kernel_path;
    }

    protected function loadConfigs(){

        $config_path = config_path();
        $config_stub = $this->filesystem->files(self::TPL_PATH . '/config');

        $this->compliedAndWriteFile(
            $config_stub,
            $config_path
        );

        return $config_path;
    }


    protected function loadUtilPackage()
    {

        // Telescope
       $this->callSilent('telescope:install');
       $this->callSilent('migrate');

        $telescope_service_provider_path = app_path('Providers/TelescopeServiceProvider.php');
        $telescope_service_provider_path_stub = $this->filesystem->get(self::TPL_PATH . '/providers/TelescopeServiceProvider.stub');

        // Update TelescopeServiceProvider
        $this->compliedAndWriteFile(
            $telescope_service_provider_path_stub,
            $telescope_service_provider_path,
        );

        // Backup
        $config_filesystems_path = config_path('filesystems.php');
        $config_filesystem = $this->filesystem->get($config_filesystems_path);

        $this->replaceAndWriteFile(
            $config_filesystem,
            $search = "'disks' => [\n",
            $search . PHP_EOL . $this->filesystem->get(self::TPL_PATH . '/config/partials/filesystem.stub'),
            $config_filesystems_path
        );

    }

    private function filterEmails(string $type)
    {
        $mail_stub = $this->filesystem->allFiles(self::TPL_PATH . "/mail/$type");
        $emails_to_create = [...self::DEFAULTS['mails'][$type], ...$this->crud_models];

        if (in_array('Mailbox', $emails_to_create)) {
            $emails_to_create[] = 'Contact';
            $emails_to_create[] = 'SendMeContactMessage';
        }

        $mail_stub = array_filter(
            $mail_stub,
            fn ($view) => in_array(ucfirst(Str::before($view->getFilenameWithoutExtension(), 'Mail')), $emails_to_create)
        );

        return $mail_stub;
    }


    public function loadEmails() :string
    {
        $data_map = $this->parseName();
        $mail_path = app_path('Mail/');

        // Front
        $this->compliedAndWriteFile(
            $this->filterEmails($data_map["{{frontLowerNamespace}}"]),
            $mail_path . $data_map["{{frontNamespace}}"]
        );

        // Back
        $this->compliedAndWriteFile(
            $this->filterEmails($data_map["{{backLowerNamespace}}"]),
            $mail_path . $data_map["{{backNamespace}}"]
        );

        return $mail_path;
    }

    public function loadDebugbar()
    {
        $composer_path = base_path('composer.json');

        $this->replaceAndWriteFile(
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

        $this->replaceAndWriteFile(
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
        $this->runProcess("composer update");

    }

}
