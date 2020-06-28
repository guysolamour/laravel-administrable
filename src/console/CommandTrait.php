<?php

namespace Guysolamour\Administrable\Console;

use RuntimeException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;

trait CommandTrait
{


    protected $models_config = null;

    /**
     * @var string
     */
    protected  $TPL_PATH = __DIR__ . '/../stubs/crud';

    /**
     * @var string
     */
    protected $IMAGEMANAGER = 'imagemanager';

    /**
     * @var string[]
     */
    protected $ACTIONS = ['index', 'show', 'create', 'edit', 'delete'];

    /**
     * @var string[]
     */
    protected $ONDELETERULES = ['cascade' => 'cascade', 'setnull' => 'set null'];


    /**
     * @var string[]
     */
    protected $TYPES = [
        'string', 'text', 'mediumText', 'longText',
        'date', 'datetime',
        'boolean', 'enum',
        'decimal', 'float', 'double',
        'integer', 'mediumInterger', 'bigInteger',
        'ipAdress', 'image', 'relation',
        'polymorphic'
    ];


    /**
     * @var array
     */
    protected $RELATION_NAMES = [
        'simple'      => ['o2o' => 'one to one', 'o2m' => 'one to many', 'm2m' => 'many to many',],
        'polymorphic' => ['o2o' => 'one to one', 'o2m' => 'one to many'],
    ];

    /**
     * @var array
     */
    protected $RELATION_TYPES = [
        'simple'      => 'simple',
        'polymorphic' => 'polymorphic'
    ];

    /**
     * @var array
     */
    protected $RELATION_CONSTRAINTS = [
        'cascade' => 'cascade',
        'setnull' => 'set null'
    ];


    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    protected function getNamespace()
    {
        $namespace = Container::getInstance()->getNamespace();
        return rtrim($namespace, '\\');
    }


    protected function getCrudConfiguration(string $key, $default = null)
    {
        if (!$this->models_config) {
            $models_config_file_path = base_path('administrable.yaml');
            $this->models_config = Yaml::parseFile($models_config_file_path);
        }

        return  Arr::get($this->models_config, $key, $default);
    }

    /**
     *
     * @param string $file // Must be the path or the file content
     * @param boolean $get_content
     * @return string
     */
    protected function compliedFile(string $file, bool $get_content = true, ?array $data_map = null): string
    {
        $file = $get_content ? $this->filesystem->get($file) : $file;
        $data_map = $data_map ?: $this->parseName();

        return strtr($file, $data_map);
    }


    /**
     * @param string|array $files
     * @param string $path
     * @return void
     */
    protected function compliedAndWriteFile($files, string $path): void
    {

        if (is_array($files)) {
            foreach ($files as $file) {
                $this->compliedAndWriteFile($file, $path);
            }
            return;
        }

        $data_map = $this->parseName();

        $stub = $this->isSingleFile($files) ? $files : $this->filesystem->get($files->getRealPath());

        $this->createDirectoryIfNotExists(
            $path,
            !$this->isSingleFile($files)
        );
        $complied = strtr($stub, $data_map);

        $this->writeFile(
            $complied,
            $this->isSingleFile($files) ? $path : $path . '/' . $files->getFilenameWithoutExtension() . '.php'
        );
    }


    protected function isSingleFile($file): bool
    {
        return is_string($file);
    }

    /**
     * @param mixte $complied
     * @param string $path
     * @return void
     */
    protected function writeFile($complied, string $path, bool $overwrite = true): bool
    {

        if ($overwrite) {
            $this->filesystem->put(
                $path,
                $complied
            );
            return true;
        }

        if (!$this->filesystem->exists($path)) {
            $this->filesystem->put(
                $path,
                $complied
            );
            return true;
        }

        return false;
    }


    /**
     * Permet de créer un dossier
     * @param string $path
     * @param boolean $folder Permet de savoir si le chemin passé est un dossier ou fichier
     * @return void
     */
    protected function createDirectoryIfNotExists(string $path, bool $folder = true): void
    {

        $dir = $folder ? $path : $this->filesystem->dirname($path);

        if ($this->filesystem->missing($dir)) {
            $this->filesystem->makeDirectory($dir, 0755, true);
        }
    }



