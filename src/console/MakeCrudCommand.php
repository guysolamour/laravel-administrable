<?php

namespace Guysolamour\Administrable\Console;


use Guysolamour\Administrable\Console\Crud\CreateCrudBreadcumb;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Guysolamour\Administrable\Console\Crud\CreateCrudController;
use Guysolamour\Administrable\Console\Crud\CreateCrudForm;
use Guysolamour\Administrable\Console\Crud\CreateCrudMigration;
use Guysolamour\Administrable\Console\Crud\CreateCrudModel;
use Guysolamour\Administrable\Console\Crud\CreateCrudRoute;
use Guysolamour\Administrable\Console\Crud\CreateCrudView;

class MakeCrudCommand extends Command
{


    protected const TYPES = [
        'string','text','boolean','date','datetime','decimal','float','enum','double','integer',
        'ipAdress','longText','mediumText','mediumInterger','image'
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


    /**
     *
     */
    public function handle()
    {
        $this->info('Initiating...');

        $progress = $this->output->createProgressBar(9);

        $this->timestamps = $this->option('timestamps');
        $this->slug = is_string($this->option('slug')) ? strtolower($this->option('slug')) : $this->option('slug');
        $this->model = $this->argument('model');

        $this->fields = $this->getFields();
        $progress->advance();


        // Models
        $this->info(PHP_EOL . 'Creating Model...');
        [$result,$model_path] = CreateCrudModel::generate($this->model, $this->fields, $this->slug, $this->timestamps);
        $this->displayResult($result,$model_path);

        $progress->advance();




        // Migrations and seeds
        $this->info(PHP_EOL . 'Creating Migration...');
        [$migration_result,$migration_path,$seed_result,$seed_path] = CreateCrudMigration::generate($this->model, $this->fields,$this->slug,$this->timestamps);
        $this->displayResult($migration_result,$migration_path);
        $this->displayResult($seed_result,$seed_path);
        $progress->advance();

        // Migrate
        $this->info(PHP_EOL . 'Migrate...');
        Artisan::call('migrate');
        $progress->advance();

        // Controllers
        $this->info(PHP_EOL . 'Controllers...');
        $controller_path = CreateCrudController::generate($this->model);
        $this->info('Controller created at ' . $controller_path);
        $progress->advance();

        // Forms
        $this->info(PHP_EOL . 'Forms...');
        $form_path = CreateCrudForm::generate($this->model, $this->fields,$this->slug);
        $this->info('Form created at ' . $form_path);
        $progress->advance();

        // Routes
        $this->info(PHP_EOL . 'Routes...');
        $route_path = CreateCrudRoute::generate($this->model);
        $this->info('Routes inserted at ' . $route_path);
        $progress->advance();


        // add breadcrumbs
        $this->info(PHP_EOL . 'Breadcrumb...');
        $breadcrumb_path = CreateCrudBreadcumb::generate($this->model,$this->fields,$this->slug);
        $this->info('Breadcrumb created at ' . $breadcrumb_path);
        $progress->advance();


        // Views and registered link to left sidebar
        $this->info(PHP_EOL . 'Views...');
        $view_path = CreateCrudView::generate($this->model,$this->fields,$this->slug,$this->timestamps);
        $this->info('Views created at ' . $view_path);
        $progress->advance();

         // update composer autoload for seeding
         \exec('composer dump-autoload > /dev/null 2>&1');






        $progress->finish();

    }

    /**
     * @return array
     */
    private function getFields() :array
    {

        $fields = [];

        $fields[] = $this->ask('Field');
        $fields[] = $this->anticipate('Type', self::TYPES);
        $fields[] = $this->ask('Rules');

        if ($this->confirm('Add another field?')) {
            $fields =  array_merge($fields,$this->getFields());
        }

        return $fields;
    }


    private function displayResult(bool $result, string $path) :void {
        if($result){
            $this->info('File created at ' . $path);
        }else{
            $this->error('File '. $path . ' already exists');
        }
    }

}
