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
        //dd($this->timestamps);

        // dd($this->option('slug'));
        $this->model = $this->argument('model');


        $fields = $this->getFields();

        // Models
        $this->info(PHP_EOL . 'Creating Model...');

        $CreateModel = new CreateModel(
            $this->model, $fields, $this->slug, $this->timestamps
        );
        $model_path = $CreateModel->generate();

        $this->info('Model created at ' . $model_path);
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
