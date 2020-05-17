<?php

namespace Guysolamour\Administrable\Console;

use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;


class BaseCommand extends Command {

    protected  const TPL_PATH = __DIR__ . '/../stubs';


    protected $filesystem;


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

        return [
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
            '{{frontLowerNamespace}}'=> Str::lower(config('administrable.front_namespace')),
            '{{backNamespace}}'=> ucfirst(config('administrable.back_namespace')),
            '{{backLowerNamespace}}'=> Str::lower(config('administrable.back_namespace')),
            '{{modelsFolder}}' => ucfirst($this->models_folder_name),
            '{{administrableLogo}}' => config('administrable.logo_url'),
            '{{theme}}' => $this->theme,
        ];
    }

    protected function compliedFile($file, $get_content = true)
    {
        $file = $get_content ? $this->filesystem->get($file) : $file;
        return strtr($file, $this->parseName());
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

    protected function recurseRmdir($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->recurseRmdir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
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

	protected function runProcess(string $command)
	{
		$process = new Process(explode(' ', $command), null, null, null, 3600);

		$process->run(function ($type, $buffer) {
			// $this->getOutput()->write('> '.$buffer);
		});

		if (! $process->isSuccessful()) {
			throw new RuntimeException($process->getErrorOutput());
		}
	}
}
