<?php

namespace Guysolamour\Administrable\Console\Extension;


use Guysolamour\Administrable\Console\BaseCommand;
use Guysolamour\Administrable\Console\CommandTrait;

class AddExtensionCommand extends BaseCommand
{
    use CommandTrait;

    protected array $extensions = ['livenews'];


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:add:extension
                             {name? : Extension name }
                             ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new extension';





    public function handle()
    {
        $name = $this->argument('name');
        // $extensions = config('administrable.extensions', []);

        // Permet de choisir l'extension dans une liste si pas passé en paramètre
        if (empty($name)){
            $name = $this->choice('Which extensions do you want to add ?', $this->extensions, 0);
        }

        // Fait une validation de l 'extension
        if (!in_array($name, $this->extensions)){
            $this->triggerError("The [$name] extension is not in available. Available extensions are [". join(',', $this->extensions) ."].");
        }

        $this->addExtension($name);

    }

    public function addExtension(string $name)
    {
        switch ($name) {
            case 'livenews':
                LivenewsExtension::init($name, $this);
                break;
            default:
                break;
        }
    }


}
