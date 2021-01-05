<?php

namespace Guysolamour\Administrable\Console\Extension;

use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;



abstract class BaseExtension
{
    protected  const TPL_PATH = __DIR__ . '/../../stubs/extensions';

    /**
     * @var string
     */
    protected string $extension_name;
    /**
     * @var Filesystem
     */
    protected $filesystem;
    /**
     * @var Command
     */
    protected $baseCommand;


    public function __construct(string $extension_name, Command $base_command)
    {
        $this->filesystem = new Filesystem;
        $this->extension_name = $extension_name;
        $this->base_command = $base_command;

    }

    protected function getExtensionStubsPath(string $path = '') :string
    {
        return self::TPL_PATH . '/' . $this->extension_name . '/'. $path;
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
     * @return array
     */
    protected function parseName(?string $name = null): array
    {

        if (!$name)
            $name = config('administrable.guard', 'admin');

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
            '{{theme}}'               =>  Str::lower(config('administrable.theme')),
            '{{frontNamespace}}'      =>  ucfirst(config('administrable.front_namespace')),
            '{{frontLowerNamespace}}' =>  Str::lower(config('administrable.front_namespace')),
            '{{backNamespace}}'       =>  ucfirst(config('administrable.back_namespace')),
            '{{backLowerNamespace}}'  =>  Str::lower(config('administrable.back_namespace')),
            '{{administrableLogo}}'   =>  config('administrable.logo_url'),
        ];
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param string $path
     * @param boolean $all
     * @return array
     */
    protected function getFilesFromDirectory(string $path, bool $all = true)
    {
        if (!$this->filesystem->exists($path)) {
            return [];
        }

        return $all ? $this->filesystem->allFiles($path) : $this->filesystem->files($path);
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

    /**
     *
     * @param string $file // Must be the path or the file content
     * @param boolean $get_content
     * @return string
     */
    protected function compliedFile(string $file, bool $get_content = true, ?array $data_map = null): string
    {
        $file = $get_content ? $this->filesystem->get($file) : $file;
        $data_map = $data_map ?: $this->parseName();

        return strtr($file, $data_map);
    }

    /**
     * @param string|object|array $files
     * @param string $path
     * @return void
     */
    protected function compliedAndWriteFile($files, string $path, string $filename_prefix = ''): void
    {

        if (is_array($files)) {
            foreach ($files as $file) {
                $this->compliedAndWriteFile($file, $path, $filename_prefix);
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
            $this->isSingleFile($files) ? $path : $path . '/' . $filename_prefix . $files->getFilenameWithoutExtension() . '.php'
        );
    }

    protected function isSingleFile($file): bool
    {
        return is_string($file);
    }

    /**
     * @param mixte $complied
     * @param string $path
     * @return void
     */
    protected function writeFile($complied, string $path, bool $overwrite = true): bool
    {

        if ($overwrite) {
            $this->filesystem->put(
                $path,
                $complied
            );
            return true;
        }

        if (!$this->filesystem->exists($path)) {
            $this->filesystem->put(
                $path,
                $complied
            );
            return true;
        }

        return false;
    }


    /**
     * Create a folder
     * @param string $path
     * @param boolean $folder Used to find out if the path passed is a folder or file
     * @return void
     */
    protected function createDirectoryIfNotExists(string $path, bool $folder = true): void
    {

        $dir = $folder ? $path : $this->filesystem->dirname($path);

        if ($this->filesystem->missing($dir)) {
            $this->filesystem->makeDirectory($dir, 0755, true);
        }
    }

    /**
     * @param string|object|array $files
     * @param string $search
     * @param string $path
     * @return void
     */
    protected function replaceAndWriteFile($files, string $search, $replace, string $path)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                $this->replaceAndWriteFile($file, $search, $replace, $path);
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

    protected function runProcess(string $command)
    {
        $process = new Process(explode(' ', $command), null, null, null, 3600);

        $process->run(function ($type, $buffer) {
            // $this->getOutput()->write('> '.$buffer);
        });

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }
    }




}
