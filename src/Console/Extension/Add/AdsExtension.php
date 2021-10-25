<?php

namespace Guysolamour\Administrable\Console\Extension\Add;

use Guysolamour\Administrable\Console\Extension\BaseExtension;

class AdsExtension extends BaseExtension
{

    public function run()
    {
        if ($this->checkifExtensionHasBeenInstalled()) {
            $this->triggerError("The [{$this->name}] extension has already been added, remove all generated files and run this command again!");
        }

        $this->loadMigrations();
        $this->loadSeeders();
        $this->loadAssets();
        $this->loadViews();
        $this->runMigrateArtisanCommand();

        $this->displayMessage("{$this->getUcfirstName()} extension added successfully.");
    }
}
