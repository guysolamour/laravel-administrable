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

        $this->loadViews();
        $this->loadAssets();
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
        $this->registerBackUrlInSidebarView();

        $search = '{{-- add livenews extension here --}}';
        $path =  resource_path("views/{$this->data_map['{{frontLowerNamespace}}']}/layouts/default.blade.php");
        $replace = "@include(front_view_path('{$this->data_map['{{extensionsFolder}}']}.{$this->data_map['{{extensionPluralSlug}}']}.index'))";
        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            $replace,
            $path
        );

        $this->displayMessage('Views created at ' . $path);
    }
}
