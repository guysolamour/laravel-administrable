<?php

namespace Guysolamour\Administrable\Console\Extension;


use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\BaseCommand;
use Guysolamour\Administrable\Console\Extension\ExtensionTrait;

class AddExtensionCommand extends BaseCommand
{
    use ExtensionTrait;


    private const EXTENSIONS = [
        'livenews', 'blog', 'testimonial', 'mailbox', 'shop', 'ad',
    ];


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:extension:add
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
        $name = $this->getExtensionName();

        $extensions = $this->getExtensions();


        if (!$extensions->has($name)){
            $this->triggerError("The [$name] extension is not in available. Available extensions are [" . join(', ', self::EXTENSIONS) . "].");
        }

        if (!$this->configHasBeenPublished()){
            $this->info("Please publish config file and activate the ['". $name ."'] extension.");
        }

        $this->addExtension($name);
    }


    private function configHasBeenPublished() :bool
    {
        return file_exists(config_path('administrable.php'));
    }

    private function getExtensionName() :string
    {
        $name = $this->argument('name');

        if (empty($name)) {
            $name = $this->choice('Which extensions do you want to add ?', self::EXTENSIONS, 0);
        }

        return $name;
    }

    public function addExtension(string $name)
    {
        $class = "Guysolamour\\Administrable\Extensions\\". Str::ucfirst($name) ."\\Console\\Commands\\InstallExtensionCommand";

        if (!class_exists($class)){
            $this->triggerError("The [$name] extension is not in available. Available extensions are [" . join(', ', self::EXTENSIONS) . "].");
        }
        /** @var \Guysolamour\Administrable\Console\Extension\BaseExtension */
        $instance = new $class($this, $name);
        $instance->run();
    }

	public function getExtensions() :\Illuminate\Support\Collection
	{
        $extensions = collect();

        foreach (self::EXTENSIONS as $extension) {
            if (!$extensions->has($extension)){
                $extensions->put($extension, ['label' => trans_fb("administrable-ad::{$extension}.label", $extension)]);
            }
        }

        return $extensions;
	}
}
