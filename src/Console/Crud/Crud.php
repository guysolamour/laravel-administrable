<?php

namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Guysolamour\Administrable\Console\Filesystem;
use Guysolamour\Administrable\Console\CommandTrait;
use Guysolamour\Administrable\Console\Crud\Generate\BaseGenerate;

class Crud
{
    use CommandTrait;
    use CrudTrait;

    /** @var int */
    private const DEFAULT_SEEDER_LENGTH = 15;

    /** @var string[] */
    private const ACTIONS = ['index', 'show', 'create', 'edit', 'delete'];

    /** @var string[] */
    private const IMAGEMANAGER = [
        'front'  => 'front image label',
        'back'   => 'back image label',
        'images' => 'images label',
    ];

    /**  @var string[] */
    private const GLOBALS_OPTIONS = ['folder', 'edit_slug', 'clone', 'fillable', 'guarded', 'seeder'];

    /**  @var string */
    private $model;

    /** @var string[] */
    private $actions =  [];

    /** @var string[] */
    private $generate =  ['model', 'migration', 'seed', 'form', 'controller', 'route', 'views'];

    /** @var string */
    private $table_name;

    /** @var string */
    private $subfolder = '';

    /** @var Collection|Field[] */
    private $fields;

    /** @var bool */
    private $append_mode = false;

    /** @var bool */
    private $timestamps = true;

    /** @var bool|array */
    private $fillable = true;

    /** @var bool|array */
    private $guarded = false;

    /** @var bool */
    private $polymorphic = false;

    /** @var bool */
    private $clone = false;

    /** @var bool */
    private $seeder = true;

    /** @var string */
    private $slug;

    /** @var string */
    private $breadcrumb;

    /** @var bool */
    private $edit_slug = false;

    /** @var string */
    private  $icon =  'fa-folder';

    /** @var string|null */
    private $trans = null;

    /** @var bool */
    private $migrate;

    /** @var array */
    private $imagemanager = [];

    /**  @var Filesystem  */
    public $filesystem;


    public function __construct(string $model, bool $migrate = false, bool $append_mode = false)
    {
        $this->migrate = $migrate;
        $this->append_mode = $append_mode;

        $this->setModel($model);

        $data = $this->getCrudModel($this->model);

        $this->setActions($data);

        $this->setGlobalOptions();

        $this->setTableName($data);
        $this->setSubFolder($data);
        $this->setImagemanager($data);

        $this->setTimestamps($data);
        $this->setClone($data);
        $this->setSeeder($data);
        $this->setIcon($data);
        $this->setTrans($data);

        $this->hydrateFields($data);
        $this->setFillableOrGuarded($data);
        $this->setSlug($data);
        $this->setEditSlug($data);
        $this->setBreadcrumb($data);

        $this->setGenerate($data);
        $this->setPolymorphic($data);

        $this->filesystem    = new Filesystem($this->getParsedName());
    }

    public function parseName(string $name): array
    {
        /**
         * @var Crud $this
         */
        return [
            '{{namespace}}'           =>  $this->getAppNamespace(),
            '{{pluralCamel}}'         =>  Str::plural(Str::camel($name)),
            '{{pluralSlug}}'          =>  Str::plural(Str::slug($name)),
            '{{pluralSnake}}'         =>  Str::plural(Str::snake($name)),
            '{{pluralClass}}'         =>  Str::plural(Str::studly($name)),
            '{{singularCamel}}'       =>  Str::singular(Str::camel($name)),
            '{{singularSlug}}'        =>  Str::singular(Str::slug($name)),
            '{{singularSnake}}'       =>  Str::singular(Str::snake($name)),
            '{{singularClass}}'       =>  Str::singular(Str::studly($name)),
            '{{frontNamespace}}'      =>  Str::ucfirst(config('administrable.front_namespace')),
            '{{frontLowerNamespace}}' =>  Str::lower(config('administrable.front_namespace')),
            '{{backNamespace}}'       =>  Str::ucfirst(config('administrable.back_namespace')),
            '{{backLowerNamespace}}'  =>  Str::lower(config('administrable.back_namespace')),
            '{{modelsFolder}}'        =>  Str::ucfirst($this->getModelsFolder()),
            '{{modelsNamespace}}'     =>  Str::ucfirst($this->getModelNamespace()),
            '{{subFolder}}'           =>  Str::ucfirst($this->getSubFolder()),
            '{{modelsFolderWithSubFolder}}'  =>  Str::ucfirst($this->getModelsFolderWithSubfolder()),
            '{{administrableLogo}}'          =>  config('administrable.logo_url'),
            '{{namespaceWithSubfolder}}'     => $this->getNamespaceWithSubfolder(),
            '{{theme}}'               =>  $this->getTheme(),
            '{{guard}}'               =>  config('administrable.guard', 'admin'),
        ];
    }

