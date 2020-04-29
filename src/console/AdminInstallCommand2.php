<?php

namespace Guysolamour\Administrable\Console;

use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Artisan;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use SebastianBergmann\CodeCoverage\Report\PHP;

class AdminInstallCommand extends BaseCommand
{
    protected  const TPL_PATH = __DIR__. '/../stubs';

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
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:install
                                {name=admin : Name of the guard }
                                {--g|generate= : Default models crud to generate }
                                {--p|preset= : Ui preset to use }
                                {--f|force : Whether to override existing files }
                                {--l|locale=fr : Locale to use default fr }
                                {--d|default : Use defaault configuration }
                            ';


    protected $description = 'Install admin package';


    protected function init() :bool
    {
        // Demander le nom du dossier Models et aussi ajouter la debug bar
        // si on n'a pas d'option par défaut alors on demande
        if (!$this->option('default')) {
            if (!$this->option('generate')) {
                if ($this->confirm('Do you want to generate some default models crud ?')) {
                    $this->crud_models = $this->choice('Give the model name (seperate by comma).', $this->crud_models, 0, null, true);
                }
            }

            if (!$this->option('preset')) {
                if ($this->confirm('Do you want to scafold front end ?')) {
                    $this->preset = $this->choice('Which preset do you want to use ?', $this->presets, 0);
                }
            }
        } else {
            // Si l'option --d est defini alors on génere les valeurs par défaut
            // $this->crud_models = array_merge(
            //     $this->crud_models,
            //     $this->default_crud_models
            // );

            // $this->preset = 'vue';
        }

        if (!$this->option('default')) {
            if ($this->option('generate')) {

                /**
                 * Le filter permet de retirer les élémenst vides du tableau comme des , simples
                 */
                $models = array_filter(explode(',', $this->option('generate')));

                $models = array_filter($models);


                // Sanitize data
                $models = array_map(function ($model) {
                    return ucfirst(trim($model));
                }, $models);

                /**
                 * Validation
                 */
                foreach ($models as $model) {
                    if (!in_array(ucfirst($model), $this->crud_models)) {
                        $this->error(
                            sprintf('Le modèle {%s} n\'est pas disponible. Les modèles disponible sont {%s}', $model, join(',', $this->crud_models))
                        );
                        return false;
                    }
                }


                $this->crud_models = $models;

                // Si le modele post est defini alors on ajoute le modele category et autre
            }

            if ($this->option('preset')) {
                $preset = strtolower($this->option('preset'));

                if (!in_array($preset, $this->presets)) {
                    $this->error(
                        sprintf('Le preset {%s} n\'est pas disponible. Les presets disponible sont {%s}', $preset, join(',', $this->presets))
                    );
                    return false;
                }

                $this->preset = $this->option('preset');
            }
        }


        // On ajoute le modele Category si le Post est dans la liste
        if (in_array('Post', $this->crud_models)) {
            $this->crud_models[] = 'Category';
        }

        return true;
    }

    /**
     *
     */
    public function handle()
    {

        $this->info('Initiating...');
        $this->name = $this->argument('name');
        $this->override = $this->option('force') ? true : false;


        if(!$this->init()){
            return;
        }







        // Passer des options pour generer les articles, mentions legales, temoignages en option

        Artisan::call('multi-auth:install', [
            'name' => $this->name,
            '--force' => $this->override
        ]);



        // Gerer l'authentification
        Artisan::call("ui {$this->preset} --auth");

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
        $this->info("Moving User.php and ". ucfirst($this->name) . ".php to app/Models folder");
        $this->moveDefaultModelsToNewModelsDirectory();


        // Assets
        $this->info(PHP_EOL . 'Publishing Assets...');
        Artisan::call('vendor:publish --tag=administrable-public');
        $this->info('Assets published at ' . public_path('vendor/adminlte'));

        // Composer dump-autoload
        $this->info("Running composer dump-autoload");
        $this->runProcess("composer dump-autoload -o");


        // Seed Database
        // $this->info(PHP_EOL . 'Seeding database...');
        // $this->seedDatabase();
        // $this->info('Database seeding completed successfully.');

    }