    protected function isImageableModel(array $fields)
    {
        return array_key_exists($this->IMAGEMANAGER, $fields);
    }

    protected function isImageableField(string $field): bool
    {
        return $field === $this->IMAGEMANAGER;
    }



    /**
     * @param string|array $files
     * @param string $search
     * @param string $path
     * @return void
     */
    protected function replaceAndWriteFile($files, string $search, $replace, string $path)
    {
        if (is_array($files)) {
            foreach ($files as $file) {
                $this->replaceAndWriteFile($file, $search, $replace, $path);
            }
            return;
        }

        $stub = $this->isSingleFile($files) ? $files : $this->filesystem->get($files->getRealPath());
        // $stub = $this->filesystem->get($files->getRealPath());
        $this->createDirectoryIfNotExists(
            $path,
            !$this->isSingleFile($files)
        );
        $complied = str_replace($search, $replace,  $stub);

        $this->writeFile(
            $complied,
            $this->isSingleFile($files) ? $path : $path . '/' . $files->getFilenameWithoutExtension() . '.php'
        );
    }

    protected function hasPolymorphicField(): bool
    {
        foreach ($this->fields as $field) {
            if ($this->isPolymorphicField($field)) {
                return true;
            }
        }

        return false;
    }

    protected function getFieldName(array $field): ?string
    {
        return Str::lower(Arr::get($field, 'name'));
    }

    protected function getFieldRules(array $field): ?string
    {
        return Arr::get($field, 'rules');
    }


    protected function getFieldType($type): string
    {
        $type = is_array($type) ? $type['type'] : $type;

        if ($type === 'string' || $type === 'text' || $type === 'mediumText' || $type === 'longText') {
            return 'text';
        } else if ($type === 'integer' || $type === 'mediumInteger' || $type === 'bigInteger') {
            return 'int';
        } else if ($type === 'boolean' || $type === 'enum') {
            return 'bool';
        } else if (is_array($type)) {
            return 'relation';
        }
    }

    protected function isBooleanField(array $field): bool
    {
        return 'bool' === $this->getFieldType($this->getNonRelationType($field));
    }

    protected function getFieldReferences(array $field): ?string
    {
        return Str::lower(Arr::get($field['type'], 'references'));
    }

    public function addFielsCustomProperty(array $field, string $key, $value, string $name, bool $force = false)
    {

        if ($force || !Arr::exists($field, $name)) {
            $field = Arr::prepend($field, $value, $name);
            $this->fields[$key] = $field;
        }

        return $field;
    }

    /**
     *
     * @param string|array $type
     * @return boolean
     */
    public function isTextField($type): bool
    {

        return 'text' == $this->getFieldType($type);
    }

    /**
     *
     * @param string|array $type
     * @return boolean
     */
    public function isIntegerField($type): bool
    {
        return 'int' == $this->getFieldType($type);
    }


    /**
     * @param string|array $type
     * @return bool
     */
    protected function isRelationField($type): bool
    {

        return is_array($type);
        // return is_array($type) && Arr::get($type, 'relation') ;
    }

    protected function getModelsFolder(): string
    {
        return ucfirst($this->getCrudConfiguration('folder', 'Models'));
    }


    protected function checkIfRelatedModelExists(string $model)
    {
        if (!class_exists($model)) {
            throw new \Exception("The related model [{$model}] does not exists. You need to create if first.");
        }

        return true;
    }

    protected function checkIfRelatedModelPropertyExists(array $field): bool
    {
        $table_name = $this->getRelatedModelTableName($field);

        return in_array($this->getRelatedModelProperty($field), Schema::getColumnListing($table_name));
    }

    protected function getRelatedModelTableName(array $field): string
    {
        $table_name = $this->getRelatedModel($field);

        return Str::plural(Str::slug($table_name));
    }

    protected function getRelatedModel(array $field): string
    {
        return ucfirst(Arr::get($field['type'], 'related'));
    }

