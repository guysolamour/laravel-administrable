<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;



class BaseCommand extends Command
{

    protected  const TPL_PATH = __DIR__ . '/../stubs';

    protected  const BASE_PATH = __DIR__ . '/../..';


    protected $filesystem;


    public function __construct()
    {
        parent::__construct();

        $this->filesystem = new Filesystem;
    }

    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    protected function getNamespace()
    {
        $namespace = Container::getInstance()->getNamespace();
        return rtrim($namespace, '\\');
    }


    /**
     * Parse guard name
     * Get the guard name in different cases
     * @param string $name
     * @return array
     */
    protected function parseName(?string $name = null): array
    {
        if (!$name)
            $name = $this->guard;

        return [
            '{{namespace}}'           =>  $this->getNamespace(),
            '{{pluralCamel}}'         =>  Str::plural(Str::camel($name)),
            '{{pluralSlug}}'          =>  Str::plural(Str::slug($name)),
            '{{pluralSnake}}'         =>  Str::plural(Str::snake($name)),
            '{{pluralClass}}'         =>  Str::plural(Str::studly($name)),
            '{{singularCamel}}'       =>  Str::singular(Str::camel($name)),
            '{{singularSlug}}'        =>  Str::singular(Str::slug($name)),
            '{{singularSnake}}'       =>  Str::singular(Str::snake($name)),
            '{{singularClass}}'       =>  Str::singular(Str::studly($name)),
            '{{frontNamespace}}'      =>  ucfirst(config('administrable.front_namespace')),
            '{{frontLowerNamespace}}' =>  Str::lower(config('administrable.front_namespace')),
            '{{backNamespace}}'       =>  ucfirst(config('administrable.back_namespace')),
            '{{backLowerNamespace}}'  =>  Str::lower(config('administrable.back_namespace')),
            '{{modelsFolder}}'        =>  ucfirst($this->models_folder_name),
            '{{administrableLogo}}'   =>  config('administrable.logo_url'),
            '{{theme}}'               =>  $this->theme,
        ];
    }



    protected function compliedAndWriteFileRecursively($files, string $path)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                $this->compliedAndWriteFileRecursively($file, $path);
            }
            return;
        }

        $this->compliedAndWriteFile(
            $this->filesystem->get($files),
            $path . '/' . $files->getRelativePath() .  '/' . $files->getFilenameWithoutExtension() . '.php'
        );
    }






    protected function recurseRmdir($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->recurseRmdir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}
