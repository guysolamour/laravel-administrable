<?php

namespace Guysolamour\Administrable\Console;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\Crud\MakeCrudTrait;

class RollbackCrudCommand extends BaseCommand
{
    use MakeCrudTrait;

    /**
     * @var string
     */
    protected $model = '';

    /**
     * @var string
     */
    protected $theme = '';

    /**
     * @var string
     */
    protected $guard = '';

    /**
     * @var bool
     */
    protected $rollback;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'administrable:rollback:crud
                             {model : Model name }
                             {--r|rollback=true : Run artisan rollback command }
                             ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback crud ';



    public function handle()
    {
        $this->info('Rollback...');

        $this->model    = $this->argument('model');
        $this->theme    = config('administrable.theme', 'adminlte');
        $this->guard    = $this->getGuard();
        $this->rollback = $this->option('rollback') == 'true' ? true : false;


        $model = ucfirst($this->model);
        $this->fields = $this->getCrudConfiguration($model);


        if (!$this->fields){
            throw new \Exception(
                "The [$model] model does not exists in the administrable.yaml file"
            );
        }

        $this->data_map = $this->parseName($this->model);


        // remove model
        $this->removeModel();


        // remove form
        $this->removeForm();

        // remove controller
        $this->removeController();

        // remove route
        $this->removeRoute();

        // remove migration
        $this->removeMigration();

        // remove seed
        $this->removeSeed();

        // remove views
        $this->removeViews();

        $this->runProcess("composer dumpautoload");
    }

    protected function removeModel()
    {
        $this->info(PHP_EOL . 'Removing model...');

        $path = app_path(sprintf("%s/%s.php", $this->data_map['{{modelsFolder}}'], $this->data_map['{{singularClass}}']));

        foreach ($this->fields as $field) {
            if ($this->isRelationField(Arr::get($field, 'type'))){

                if ($this->isSimpleRelation($field)){
                    $data_map = array_merge(
                        $this->parseName($this->model),
                        $this->parseRelationName($this->model, $this->getRelatedModel($field)),
                        ['{{morphFieldName}}' => $this->getFieldName($field)],
                        ['{{morphRelationableName}}' => $this->getMorphRelationableName($field)],
                    );

                    $related_model_path = $this->getRelationRelatedModelPath($field, $data_map);

                    $start_key = '// ' . $data_map['{{modelSingularSlug}}'] . ' relation';
                    $end_key = '// end ' .  $data_map['{{modelSingularSlug}}'] . ' relation';

                    $complied = delete_all_between($start_key, $end_key, $this->filesystem->get($related_model_path));

                    $this->writeFile($complied, $related_model_path);
                    // dd($complied, 'salut');
                }
            }
        }

        $this->filesystem->delete($path);

        $this->info(PHP_EOL . 'Model file removed at ' . $path);

    }

    protected function removeForm()
    {
        $this->info(PHP_EOL . 'Removing form...');

        $path = app_path("Forms/" . $this->data_map['{{backNamespace}}'] . "/{$this->data_map['{{singularClass}}']}Form.php");
        $this->filesystem->delete($path);

        $this->info(PHP_EOL . 'Form file removed at ' . $path);
    }

    protected function removeController()
    {
        $this->info(PHP_EOL . 'Removing controller...');

        $path =  app_path('Http/Controllers/' . $this->data_map['{{backNamespace}}'] . "/{$this->data_map['{{singularClass}}']}Controller.php");;
        $this->filesystem->delete($path);

        $this->info(PHP_EOL . 'Controller file removed at ' . $path);
    }

    protected function removeRoute()
    {
        $this->info(PHP_EOL . 'Removing route...');

        $path = base_path('/routes/web/' . $this->data_map['{{backLowerNamespace}}'] . "/{$this->data_map['{{singularSlug}}']}.php");
        $this->filesystem->delete($path);

        $this->info(PHP_EOL . 'Route file removed at ' . $path);
    }

