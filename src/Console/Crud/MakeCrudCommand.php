<?php

namespace Guysolamour\Administrable\Console\Crud;


use Guysolamour\Administrable\Console\CommandTrait;
use Guysolamour\Administrable\Console\Crud\CreateCrudForm;
use Guysolamour\Administrable\Console\Crud\CreateCrudView;
use Guysolamour\Administrable\Console\Crud\CreateCrudModel;
use Guysolamour\Administrable\Console\Crud\CreateCrudRoute;
use Guysolamour\Administrable\Console\Crud\CreateCrudMigration;
use Guysolamour\Administrable\Console\Crud\CreateCrudController;

class MakeCrudCommand extends BaseCrudCommand
{


    use CommandTrait;


    /**
     * @var bool
     */
    protected $migrate;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:make:crud
                             {model : Model name }
                             {--m|migrate=true : Run artisan migrate command }
                             ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create, model, migration and all views (crud)';

    /**
     *
     */
    public function handle()
    {
        $this->info('Initiating...');

        $progress = $this->output->createProgressBar(10);

        $this->model = $this->argument('model');

        $this->migrate = $this->option('migrate') == 'true' ? true : false;

        $this->theme = config('administrable.theme');


        $this->checkIfCrudHasAlreadyBeenDoneForModel($this->model);


        $config_fields = $this->getCrudConfiguration(ucfirst($this->model));

        $this->fields = $this->getCleanFields($config_fields);


        // Models
        $this->info(PHP_EOL . 'Creating Model...');
        [$result, $model_path] = CreateCrudModel::generate(
            $this->model,
            $this->fields,
            $this->actions,
            $this->breadcrumb,
            $this->theme,
            $this->slug,
            $this->timestamps
        );
        $this->displayResult($result, $model_path);
        $progress->advance();


        // Migrations and seeds
        $this->info(PHP_EOL . 'Creating Migration...');
        [$migration_path, $seeder_path] = CreateCrudMigration::generate(
            $this->model,
            $this->fields,
            $this->actions,
            $this->breadcrumb,
            $this->theme,
            $this->slug,
            $this->timestamps,
            $this->entity,
            $this->seeder
        );

        $this->info('Migration file created at ' . $migration_path);
        $this->info('Seeder file created at ' . $seeder_path);
        $progress->advance();


        // Migrate
        if ($this->migrate) {
            $this->info(PHP_EOL . 'Migrate...');
            $this->call('migrate');
            $progress->advance();
        }




        if (!$this->entity) {
            // Forms
            $this->info(PHP_EOL . 'Forms...');
            $form_path = CreateCrudForm::generate(
                $this->model,
                $this->fields,
                $this->actions,
                $this->breadcrumb,
                $this->theme,
                $this->slug,
                $this->timestamps,
                $this->entity,
                $this->seeder,
                $this->edit_slug
            );
            $this->info('Form created at ' . $form_path);
            $progress->advance();



            // Controllers
            $this->info(PHP_EOL . 'Controllers...');
            $controller_path = CreateCrudController::generate(
                $this->model,
                $this->fields,
                $this->actions,
                $this->breadcrumb,
                $this->theme,
                $this->slug,
                $this->timestamps,
                $this->entity
            );
            $this->info('Controller created at ' . $controller_path);
            $progress->advance();



            // Routes
            $this->info(PHP_EOL . 'Routes...');
            $route_path = CreateCrudRoute::generate(
                $this->model,
                $this->fields,
                $this->actions,
                $this->breadcrumb,
                $this->theme,
                $this->slug,
                $this->timestamps,
                $this->entity
            );
            $this->info('Routes created at ' . $route_path);
            $progress->advance();


            //  Views and registered link to left sidebar
            $this->info(PHP_EOL . 'Views...');
            $view_path = CreateCrudView::generate(
                $this->model,
                $this->fields,
                $this->actions,
                $this->breadcrumb,
                $this->theme,
                $this->slug,
                $this->timestamps,
                $this->imagemanager,
                $this->icon,
                $this->trans,
                $this->clone,
            );
            $this->info('Views created at ' . $view_path);
            $progress->advance();
        }


        // update composer autoload for seeding
        $this->runProcess("composer dump-autoload");

        $progress->finish();
    }





    private function removeFileExtension(string $file): string
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }



    private function displayResult(bool $result, string $path): void
    {
        if ($result) {
            $this->info('File created at ' . $path);
        } else {
            if (!$this->polymorphic)
                $this->info('File ' . $path . ' already exists');
        }
    }

}