    public function getRoute(string $action): string
    {
        /**
         * @var Crud $this
         */
        $data_map = $this->getParsedName();

        $route = $data_map['{{backLowerNamespace}}'] . '.';

        if ($subfolder = $this->getSubFolder()) {
            $route .= $subfolder . '.';
        }

        $route .= $data_map['{{singularSlug}}'] . '.' . $action;

        return $route;
    }

    public function getNamespaceWithSubfolder(): ?string
    {
        /**
         * @var Crud $this
         */
        $namespace = $this->getSubFolder();

        if (!$namespace) {
            return "";
        }

        return  '\\' . Str::ucfirst($namespace);
    }

    


    public function getParsedName(?string $name = null): array
    {
        /**
         * @var Crud $this
         */
        $name ??= $this->getModel();

        return $this->parseName($name);
    }

    public function getModelNamespace(): string
    {
        /**
         * @var Crud $this
         */
        $subfolder = Str::ucfirst($this->getSubFolder());

        if (empty($subfolder)) {
            return Str::ucfirst($this->getModelsFolder());
        }

        return sprintf("%s\%s", Str::ucfirst($this->getModelsFolder()), $subfolder);
    }

    public function getModelsFolderWithSubfolder(): string
    {
        /**
         * @var Crud $this
         */
        $subfolder = Str::ucfirst($this->getSubFolder());

        if (empty($subfolder)) {
            return Str::ucfirst($this->getCrudGlobalConfiguration('folder', 'Models'));
        }

        return sprintf("%s/%s", Str::ucfirst($this->getCrudGlobalConfiguration('folder', 'Models')),  $subfolder);
    }

    public function getAttributeSeparator(string $attributes): ?string
    {
        if (Str::contains($attributes, ',')) {
            $separator = ',';
        } else if (Str::contains($attributes, '|')) {
            $separator = '|';
        }

        return $separator;
    }


    public function generateFieldIsAllowedFor(BaseGenerate $class_name): bool
    {
        $generator = Str::afterLast(get_class($class_name), '\\');
        $generator = Str::lower(Str::after($generator, 'Generate'));

        /**
         * @var Crud $this
         */
        return in_array($generator, $this->getGenerate());
    }


    private function setGlobalOptions() :void
    {
        $globals = $this->getCrudGlobalConfiguration();

        foreach ($globals as $key => $value) {

            if (!in_array($key, self::GLOBALS_OPTIONS)){
                $this->triggerError(
                    sprintf(
                        'The [%s] global option can not be used.',
                        $key
                    )
                );
            }

            if (!property_exists($this, $key)){
                continue;
            }

            $this->$key = $value;
        }
    }

    private function setGenerate(array $data) :void
    {
        $generate = Arr::get($data, 'generate');

        if (is_null($generate)){
            return;
        }

        if (!is_string($generate) && !is_array($generate)) {
            $this->triggerError(
                sprintf(
                    'The [%s] generate attribute must be a string or array.',
                    $this->model
                )
            );
        }

        if (is_string($generate)){
            $generate = $this->parseCrudParameter($generate, $this->getAttributeSeparator($generate));
        }

        $this->generate = $generate;
    }

    private function setTableName(array $data) :void
    {
        $table_name = Arr::get($data, 'table_name');

        if (!$table_name){
            return;
        }

        $this->table_name = $table_name;
    }