    protected function removeMigration()
    {
        $this->info(PHP_EOL . 'Removing migration...');


        $migrations_path =  database_path('migrations');
        $migration = '_create_' . $this->data_map['{{pluralSnake}}'] . '_table.php';
        $path = Arr::first($this->filesystem->glob($migrations_path . "/*{$migration}"));

        if ($this->rollback) {
            $this->call('migrate:rollback');
        }

        $this->filesystem->delete($path);

        foreach ($this->fields as $field) {
            if (is_array($field) && $this->isSimpleManyToManyRelation($field)) {
                $migration_name = Str::snake($this->getIntermediateClassName($field));
                $path = Arr::first($this->filesystem->glob($migrations_path . "/*{$migration_name}"));
                $this->filesystem->delete($path);
            }
        }


        $this->info(PHP_EOL . 'Migration file removed at ' . $path);
    }

    protected function removeSeed()
    {
        $this->info(PHP_EOL . 'Removing seed...');

        $path = database_path("/seeds/" . $this->data_map['{{pluralClass}}'] . 'TableSeeder.php');
        $this->filesystem->delete($path);

        // remove entry in database seeder file
        $database_seeder_path = database_path('seeds/DatabaseSeeder.php');
        $database_seeder = $this->filesystem->get($database_seeder_path);
        $search = ' $this->call(' .  $this->data_map['{{pluralClass}}'] . 'TableSeeder::class' . ");";
        $database_seeder = str_replace($search, "", $database_seeder);
        $this->filesystem->put($database_seeder_path, $database_seeder);

        $this->info(PHP_EOL . 'Seed file removed at ' . $path);
    }

    protected function removeViews()
    {
        $this->info(PHP_EOL . 'Removing views...');

        $path = resource_path("views/{$this->data_map['{{backLowerNamespace}}']}" . '/' . $this->data_map['{{pluralSlug}}']);
        $this->filesystem->deleteDirectory($path);
        $this->info(PHP_EOL . 'Views file removed at ' . $path);

        // remove entry in sidebar
        $sidebar_path =  resource_path("views/{$this->data_map['{{backLowerNamespace}}']}/partials/_sidebar.blade.php");


        $start_key = "<!-- {$this->data_map['{{singularSlug}}']} link -->";
        $end_key = "<!-- end {$this->data_map['{{singularSlug}}']} link -->";

        $sidebar = delete_all_between($start_key, $end_key, $this->filesystem->get($sidebar_path));
        $this->writeFile($sidebar, $sidebar_path);


        // remove route in header
        $header_path =  resource_path("views/{$this->data_map['{{backLowerNamespace}}']}/partials/_header.blade.php");
        $header = delete_all_between($start_key, $end_key, $this->filesystem->get($header_path));
        $this->writeFile($header, $header_path);

    }

    protected function parseName(?string $name = null): array
    {
        if (!$name)
            $name = $this->model;

        return [
            '{{namespace}}'            =>  $this->getNamespace(),
            '{{pluralCamel}}'          =>  Str::plural(Str::camel($name)),
            '{{pluralSlug}}'           =>  Str::plural(Str::slug($name)),
            '{{pluralSnake}}'          =>  Str::plural(Str::snake($name)),
            '{{pluralClass}}'          =>  Str::plural(Str::studly($name)),
            '{{singularCamel}}'        =>  Str::singular(Str::camel($name)),
            '{{singularSlug}}'         =>  Str::singular(Str::slug($name)),
            '{{singularSnake}}'        =>  Str::singular(Str::snake($name)),
            '{{singularClass}}'        =>  Str::singular(Str::studly($name)),
            '{{frontNamespace}}'       =>  ucfirst(config('administrable.front_namespace')),
            '{{frontLowerNamespace}}'  =>  Str::lower(config('administrable.front_namespace')),
            '{{backNamespace}}'        =>  ucfirst(config('administrable.back_namespace')),
            '{{backLowerNamespace}}'   =>  Str::lower(config('administrable.back_namespace')),
            '{{modelsFolder}}'         =>  $this->getCrudConfiguration('folder', 'Models'),
            '{{theme}}'                =>  $this->theme,
            '{{guard}}'                =>  $this->guard,
        ];
    }

}