    protected function loadHelpers()
    {
        // Helper
        Artisan::call('make:helper', [
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

        $models_to_create = array_merge(
            $this->crud_models,
            self::DEFAULTS['models']
        );

        $data_map = $this->parseName();

        $guard = $data_map['{{singularClass}}'];

        $models = $this->filesystem->files(self::TPL_PATH . '/models');

        /**
         * Remove uncreate models in the list
         */
        $models = (collect($models)->filter( function($model) use ($models_to_create) {
            return in_array($model->getFilenameWithoutExtension(), $models_to_create);
        }))->toArray();


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


        // Renommer du model user et le déplacer à la racine du dossier app
        $this->filesystem->move(
            $model_path . '/User.php',
            app_path('User.php')
        );



       return $model_path;
    }

    /**
     * Move User|Guard={admin} To Models Directory
     *
     * @return $this
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function moveDefaultModelsToNewModelsDirectory()
    {

        $data_map = $this->parseName();
        $guard = $data_map['{{singularClass}}'];

        foreach (['User', $guard] as $model ) {
            if ($this->filesystem->exists($userPath = app_path("$model.php"))) {
                $this->filesystem->move($userPath, $targetPath = app_path("Models/$model.php"));
                $this->filesystem->put(
                    $targetPath,
                    strtr($this->filesystem->get($targetPath), [
                        'App;' => "App\\Models;",
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
        $this->info("Changing $model uses and imports from App\\$model to App\\Models\\$model");

        $files = Finder::create()
            ->in(base_path())
            ->contains("App\\$model")
            ->exclude('vendor')
            ->name('*.php');

        foreach ($files as $file) {
            $path = $file->getRealPath();
            if ($this->filesystem->exists($path)) {
                $this->filesystem->put($path, strtr($this->filesystem->get($path), [
                    "App\\$model" => "App\\Models\\$model",
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
            base_path('routes/admin.php'),
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


    protected function loadBreadcrumbs()
    {
        $data_map = $this->parseName();

        // modification du fichier de configuration
        Artisan::call('vendor:publish --tag=breadcrumbs-config');

        $path = config_path('breadcrumbs.php');
        $config_file = $this->filesystem->get($path);

        $search = "base_path('routes/breadcrumbs.php'),";

        $replace = "glob(base_path('routes/breadcrumbs/*/*.php')),";


        $this->replaceAndWriteFile(
            $config_file,
            $search,
            $replace,
            $path,
        );

        $breadcrumb_path = base_path('routes/breadcrumbs/');

        // Front
        $breadcrumb_stub = $this->filesystem->files(self::TPL_PATH . '/routes/breadcrumbs/front');
        $breadcrumb_to_create = array_merge(self::DEFAULTS['breadcrumbs']['front'], $this->crud_models);
        $breadcrumb_stub = array_filter($breadcrumb_stub, function ($form) use ($breadcrumb_to_create) {
            return in_array(ucfirst($form->getFilenameWithoutExtension()), $breadcrumb_to_create);
        });
        $this->compliedAndWriteFile(
            $breadcrumb_stub,
            $breadcrumb_path . $data_map["{{frontLowerNamespace}}"]
        );

        // Front
        $breadcrumb_stub = $this->filesystem->files(self::TPL_PATH . '/routes/breadcrumbs/back');
        $breadcrumb_to_create = array_merge(self::DEFAULTS['breadcrumbs']['back'], $this->crud_models);
        $breadcrumb_stub = array_filter($breadcrumb_stub, fn ($form) => in_array(ucfirst($form->getFilenameWithoutExtension()), $breadcrumb_to_create));

        $this->compliedAndWriteFile(
            $breadcrumb_stub,
            $breadcrumb_path . $data_map["{{backLowerNamespace}}"]
        );

        // renommage du dossier avec le guard
        $this->filesystem->move(
            $breadcrumb_path . $data_map["{{backLowerNamespace}}"] . '/guard.php',
            $breadcrumb_path . $data_map["{{backLowerNamespace}}"] . '/' .  $data_map['{{singularSlug}}'] .'.php'
        );

        return $breadcrumb_path;
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

    protected function loadViews()
    {
        $data_map = $this->parseName();

        $views_path = resource_path('views/');

        // Mettre les models au pluriel pour les Views

        $crud_models = array_map(fn($item) => Str::plural($item),$this->crud_models);


        $views_stub = $this->filesystem->allFiles(self::TPL_PATH . '/views/back');
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
        $aside_path = resource_path("views/{$data_map["{{backLowerNamespace}}"]}/partials/_aside.blade.php");


        $links_stub = $this->filesystem->files(self::TPL_PATH . '/views/partials');

        $links_stub = array_filter($links_stub, function($link){
            $name = ucfirst(Str::before($link->getFileNameWithoutExtension(), 'Link'));
            dump($name);
            return in_array($name, $this->crud_models);
        });
        $search = "{{-- insert sidebar links here --}}";

        foreach ($links_stub as $link ) {

            $this->replaceAndWriteFile(
                $this->filesystem->get($aside_path),
                $search,
                $this->compliedFile($link) . PHP_EOL . PHP_EOL . $search,
                $aside_path
            );
        }


        // Gestion du lien dans le header
        if(in_array('Mailbox', $this->crud_models)){
            $header_path = resource_path("views/{$data_map["{{backLowerNamespace}}"]}/partials/_header.blade.php");
            $stub = $this->filesystem->get(self::TPL_PATH . '/views/partials/headerLink.blade.stub');

            $search = "{{-- Insert Mailbox Link --}}";

            $this->replaceAndWriteFile(
                $this->filesystem->get($header_path),
                $search,
                $this->compliedFile($stub, false),
                $header_path
            );
        }



        // $views_stub = $this->filesystem->allFiles(self::TPL_PATH . '/views/emails');
        // $this->compliedAndWriteFileRecursively(
        //     $views_stub,
        //     $views_path . $data_map["{{frontLowerNamespace}}"]
        // );


        $this->loadEmailsViews();

        // renommage du dossier avec le guard
        // $this->filesystem->moveDirectory(
        //     $views_path . '/guard',
        //     $views_path . '/' . $data_map['{{pluralSlug}}']
        // );

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
        Artisan::call('telescope:install');
        Artisan::call('migrate');

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

}
