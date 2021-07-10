<?php

namespace Guysolamour\Administrable\Console\Extension\Add;


use Guysolamour\Administrable\Console\Extension\BaseExtension;

class MailboxExtension extends BaseExtension
{

    public function run()
    {
        if ($this->checkifExtensionHasBeenInstalled()){
            $this->triggerError("The [{$this->name}] extension has already been added, remove all generated files and run this command again!");
        }

        $this->loadMigrations();

        $this->loadControllers();
        $this->loadSeeders();
        $this->loadViews();
        $this->runMigrateArtisanCommand();

        $this->displayMessage("{$this->name} extension added successfully.");
    }

    protected function getHeaderFrontUrlRoute() :string
    {
        return 'create';
    }

}
