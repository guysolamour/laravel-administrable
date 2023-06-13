<?php

namespace Guysolamour\Administrable\Console\Administrable;

use Guysolamour\Administrable\Console\Filesystem;
use Guysolamour\Administrable\Console\CommandTrait;

class MultiAuthGenerator
{
    use CommandTrait;

    protected string $guard;

    protected array $dataMap;

    protected Filesystem $filesystem;

    public function __construct(string $guard, array $dataMap, Filesystem $filesystem)
    {
        $this->guard = $guard;
        $this->dataMap = $dataMap;
        $this->filesystem = $filesystem;
    }

    public function generate()
    {
        $this->loadMigrations();
        $this->loadFactories();
        $this->loadMiddlewares();

        $this->registerConfig();
    }

    private function registerConfig()
    {
        $auth_config_path = config_path('auth.php');

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($auth_config_path),
        $search = "'guards' => [",
        <<<TEXT
            $search
                    '{$this->dataMap["{{singularSlug}}"]}' => [
                        'driver' => 'session',
                        'provider' => '{$this->dataMap["{{pluralSlug}}"]}',
                    ],
            TEXT,
        $auth_config_path
        );


        $modelClass = $this->dataMap["{{namespace}}"] . '\\' . $this->dataMap["{{modelsFolder}}"] . '\\' . $this->dataMap["{{singularClass}}"] . '::class';
        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($auth_config_path),
        $search = "'providers' => [",
        <<<TEXT
            $search
                    '{$this->dataMap["{{pluralSlug}}"]}' => [
                        'driver' => 'eloquent',
                        'model' => {$modelClass},
                    ],
            TEXT,
        $auth_config_path
        );

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($auth_config_path),
        $search = "'passwords' => [",
        <<<TEXT
            $search
                    '{$this->dataMap["{{pluralSlug}}"]}' => [
                        'provider' => '{$this->dataMap["{{pluralSlug}}"]}',
                        'table' => '{$this->dataMap["{{singularSnake}}"]}_password_resets',
                        'expire' => 60,
                        'throttle' => 60,
                    ],
            TEXT,
        $auth_config_path
        );
    }

    private function loadMigrations() {
        $migrationsStubs = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/multi-auth/migrations'), false);

        $migrationsPath =  database_path('migrations');

        foreach ($migrationsStubs as $stub) {
            $fileName = $this->filesystem->compliedOnly($stub->getFilename());

            $this->filesystem->writeFile(
                $migrationsPath . DIRECTORY_SEPARATOR . $fileName,
                $this->filesystem->compliedFile($stub)
            );
        }
    }

    private function loadFactories(){
        $factoriesStubs = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/multi-auth/factories'), false);

        $factoriesPath =  database_path('factories');

        foreach ($factoriesStubs as $stub) {
            $fileName = $this->filesystem->compliedOnly($stub->getFilename());

            $this->filesystem->writeFile(
                $factoriesPath . DIRECTORY_SEPARATOR . $fileName,
                $this->filesystem->compliedFile($stub)
            );
        }
    }

    private function loadMiddlewares(){
        $stubs = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/multi-auth/middlewares'), false);

        $path =  app_path('Http/Middleware');

        foreach ($stubs as $stub) {
            $fileName = $this->filesystem->compliedOnly($stub->getFilename());

            $this->filesystem->writeFile(
                $path . DIRECTORY_SEPARATOR . $fileName,
                $this->filesystem->compliedFile($stub)
            );
        }
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Multi auth command');

        // Initialisation
        $guard = $this->argument('name');

        $dataMap = $this->getParsedName($guard);
        $filesystem = new Filesystem($dataMap);

        dd($filesystem);



        // load migrations
        $migrations = $this->filesystem->getFilesFromDirectory($this->getTemplatePath('/migrations'), false);

        $migrations_path =  database_path('migrations');

        dd($guard);


        $this->info('Own multi auth command');
    }
}
