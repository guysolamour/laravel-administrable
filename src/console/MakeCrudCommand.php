<?php

namespace Guysolamour\Admin\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeCrudCommand extends Command
{


    protected const TYPES = [
        'string','text','boolean','date','datetime','decimal','float','enum','double','integer',
        'ipAdress','longText','mediumText','mediumInterger',
    ];

    protected $model = '';
    protected $fields = [];
    protected $timestamps;
    protected $slug;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:make:crud 
                            {model : Model name.}
                             {--s|slug= : The field to slugify}                            
                             {--t|timestamps : Determine if the model is not timestamped}                            
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create, model, migration and all views';


    public function handle()
    {

        $this->info('Initiating...');

        $progress = $this->output->createProgressBar(12);

        $this->timestamps = $this->option('timestamps');
        $this->slug = $this->option('slug');

        $this->model = $this->argument('model');


        $this->fields = $this->getFields();

        // Models
        $this->info(PHP_EOL . 'Creating Model...');

        //$model_path = CreateCrudModel::generate($this->model, $this->fields, $this->slug, $this->timestamps);

       // $this->info('Model created at ' . $model_path);
        $progress->advance();

        // Migrations and seeds
        $this->info(PHP_EOL . 'Creating Migration...');

//        [$migration_path,$seed_file_name] = CreateCrudMigration::generate($this->model, $this->fields,$this->slug,$this->timestamps);

//        $this->info('Migration created at ' . $migration_path);
        $progress->advance();

        // Migrate
        $this->info(PHP_EOL . 'Migrate...');
//        Artisan::call('migrate');
        $progress->advance();

        // Controllers
        $this->info(PHP_EOL . 'Controllers...');
//        $controller_path = CreateCrudController::generate($this->model, $this->fields,$this->slug,$this->timestamps);
        //$controller_path = CreateCrudController::generate($this->model);
        //$this->info('Controller created at ' . $controller_path);
        $progress->advance();

        // Forms
        $this->info(PHP_EOL . 'Forms...');
        $form_path = CreateCrudForm::generate($this->model, $this->fields,$this->slug);
//        $controller_path = CreateCrudController::generate($this->model);
        $this->info('Form created at ' . $form_path);
        $progress->advance();



    }
    private function getFields()
    {

        $fields = [];

        $fields[] = $this->ask('Field');
        $fields[] = $this->anticipate('Type', self::TYPES);
        $fields[] = $this->ask('Rules');

        if ($this->confirm('Add another field?')) {
            $fields =  array_merge($fields,$this->getFields());
            //return [$fields];
//            $fields[] = $this->getFields();
        }
        //$fields[] = $this->ask('Add another field');

        return $fields;
    }

}
