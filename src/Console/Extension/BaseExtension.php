<?php

namespace Guysolamour\Administrable\Console\Extension;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Guysolamour\Administrable\Console\Filesystem;
use Guysolamour\Administrable\Console\BaseCommand;
use Guysolamour\Administrable\Console\CommandTrait;



abstract class BaseExtension
{
    use CommandTrait;
    use ExtensionTrait;

    /** @var string */
    protected $name;

    /** @var Filesystem */
    protected $filesystem;

    /** @var BaseCommand */
    protected $extension;

    /** @var array */
    protected $data_map;

    /** @var string */
    protected const SUBFOLDER = 'extensions';


    public function __construct(BaseCommand $extension, string $name)
    {

        $this->name       = $name;
        $this->extension  = $extension;

        $this->data_map   = method_exists($this, 'getParsedName') ?
                            call_user_func([$this, 'getParsedName']) :
                            $this->extension->getParsedName();

        $this->filesystem = new Filesystem($this->data_map);

    }

    /**  @return mixed  */
    abstract public function run();


    protected function checkifExtensionHasBeenInstalled(): bool
    {
        return $this->filesystem->exists(app_path($this->data_map['{{modelsFolder}}'] . "/{$this->getSubfolder()}" . Str::ucfirst($this->name)  . '/' . Str::ucfirst($this->name) .".php"));
    }


    protected function getUcfirstName() :string
    {
        return Str::ucfirst($this->name);
    }

    protected function getUcfirstSubfolder() :string
    {
        return Str::ucfirst(self::SUBFOLDER);
    }

    protected function getSubfolder() :string
    {
        return self::SUBFOLDER;
    }

    protected function triggerError(string $message)
    {
        $this->extension->error($message);
        exit();
    }

    protected function getExtensionStubsPath(string $path = '') :string
    {
        return $this->getExtensionsStubsBasePath('stubs' . DIRECTORY_SEPARATOR . $path);
    }

    /**
     * @param string $path
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    protected function getExtensionStubs(string $path = '')
    {
        $path = $this->getExtensionStubsPath($path);

        if (!$this->filesystem->exists($path)){
            return [];
        }

        return $this->filesystem->allFiles($path);
    }

    protected function getExtensionsStubsBasePath(string $path = '')
    {
        return $this->extension->getTemplatePath('extensions' . '/' . $path);
    }


    public function getParsedName(?string $name = null): array
    {
        return array_merge($this->parseName($name), [
            '{{modelsFolder}}'          => config('administrable.models_folder', 'Models'),
            '{{extensionName}}'         => Arr::get($this->getExtension(), 'name', $this->name),
            '{{extensionLabel}}'        => Str::ucfirst(Arr::get($this->getExtension(), 'label', $this->name)),
            '{{extensionsFolder}}'      => $this->getSubfolder(),
            '{{extensionsFolderClass}}' => Str::ucfirst($this->getSubfolder()),
            '{{extensionClass}}'        => Str::ucfirst($this->name),
            '{{extensionPluralClass}}'  => Str::ucfirst($this->getExtensionPlural($this->name)),
            '{{extensionPluralSlug}}'   => $this->getExtensionPlural($this->name),
            '{{extensionSingularSlug}}' => Str::lower($this->name),
            '{{extensionTableName}}'    => $this->getSubfolder()  . '_' . $this->getExtensionPlural($this->name),
        ]);
    }

    protected function getExtensionPlural(?string $name = null) :string
    {
        $name ??= $this->name;

        return Str::plural($name);
    }

    protected function runProcess(string $command)
    {
        $process = new Process(explode(' ', $command), null, null, null, 3600);

        $process->run(function ($type, $buffer) {
            // $this->getOutput()->write('> '.$buffer);
        });

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
    }

    protected function getExtension(?string $name = null) :array
    {
        $name ??= $this->name;

        return Arr::get($this->extension->getExtensions(), $name, []);
    }

    protected function runMigrateArtisanCommand(): void
    {
        $this->runProcess("composer dump-autoload --no-scripts");
        $this->extension->call('migrate');
    }

    /**
     * Parse guard name
     * Get the guard name in different cases
     * @return array
     */
    protected function parseName(?string $name = null): array
    {
        if (!$name)
            $name = config('administrable.guard', 'admin');

        return [
            '{{namespace}}'           =>  $this->extension->getAppNamespace(),
            '{{pluralCamel}}'         =>  Str::plural(Str::camel($name)),
            '{{pluralSlug}}'          =>  Str::plural(Str::slug($name)),
            '{{pluralSnake}}'         =>  Str::plural(Str::snake($name)),
            '{{pluralClass}}'         =>  Str::plural(Str::studly($name)),
            '{{singularCamel}}'       =>  Str::singular(Str::camel($name)),
            '{{singularSlug}}'        =>  Str::singular(Str::slug($name)),
            '{{singularSnake}}'       =>  Str::singular(Str::snake($name)),
            '{{singularClass}}'       =>  Str::singular(Str::studly($name)),
            '{{theme}}'               =>  Str::lower(config('administrable.theme')),
            '{{frontNamespace}}'      =>  Str::ucfirst(config('administrable.front_namespace')),
            '{{frontLowerNamespace}}' =>  Str::lower(config('administrable.front_namespace')),
            '{{backNamespace}}'       =>  Str::ucfirst(config('administrable.back_namespace')),
            '{{backLowerNamespace}}'  =>  Str::lower(config('administrable.back_namespace')),
            '{{administrableLogo}}'   =>  config('administrable.logo_url'),
        ];
    }

}
