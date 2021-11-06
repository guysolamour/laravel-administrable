<?php

namespace Guysolamour\Administrable\Console;

use Illuminate\Support\Arr;

class Filesystem extends \Illuminate\Filesystem\Filesystem
{
    /** @var array  */
    public $data_map = [];

    public function __construct(array $data_map = [])
    {
        $this->data_map = $data_map;
    }

    public function compliedFile(string $file, bool $get_file_content = true, ?array $data_map = null): string
    {
        $data_map ??= $this->data_map;

        $file = $get_file_content ? $this->get($file) : $file;

        return strtr($file, $data_map);
    }

    public function complied(string $file, ?array $data_map = null): string
    {
        $data_map ??= $this->data_map;

        return strtr($this->get($file), $data_map);
    }

    public function compliedOnly(string $file, ?array $data_map = null): string
    {
        $data_map ??= $this->data_map;

        return strtr($file, $data_map);
    }

    public function compliedAndWriteFile($files, string $path, ?array $data_map = null, string $filename_prefix = '', string $extension = '.php'): void
    {
        $data_map ??= $this->data_map;
        /**
         * @var \Symfony\Component\Finder\SplFileInfo[]
         */
        $files = $this->isSingleFile($files) ? Arr::wrap($files) : $files;

        foreach ($files as $file) {

            $complied = $this->isSingleFile($file) ? $file : $this->complied($file->getRealPath(), $data_map);

            $this->writeFile(
                $this->isSingleFile($file) ? $path : $path . '/' . $filename_prefix . $file->getFilenameWithoutExtension() . $extension,
                $complied
            );
        }
    }

    public function renameFile(string $directory, string $old_name, string $new_name) :void
    {
        if (!$this->isDirectory($directory)){
            throw new \Exception("The [{$directory}] does not exixts");
        }

        $old_name = $directory . '/' . $old_name;
        $new_name = $directory . '/' . $new_name;

        $file = $this->get($old_name);

        $this->delete($old_name);

        $this->writeFile($new_name, $file);
    }

    /**
     *
     * @param string|string[] $files
     * @param string $path
     * @return void
     */
    public function compliedAndWriteFileRecursively($files, string $path, ?array $data_map = null): void
    {
        /**
         * @var \Symfony\Component\Finder\SplFileInfo[]
         */
        $files = $this->isSingleFile($files) ? Arr::wrap($files) : $files;

        foreach ($files as $file){
            $this->compliedAndWriteFile(
                $this->compliedFile($file),
                $path . '/' . $file->getRelativePath() .  '/' . $file->getFilenameWithoutExtension() . '.php',
                $data_map
            );
        }
    }

    public function replaceAndWriteFile($files, string $search, $replace, string $path) :void
    {
        /**
         * @var \Symfony\Component\Finder\SplFileInfo[]
         */
        $files = $this->isSingleFile($files) ? Arr::wrap($files) : $files;

        foreach ($files as $file) {

            $stub = $this->isSingleFile($file) ? $file : $this->get($file->getRealPath());

            $complied = str_replace($search, $replace,  $stub);
            $this->writeFile(
                $this->isSingleFile($file) ? $path : $path . '/' . $file->getFilenameWithoutExtension() . '.php',
                $complied,
            );

        }
    }

    /**
     * Get an array of all files in a directory.
     *
     * @param string $path
     * @param boolean $all
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function getFilesFromDirectory(string $path, bool $all = true)
    {
        if (!$this->exists($path)) {
            return [];
        }

        return $all ? $this->allFiles($path) : $this->files($path);
    }

    public function isSingleFile($file): bool
    {
        return is_string($file);
    }

    public function writeFile(string $path, string $complied, bool $overwrite = true): bool
    {
        $this->createDirectoryIfNotExists($path);

        if ($overwrite || !$this->exists($path)) {
            $this->put(
                $path,
                $complied
            );

            return true;
        }

        return false;
    }

    public function createDirectoryIfNotExists(string $path): void
    {
        $dir = $this->isFolder($path) ? $path : $this->dirname($path);

        if ($this->missing($dir)) {
            $this->makeDirectory($dir, 0755, true);
        }
    }

    public function isFolder(string $file) :bool
    {
        $f = pathinfo($file, PATHINFO_EXTENSION);
        return strlen($f) == 0;
    }
}
