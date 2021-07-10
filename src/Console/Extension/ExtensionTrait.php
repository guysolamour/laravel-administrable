<?php

namespace Guysolamour\Administrable\Console\Extension;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\YamlTrait;


trait ExtensionTrait
{
    use YamlTrait;

    public function getExtensionConfiguration(?string $key = null, $default = null)
    {
        $extensions = $this->getCrudConfiguration('extensions', []);

        if (is_null($key)) {
            return $extensions;
        }

        return Arr::get($extensions, $key, $default);
    }

    public function displayMessage(string $message): void
    {
        $this->extension->line(PHP_EOL . "<------------------------------------------------------------------------------------------------------>" .  PHP_EOL);
        $this->extension->info(PHP_EOL . $message .  PHP_EOL);
    }

    protected function loadSettings(): void
    {
        $stubs = $this->getExtensionStubs('settings');

        if (empty($stubs)) {
            return;
        }

        $path = app_path('Settings/' . $this->getFolder());


        $this->load($stubs, $path);


        // register setting
        $search = "'settings' => [";
        $path =  config_path('settings.php');
        $replace = PHP_EOL . "       \\{$this->extension->getAppNamespace()}\Settings\\{$this->getFolder(null, '\\')}\LivenewsSettings::class,";
        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            $search . $replace,
            $path
        );

