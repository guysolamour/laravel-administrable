<?php

namespace Guysolamour\Admin\Console;


use Illuminate\Console\Command;

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
//        $this->info(PHP_EOL . 'Creating Model...');
//
//        $model_path = CreateCrudModel::generate($this->model, $this->fields, $this->slug, $this->timestamps);
//
//        $this->info('Model created at ' . $model_path);
//        $progress->advance();

        // Migrations
        $this->info(PHP_EOL . 'Creating Migration...');

        $migration_path = CreateCrudMigration::generate($this->model, $this->fields,$this->slug,$this->timestamps);

        $this->info('Model created at ' . $migration_path);
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
