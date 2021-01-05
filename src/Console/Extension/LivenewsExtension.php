<?php

namespace Guysolamour\Administrable\Console\Extension;

use Illuminate\Console\Command;

class LivenewsExtension extends BaseExtension
{

    /**
     * @param string $extension_name
     * @return void
     */
    public static function init(string $extension_name,Command $baseCommand)
    {
        $livenews = new static($extension_name, $baseCommand);

        // check if extension has been installed
        if ($livenews->filesystem->exists(app_path('Settings/LivenewsSettings.php'))) {
            $livenews->base_command->error("The [{$extension_name}] extension has already been added, remove all generated files and run this command again!");
            exit();
        }
        

        // settings
        $livenews->loadSettings();
        // routes (front|back)
        $livenews->loadRoutes();
        // controller (front|back)
        $livenews->loadControllers();
        // views (front|back)
        $livenews->loadViews();
        // migration
        $livenews->loadMigrations();
        
        $livenews->base_command->info("{$extension_name} extension added successfully.");
    }

    protected function loadSettings()
    {
        $stubs = $this->getFilesFromDirectory($this->getExtensionStubsPath('settings'), false);

        $path = app_path('Settings');

        $this->compliedAndWriteFile(
            $stubs,
            $path
        );

        // register view in front
        $search = "'settings' => [";
        $path =  config_path('settings.php');
        $replace = "\\{$this->parseName()['{{namespace}}']}\Settings\LivenewsSettings::class,";
        $this->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            $search.$replace,
            $path
        );
    }

    protected function loadMigrations()
    {
        $stubs = $this->getFilesFromDirectory($this->getExtensionStubsPath('migrations'), false);

        $path = database_path('settings');

        $this->compliedAndWriteFile(
            $stubs,
            $path,
            date('Y_m_d_His') . '_'
        );

        $this->runProcess("composer dump-autoload --no-scripts");
        $this->base_command->callSilent('migrate');
    }

    protected function loadRoutes()
    {
        $stubs = $this->getFilesFromDirectory($this->getExtensionStubsPath('routes/back'), false);

        $path = base_path('routes/web/'. $this->parseName()['{{backLowerNamespace}}'] . '/extensions');

        $this->compliedAndWriteFile(
            $stubs,
            $path,
        );
    }

    protected function loadControllers()
    {
        $stubs = $this->getFilesFromDirectory($this->getExtensionStubsPath('controllers/back'), false);

        $path = app_path('Http/Controllers/'. $this->parseName()['{{backNamespace}}'] . '/Extension');

        $this->compliedAndWriteFile(
            $stubs,
            $path,
        );
    }

    protected function loadViews()
    {
        $data_map = $this->parseName();
        // back
        $stubs = $this->getFilesFromDirectory($this->getExtensionStubsPath('views/back/' . $data_map['{{theme}}']), false);

        $path = resource_path('views/'. $data_map['{{backLowerNamespace}}'] . "/extensions/" . $this->extension_name);

        $this->compliedAndWriteFile(
            $stubs,
            $path,
        );

        // front
        $stubs = $this->getFilesFromDirectory($this->getExtensionStubsPath('views/front'), false);

        $path = resource_path('views/'. $data_map['{{frontLowerNamespace}}'] . "/extensions/" . $this->extension_name);

        $this->compliedAndWriteFile(
            $stubs,
            $path,
        );

        // register back url in sidebar
        $stub  =  $this->getExtensionStubsPath('views/partials/back/'. $data_map['{{theme}}'] .'/_sidebarlink.blade.stub');
        $path =  resource_path("views/{$data_map['{{backLowerNamespace}}']}/partials/_sidebar.blade.php");

        $search = '{{--  insert extensions links here  --}}';
        $replace = $this->compliedFile($stub, true, $data_map);
        $this->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            $replace . PHP_EOL . $search,
            $path
        );
        
        // register view in front
        $search = '{{-- add livenews extension here --}}';
        $path =  resource_path("views/{$data_map['{{frontLowerNamespace}}']}/layouts/default.blade.php");
        $replace = "@include('{$data_map['{{frontLowerNamespace}}']}.extensions.livenews.index')";
        $this->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            $replace,
            $path
        );
    }
}
