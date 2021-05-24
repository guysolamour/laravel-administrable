<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\BaseCommand;

class RollbackCrudCommand extends BaseCommand
{
    use CrudTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:rollback:crud
                             {model? : Model name }
                             {--r|rollback=true : Run artisan rollback command }
                             ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback crud';


    public function handle()
    {
        $this->info('Rollback...');

        $model = $this->getModel();

        $crud = new Crud($model);


        // Migration
        $this->displayTitle('Removing Migration...');
        $message = $crud->rollback('migration');
        $this->info($message);

        die;

        // Model
        $this->displayTitle('Removing Model...');
        $message = $crud->rollback('model');
        $this->info($message);

        // Form
        $this->displayTitle('Removing Form...');
        $message = $crud->rollback('form');
        $this->info($message);

        // Controller
        $this->displayTitle('Removing Controller...');
        $message = $crud->rollback('controller');
        $this->info($message);

        // Route
        $this->displayTitle('Removing Route...');
        $message = $crud->rollback('route');
        $this->info($message);

        // Rollback migration
        $this->displayTitle('Rollback migration...');
        $this->runRollbackMigrationArtisanCommand();

        // Migration
        $this->displayTitle('Removing Migration...');
        $message = $crud->rollback('migration');
        $this->info($message);

        // Seed
        $this->displayTitle('Removing seed...');
        $message = $crud->rollback('seed');
        $this->info($message);

        // Views
        $this->displayTitle('Removing views...');
        $message = $crud->rollback('views');
        $this->info($message);

        $this->runProcess("composer dumpautoload");
    }


    protected function runRollbackMigrationArtisanCommand(): void
    {
        if (!$this->getRollback()) {
            return;
        }

        $this->call('migrate:rollback');
    }

    protected function getModel(): ?string
    {
        $model = Str::ucfirst($this->argument('model'));

        if (empty($model)) {
            $models = $this->getAllCrudConfigModels();

            if (empty($models)) {
                $this->triggerError("You must defined a model in configuration yaml file");
            }

            $model = $this->choice('Which model will be used ?', array_keys($models), array_key_first($models));
        }

        return $model;
    }

    protected function getRollback(): bool
    {
        return $this->option('rollback') == 'true';
    }

}
