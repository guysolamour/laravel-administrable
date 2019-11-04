<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class AdminInstallCommand extends Command
{
    protected  const TPL_PATH = __DIR__. '/../templates';

    protected  $name = '';
    protected $exits = false;

    protected $override = false;

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


        $progress = $this->output->createProgressBar(21);


        // Models
        $this->info(PHP_EOL . 'Creating Model...');
        $this->loadModel(self::TPL_PATH);
        $this->info('Model created at ');
        $progress->advance();


        // Factories
        $this->info(PHP_EOL . 'Creating Factory...');
        $factory_path = $this->loadFactory(self::TPL_PATH);
        $this->info('Factory created at ' . $factory_path);
        $progress->advance();

        // Migrations
        $this->info(PHP_EOL . 'Creating Migrations...');
        $migrations_path = $this->loadMigrations(self::TPL_PATH);
        $this->info('Migrations created at ' . $migrations_path);
        $progress->advance();


        // Run migrations
        $this->info(PHP_EOL . 'Migrate');
        Artisan::call('migrate');
        $this->info('Migrations done');
        $progress->advance();

        // Seeds
        $this->info(PHP_EOL . 'Creating Seed...');
        $seed_path = $this->loadSeed(self::TPL_PATH);

        $this->info('Seed created at ' . $seed_path);
        $progress->advance();


        // DatabaseSeeder
        $this->info(PHP_EOL . 'Registering seeder...');
        $database_seeder_path = $this->registerSeed(self::TPL_PATH);
        $this->info('Seed registered in ' . $database_seeder_path);
        $progress->advance();

        // Controllers
        $this->info(PHP_EOL . 'Creating Controllers...');
        $controllers_path = $this->loadControllers(self::TPL_PATH);
        $this->info('Controllers created at ' . $controllers_path);
        $progress->advance();


        // Middleware
        $this->info(PHP_EOL . 'Creating Middleware...');
        $middleware_path = $this->loadMiddleware(self::TPL_PATH);
        $this->info('Middleware created at ' . $middleware_path);
        $progress->advance();


        // Route Middleware
        $this->info(PHP_EOL . 'Registering route middleware...');
        $kernel_path = $this->registerRouteMiddleware(self::TPL_PATH);
        $this->info('Route middleware registered in ' . $kernel_path);

        // Traits
        $this->info(PHP_EOL . 'Creating Traits...');
        $traits_path = $this->loadTraits(self::TPL_PATH);
        $this->info('Traits created at ' . $traits_path);
        $progress->advance();


        // Forms
        $this->info(PHP_EOL . 'Creating Forms...');
        $forms_path = $this->loadForms(self::TPL_PATH);
        $this->info('Forms created at ' . $forms_path);
        $progress->advance();



        // lfm congfig
        $this->info(PHP_EOL . 'Creating Lfm config...');
        $config_path = $this->loadLfmConfig(self::TPL_PATH);
        $this->info('Forms created at ' . $config_path);
        $progress->advance();



        // routes and breadcrumbs
        $this->info(PHP_EOL . 'Creating Routes and breadcrumb...');
        $routes_path = $this->loadRoutes(self::TPL_PATH);
        $this->info('Routes and breadcrumb created at ' . $routes_path);
        $progress->advance();

        // Views
        $this->info(PHP_EOL . 'Creating Views...');
        $admin_views_path = $this->loadAdminViews(self::TPL_PATH);
        $this->info('Views created at ' . $admin_views_path);
        $progress->advance();

        // Locales
        $this->info(PHP_EOL . 'Adding locale...');
        $config_path = $this->loadLocale();
        $this->info('Locale added at ' . $config_path);
        $progress->advance();

        // change default route to controllers
        $this->info(PHP_EOL . 'Change default home route');
        $controller_path = $this->changeDefaultRoute();
        $this->info(PHP_EOL . 'Route change and controller created at '. $controller_path);
        $progress->advance();

        // Social links
        $this->info(PHP_EOL . 'Creating social links...');
        $routes_path = $this->loadSocialLink();
        $this->info('Social links created at ' . $routes_path);
        $progress->advance();

        // Contact
        $this->info(PHP_EOL . 'Adding contact form and controller...');
        $controller_path = $this->loadContact();
        $this->info('contact form and controller created ' . $controller_path);
        $progress->advance();

        // Assets
        $this->info(PHP_EOL . 'Publishing Assets...');
        Artisan::call('vendor:publish --tag=administrable-public');
        $this->info('Assets published at ' . public_path('vendor/adminlte'));
        $progress->advance();


        // add variable in .env file
        $this->info(PHP_EOL . 'Adding env variables...');
        $env_path = $this->addEnvVariables();
        $this->info('Set env variables at ' . $env_path);
        $progress->advance();

        // add app config keys
        $this->info(PHP_EOL . 'Adding config keys...');
        $env_path = $this->addAppConfigKeys();
        $this->info('App config set at ' . $env_path);
        $progress->advance();


        // update composer autoload for seeding
        \exec('composer dump-autoload > /dev/null 2>&1');

        // seed
        $this->callSilent('db:seed', [
            '--class' => 'ConfigurationsTableSeeder',
        ]);

        $progress->finish();

    }

    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    protected function getNamespace()
    {
        $namespace = Container::getInstance()->getNamespace();
        return rtrim($namespace, '\\');
    }


    /**
     * Parse guard name
     * Get the guard name in different cases
     * @param string $name
     * @return array
     */
    protected function parseName($name = null)
    {
        if (!$name)
            $name = $this->name;

        return $parsed = array(
            '{{namespace}}' => $this->getNamespace(),
            '{{pluralCamel}}' => Str::plural(Str::camel($name)),
            '{{pluralSlug}}' => Str::plural(Str::slug($name)),
            '{{pluralSnake}}' => Str::plural(Str::snake($name)),
            '{{pluralClass}}' => Str::plural(Str::studly($name)),
            '{{singularCamel}}' => Str::singular(Str::camel($name)),
            '{{singularSlug}}' => Str::singular(Str::slug($name)),
            '{{singularSnake}}' => Str::singular(Str::snake($name)),
            '{{singularClass}}' => Str::singular(Str::studly($name)),
        );
    }

    /**
     * Load model
     * @param $stub_path
     * @return string
     */
    protected function loadModel($stub_path)
    {
        try {

            $data_map = $this->parseName();

            $models = [
                [
                    'stub' =>  $stub_path . '/models/model.stub',
                    'path'  => app_path($data_map['{{singularClass}}'] . '.php')
                ],
                [
                    'stub'  => $stub_path . '/models/configuration.stub',
                    'path' =>  app_path('/Models/Configuration.php'),
                ],
                [
                    'stub'  => $stub_path . '/models/BaseModel.stub',
                    'path' =>  app_path('/Models/BaseModel.php'),
                ],
                [
                    'stub'  => $stub_path . '/models/mailbox.stub',
                    'path' =>  app_path('/Models/Mailbox.php'),
                ],
            ];

            foreach ($models as $model){
                $stub = file_get_contents($model['stub']);
                $stub = strtr($stub, $data_map);

                $dir = dirname($model['path']);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                file_put_contents($model['path'], $stub);
            }

            return app_path('models');

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }
    protected function loadSeed($stub_path)
    {
        try {
            $data_map = $this->parseName();

            $seeds = [
                [
                    'stub' =>  $stub_path . '/seeds/TableSeeder.stub',
                    'path'  => database_path('/seeds/'.$data_map['{{pluralClass}}'] . 'TableSeeder.php'),
                ],
                [
                    'stub'  => $stub_path . '/seeds/ConfigurationSeeder.stub',
                    'path' =>  database_path('/seeds/ConfigurationsTableSeeder.php'),
                ],
                [
                    'stub'  => $stub_path . '/seeds/MailboxSeeder.stub',
                    'path' =>  database_path('/seeds/MailboxesTableSeeder.php'),
                ],
            ];

            foreach ($seeds  as $seed){
                $stub = file_get_contents($seed['stub']);
                $stub = strtr($stub, $data_map);

                $dir = dirname($seed['path']);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                file_put_contents($seed['path'], $stub);
            }



            return database_path('/seeds');

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    protected function loadFactory($template_path)
    {
        try {

            $stub = file_get_contents($template_path . '/factory.stub');


            $data_map = $this->parseName();

            $factory = strtr($stub, $data_map);

            $factory_path = database_path('factories/' . $data_map['{{singularClass}}'] . 'Factory.php');

            file_put_contents($factory_path, $factory);

            return $factory_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }


    protected function loadMigrations($template_path)
    {

            $data_map = $this->parseName();
            $guard = $data_map['{{pluralSlug}}'];


            $migrations = [
                [
                    'stub' =>  $template_path . '/migrations/provider.stub',
                    'path'  => Arr::first(glob(database_path('migrations').'/*_create_'. $guard .'_table.php'))
                ],
                [
                    'stub'  => $template_path . '/migrations/administrable.stub',
                    'path'  => database_path('migrations/2015_10_29_201929_create_administrable_table.php'),
                ],
                [
                    'stub'  => $template_path . '/migrations/mailbox.stub',
                    'path'  => database_path('migrations/2015_10_30_201929_create_mailboxes_table.php'),
                ],
            ];

            foreach ($migrations as $migration){
                $stub = file_get_contents($migration['stub']);
                $complied = strtr($stub, $data_map);

                file_put_contents($migration['path'], $complied);
            }

            return database_path('migrations');

    }

    protected function loadControllers($template_path)
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularClass}}'];

        $controllers_path = app_path('/Http/Controllers/' . $guard);

        $controllers = [
            [
                'stub' => $template_path . '/Controllers/controller.stub',
                'path' => $controllers_path . '/'. $guard . 'Controller.php',
            ],
            [
                'stub' => $template_path . '/Controllers/configuration.stub',
                'path' => $controllers_path . '/ConfigurationController.php',
            ],
            [
                'stub' => $template_path . '/Controllers/mailbox.stub',
                'path' => $controllers_path . '/MailboxController.php',
            ],
        ];

        foreach ($controllers as $controller) {
            $stub = file_get_contents($controller['stub']);
            $complied = strtr($stub, $data_map);

            $dir = dirname($controller['path']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($controller['path'], $complied);
        }

        // Add redirectTo method to auth controllers
        $auth_controllers = glob($controllers_path . '/Auth/*.php');
        $search = 'protected $redirectTo = '. "'/{$data_map['{{singularSlug}}']}'" . ';';
        $stub = file_get_contents($template_path . '/Controllers/redirectTo.stub');


        $this->replaceAndRegisterStub($search,$stub,$auth_controllers);


        // home controller
        $file = $controllers_path . '/HomeController.php';
        $search = 'protected $redirectTo = '. "'/{$data_map['{{singularSlug}}']}/login'" . ';';
        $this->replaceAndRegisterStub($search,$stub,$file);

        // PseudoEmailLoginTrait;
        $file = $controllers_path . '/Auth/LoginController.php';
        $search = 'use AuthenticatesUsers;';
        $stub = file_get_contents($template_path . '/Controllers/pseudoemaillogin.stub');
        $this->replaceAndRegisterStub($search,$search . PHP_EOL. PHP_EOL. $stub,$file);

        // Register
        $file = $controllers_path . '/Auth/RegisterController.php';
        $stub = file_get_contents($template_path . '/Controllers/register.stub');
        $complied = strtr($stub, $data_map);
        file_put_contents($file,$complied);


        $this->replaceAndRegisterStub($search,$search . PHP_EOL. PHP_EOL. $stub,$file);


        return $controllers_path;
    }

    /**
     * @param $search
     * @param $replace
     * @param  string|array $file
     */
    private function replaceAndRegisterStub($search, $replace, $file)
    {
        if (is_array($file)){
            foreach ($file as $value) {
                $this->replaceAndRegisterStub($search,$replace,$value);
            }
            return;
        }

        $provider = file_get_contents($file);
        $provider = str_replace($search, $replace, $provider);
        // Overwrite file
        file_put_contents($file, $provider);
    }

    protected function loadTraits($template_path)
    {
        $data_map = $this->parseName();

        $traits_path = app_path('/Traits');

        $traits = array(
            [
                'stub' => $template_path . '/traits/FormBuilderTrait.stub',
                'path' => $traits_path . '/FormBuilderTrait.php',
            ],
        );

        foreach ($traits as $trait) {
            $stub = file_get_contents($trait['stub']);
            $complied = strtr($stub, $data_map);

            $dir = dirname($trait['path']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($trait['path'], $complied);
        }

        return $traits_path;
    }

    protected function loadForms($template_path)
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularClass}}'];


        $form_path = app_path('/Forms/'.$guard);


        $forms = array(
            [
                'stub' => $template_path . '/forms/CreateForm.stub',
                'path' => $form_path . '/Create'.$guard.'Form.php',
            ],
            [
                'stub' => $template_path . '/forms/AdminForm.stub',
                'path' => $form_path . '/'.$guard.'Form.php',
            ],
            [
                'stub' => $template_path . '/forms/ResetAdminPasswordForm.stub',
                'path' => $form_path . '/Reset'.$guard.'PasswordForm.php',
            ],
            [
                'stub' => $template_path . '/forms/ConfigurationForm.stub',
                'path' => $form_path . '/ConfigurationForm.php',
            ],
        );
        //dd($forms);

        foreach ($forms as $form) {
            $stub = file_get_contents($form['stub']);
            $complied = strtr($stub, $data_map);

            $dir = dirname($form['path']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($form['path'], $complied);
        }

        return $form_path;
    }
    /**
     * Load routes
     * @param $stub_path
     * @return string
     */
    protected function loadRoutes($stub_path)
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularSlug}}'];

        $routes_path = base_path('/routes/');

        $routes = [
            [
                'stub' => $stub_path . '/routes/routes.stub',
                'path' => $routes_path  . $guard . '.php',
            ],
            [
                'stub' => $stub_path . '/routes/breadcrumbs.stub',
                'path' => $routes_path . 'breadcrumbs.php',
            ],
        ];

        foreach ($routes as $route) {

            $stub = file_get_contents($route['stub']);
            $complied = strtr($stub, $data_map);

            file_put_contents($route['path'], $complied);
        }

        // register route prefix
        $provider_path = app_path('Providers/RouteServiceProvider.php');
        $provider = file_get_contents($provider_path);
        $data_map = $this->parseName();

        $map_call_bait = "Route::prefix('{$data_map['{{singularSlug}}']}')";

        $prefix = "Route::prefix(config('administrable.auth_prefix_path'))";

        $provider = str_replace($map_call_bait, $prefix, $provider);

        // Overwrite file
        file_put_contents($provider_path, $provider);

        return $routes_path;
    }

    protected function loadAdminViews($template_path)
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{singularSlug}}'];

        $views_path = resource_path('views/' . $guard);


        $views = array(
            [
                'stub' => $template_path . '/views/layouts/base.blade.stub',
                'path' => $views_path . '/layouts/base.blade.php',
            ],
            [
                'stub' => $template_path . '/views/partials/_aside.blade.stub',
                'path' => $views_path . '/partials/_aside.blade.php',
            ],
            [
                'stub' => $template_path . '/views/partials/_footer.blade.stub',
                'path' => $views_path . '/partials/_footer.blade.php',
            ],
            [
                'stub' => $template_path . '/views/partials/_header.blade.stub',
                'path' => $views_path . '/partials/_header.blade.php',
            ],
            [
                'stub' => $template_path . '/views/admins/index.blade.stub',
                'path' => $views_path . '/admins/index.blade.php',
            ],
            [
                'stub' => $template_path . '/views/admins/create.blade.stub',
                'path' => $views_path . '/admins/create.blade.php',
            ],
            [
                'stub' => $template_path . '/views/admins/show.blade.stub',
                'path' => $views_path . '/admins/show.blade.php',
            ],
            [
                'stub' => $template_path . '/views/partials/_datatable.blade.stub',
                'path' => $views_path . '/partials/_datatable.blade.php',
            ],
            // auth filesS
            [
                'stub' => $template_path . '/views/layouts/app.blade.stub',
                'path' => $views_path . '/layouts/app.blade.php',
            ],
            [
                'stub' => $template_path . '/views/home.blade.stub',
                'path' => $views_path . '/home.blade.php',
            ],
            [
                'stub' => $template_path . '/views/auth/login.blade.stub',
                'path' => $views_path . '/auth/login.blade.php',
            ],
            [
                'stub' => $template_path . '/views/auth/register.blade.stub',
                'path' => $views_path . '/auth/register.blade.php',
            ],
            [
                'stub' => $template_path . '/views/auth/verify.blade.stub',
                'path' => $views_path . '/auth/verify.blade.php',
            ],
            [
                'stub' => $template_path . '/views/auth/passwords/email.blade.stub',
                'path' => $views_path . '/auth/passwords/email.blade.php',
            ],
            [
                'stub' => $template_path . '/views/auth/passwords/reset.blade.stub',
                'path' => $views_path . '/auth/passwords/reset.blade.php',
            ],
            // Configuration
            [
                'stub' => $template_path . '/views/configuration/edit.blade.stub',
                'path' => $views_path . '/configuration/edit.blade.php',
            ],
            // Mailbox
            [
                'stub' => $template_path . '/views/mailbox/index.blade.stub',
                'path' => $views_path . '/mailbox/index.blade.php',
            ],[
                'stub' => $template_path . '/views/mailbox/show.blade.stub',
                'path' => $views_path . '/mailbox/show.blade.php',
            ],

        );

        // remove auth directory created by nulti auth package
        $this->recurseRmdir($views_path . '/auth');

        foreach ($views as $view) {
            $stub = file_get_contents($view['stub']);
            $complied = strtr($stub, $data_map);


            $dir = dirname($view['path']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($view['path'], $complied);
        }

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






    protected function loadMiddleware($template_path)
    {
        try {

            $data_map = $this->parseName();

            $middleware_path = app_path('Http/Middleware');


            $middlewares = array(
                [
                    'stub' => $template_path . '/Middleware/RedirectIfNotSuperAdmin.stub',
                    'path' => $middleware_path . '/RedirectIfNotSuper' . $data_map['{{singularClass}}'] . '.php',
                ],
            );

            foreach ($middlewares as $middleware) {
                $stub = file_get_contents($middleware['stub']);
                file_put_contents($middleware['path'], strtr($stub, $data_map));
            }

            // change middleware redirectifNot{$guard) redirect method

            $provider_path = $middleware_path . "/RedirectIfNot{$data_map['{{singularClass}}']}.php";
            $provider = file_get_contents($provider_path);


            $search = "'{$data_map['{{singularSlug}}']}/login'";
            $prefix = file_get_contents($template_path . '/Middleware/RedirectTo.stub');

            $provider = str_replace($search, $prefix, $provider);

            // Overwrite file
            file_put_contents($provider_path, $provider);



            return $middleware_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    protected function registerRouteMiddleware($stub_path)
    {
        try {

            $data_map = $this->parseName();

            $kernel_path = app_path('Http/Kernel.php');

            $kernel = file_get_contents($kernel_path);

            /********** Route Middleware **********/

            $route_mw = file_get_contents($stub_path . '/Middleware/Kernel.stub');

            $route_mw = strtr($route_mw, $data_map);

            $route_mw_bait = 'protected $routeMiddleware = [';

            $kernel = str_replace($route_mw_bait, $route_mw_bait . $route_mw, $kernel);

            // Overwrite config file
            file_put_contents($kernel_path, $kernel);

            return $kernel_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }
    protected function registerSeed($stub_path)
    {
        try {

            $data_map = $this->parseName();

            $database_seeder_path = database_path('seeds/DatabaseSeeder.php');

            $database_seeder = file_get_contents($database_seeder_path);


            $route_mw = file_get_contents($stub_path . '/seeds/DatabaseSeeder.stub');


            $route_mw = strtr($route_mw, $data_map);
            $route_mw_bait = "  {\n";


            $database_seeder = str_replace($route_mw_bait, $route_mw_bait . $route_mw, $database_seeder);

            // Overwrite config file
            file_put_contents($database_seeder_path, $database_seeder);

            return $database_seeder_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    protected function recurseRmdir($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->recurseRmdir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }


    protected function addEnvVariables()
    {


        $env_path = base_path('.env');

        $env = file_get_contents($env_path);


        $env_stub = file_get_contents(self::TPL_PATH . '/env/env.stub');


        $search = 'APP_ENV';

        $env_file = str_replace($search,  $env_stub . $search, $env);

        // add port to the APP_URL

        $search = 'APP_URL=http://localhost';

        $port = 8000;

        $env_file = str_replace($search,  $search . ":{$port}" , $env_file);


        // add maildev configuration
        $search = 'MAIL_HOST=smtp.mailtrap.io';
        $env_file = str_replace($search,  'MAIL_HOST=127.0.0.1' , $env_file);

        $search = 'MAIL_PORT=2525';
        $env_file = str_replace($search,  'MAIL_PORT=1030' , $env_file);


        // Overwrite config file
        file_put_contents($env_path, $env_file);



        return $env_path;
    }

    protected function addAppConfigKeys()
    {
        $app_path = config_path('app.php');

        $app = file_get_contents($app_path);

        $app_stub = file_get_contents(self::TPL_PATH . '/env/app.stub');


        $search = "'name' =>";

        $app_file = str_replace($search,    $app_stub . $search   , $app);

        // Overwrite config file
        file_put_contents($app_path, $app_file);

    }

    /**
     * Load Locales
     * @return string
     * @throws \Exception
     */
    protected function loadLocale() :string
    {

        // copy files to ressource/lang
        $dir = self::TPL_PATH . '/locales/' . $this->option('locale') .'/'. $this->option('locale');
        $json_dir = self::TPL_PATH . '/locales/' . $this->option('locale') .'/json';

        $r_dir = resource_path("lang/" . $this->option('locale')) . '/';

        if(!is_dir($dir)){
            throw new \Exception("The locale path [{$dir}] does not exists", 1);
        }

        if(!is_dir($json_dir)){
            throw new \Exception("The locale path [{$json_dir}] does not exists", 1);
        }

        $files = array_slice(scandir($dir),2);


        foreach ($files as $file) {

            $stub = file_get_contents($dir . '/' .$file);
            // $complied = strtr($stub, $data_map);

            if (!is_dir($r_dir)) {
                mkdir($r_dir, 0755, true);
            }

            file_put_contents($r_dir . $file, $stub);
        }


        $json_files = array_slice(scandir($json_dir),2);
        $r_dir = resource_path("lang/");

        foreach ($json_files as $file) {
            $stub = file_get_contents($json_dir . '/' .$file);
            // $complied = strtr($stub, $data_map);

            file_put_contents($r_dir . $file, $stub);
        }


        // change locale configuration in config file
        $config_path = config_path('app.php');

        $provider = file_get_contents($config_path);


        $search = "'locale' => 'en'";
        $prefix = "'locale' => '{$this->option('locale')}'";
        $provider = str_replace($search, $prefix, $provider);

        $search = "'faker_locale' => 'en_US'";
        $prefix = "'faker_locale' => '". $this->option('locale') . '_' . strtoupper($this->option('locale'))."'";

        $provider = str_replace($search, $prefix, $provider);


        // Overwrite file
        file_put_contents($config_path, $provider);

        return $config_path;
    }

    public function loadSocialLink()
    {
        // Routes
        $this->loadSocialLinkRoute();

        // Controller
        $this->loadSocialLinkController();

        return base_path('routes');

    }

    /**
     * @return string
     */
    private function loadSocialLinkRoute()
    {
        $routes_path = base_path('/routes/web.php');
        $route = file_get_contents($routes_path);

        $stub = self::TPL_PATH . '/routes/sociallinks.stub';
        $stub = file_get_contents($stub);


        $search = '});';
        $complied = str_replace($search, $stub. PHP_EOL  . $search , $route);

        file_put_contents($routes_path, $complied);
    }

    private function loadSocialLinkController()
    {
        $data_map = $this->parseName();

        $controller_path = app_path('/Http/Controllers/User/RedirectController.php');

        $dir = dirname($controller_path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $stub = self::TPL_PATH . '/Controllers/sociallinks.stub';
        $stub = file_get_contents($stub);

        $complied = strtr($stub, $data_map);



        file_put_contents($controller_path , $complied);
    }

    /**
     * @return string
     */
    public function changeDefaultRoute() :string
    {
        $data_map = $this->parseName();

        // Change route
        $routes_path = base_path('/routes/web.php');
        $stub = self::TPL_PATH . '/routes/default.stub';
        $route = file_get_contents($stub);
        file_put_contents($routes_path , $route);

        //  Add controller
        $controller_stub = self::TPL_PATH . '/Controllers/page.stub';
        $stub = file_get_contents($controller_stub);
        $controller = strtr($stub, $data_map);
        $controller_path = app_path('/Http/Controllers/User/PageController.php');

        $dir = dirname($controller_path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($controller_path, $controller);

        return $controller_path;
    }

    public function loadContact()
    {
        $data_map = $this->parseName();
        // Routes
        $routes_path = base_path('/routes/web.php');
        $route = file_get_contents($routes_path);

        $stub = self::TPL_PATH . '/routes/contact.stub';
        $stub = file_get_contents($stub);

        $search = '});';
        $complied = str_replace($search, $stub. PHP_EOL . PHP_EOL . $search , $route);

        file_put_contents($routes_path, $complied);

        // Form
        $form_path = app_path('/Forms/User/ContactForm.php');

        $dir = dirname($form_path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $stub = self::TPL_PATH . '/forms/ContactForm.stub';
        $stub = file_get_contents($stub);
        $form = strtr($stub, $data_map);

        file_put_contents($form_path, $form);

        // Controller
        $controller_stub = self::TPL_PATH . '/Controllers/contact.stub';
        $stub = file_get_contents($controller_stub);

        $controller = strtr($stub, $data_map);
        $controller_path = app_path('/Http/Controllers/User/ContactController.php');

        $dir = dirname($controller_path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($controller_path, $controller);

        // Mailable
        $mail_stub = self::TPL_PATH . '/mail/contact.stub';
        $stub = file_get_contents($mail_stub);

        $mail = strtr($stub, $data_map);
        $mail_path = app_path('/Mail/User/ContactMail.php');

        $dir = dirname($mail_path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($mail_path, $mail);

        // Mailable views
        $view_stub = self::TPL_PATH . '/views/mail/contact.stub';
        $stub = file_get_contents($view_stub);

        $view_path = resource_path('/views/emails/user/contact.blade.php');

        $dir = dirname($view_path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($view_path, $mail);

    }

}