    protected function getRelationName(array $field): string
    {
        return Arr::get($field['type'], 'relation');
    }

    protected function getRelationRelatedForeignKey(array $field, ?string $key = null)
    {
        $value = Arr::get($field['type'], 'related_keys');

        return is_null($key) ? $value : Arr::get($value, $key);
    }



    protected function getRelationLocalForeignKey(array $field, ?string $key = null)
    {
        $value = Arr::get($field['type'], 'local_keys');

        return is_null($key) ? $value : Arr::get($value, $key);
    }




    protected function getRelationIntermediateTable(array $field, bool $guest = false): ?string
    {
        $table_name = Arr::get($field['type'], 'intermediate_table');

        if (!$guest) {
            return $table_name;
        }

        return $this->guestIntermediataTableName($field);
    }



    protected function getRelationType(array $field): ?string
    {
        return Arr::get($field['type'], 'type');
    }

    protected function getNonRelationType(array $field)
    {
        return Arr::get($field, 'type');
    }

    protected function getRelatedModelProperty(array $field): ?string
    {
        return Arr::get($field['type'], 'property');
    }


    protected function getFieldConstraints(array $field)
    {
        return Arr::get($field, 'constraints');
    }

    protected function getFieldOnDelete(array $field): ?string
    {
        return Str::lower(Arr::get($field['type'], 'onDelete'));
    }

    protected function guestIntermediataTableName(array $field): string
    {
        $segments = [
            Str::snake($this->getRelatedModelWithoutNamespace($field)),
            Str::snake($this->model)
        ];

        sort($segments);

        return strtolower(implode('_', $segments));
    }

    protected function guestRelatedModelName(array $field): string
    {
        $related_model = $this->getRelatedModel($field);

        if ($related_model) {
            return $related_model;
        }

        $field_name = $this->getFieldName($field);

        if (Str::endsWith($field_name, '_id')) {
            $field_name = $this->getRelationModelWithoutId($field_name);
        }

        return sprintf("%s\%s\%s", $this->getNamespace(), $this->getModelsFolder(), ucfirst($field_name));
    }


    /**
     * @param string $name
     * @return string
     */
    protected function getRelationModelWithoutId(string $name): string
    {
        if (Str::endsWith($name, '_id')) {
            return Arr::first(explode('_', $name));
        }

        return $name;
    }

    protected function getRelatedModelWithoutNamespace(array $field): string
    {
        return $this->modelNameWithoutNamespace($this->getRelatedModel($field));
    }



    protected function modelNameWithoutNamespace(string $model): string
    {
        $parts = explode('\\', $model);
        return end($parts);
    }





    protected function isPolymorphicField(array $field): bool
    {
        return ($this->getRelationType($field) === 'polymorphic') ||
            (Arr::exists($field, 'polymorphic') && Arr::get($field, 'polymorphic'));
    }

    protected function getPolymorphicModelId(array $field): ?string
    {
        return Str::lower(Arr::get($field, 'model_id'));
    }

    protected function getPolymorphicModelType(array $field): ?string
    {
        return Str::lower(Arr::get($field, 'model_type'));
    }

    protected function getMorphRelationableName(array $field): ?string
    {
        $value = Str::lower(Arr::get($field['type'], 'name'));

        return $value ?: $this->getFieldName($field) . 'able';
    }


    protected function runProcess(string $command)
    {
        $process = new Process(explode(' ', $command), null, null, null, 3600);

        $process->run(function ($type, $buffer) {
            // $this->getOutput()->write('> '.$buffer);
        });

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }
    }

    protected function guestBreadcrumbFieldNane(): string
    {
        if ($this->breadcrumb) {
            return $this->breadcrumb;
        }

        foreach ($this->fields as $field) {
            if ($this->isTextField($field)) {
                return $this->getFieldName($field);
            }
        }
    }

    protected function isTheadminTheme(): bool
    {
        return $this->isTheme('theadmin');
    }

    protected function isThemeKitTheme(): bool
    {
        return $this->isTheme('themekit');
    }

    protected function isTheme(string $theme): bool
    {
        return $this->theme === $theme;
    }
}
