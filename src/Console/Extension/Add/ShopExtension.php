<?php

namespace Guysolamour\Administrable\Console\Extension\Add;

use Guysolamour\Administrable\Console\Extension\BaseExtension;

class ShopExtension extends BaseExtension
{
    public function run()
    {
        if ($this->checkifExtensionHasBeenInstalled()){
            $this->triggerError("The [{$this->name}] extension has already been added, remove all generated files and run this command again!");
        }

        $this->loadViews();
        $this->loadAssets();
        $this->registerSettings();
        $this->loadMigrations(false);
        $this->loadControllers();
        $this->loadRoutes();
        $this->loadSeeders();
        $this->runMigrateArtisanCommand();

        $this->extension->info("{$this->name} extension added successfully.");
    }

    public function registerSettings()
    {
        $path = config_path('settings.php');
        $search =  "'settings' => [";
        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            <<<TEXT
            $search
                   \Guysolamour\Administrable\Settings\ShopSettings::class,
            TEXT,
            $path
        );

        $this->displayMessage('Settings registered at ' . $path);
    }


    protected function loadViews(): void
    {
        parent::loadViews();

        $this->registerFrontUrlInHeader('cart.show', [], true, 'Panier');
    }
}