    private function validateImagemanager(string $value) :void
    {
        if (!Arr::exists(self::IMAGEMANAGER, $value)) {
            $this->triggerError(
                sprintf(
                    'The [%s] imagemanager value is not allowed. Allowed values are [%s].',
                    $value,
                    join(', ', array_keys(self::IMAGEMANAGER))
                )
            );
        }
    }

    private function setImagemanager(array $data) :void
    {
        $imagemanager = Arr::get($data, 'imagemanager');

        if (is_null($imagemanager)){
            return;
        }

        if (!is_array($imagemanager) && !is_bool($imagemanager)){
            $this->triggerError(
                sprintf("imagemanager must be an array or boolean. Current value is [%s].", $imagemanager)
            );
        }

        if (is_array($imagemanager) && Arr::isAssoc($imagemanager)){
            foreach ($imagemanager as $key => $value) {
                $this->validateImagemanager($key);
            }

            $imagemanager = array_map(fn($item) => trim($item), array_filter($imagemanager));

        }else if(is_array($imagemanager)) {
            $new_imagemanager = [];

            foreach ($imagemanager as $value) {
                $this->validateImagemanager($value);

                $new_imagemanager[$value] = self::IMAGEMANAGER[$value];
            }

            $imagemanager =  $new_imagemanager;

        } else if (is_bool($imagemanager) && $imagemanager){
            $imagemanager = self::IMAGEMANAGER;

        }else {
            $imagemanager = [];
        }

        $this->imagemanager = $imagemanager;
    }

    private function setSubFolder(array $data) :void
    {
        $subfolder = Arr::get($data, 'subfolder');

        if (!$subfolder){
            return;
        }

        $this->subfolder = $subfolder;
    }


    private function setPolymorphic(array $data) :void
    {
        $polymorphic = Arr::get($data, 'polymorphic');

        if (is_null($polymorphic)) {
            return;
        }

        if (!is_bool($polymorphic)) {
            $this->triggerError(
                sprintf(
                    'The [%s] polymorphic attribute must be a boolean (true or false).',
                    $this->model
                )
            );
        }

        if ($polymorphic){
            $this->generate = ['model', 'migration']; // only migration and model for polymorphic field
        }

        $this->polymorphic = $polymorphic;
    }

    private function setModel(string $model) :void
    {
        $this->model  = Str::ucfirst($model);
    }

    private function setActions(array $data) :void
    {
        $actions = Arr::get($data, 'actions');

        if (!$actions){
            $this->actions = self::ACTIONS;
            return;
        }

        if (!is_string($actions) && !is_array($actions)){
            $this->triggerError("The actions must be an array or string separated with [|] or [,]. Only one can be used.");
        }

        $actions = $this->parseCrudParameter($actions);

        $actions = array_map(function ($action) {
            if (!in_array($action, self::ACTIONS)) {
                $this->triggerError(
                    sprintf("[%s] action is not allowed. Allowed actions are [%s].", $action, join(',', $this->actions))
                );
            }
            return trim($action);
        }, $actions);

        $this->actions  =  $actions;
    }

    /**
     * @param string|array|bool $fillable
     * @return void
     */
    private function setFillable($fillable) :void
    {
        if (is_null($fillable)) {
            return;
        }
        if (is_string($fillable) ) {
            $fillable = $this->parseCrudParameter($fillable);
        }

        if (is_array($fillable)) {
            $fillable = $this->parseCrudParameter($fillable);
            foreach ($fillable as $field) {
                if (!$this->fields->has($field)) {
                    $this->triggerError(
                        sprintf(
                            'The fillable [%s] field is not allowed. Allowed fields are [%s].',
                            $field,
                            $this->getJoinedFields()
                        )
                    );
                }
            }
        }

        $this->fillable = $fillable;
        $this->guarded = false;
    }

    /**
     * @param string|array $guarded
     * @return void
     */
    private function setGuarded($guarded) :void
    {
        if (is_null($guarded)) {
            return;
        }
        if (is_string($guarded)) {
            $guarded = $this->parseCrudParameter($guarded);
        }

        if (is_array($guarded)) {
            $guarded = $this->parseCrudParameter($guarded);
            foreach ($guarded as $field) {
                if (!$this->fields->has($field)) {
                    $this->triggerError(
                        sprintf(
                            'The guarded [%s] field is not allowed. Allowed fields are [%s].',
                            $field,
                            $this->getJoinedFields()
                        )
                    );
                }
            }
        }

        $this->guarded  = $guarded;
        $this->fillable = false; // because a field can not be fillable and guarded
    }

