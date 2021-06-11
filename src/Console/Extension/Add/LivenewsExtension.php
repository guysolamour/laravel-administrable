<?php

namespace Guysolamour\Administrable\Console\Extension\Add;

use Guysolamour\Administrable\Console\Extension\BaseExtension;

class LivenewsExtension extends BaseExtension
{

    public function run()
    {
        if ($this->checkifExtensionHasBeenInstalled()){
            $this->triggerError("The [{$this->name}] extension has already been added, remove all generated files and run this command again!");
        }

        $this->loadSettings();
        $this->loadRoutes();
        $this->loadControllers();
        $this->loadViews();
        $this->loadMigrations();
        $this->runMigrateArtisanCommand();

        $this->extension->info("{$this->name} extension added successfully.");
    }

    protected function enableRegisteringFrontUrlInHeader(): bool
    {
        return false;
    }


    protected function loadViews(): void
    {
        parent::loadViews();

        $search = '{{-- add livenews extension here --}}';
        $path =  resource_path("views/{$this->data_map['{{frontLowerNamespace}}']}/layouts/default.blade.php");
        $replace = "@include('{$this->data_map['{{frontLowerNamespace}}']}.{$this->data_map['{{extensionsFolder}}']}.{$this->data_map['{{extensionPluralSlug}}']}.index')";
        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            $replace,
            $path
        );
    }

}