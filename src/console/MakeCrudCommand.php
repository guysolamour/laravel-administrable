<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Guysolamour\Administrable\Console\Crud\CreateCrudForm;
use Guysolamour\Administrable\Console\Crud\CreateCrudView;
use Guysolamour\Administrable\Console\Crud\CreateCrudModel;
use Guysolamour\Administrable\Console\Crud\CreateCrudRoute;
use Guysolamour\Administrable\Console\Crud\CreateCrudBreadcumb;
use Guysolamour\Administrable\Console\Crud\CreateCrudMigration;
use Guysolamour\Administrable\Console\Crud\CreateCrudController;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MakeCrudCommand extends Command
{




    protected const TYPES = [
        'string','text','boolean','date','datetime','decimal','float','enum','double','integer',
        'ipAdress','longText','mediumText','mediumInterger','image','relation'
    ];

    protected const RELATION_TYPES = [
        'One to One','One to Many','Many to One'
    ];



    protected $model = '';
    protected $fields = [];
    protected $tempFields = [];
    protected $timestamps;
    protected $seed;
    protected $slug;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:make:crud
                            {model : Model name.}
                             {--s|slug= : The field to slugify}
                             {--d|seed : Seed the table}
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

        $progress = $this->output->createProgressBar(10);

        $this->timestamps = $this->option('timestamps');
        $this->seed = $this->option('seed');
        $this->slug = is_string($this->option('slug')) ? strtolower($this->option('slug')) : $this->option('slug');
        $this->model = $this->argument('model');

        // check if the model exists
        $config = "administrable.models.".strtolower($this->model);
        $config_fields = config($config);


        if (!empty($config_fields)) {
            $this->fields = $config_fields;


            if(isset($this->fields['slug']) && !empty($this->fields['slug'])){

                // check if the relqted slug exists in model fields
                if (array_key_exists($this->fields['slug'], $this->fields)){
                    // affect slug and remove the slug field in all model field
                    $this->slug = is_string($this->fields['slug']) ? strtolower($this->fields['slug']) : $this->fields['slug'];
                    $this->fields = Arr::except($this->fields,'slug');
                }
                else{
                    $this->error('The related slug field [' . $this->fields['slug'] . '] does not exists in model fields');
                    die;
                }

            }
        } else {
            $this->fields = $this->getFields();
        }


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
        $this->call('migrate');
        $progress->advance();

        // Forms
        $this->info(PHP_EOL . 'Forms...');
        $form_path = CreateCrudForm::generate($this->model, $this->fields,$this->slug);
        $this->info('Form created at ' . $form_path);
        $progress->advance();



        // Controllers
        $this->info(PHP_EOL . 'Controllers...');
        $controller_path = CreateCrudController::generate($this->model);
        $this->info('Controller created at ' . $controller_path);
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

        // seed table
        if ($this->seed){
            $this->info(PHP_EOL . 'Seeding...');
            $this->callSilent('db:seed', [
                '--class' => Str::plural(Str::studly($this->model))  . 'TableSeeder',
            ]);
            $this->info('Database seeding completed successfully.');
            $progress->advance();
        }




        $progress->finish();

    }

    /**
     * @return array
     */
    private function getFields() :array
    {


        $field = $this->ask('Field');
        $type = $this->anticipate('Type', self::TYPES);

        if ($type === 'relation'){
            $relation_type = $this->choice('Which type of relation is it ?', self::RELATION_TYPES, 1);
            $relation_property = $this->ask('What property will be used to access relation ?');
            $relation_model = $this->anticipate('Which model is associated to ?', $this->getAllAppModels());
            $relation_model_with_namespace = $this->getAllAppModels(true)[$relation_model];
            $rules = '';
            $this->tempFields[$field] = [
                'name' => $field,
                'type'=>
                    [$type => ['name' => $relation_type,'model' => $relation_model_with_namespace,'property' => $relation_property]],
                'rules' => $rules];
        }else {
            $rules = $this->ask('Rules');
            $this->tempFields[$field] = ['name' => $field,'type'=> $type,'rules' => $rules];
        }


        if ($this->confirm('Add another field?')) {
            $this->getFields();
        }

        return $this->tempFields;


    }

    private function removeFileExtension(string $file) :string{
        // substr($file,0, strpos($file, '.'));
        return pathinfo($file, PATHINFO_FILENAME);
    }

    private function getAllAppModels(bool $withNamespace = false) :array
    {
        // get all models in app folder
       $results = glob(app_path() . '/*.php');

       $out = [];
       foreach ($results as $file){
           $parts = explode('/',$file);

           if ($withNamespace){
               $model = Container::getInstance()->getNamespace() . end($parts);
           }else {
               $model = end($parts);
           }
           $out[$this->removeFileExtension(end($parts))] = $this->removeFileExtension($model);
       }

       // get all models in app/models folder
        $path = app_path() . "/Models";
        $results = scandir($path);

        foreach ($results as $result) {
            if ($result === '.' || $result === '..' || $result === 'BaseModel.php') continue;

            if ($withNamespace){
                $model = Container::getInstance()->getNamespace() . 'Models\\' . $result;
            }else {
                $model = $result;
            }
            $out[$this->removeFileExtension($result)] = $this->removeFileExtension($model);
        }

        return $out;
    }



    private function displayResult(bool $result, string $path) :void {
        if($result){
            $this->info('File created at ' . $path);
        }else{
            $this->info('File '. $path . ' already exists');
        }
    }

}