        $this->displayMessage('Settings created at ' . $path);
    }

    protected function loadModels(): void
    {
        $stubs = $this->getExtensionStubs('models');

        if (empty($stubs)) {
            return;
        }

        $path = app_path($this->getFolder('modelsFolder'));

        $this->load($stubs, $path);

        $this->displayMessage('Models created at ' . $path);
    }

    protected function loadMigrations(): void
    {
        $stubs = $this->getExtensionStubs('migrations');

        if (empty($stubs)) {
            return;
        }

        $path = database_path("{$this->data_map['{{extensionsFolder}}']}/");
        $signature = now();

        foreach ($stubs as $stub) {

            $name = Str::after($stub->getFilenameWithoutExtension(), '|');
            $signature = $signature->addMinute();
            $file_name = $path . $signature->format('Y_m_d_His') . '_' . $name . ".php";

            $complied = $this->filesystem->compliedFile($stub->getRealPath());

            $this->filesystem->writeFile($file_name, $complied);
        }

        $this->displayMessage('Migrations created at ' . $path);
    }


    protected function loadForms(): void
    {
        $this->loadFrontForms();
        $this->loadBackForms();
    }

    protected function loadAssets() :void
    {
        $stubs = $this->getExtensionStubsPath('assets');

        if (empty($stubs)) {
            return;
        }

        $path = public_path("vendor/{$this->data_map['{{extensionsFolder}}']}/{$this->data_map['{{extensionPluralSlug}}']}");

        $this->filesystem->copyDirectory($stubs, $path);

        $this->displayMessage('Assets copied to ' . $path);
    }

    protected function loadFrontViews() :void
    {
        $stubs = $this->getExtensionStubs('views/front');

        if (empty($stubs)) {
            return;
        }

        $path = resource_path('views/' . $this->data_map['{{frontLowerNamespace}}'] . '/' . $this->getSubfolder() .'/' . $this->data_map['{{extensionPluralSlug}}']);

        $this->filesystem->compliedAndWriteFileRecursively(
            $stubs,
            $path,
        );

        $this->displayMessage('Front views created at ' . $path);
    }

    protected function loadBackViews() :void
    {
        $stubs = $this->getExtensionStubs('views/back/' . $this->data_map['{{theme}}']);

        if (empty($stubs)) {
            return;
        }

        $path = resource_path('views/' . $this->data_map['{{backLowerNamespace}}'] . '/' . $this->getSubfolder() .'/' . $this->data_map['{{extensionPluralSlug}}']);

        $this->filesystem->compliedAndWriteFileRecursively(
            $stubs,
            $path,
        );

        $this->displayMessage('Back views created at ' . $path);
    }

    protected function registerBackUrlInSidebarView() :void
    {
        $stub  =  $this->getExtensionStubsPath('views/back/sidebarlinks/' . $this->data_map['{{theme}}'] . '/_sidebarlink.blade.stub');
        $path =  resource_path("views/{$this->data_map['{{backLowerNamespace}}']}/partials/_sidebar.blade.php");

        $search = '{{--  insert extensions links here  --}}';
        $replace = $this->filesystem->compliedFile($stub, true, $this->data_map);
        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            $replace . PHP_EOL . $search,
            $path
        );

    }

    protected function registerFrontUrlInHeader(string $route, array $set_active_routes = [])
    {
        $path =  resource_path("views/{$this->data_map['{{frontLowerNamespace}}']}/partials/_nav.blade.php");

        if (!in_array($route, $set_active_routes)) {
            $set_active_routes[] = $route;
        }

        $set_active_routes = array_map(fn ($item) => "'{$this->data_map['{{frontLowerNamespace}}']}.{$this->getSubfolder()}.{$this->name}.{$item}'", $set_active_routes);
        $set_active_routes = join(', ', $set_active_routes);


        $search = "{{-- insert extensions links here --}}";

        $this->filesystem->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            <<<HTML
            {$search}

                    <li class="nav-item {{ set_active_link({$set_active_routes})  }}">
                        <a class="nav-link" href="{{ front_route('{$this->getSubfolder()}.{$this->name}.{$this->name}.{$route}') }}">{$this->data_map['{{extensionLabel}}']}</a>
                    </li>
            HTML,
            $path
        );

        $this->displayMessage('Register front url link in ' . $path);
    }

    protected function loadFrontMails()
    {
        $stubs = $this->getExtensionStubs('mails/front');

        if (empty($stubs)) {
            return;
        }

        $path  = app_path("Mail/{$this->getFrontFolder()}");

        $this->load($stubs, $path);

        $this->displayMessage('Front mails created at ' . $path);
    }

    protected function loadBackMails()
    {
        $stubs = $this->getExtensionStubs('mails/back');

        if (empty($stubs)) {
            return;
        }

        $path  = app_path("Mail/{$this->getBackFolder()}");

        $this->load($stubs, $path);

        $this->displayMessage('Back mails created at ' . $path);
    }


    protected function loadMails(): void
    {
        $this->loadFrontMails();
        $this->loadBackMails();
    }

    protected function loadFrontNotifications()
    {
        $stubs = $this->getExtensionStubs('notifications/front');

        if (empty($stubs)) {
            return;
        }

        $path  = app_path("Notifications/{$this->getFrontFolder()}");

        $this->load($stubs, $path);

        $this->displayMessage('Front notifications created at ' . $path);
    }

    protected function loadBackNotifications()
    {
        $stubs = $this->getExtensionStubs('notifications/back');

        if (empty($stubs)) {
            return;
        }

        $path  = app_path("Notifications/{$this->getBackFolder()}");

        $this->load($stubs, $path);

        $this->displayMessage('Back notifications created at ' . $path);
    }

    protected function loadNotifications(): void
    {
        $this->loadFrontNotifications();
        $this->loadBackNotifications();
    }

    protected function loadFrontEmails() :void
    {
        $stubs = $this->getExtensionStubs('views/emails/front');

        if (empty($stubs)) {
            return;
        }

        $path = resource_path('views/emails/' . $this->data_map['{{frontLowerNamespace}}'] . '/' . $this->getSubfolder() . '/' . $this->name);


        $this->load($stubs, $path);

        $this->displayMessage('Front emails created at ' . $path);
    }

    protected function loadBackEmails() :void
    {
        $stubs = $this->getExtensionStubs('views/emails/back');

        if (empty($stubs)) {
            return;
        }

        $path = resource_path('views/emails/' . $this->data_map['{{backLowerNamespace}}'] . '/' . $this->getSubfolder() . '/' . $this->name);


        $this->load($stubs, $path);

        $this->displayMessage('Back emails created at ' . $path);
    }

    protected function loadEmails(): void
    {
        $this->loadFrontEmails();
        $this->loadBackEmails();
    }

    protected function enableRegisteringFrontUrlInHeader(): bool
    {
        return true;
    }

    protected function getHeaderFrontUrlRoute(): string
    {
        return 'index';
    }

    protected function loadViews(): void
    {
        $this->loadFrontViews();
        $this->loadBackViews();

        $this->registerBackUrlInSidebarView();

        if ($this->enableRegisteringFrontUrlInHeader()){
            $this->registerFrontUrlInHeader($this->getHeaderFrontUrlRoute());
        }

    }

    protected function loadSeeders(): void
    {
        $stubs = $this->getExtensionStubs("seeds");

        if (empty($stubs)) {
            return;
        }

        $path = database_path("seeders/{$this->getFolder()}/");

        $this->load($stubs,$path);

        $this->registerSeedersInDatabaseSeederFile($stubs);

        $this->displayMessage('Seeders created at ' . $path);
    }

    protected function registerSeedersInDatabaseSeederFile($stubs): void
    {
        $database_seeder_path = database_path('seeders/DatabaseSeeder.php');

        foreach ($stubs as $stub) {
            $class_name = "{$this->getFolder(null, '\\')}\\{$stub->getFilenameWithoutExtension()}";

            $this->filesystem->replaceAndWriteFile(
                $this->filesystem->get($database_seeder_path),
                $search = "  {\n",
                $search . '         $this->call(' . $class_name . '::class);' . PHP_EOL,
                $database_seeder_path
            );
        }

        $this->displayMessage('Seeders registered in ' . $database_seeder_path .  ' file');
    }

    protected function loadFrontForms(): void
    {
        $stubs = $this->getExtensionStubs('forms/front');

        if (empty($stubs)){
            return;
        }

        $path  = app_path("Forms/{$this->getFrontFolder()}");

        $this->load($stubs, $path);

        $this->displayMessage('Front forms created at ' . $path);
    }

    protected function loadBackForms(): void
    {
        $stubs = $this->getExtensionStubs('forms/back');

        if (empty($stubs)) {
            return;
        }
        $path  = app_path("Forms/{$this->getBackFolder()}");

        $this->load($stubs, $path);

        $this->displayMessage('Back forms created at ' . $path);
    }

    protected function loadControllers(): void
    {
        $this->loadFrontControllers();
        $this->loadBackControllers();
    }

    protected function loadFrontControllers() :void
    {
        $stubs = $this->getExtensionStubs('controllers/front');

        if (empty($stubs)) {
            return;
        }

        $path =  app_path("/Http/Controllers/{$this->getFrontFolder()}");

        $this->load($stubs, $path);

        $this->displayMessage('Front Controllers created at ' . $path);
    }

    protected function loadBackControllers() :void
    {
        $stubs = $this->getExtensionStubs('controllers/back');

        if (empty($stubs)) {
            return;
        }

        $path =  app_path("/Http/Controllers/{$this->getBackFolder()}");

        if (
            $this->filesystem->exists($this->getExtensionStubsPath("controllers/{$this->getTheme()}")) &&
            $this->isTheadminTheme()
        ){
            $theadmin_controllers = $this->filesystem->files($this->getExtensionStubsPath("controllers/{$this->getTheme()}"));
            $stubs = array_merge($stubs, $theadmin_controllers);
        }

        $this->load($stubs, $path);

        $this->displayMessage('Back Controllers created at ' . $path);
    }

    protected function loadRoutes(): void
    {
        $this->loadFrontRoutes();
        $this->loadBackRoutes();
    }

    protected function loadFrontRoutes() :void
    {
        $prefix = $this->getRoutesStubsFolderPrefix();
        $stubs = $this->getExtensionStubs("/routes/web/front/${prefix}");

        if (empty($stubs)) {
            return;
        }
        $path   = base_path("routes/web/" . Str::lower($this->getFrontFolder()));

        $this->load($stubs, $path);

        $this->displayMessage('Front routes created at ' . $path);
    }

    protected function loadBackRoutes() :void
    {
        $prefix = $this->getRoutesStubsFolderPrefix();
        $stubs = $this->getExtensionStubs("/routes/web/back/${prefix}");

        if (empty($stubs)) {
            return;
        }

        $path   = base_path("routes/web/" . Str::lower($this->getBackFolder()));

        $this->load($stubs, $path);

        $this->displayMessage('Back routes created at ' . $path);
    }

    protected function load($stubs, string $path)
    {
        $this->filesystem->compliedAndWriteFile(
            $stubs,
            $path,
        );
    }

    protected function getFolder(?string $folder = null, string $separator = '/') :string
    {
        $path = "{$this->getUcfirstSubfolder()}{$separator}{$this->getUcfirstName()}";

        if ($folder){
            $path = $this->data_map['{{' . $folder . '}}'] . $separator .$path;
        }

        return $path;
    }

    protected function getFrontFolder(string $name = 'front') :string
    {
        return $this->getFolder($name . 'Namespace');
    }

    protected function getBackFolder(string $name = 'back') :string
    {
        return $this->getFolder($name . 'Namespace');
    }
}
