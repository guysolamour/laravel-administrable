<?php

namespace Guysolamour\Administrable\Console\Storage;


class Zip extends \ZipArchive
{
    protected ?string $file_path = null;

    public function __construct(?string $file_path = null)
    {
        $this->setPath($file_path);
    }

    
    public function setPath(?string $file_path) :self
    {
        $this->file_path = $file_path;

        return $this;
    }


    public function addFolder(string $folder_path) :self
    {
        if (is_null($this->file_path)){
            throw new \RuntimeException("File path is required  before adding content");
        }

        if ($this->open($this->file_path, self::CREATE) !== true) {
            throw new \RuntimeException('Cannot open ' . $this->file_path);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $folder_path,
                \FilesystemIterator::FOLLOW_SYMLINKS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        while ($iterator->valid()) {

            if (!$iterator->isDot()) {
                $filePath = $iterator->getPathName();
                $relativePath = substr($filePath, mb_strlen($folder_path));
                if (!$iterator->isDir()) {
                    $this->addFile($filePath, $relativePath);
                } else {
                    if ($relativePath !== false) {
                        $this->addEmptyDir($relativePath);
                    }
                }
            }
            $iterator->next();
        }

        return $this;
    }

    public function save() :?string
    {
        $this->close();

        return $this->file_path;
    }
}