    private function setFillableOrGuarded(array $data) :void
    {
        $fillable = Arr::get($data, 'fillable');
        $guarded = Arr::get($data, 'guarded');

        if ($fillable && $guarded){
            $this->triggerError(
                sprintf("The [%s] model can not have both [fillable] and [guarded] attribute.", $this->model)
            );
        }

        $this->setFillable($fillable);
        $this->setGuarded($guarded);
    }

    private function setTimestamps(array $data) :void
    {
        $timestamps = Arr::get($data, 'timestamps');

        if (is_null($timestamps)) {
            return;
        }

        if (!is_bool($timestamps)) {
            $this->triggerError(
                sprintf(
                    'The [%s] timestamps attribute must be a boolean (true or false).',
                    $this->model
                )
            );
        }

        $this->timestamps = $timestamps;
    }

    private function setEditSlug(array $data) :void
    {
        $edit_slug = Arr::get($data, 'edit_slug');

        if (is_null($edit_slug)){
            return;
        }

        if (!is_bool($edit_slug)) {
            $this->triggerError(
                sprintf(
                    'The [%s] edit slug attribute must be a boolean (true or false).',
                    $this->model
                )
            );
        }

        // Il faut avoir un champ slug avant d'utiliser le edit_slug
        if ($edit_slug && !$this->checkIfAfieldIsSluggable()) {
            $this->triggerError(
                sprintf(
                    'You have to use edit_slug when the model is sluggable'
                )
            );
        }

        $this->edit_slug = $edit_slug;
    }

    private function setClone(array $data) :void
    {
        $clone = Arr::get($data, 'clone');

        if (is_null($clone)){
            return;
        }

        if (!is_bool($clone)) {
            $this->triggerError(
                sprintf(
                    'The [%s] clone attribute must be a boolean (true or false).',
                    $this->model
                )
            );
        }

        $this->clone = $clone;
    }

    private function setSeeder(array $data) :void
    {
        $seeder = Arr::get($data, 'seeder');

        if (is_null($seeder)){
            return;
        }

        if (!is_bool($seeder) && !is_int($seeder)) {
            $this->triggerError(
                sprintf(
                    'The [%s] seeder attribute must be a boolean (true or false) or integer.',
                    $this->model
                )
            );
        }

        $this->seeder = $seeder;
    }

    private function setIcon(array $data) :void
    {
        $icon = Arr::get($data, 'icon');

        if (!$icon){
            return;
        }

        $this->icon = $icon;
    }

    private function setTrans(array $data) :void
    {
        $trans = Arr::get($data, 'trans');

        if (!$trans){
            return;
        }

        $this->trans = $trans;
    }

    private function setBreadcrumb(array $data) :void
    {
        $breadcrumb = Arr::get($data, 'breadcrumb');

        if ($breadcrumb && !$this->fields->has($breadcrumb)){
            $this->triggerError(
                sprintf("The field [%s] used for the breadcrumb is not present in [%s] model's fields.", $breadcrumb)
            );
        }

        if ($breadcrumb && $field = $this->checkIfAfieldIsBreadcrumb()){
            $this->triggerError(
                sprintf('The breadcrumb must be used on only one field and once. Ocurred on field [%s]. You have to remove the global breadcrumb with the [%s] value', $field->getName(), $breadcrumb)
            );
        }

        if ($field = $this->checkIfAfieldIsBreadcrumb()){
            $this->breadcrumb = $field->getName();
        }

        if ($breadcrumb && $this->fields->has($breadcrumb)){
            $this->breadcrumb = $breadcrumb;
        }
    }

