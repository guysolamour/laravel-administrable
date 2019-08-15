<?php

namespace Guysolamour\Admin\Console;


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
                                {--f|force : Whether to override existing files}';


    protected $description = 'Install admin package';


    public function handle()
    {

        $this->info('Initiating...');

        $this->name = $this->argument('name');

        $this->override = $this->option('force') ? true : false;


        Artisan::call('multi-auth:install',[
            'name' => $this->name,
            '--force' => $this->override
        ]);

        $progress = $this->output->createProgressBar(14);

        // Models
        $this->info(PHP_EOL . 'Creating Model...');
        $model_path = $this->loadModel(self::TPL_PATH);
        $this->info('Model created at ' . $model_path);
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

        // Assets
        $this->info(PHP_EOL . 'Publishing Assets...');
        Artisan::call('vendor:publish --tag=administrable-public');
        $this->info('Assets published at ' . public_path('vendor/adminlte'));
        $progress->advance();

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
            '{{pluralCamel}}' => str_plural(camel_case($name)),
            '{{pluralSlug}}' => str_plural(str_slug($name)),
            '{{pluralSnake}}' => str_plural(snake_case($name)),
            '{{pluralClass}}' => str_plural(studly_case($name)),
            '{{singularCamel}}' => str_singular(camel_case($name)),
            '{{singularSlug}}' => str_singular(str_slug($name)),
            '{{singularSnake}}' => str_singular(snake_case($name)),
            '{{singularClass}}' => str_singular(studly_case($name)),
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

            $stub = file_get_contents($stub_path . '/model.stub');

            $data_map = $this->parseName();

            $model = strtr($stub, $data_map);


            $model_path = app_path($data_map['{{singularClass}}'] . '.php');

            file_put_contents($model_path, $model);

            return $model_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }
    protected function loadSeed($stub_path)
    {
        try {

            $stub = file_get_contents($stub_path . '/seeds/TableSeeder.stub');

            $data_map = $this->parseName();

            $seed = strtr($stub, $data_map);


            $seed_path = database_path('/seeds/'.$data_map['{{pluralClass}}'] . 'TableSeeder.php');

            file_put_contents($seed_path, $seed);

            return $seed_path;

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
        try {


            $data_map = $this->parseName();
            $guard = $data_map['{{pluralSlug}}'];

            $migration_path = Arr::first(glob(database_path('migrations').'/*_create_'. $guard .'_table.php'));
            $migration_stub = $template_path . '/migrations/provider.stub';

            $stub = file_get_contents($migration_stub);
            $complied = strtr($stub, $data_map);


            file_put_contents($migration_path, $complied);

            return database_path('migrations');

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
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
                'stub' => $template_path . '/views/adminlte/layouts/app.blade.stub',
                'path' => $views_path . '/adminlte/layouts/app.blade.php',
            ],
            [
                'stub' => $template_path . '/views/adminlte/partials/_aside.blade.stub',
                'path' => $views_path . '/adminlte/partials/_aside.blade.php',
            ],
            [
                'stub' => $template_path . '/views/adminlte/partials/_footer.blade.stub',
                'path' => $views_path . '/adminlte/partials/_footer.blade.php',
            ],
            [
                'stub' => $template_path . '/views/adminlte/partials/_header.blade.stub',
                'path' => $views_path . '/adminlte/partials/_header.blade.php',
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


//            $route_mw_bait = '// $this->call(UsersTableSeeder::class);'."\n";
            $route_mw_bait = "  {\n";


            $database_seeder = str_replace($route_mw_bait, $route_mw_bait . $route_mw, $database_seeder);

            // Overwrite config file
            file_put_contents($database_seeder_path, $database_seeder);

            return $database_seeder_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    private function recurseRmdir($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->recurseRmdir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }



}
