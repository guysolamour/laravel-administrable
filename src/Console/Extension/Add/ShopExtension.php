<?php

namespace Guysolamour\Administrable\Console\Extension\Add;

use Illuminate\Support\Str;
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
        $this->addBuyerTraitToUserModel();
        $this->runMigrateArtisanCommand();
        $this->addInvoicePackageInConposerFile();

        $this->extension->info("{$this->name} extension added successfully. Don't forget to run composer update for invoice package ");
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

    private function addBuyerTraitToUserModel() :void
    {
        $path = app_path(Str::ucfirst(config('administrable.models_folder'))). '/User.php';
        $search = "use ModelTrait;";

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            <<<TEXT
            $search
                use BuyerTrait;
            TEXT,
            $path
        );

        $search = "use Guysolamour\Administrable\Traits\ModelTrait;";
        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            <<<TEXT
            $search
            use Guysolamour\Administrable\Traits\Shop\BuyerTrait;
            TEXT,
            $path
        );
    }

	private function addInvoicePackageInConposerFile() :void
	{
        $composer_path = base_path('composer.json');

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($composer_path),
            $search = '"require": {',
            <<<TEXT
            $search
                    "laraveldaily/laravel-invoices": "^2.0",
            TEXT,
            $composer_path
        );
	}
}