    private function setSlug(array $data) :void
    {
        // dd($this->checkIfAfieldIsSluggable());
        $slug = Arr::get($data, 'slug');

        if ($slug && !$this->fields->has($slug)){
            $this->triggerError(
                sprintf('The field [%s] used for the breadcrumb is not present in [%s] model\'s fields.', $slug)
            );
        }

        if ($slug && $field = $this->checkIfAfieldIsSluggable()){
            $this->triggerError(
                sprintf('The slug must be used on only one field and once. Ocurred on field [%s]. You have to remove the global slug with the [%s] value', $field->getName(), $slug)
            );
        }

        if ($field = $this->checkIfAfieldIsSluggable()){
            $this->slug = $field->getName();
        }

        if ($slug && $this->fields->has($slug)){
            $this->slug = $slug;
        }
    }

    private function checkIfAfieldIsSluggable() :?Field
    {
        $fields = $this->fields->filter(fn (Field $item) => $item->getSlug());

        if ($fields->count() > 1) {
            $this->triggerError(
                sprintf("Only one field can be sluggable. [%s].", $fields->map(fn(Field $field) => $field->getName())->join(', '))
            );
        }

        return $fields->first();
    }

    private function checkIfAfieldIsBreadcrumb() :?Field
    {
        return $this->fields->filter(fn(Field $item) => $item->getBreadcrumb())->first();
    }

    private  function hydrateFields(array $data) :void
    {
        $fields = Arr::get($data, 'fields', []);

        if (empty($fields)){
            $this->triggerError("You have to defined some fields for the [{$this->model}] model.");
        }

        $hydrate_fields = collect();

        foreach ($fields as $field) {
            $hydrate_field = new Field($field, $this);

            $hydrate_fields->put($hydrate_field->getName(), $hydrate_field);
        }

        if ($hydrate_fields->isEmpty()) {
            $this->triggerError(
                sprintf("No fields to append. Please define some fields in [%s]", $this->getConfigurationYamlPath())
            );
        }

        $this->fields = $hydrate_fields;
    }

    public function getJoinedFields(string $separator = ', ') :string
    {
        return $this->fields->map(fn(Field $item) => $item->getName())->join($separator);
    }

    public function getModel() :string
    {
        return $this->model;
    }

    public function getModelWithNamespace() :string
    {
        return sprintf('%s\%s\%s', $this->getAppNamespace(), $this->getModelNamespace(), $this->model);
    }

    public function getModelInstance() :?\Illuminate\Database\Eloquent\Model
    {
        $model = $this->getModelWithNamespace();

        return  new $model;
    }

    public function getModelTableName() :string
    {
        return $this->getModelInstance()->getTable();
    }

    public function getModelAttributes() :?array
    {
        $columns = Schema::getColumnListing($this->getTableName());

        $model = $this->getModelInstance();

        if (method_exists($model, 'getDateranges')){
            $columns = array_merge($columns, call_user_func([$model, 'getDateranges']));
        }

        if (method_exists($model, 'getDatepickers')){
            $columns = array_merge($columns, call_user_func([$model, 'getDatepickers']));
        }

        return $columns;
    }

    private function getModelSavedFolder(string $model) :string
    {
        $model = explode('\\', $model);

        return $model[array_key_last($model) - 1];
    }

    public function modelAndRelatedModelAreInTheSameFolder(string $related_model) :bool
    {
        $model_folder    = $this->getModelSavedFolder($this->getModelWithNamespace());
        $related_folder  = $this->getModelSavedFolder($related_model);

        return $model_folder === $related_folder;
    }

    public function getActions() :array
    {
        return $this->actions;
    }

    public function getUnusedActions() :array
    {
        $unused_actions = array_diff(self::ACTIONS, $this->actions);

        if (in_array('create', $unused_actions)) {
            $unused_actions[] = 'store';
        }

        if (in_array('edit', $unused_actions)) {
            $unused_actions[] = 'update';
        }

        return $unused_actions;
    }

    public function hasAction(string $key): bool
    {
        return in_array($key, $this->actions);
    }

    /**
     * @param string[]|string $actions
     * @return boolean
     */
    public function hasActions($actions): bool
    {
        $actions = is_array($actions) ? $actions : func_get_args();

        return multiple_in_array($actions, $this->actions);
    }


    public function getTableName() :string
    {
        if (!$this->checkIfTableNameIsEmpty()){
            return $this->table_name;
        }

        return $this->guestTableName();
    }

