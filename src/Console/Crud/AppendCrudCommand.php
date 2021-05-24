<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\BaseCommand;

class AppendCrudCommand extends BaseCommand
{
    use CrudTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:append:crud
                             {model? : Model name }
                             {--m|migrate=true : Run artisan migrate command }
                             ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Append field to existed crudtab model';



    public function handle()
    {
        $model = $this->getModel();

        if (!$this->checkIfCrudHasAlreadyBeenDoneForModel($model)) {
            $this->triggerError(
                sprintf("Can not append. The [%s] crud has not been done. Run [administrable:make:crud] before appending", $model)
            );
        }

        $migrate = $this->getMigrate();
        $crud = new Crud($model, $migrate, true);


        // Model
        $this->displayTitle('Append Model...');
        $message = $crud->append('model');
        $this->info($message);

        // Form
        $this->displayTitle('Append Form...');
        $message =  $crud->append('form');
        $this->info($message);

        // Views
        $this->displayTitle('Append Views...');
        $message =  $crud->append('views');
        $this->info($message);

        // Seed
        $this->displayTitle('Append Seed');
        $message =  $crud->append('seed');
        $this->info($message);

        // Migration
        $this->displayTitle('Append Migration');
        $message =  $crud->append('migration');
        $this->info($message);

        // Run migration
        $this->displayTitle('Migrate...');
        $this->runMigration($migrate);
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
}
