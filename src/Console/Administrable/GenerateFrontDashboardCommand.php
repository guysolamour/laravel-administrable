<?php

namespace Guysolamour\Administrable\Console\Administrable;

use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\BaseCommand;
use Guysolamour\Command\Console\Commands\Filesystem;

class GenerateFrontDashboardCommand extends BaseCommand
{
    /**
     *
     * @var string
     */
    protected $theme;

    /** @var Filesystem */
    protected $filesystem;

    /**
     * @var string[]
     */
    protected $themes = ['sleek'];
    /**
     *
     * @var string[]
     */
    protected $data_map = [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "administrable:dashboard:generate
                            {--t|theme= : Theme to use }
                            ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate authentication routes and views for user dashboard';

    public function __construct()
    {
        parent::__construct();

        $this->data_map = $this->getParsedName();

        $this->filesystem = new Filesystem($this->data_map);
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Generating user dashbord...');

        $this->setTheme();

        $this->loadViews();
        $this->publishAssets();
        $this->loadRoutes();
        $this->loadControllers();

        $this->info('User dashboard generated with success.');
    }

    public function loadRoutes(): void
    {
        $routes_path = base_path('routes/web/');

        // Front routes;
        $route_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath("/routes/web/front/dashboard"), false);

        $this->filesystem->compliedAndWriteFile(
            $route_stub,
            $routes_path . $this->data_map["{{frontLowerNamespace}}"]
        );
    }

    private function loadViews(): void
    {
        $views_path = resource_path('views/');

        $views_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath() . '/views/front/' . $this->theme);

        $this->filesystem->compliedAndWriteFileRecursively(
            $views_stub,
            $views_path . $this->data_map["{{frontLowerNamespace}}"]
        );
    }

    private function loadControllers(): void
    {
        $controllers_path =  app_path('/Http/Controllers/') . $this->data_map["{{frontNamespace}}"] . '/Dashboard';

        // Front controllers
        $controllers_stub = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/controllers/front/dashboard'), true);

        $this->filesystem->compliedAndWriteFileRecursively(
            $controllers_stub,
            $controllers_path
        );
    }

    public function publishAssets() :void
    {
        $this->filesystem->copyDirectory(
            $this->getTemplatePath() . "/assets/front/{$this->theme}",
            public_path("vendor/{$this->theme}"),
        );
    }

    private function setTheme(): void
    {
        $theme = $this->option('theme') ? Str::lower($this->option('theme')) : Str::lower(config('administrable.modules.user_dashboard.theme', 'sleek'));

        if (!in_array($theme, $this->themes)) {
            $this->triggerError(sprintf('The {%s} theme is not available. Available theme are {%s}', $theme, join(',', $this->themes)));
        }

        $this->theme = $theme;

        $this->updateThemeConfig();
    }

    private function updateThemeConfig(): void
    {
        $config_path = config_path('administrable.php');

        $theme = config('administrable.modules.user_dashboard.theme');
        if ($theme !== $this->theme) {
            $this->filesystem->replaceAndWriteFile(
                $this->filesystem->get($config_path),
                "'theme' => '{$theme}',",
                "'theme' => '{$this->theme}',",
                $config_path
            );
        }
    }

    protected function parseName(?string $name = null): array
    {
        // if (!$name)
        //     $name = $this->guard;

        return [
            '{{namespace}}'           =>  get_app_namespace(),
            // '{{pluralCamel}}'         =>  Str::plural(Str::camel($name)),
            // '{{pluralSlug}}'          =>  Str::plural(Str::slug($name)),
            // '{{pluralSnake}}'         =>  Str::plural(Str::snake($name)),
            // '{{pluralClass}}'         =>  Str::plural(Str::studly($name)),
            // '{{singularCamel}}'       =>  Str::singular(Str::camel($name)),
            // '{{singularSlug}}'        =>  Str::singular(Str::slug($name)),
            // '{{singularSnake}}'       =>  Str::singular(Str::snake($name)),
            // '{{singularClass}}'       =>  Str::singular(Str::studly($name)),
            '{{frontNamespace}}'      =>  Str::ucfirst(config('administrable.front_namespace')),
            '{{frontLowerNamespace}}' =>  Str::lower(config('administrable.front_namespace')),
            // '{{backNamespace}}'       =>  Str::ucfirst(config('administrable.back_namespace')),
            // '{{backLowerNamespace}}'  =>  Str::lower(config('administrable.back_namespace')),
            // '{{modelsFolder}}'        =>  Str::ucfirst($this->models_folder_name),
            // '{{administrableLogo}}'   =>  config('administrable.logo_url'),
            // '{{theme}}'               =>  $this->theme,
        ];
    }

}

