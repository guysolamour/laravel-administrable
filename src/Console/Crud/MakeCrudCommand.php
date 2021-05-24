<?php

namespace Guysolamour\Administrable\Console\Crud;


use Guysolamour\Administrable\Console\BaseCommand;


class MakeCrudCommand extends BaseCommand
{
    use CrudTrait;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:make:crud
                             {model? : Model name}
                             {--m|migrate=true : Run artisan migrate command}
                             ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create, model, migration and all views (crud)';


    public function handle()
    {
        $this->info('Initiating...');

        $progress = $this->output->createProgressBar(10);

        // on récupère le modele que nous allons créer
        $model =  $this->getModel($this->argument('model'));

        if ($this->checkIfCrudHasAlreadyBeenDoneForModel($model)){
            $this->triggerError("The model [{$model}] crud has already been done.");
        }

        $migrate = $this->getMigrate();

        $crud = new Crud($model, $migrate);

        // Model
        $this->displayTitle('Creating Model...');
        [$result, $path] = $crud->generate('model');
        $this->displayResult($result, $path);
        $progress->advance();

        // Migration
        $this->displayTitle('Creating Migration...');
        [$result, $migration] = $crud->generate('migration');
        $this->displayResult($result, $migration);
        $progress->advance();

        // Seeder
        $this->displayTitle('Creating Seed...');
        [$result, $path] = $crud->generate('seed');
        $this->displayResult($result, $path);
        $progress->advance();

        // Run migration
        $this->displayTitle('Migrate...');
        $this->runMigration($migrate);
        $progress->advance();

        // Form
        $this->displayTitle('Creating form...');
        [$result, $path] = $crud->generate('form');
        $this->displayResult($result, $path);
        $progress->advance();

        // Controller
        $this->displayTitle('Creating controller...');
        [$result, $path] = $crud->generate('controller');
        $this->displayResult($result, $path);
        $progress->advance();

        // Route
        $this->displayTitle('Creating route...');
        [$result, $path] = $crud->generate('route');
        $this->displayResult($result, $path);
        $progress->advance();

        // Views
        $this->displayTitle('Creating views...');
        [$result, $path] = $crud->generate('views');
        $this->displayResult($result, $path);
        $progress->advance();

        // update composer autoload for seeding
        $this->runProcess("composer dump-autoload");

        $progress->finish();
    }

}
