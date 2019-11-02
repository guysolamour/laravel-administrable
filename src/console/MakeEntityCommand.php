<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Console\Command;

class MakeEntityCommand extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:make:entity
                             {model : Model name.}
                             {--s|slug= : The field to slugify}
                             {--d|seed : Seed the table}
                             {--p|polymorphic : The model will be a polymorphic model (morphTo)}
                             {--t|timestamps : Determine if the model is not timestamped}
                            ';

    public function handle()
    {
        $this->call('admin:make:crud',[
            'model' => $this->argument('model'),
           '--slug' => $this->option('slug'),
           '--seed' => $this->option('seed'),
           '--polymorphic' => $this->option('polymorphic'),
           '--entity' => true,
        ]);
    }
}