    public function checkIfTableNameIsEmpty() :bool
    {
        return empty($this->table_name);
    }

    public function checkIfThereAreDaterangeFields(): bool
    {
        return $this->fields->filter(fn (Field $field) => $field->isDaterange())->isNotEmpty();
    }

    public function checkIfThereAreDatepickerFields(): bool
    {
        return $this->fields->filter(fn (Field $field) => $field->isDatepicker())->isNotEmpty();
    }

    public function checkIfThereAreCastableFields(): bool
    {
        return $this->fields->filter(fn(Field $field) => $field->getCast() )->isNotEmpty();
    }

    private function guestTableName() :string
    {
        $prefix = "";

        if ($subfolder = $this->subfolder){
            $prefix .= $subfolder . '_';
        }

        return $prefix . Str::plural(Str::snake($this->model));
    }

    public function getTimestamps() :bool
    {
        return $this->timestamps;
    }

    public function getClone() :bool
    {
        return $this->clone;
    }

    public function getSlug() :?string
    {
        return $this->slug;
    }

    public function getEditSlug() :bool
    {
        return $this->edit_slug;
    }

    /**
     * @return  Collection|field[]
     */
    public function getFields()
    {
        // dd($this->append_mode);
        if ($this->append_mode){
            return $this->getAppendFields();
        }
        return $this->fields;
    }

    /**
     * @return  Collection|Field[]
     */
    public function getRelationFields()
    {
        return $this->fields->filter(fn(Field $field) => $field->isRelation());
    }

    /**
     * @return  Collection<Field[]>
     */
    public function getAppendFields()
    {
        return $this->fields->filter(fn(Field $field) => !$field->isAppend());
    }

    public function generateModel() :array
    {
        return $this->generate('model');
    }

    private function exec(string $class_name)
    {
        $class_name = __NAMESPACE__ . $class_name;

        /**
         * @var BaseGenerate
         */
        $generator = new $class_name($this);

        return $generator->run();
    }

    public function generate(string $type)
    {
        return $this->exec("\Generate\Generate" . Str::ucfirst($type));
    }

    public function append(string $type)
    {
        return $this->exec("\Append\Append" . Str::ucfirst($type));
    }

    public function rollback(string $type)
    {
        return $this->exec("\Rollback\Rollback" . Str::ucfirst($type));
    }

    public function getSubFolder() :string
    {
        return $this->subfolder;
    }

    public function hasSubfolder(): bool
    {
        return !empty($this->subfolder);
    }

    /** @return  bool|array */
    public function getFillable()
    {
        return $this->fillable;
    }

    /** @return  bool|array */
    public function getGuarded()
    {
        return $this->guarded;
    }

    public function isFillable() :bool
    {
        return $this->fillable && !$this->guarded;
    }

    public function isPolymorphic() :?bool
    {
        return $this->polymorphic;
    }

    public function isGuarded() :bool
    {
        return $this->guarded && !$this->fillable;
    }

    /** @return int|bool */
    public function getSeeder()
    {
        if (is_int($this->seeder)){
            return $this->seeder;
        }

        if (is_bool($this->seeder) && $this->seeder){
            return self::DEFAULT_SEEDER_LENGTH;
        }

        return $this->seeder;
    }

    public function getGenerate() :array
    {
        return $this->generate;
    }

    public function getBreadcrumb() :?string
    {
        return $this->breadcrumb;
    }

    public function guestBreadcrumbFieldName(): string
    {
        if ($this->breadcrumb) {
            return $this->breadcrumb;
        }

        foreach ($this->fields as $field) {
            if ($field->isText()) {
                return $field->getName();
            }
        }

        return '';
    }

    public function getImagemanager() :array
    {
        return $this->imagemanager;
    }

    public function hasImagemanager() :bool
    {
        return !empty($this->imagemanager);
    }

    public function hasTinymceField() :bool
    {
        foreach ($this->fields as $field) {
            if ($field->getTinymce()){
                return true;
            }
        }

        return false;
    }

    public function getTrans() :?string
    {
        return $this->trans;
    }

    public function getIcon() :string
    {
        return $this->icon;
    }
}

