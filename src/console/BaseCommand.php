<?php

namespace Guysolamour\Administrable\Console;


use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;


class BaseCommand extends Command {

    protected  const TPL_PATH = __DIR__ . '/../templates';



    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
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
    protected function parseName(?string $name = null) :array
    {
        if (!$name)
        $name = $this->name;

        return array(
            '{{namespace}}' => $this->getNamespace(),
            '{{pluralCamel}}' => Str::plural(Str::camel($name)),
            '{{pluralSlug}}' => Str::plural(Str::slug($name)),
            '{{pluralSnake}}' => Str::plural(Str::snake($name)),
            '{{pluralClass}}' => Str::plural(Str::studly($name)),
            '{{singularCamel}}' => Str::singular(Str::camel($name)),
            '{{singularSlug}}' => Str::singular(Str::slug($name)),
            '{{singularSnake}}' => Str::singular(Str::snake($name)),
            '{{singularClass}}' => Str::singular(Str::studly($name)),
            '{{frontNamespace}}'=> ucfirst(config('administrable.front_namespace')),
            '{{backNamespace}}'=> ucfirst(config('administrable.back_namespace')),
        );
    }

    /**
    * @param string|array $files
    * @param string $path
    * @return void
    */
    protected function compliedAndWriteFile($files,string $path) :void
    {

        if (is_array($files)) {
            foreach ($files as $file) {
                $this->compliedAndWriteFile($file, $path);
            }
            return;
        }

        $data_map = $this->parseName();

        $stub = $this->isSingleFile($files) ? $files : $this->filesystem->get($files->getRealPath());

        $this->createDirectoryIfNotExists(
            $path,
            !$this->isSingleFile($files)
        );
        $complied = strtr($stub, $data_map);

        $this->writeFile(
            $complied,
            $this->isSingleFile($files) ? $path : $path . '/' . $files->getFilenameWithoutExtension() . '.php'
        );


    }


    protected function isSingleFile($file) :bool
    {
        return is_string($file);
    }

    /**
    * @param string|array $files
    * @param string $search
    * @param string $path
    * @return void
    */
    protected function replaceAndWriteFile($files,string $search, $replace , string $path)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                $this->replaceAndWriteFile($file, $search,$replace, $path);
            }
            return;
        }

        $stub = $this->isSingleFile($files) ? $files : $this->filesystem->get($files->getRealPath());
        // $stub = $this->filesystem->get($files->getRealPath());
        $this->createDirectoryIfNotExists(
            $path,
            !$this->isSingleFile($files)
        );
        $complied = str_replace($search, $replace,  $stub);

        $this->writeFile(
            $complied,
            $this->isSingleFile($files) ? $path : $path . '/' . $files->getFilenameWithoutExtension() . '.php'
        );
    }



    /**
    * Permet de créer un dossier
    * @param string $path
    * @param boolean $folder Permet de savoir si le chemin passé est un dossier ou fichier
    * @return void
    */
    protected function createDirectoryIfNotExists(string $path, bool $folder = true): void
    {

        $dir = $folder ? $path : $this->filesystem->dirname($path);

        if (!$this->filesystem->exists($dir)) {
            $this->filesystem->makeDirectory($dir, 0755, true);
        }
    }

    /**
    * @param mixte $compiled
    * @param string $path
    * @return void
    */
    protected function writeFile($compiled, string $path) :void
    {
        // dd($path, $compiled);
        $this->filesystem->put(
            $path,
            $compiled
        );
    }

}
