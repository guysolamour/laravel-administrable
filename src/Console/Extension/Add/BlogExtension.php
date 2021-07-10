<?php

namespace Guysolamour\Administrable\Console\Extension\Add;

use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\Extension\BaseExtension;

class BlogExtension extends BaseExtension
{
    public function run()
    {
        if ($this->checkifExtensionHasBeenInstalled()){
            $this->triggerError("The [{$this->name}] extension has already been added, remove all generated files and run this command again!");
        }

        $this->loadModels();
        $this->loadMigrations();
        $this->loadControllers();
        $this->loadSeeders();
        $this->loadViews();
        $this->runMigrateArtisanCommand();

        $this->displayMessage("{$this->name} extension added successfully.");
    }


    protected function loadViews() :void
    {
        $this->loadFrontViews();
        $this->loadBackViews();

        $this->registerBackUrlInSidebarView();

        if ($this->enableRegisteringFrontUrlInHeader()) {
            $this->registerFrontUrlInHeader(
                "index", ["show"]
            );
        }
    }

    protected function getExtensionPlural(?string $name = null): string
    {
        $name ??= $this->name;

        return $name;
    }

    protected function loadModels(): void
    {
        parent::loadModels();

        $this->addPostRelationInDefaultGuardModel();
    }

    protected function addPostRelationInDefaultGuardModel() :void
    {
        $path = app_path("{$this->data_map['{{modelsFolder}}']}/{$this->data_map['{{singularClass}}']}.php");
        $complied = $this->filesystem->get($path);

        $complied = $this->addNamespaceOnTheTop($complied);

        $search = "// add relation methods below";
        $relation = <<<TEXT
        $search

            public function posts()
            {
                return \$this->hasMany(config('administrable.extensions.blog.post.model'), 'author_id');
            }
        TEXT;

        $this->filesystem->replaceAndWriteFile(
            $complied,
            $search,
            $relation,
            $path
        );
    }

    protected function getModelNamespace(string $model) :string
    {
        return "{$this->data_map['{{namespace}}']}\\{$this->data_map['{{modelsFolder}}']}\\{$this->getFolder(null, '\\')}\\" . Str::ucfirst($model);
    }

    protected function addNamespaceOnTheTop(string $model): string
    {
        $search = "Traits\ModelTrait;";
        $replace = "use {$this->getModelNamespace('Post')};" ;

        $model = str_replace($search, $search . PHP_EOL . $replace, $model);

        return $model;
    }

    protected function checkifExtensionHasBeenInstalled(): bool
    {
        return $this->filesystem->exists(app_path("Models/{$this->getFolder()}/Post.php"));
    }
}
