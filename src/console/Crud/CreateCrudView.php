<?php

namespace Guysolamour\Administrable\Console\Crud;




use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Filesystem\Filesystem;

class CreateCrudView
{

    use MakeCrudTrait;
    /**
     * @var string
     */
    private $model;
    /**
     * @var array
     */
    private $fields;
    /**
     * @var array
     */
    private $actions;


    private $timestamps;
    /**
     * @var null|string
     */
    private $slug;

    /**
     *
     * @var string
     */
    private $breadcrumb;

    /**
     *
     * @var string
     */
    private $imagemanager;

    /**
     *
     * @var string
     */
    private $theme;

    /**
     *
     * @var string
     */
    private $icon;

    /**
     * CreateCrudView constructor.
     * @param string $model
     * @param array $fields
     * @param string $breadcrumb
     * @param null|string $slug
     * @param bool $timestamps
     */
    public function __construct(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug, bool $timestamps,  $imagemanager, string $icon)
    {
        $this->model           = $model;
        $this->fields          = $fields;
        $this->actions         = $actions;
        $this->timestamps      = $timestamps;
        $this->slug            = $slug;
        $this->breadcrumb      = $breadcrumb;
        $this->imagemanager    = $imagemanager;
        $this->theme           = $theme;
        $this->icon            = $icon;

        $this->filesystem      = new Filesystem;
    }

    /**
     * @param string $model
     * @param array $fields
     * @param string $breadcrumb
     * @param string $theme
     * @param null|string $slug
     * @param bool $timestamps
     * @return string
     */
    public static function generate(string $model, array $fields, array $actions, ?string $breadcrumb, string $theme, ?string $slug, bool $timestamps,  $imagemanager, string $icon)
    {
        return (new CreateCrudView(
            $model,
            $fields,
            $actions,
            $breadcrumb,
            $theme,
            $slug,
            $timestamps,
            $imagemanager,
            $icon
        ))->loadViews();
    }


    /**
     * Parse guard name
     * Get the guard name in different cases
     * @param string|null $name
     * @return array
     */
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
            '{{administrableLogo}}'    =>  asset(config('administrable.logo_url')),
            '{{theme}}'                =>  $this->theme,
            '{{icon}}'                =>  $this->icon,
            '{{breadcrumb}}'           =>  $this->breadcrumb,
            '{{guard}}'                =>  config('administrable.guard', 'admin')
        ];
    }

    /**
     * @return string
     */
    public function loadViews(): string
    {
        $data_map = $this->parseName();

        $guard = $data_map['{{pluralSlug}}'];

        $views_path = resource_path("views/{$data_map['{{backLowerNamespace}}']}");

        if ($this->hasAction('index')) {
            $this->loadIndexView($guard, $views_path, $data_map);

            // register sidebar link
            $this->registerLinkToLeftSidebar($views_path, $data_map);
        }

        if ($this->hasAction('show')) {
            $this->loadShowView($guard, $views_path, $data_map);
        }

        if ($this->hasAction('create')) {
            $this->loadCreateView($guard, $views_path, $data_map);
        }


        if ($this->hasAction('edit')) {
            $this->loadEditView($guard, $views_path, $data_map);
        }

        if ($this->hasAction('create') || $this->hasAction('edit')) {
            $this->loadFormPartial($guard, $views_path, $data_map);
        }

        return $views_path;
    }



    protected function loadBreadcrumbFor(string $key, string $complied, array $data_map): string
    {
        if ($this->breadcrumb) {
            $breadcrumb_stub = $this->TPL_PATH . '/views/' . $this->theme . "/breadcrumbs/{$key}.blade.stub";
            $replace = $this->compliedFile($breadcrumb_stub, true, $data_map);
            return  str_replace('{{-- breadcrumb --}}', $replace, $complied);
        }

        return $complied;
    }

    private function loadIndexView($guard, $views_path, $data_map)
    {
        $stub  =  $this->TPL_PATH . '/views/' . $this->theme . '/index.blade.stub';
        $path =  $views_path . '/' . $guard . '/index.blade.php';

        $complied =  $this->compliedFile($stub);

        $complied =  $this->loadBreadcrumbFor('index', $complied, $data_map);

        $complied =  $this->loadBreadcrumbFor('create', $complied, $data_map);

        $complied =  $this->loadLinkButtonFor('create', $complied, $data_map);


        $this->createDirectoryIfNotExists($path, false);

        $var_name = $data_map['{{singularSlug}}'];
        [$values, $fields] = $this->showIndexFields($var_name, $views_path, $data_map);


        $view = $this->insertFieldToViewIndex($fields, $complied, $values, $data_map);


        $this->writeFile($view, $path, false);
    }

    protected function loadLinkButtonFor(string $key, string $complied, array $data_map): string
    {
        if ($this->hasCrudAction($key)) {
            $stub  =  $this->TPL_PATH . '/views/' . $this->theme . "/partials/links/_{$key}link.blade.stub";
            $replace = $this->compliedFile($stub, true, $data_map);

            $complied = str_replace("{{-- {$key} link --}}", $replace, $complied);

            if ($this->isTheAdminTheme() && ('edit' === $key || 'delete' === $key)) {
                $stub  =  $this->TPL_PATH . '/views/' . $this->theme . "/partials/links/_show{$key}link.blade.stub";
                $replace = $this->compliedFile($stub, true, $data_map);

                $complied = str_replace("{{-- show{$key} link --}}", $replace, $complied);
            }
        }


        return $complied;
    }
    private function loadShowView($guard, $views_path, $data_map)
    {
        $stub  =  $this->TPL_PATH . '/views/' . $this->theme . '/show.blade.stub';
        $path =  $views_path . '/' . $guard . '/show.blade.php';

        $complied =  $this->compliedFile($stub);



        $complied =  $this->loadBreadcrumbFor('show', $complied, $data_map);
        $complied =  $this->loadLinkButtonFor('edit', $complied, $data_map);
        $complied =  $this->loadLinkButtonFor('delete', $complied, $data_map);


        $this->createDirectoryIfNotExists($path, false);

        $var_name = $data_map['{{singularSlug}}'];
        $show_views = $this->showViewFields($var_name, $data_map);

        $complied = $this->loadImagemanagerViewsAndAssets($data_map, $complied, 'show');


        $view = $this->insertFieldToViewSHow($show_views, $complied);


        $this->writeFile($view, $path, false);
    }


    private function loadFormPartial($guard, $views_path, $data_map)
    {
        $stub  =  $this->TPL_PATH . '/views/' . $this->theme . '/partials/_form.blade.stub';
        $path =  $views_path . '/' . $guard . '/_form.blade.php';

        $complied =  $this->compliedFile($stub);

        $this->createDirectoryIfNotExists($path, false);

        // tinymce a field
        if ($this->modelHasTinymceField()) {
            $tinymce = <<<TEXT
            @include('{$data_map['{{backLowerNamespace}}']}.partials._tinymce', [
                'selector'   => "form[name={\$form->getModel()->form_name}] textarea[data-tinymce]",
                'model'      => \$form->getModel(),
                'model_name' => get_class(\$form->getModel()),
                'prefix'     => config('administrable.auth_prefix_path')
            ])
            TEXT;

            $complied =  str_replace('{{-- add tinymce here --}}', $tinymce, $complied);
        } else {
            $complied =  str_replace('{{-- add tinymce here --}}', '', $complied);
        }

        if ($this->hasAction('edit')) {
            $edit_button = <<<'TEXT'
                @if (isset($edit) && $edit)
                <div class="form-group">
                    <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i> Enregistrer</button>
                </div>
                @endif
            TEXT;
            $complied =  str_replace('{{-- form edit button --}}', $edit_button, $complied);
        } else {
            $complied =  str_replace('{{-- form edit button --}}', '', $complied);
        }

        if ($this->hasAction('create')) {
            $create_button = <<<'TEXT'
                @if (!isset($edit))
                <div class="form-group">
                    <button type="submit" class="btn btn-success"> <i class="fa fa-edit"></i> Modifier</button>
                </div>
                @endif
            TEXT;
            $complied =  str_replace('{{-- form create button --}}', $create_button, $complied);
        } else {
            $complied =  str_replace('{{-- form create button --}}', '', $complied);
        }

        $this->writeFile($complied, $path, false);
    }
    private function loadCreateView($guard, $views_path, $data_map)
    {


        $stub  =  $this->TPL_PATH . '/views/' . $this->theme . '/create.blade.stub';
        $path =  $views_path . '/' . $guard . '/create.blade.php';

        $complied =  $this->compliedFile($stub);

        $complied =  $this->loadBreadcrumbFor('create', $complied, $data_map);

        $this->createDirectoryIfNotExists($path, false);

        $complied = $this->loadImagemanagerViewsAndAssets($data_map, $complied, 'create');

        $this->writeFile($complied, $path, false);
    }

    private function loadEditView($guard, $views_path, $data_map)
    {
        $stub  =  $this->TPL_PATH . '/views/' . $this->theme . '/edit.blade.stub';
        $path =  $views_path . '/' . $guard . '/edit.blade.php';

        $complied =  $this->compliedFile($stub);

        $complied =  $this->loadBreadcrumbFor('edit', $complied, $data_map);

        $complied =  $this->loadLinkButtonFor('delete', $complied, $data_map);


        $this->createDirectoryIfNotExists($path, false);

        $complied = $this->loadImagemanagerViewsAndAssets($data_map, $complied, 'edit');


        $this->writeFile($complied, $path, false);
    }




    /**
     * @param $field_name
     * @return string
     */
    protected function showViewFields(string $field_name, array $data_map): string
    {
        $show_views = '';

        $guard = $data_map['{{backLowerNamespace}}'];


        foreach ($this->fields as $key =>  $field) {
            /**
             * Si le champ est de type imagemanager on le saute
             */
            if ($this->isImageableField($key)) {
                continue;
            } else if ($this->isRelationField($field['type'])) {

                if ($this->isSimpleRelation($field)) {
                    if ($this->isSimpleOneToOneRelation($field) || $this->isSimpleOneToManyRelation($field)) {
                        $model = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2">
                                            <b>$model: </b>
                                            <a href="{{ route('$guard.{$this->getRelationModelWithoutId($this->getFieldName($field))}.show', \${$field_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}) }}">
                                                {{ \${$field_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </p>
                        TEXT;
                    } else {
                        $model = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0]->{$this->getRelatedModelProperty($field)} }}</p>
                        TEXT;
                    }
                } else if ($this->isPolymorphicField($field)) {
                    if ($this->isPolymorphicOneToOneRelation($field)) {
                        $model = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}->{$this->getRelatedModelProperty($field)} }}</p>
                        TEXT;
                    } else {
                        $model = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0]->{$this->getRelatedModelProperty($field)} }}</p>
                        TEXT;
                    }
                }
            } else {
                $model = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                $show_views = <<<TEXT
                    $show_views
                            <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$this->getFieldName($field)} }}</p>
                TEXT;
            }
        }

        if ($this->timestamps) {
            $show_views = <<<TEXT
                $show_views
                            <p class="pb-2"><b>Date ajout: </b>{{ \${$field_name}->created_at->format('d/m/Y h:i') }}</p>
            TEXT;
            // $show_views .= '                <p class="pb-2"><b>Date ajout:</b> {{ $' . $field_name . '->created_at->format(\'d/m/Y h:i\') }}</p>' . "\n";
        }

        // si on doit afficher le media show

        return $show_views;
    }

    /**
     * @param $show_views
     * @param $view
     * @return array
     */
    protected function insertFieldToViewSHow($show_views, $view)
    {
        $search = '{{showView}}';
        $view = str_replace($search, $show_views, $view);
        return $view;
    }

    protected function insertFieldToViewEdit($show_views, $view)
    {
        $search = '{{morphImagesEdit}}';
        $view = str_replace($search, $show_views, $view);
        return $view;
    }

    /**
     * @param string $var_name
     * @return array
     * @internal param $data_map
     */
    private function showIndexFields(string $var_name, string $views_path, array $data_map): array
    {
        $values = '';

        $fields = '';

        $guard = $data_map['{{backLowerNamespace}}'];


        $stub  =  $this->TPL_PATH . '/views/' . $this->theme . '/partials/_checkbox.blade.stub';

        $complied =  $this->compliedFile($stub);


        // ajout du checkbox de suppression multiple
        $values = <<<TEXT
        $complied
        TEXT;


        $values = <<<TEXT
            $values
                        <td>{{ \$loop->iteration }}</td>
        TEXT;

        foreach ($this->fields as $key => $field) {


            if ($this->isImageableField($key)) {
                continue;
            }

            // si le champ est de type image on le saute parcequ'on ne souhaite pas l'afficher sur la page d´index
            if ($field['type'] == 'image') {
                continue;
            };



            if ($this->isRelationField($field['type'])) {
                $value = ucfirst(translate_model_field($this->getRelationModelWithoutId($field['name']), $field['trans'] ?? null));
                $fields = <<<TEXT
                    $fields
                                    <th>$value</th>
                    TEXT;
            } else {
                $value = ucfirst(translate_model_field($this->getFieldName($field), $field['trans'] ?? null));
                $fields = <<<TEXT
                    $fields
                                    <th>$value</th>
                    TEXT;
            }


            if (!$this->isRelationField($field['type']) && $this->isTextField($this->getFieldType($field))) {

                if ($this->isTheadminTheme() && ($this->getFieldName($field) === $this->guestBreadcrumbFieldNane())) {
                    $values = <<<TEXT
                        $values
                                    <td>
                                        <a class="text-dark" data-provide="tooltip" title="Apercu rapide"
                                            href="#qv-{$data_map['{{pluralSlug}}']}-details-{{ \${$var_name}->id }}" data-toggle="quickview"
                                        >
                                            {{ Str::limit(\${$var_name}->{$this->getFieldName($field)},50) }}
                                        </a>
                                    </td>
                    TEXT;
                } else {
                    $values = <<<TEXT
                        $values
                                    <td>{{ Str::limit(\${$var_name}->{$this->getFieldName($field)},50) }}</td>
                    TEXT;
                }
            } else if ($this->isRelationField($field['type'])) {

                if ($this->isSimpleRelation($field)) {
                    if ($this->isSimpleOneToOneRelation($field) || $this->isSimpleOneToManyRelation($field)) {
                        $values = <<<TEXT
                        $values
                                        <td>
                                            <a href="{{ route( '{$guard}.{$this->getRelationModelWithoutId($this->getFieldName($field))}.show',\${$var_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))} ) }}" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </td>
                        TEXT;
                    } else {
                        $values = <<<TEXT
                        $values
                                        <td>
                                            <a href="{{ route( '{$guard}.{$this->getRelationModelWithoutId($this->getFieldName($field))}.show',\${$var_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0] ) }}" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0]->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </td>
                        TEXT;
                    }
                } else if ($this->isPolymorphicField($field)) {
                    if ($this->isPolymorphicOneToOneRelation($field)) {
                        $values = <<<TEXT
                        $values
                                        <td>
                                            <a href="Javascript:void(0)" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </td>
                        TEXT;
                    } else {
                        $values = <<<TEXT
                        $values
                                        <td>
                                            <a href="Javascript:void(0)" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0]->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </td>
                        TEXT;
                    }
                }
            }
        }

        if (!$this->timestamps) {
            $fields .= '                                    <th>Date création</th>' . "\n";
            $values = <<<TEXT
                $values
                            <td>{{ \${$var_name}->created_at->format('d/m/Y h:i') }}</td>
            TEXT;
            // $values .= '                                        <td>{{ $' . $var_name . '->created_at->format(\'d/m/Y h:i\') }}</td>' . "\n";
        }


        return [$values, $fields];
    }
    /**
     * @param string $var_name
     * @return string
     * @internal param $data_map
     */
    private function getIndexFields(array $data_map, string $tag = 'div'): string
    {

        $values = '';

        $guard = $data_map['{{backLowerNamespace}}'];
        $var_name = $data_map['{{singularSlug}}'];



        // ajout du checkbox de suppression multiple

        foreach ($this->fields as $key => $field) {


            if ($this->isImageableField($key)) {
                continue;
            }

            // si le champ est de type image on le saute parcequ'on ne souhaite pas l'afficher sur la page d´index
            if ($field['type'] == 'image') {
                continue;
            };




            if (!$this->isRelationField($field['type']) && $this->isTextField($this->getFieldType($field))) {
                $values = <<<TEXT
                    $values
                                <{$tag}>{{ Str::limit(\${$var_name}->{$this->getFieldName($field)},50) }}</{$tag}>
                TEXT;
                // $values .= '                                        <td>{{ Str::limit($' . $var_name . '->' . $field['name'] . ') }}</td>' . "\n";
            } else if ($this->isRelationField($field['type'])) {

                if ($this->isSimpleRelation($field)) {
                    if ($this->isSimpleOneToOneRelation($field) || $this->isSimpleOneToManyRelation($field)) {
                        $values = <<<TEXT
                        $values
                                        <{$tag}>
                                            <a href="{{ route( '{$guard}.{$this->getRelationModelWithoutId($this->getFieldName($field))}.show',\${$var_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))} ) }}" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </{$tag}>
                        TEXT;
                    } else {
                        $values = <<<TEXT
                        $values
                                        <{$tag}>
                                            <a href="{{ route( '{$guard}.{$this->getRelationModelWithoutId($this->getFieldName($field))}.show',\${$var_name}->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0] ) }}" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0]->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </{$tag}>
                        TEXT;
                    }
                } else if ($this->isPolymorphicField($field)) {
                    if ($this->isPolymorphicOneToOneRelation($field)) {
                        $values = <<<TEXT
                        $values
                                        <{$tag}>
                                            <a href="Javascript:void(0)" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </{$tag}>
                        TEXT;
                    } else {
                        $values = <<<TEXT
                        $values
                                        <{$tag}>
                                            <a href="Javascript:void(0)" classs="badge badge-secondary p-2">
                                                {{ \$$var_name->{$this->getRelationModelWithoutId($this->getFieldName($field))}[0]->{$this->getRelatedModelProperty($field)} }}
                                            </a>
                                        </{$tag}>
                        TEXT;
                    }
                }
            }
        }

        if (!$this->timestamps) {
            $values = <<<TEXT
                $values
                            <{$tag}>{{ \${$var_name}->created_at->format('d/m/Y h:i') }}</{$tag}>
            TEXT;
            // $values .= '                                        <td>{{ $' . $var_name . '->created_at->format(\'d/m/Y h:i\') }}</td>' . "\n";
        }


        return $values;
    }




    /**
     * @param $fields
     * @param $complied
     * @param $values
     * @return string
     */
    private function insertFieldToViewIndex($fields, $complied, $values, array $data_map): string
    {
        $search = '{{fields}}';
        $form = str_replace($search, $fields, $complied);


        $search = '{{values}}';
        $form = str_replace($search, $values, $form);




        $search = '{{quickViewValues}}';
        if ('theadmin' === $this->theme) {
            $values = $this->getIndexFields($data_map);
            $form = str_replace($search, $values, $form);
        } else {
            $form = str_replace($search, '', $form);
        }

        return $form;
    }

    /**
     * @param $data_map
     */
    private function registerLinkToLeftSidebar($views_path, $data_map): void
    {
        $stub  =  $this->TPL_PATH . '/views/' . $this->theme . '/partials/_sidebar.blade.stub';
        $path =  $views_path  . '/partials/_sidebar.blade.php';

        $search = '{{-- insert sidebar links here --}}';
        $replace = $this->compliedFile($stub, true, $data_map);
        $this->replaceAndWriteFile(
            $this->filesystem->get($path),
            $search,
            $search . PHP_EOL . $replace,
            $path
        );
    }

    /**
     * @param string|string[] $field
     * @return bool
     */
    private function checkTextFieldType($field): bool
    {
        if ($this->isRelationField($field)) return false;

        return
            $field === 'text' || $field === 'mediumText' ||
            $field === 'longText';
    }





    /**
     * @param bool|array $field
     */
    public function loadImagemanagerViewsAndAssets(array $data_map, string $complied, string $action)
    {


        if (!$this->imagemanager) {
            $search = "{{-- add {$this->IMAGEMANAGER} here --}}";
            return str_replace($search, '', $complied);
        }

        $field = $this->imagemanager;


        $default_label = [
            'front'       =>  'Image à la une',
            'back'        =>  'Seconde image à la une',
            'images'      =>  'Gallerie',
        ];

        if (true === $field) {
            $labels = <<<LABEL
            'front_image_label' =>  {$default_label['front']},
            'back_image_label'  =>  {$default_label['back']},
            'images_label'      =>  {$default_label['images']},
            LABEL;

            if ('show' !== $action) {
                $labels = <<<LABEL
                $labels
                'front_image'       =>  true,
                'back_image'        =>  true,
                'images'            =>  true,
                LABEL;
            }
        } else if (Arr::isAssoc($field)) {
            $labels = '';
            $collections = '';

            foreach (['front', 'back', 'images'] as $collection) {
                if (array_key_exists($collection, $field)) {
                    if ('images' === $collection) {
                        $labels .= "'{$collection}' =>  '{$field[$collection]}'," . PHP_EOL;
                        $collections .= "'{$collection}' =>  true," . PHP_EOL;
                    } else {
                        $labels .= "'{$collection}_image_label' =>  '{$field[$collection]}'," . PHP_EOL;
                        $collections .= "'{$collection}_image' =>  true," . PHP_EOL;
                    }
                } else {
                    $collections .= "'{$collection}' =>  false," . PHP_EOL;
                }
            }
            if ('show' !== $action) {
                $labels .= $collections;
            }
        } else if (is_array($field)) {
            $labels = '';
            $collections = '';
            foreach (['front', 'back', 'images'] as $collection) {
                if (in_array($collection, $field)) {
                    if ('images' === $collection) {
                        $labels .= <<<LABEL
                            '{$collection}' =>  '{$default_label[$collection]}',\n
                        LABEL;
                        // $labels .= "'{$collection}' =>  '{$default_label[$collection]}'," . PHP_EOL;
                        $collections .= "'{$collection}' =>  true," . PHP_EOL;
                    } else {
                        // $labels .= "'{$collection}_image_label' =>  '{$default_label[$collection]}',". PHP_EOL;
                        $labels .= <<<LABEL
                            '{$collection}_image_label' =>  '{$default_label[$collection]}',\n
                        LABEL;
                        $collections .= "'{$collection}_image' =>  true," . PHP_EOL;
                    }
                } else {
                    $collections .= <<<TEX
                            '{$collection}' =>  false,\n
                        TEX;
                }
            }

            if ('show' !== $action) {
                $labels .= $collections;
            }
        } else {
            $labels = '';
        }

        if ($action === 'create') {
            $models = <<<TEXT
                'model'             =>  \$img_model = new {$data_map["{{namespace}}"]}\\{$data_map["{{modelsFolder}}"]}\\{$data_map["{{singularClass}}"]},
                'model_name'        =>  get_class(\$img_model),
                'form_name'         =>  \$img_model->form_name,
            TEXT;
        } else if ($action === 'edit') {
            $model = $data_map["{{singularSlug}}"];
            $models = <<<TEXT
               'model'      =>  \${$model},
               'model_name' =>  get_class(\${$model}),
               'form_name'  =>  \${$model}->form_name,
            TEXT;
        } else if ('show' === $action) {
            $model = $data_map["{{singularSlug}}"];
            $models = <<<TEXT
                'model'   =>  \${$model},
            TEXT;
        }

        if ('show' === $action) {
            $model = $data_map["{{singularSlug}}"];
            $partial =  <<<TEXT
             @include('{$data_map["{{backLowerNamespace}}"]}.media._show', [
                $models
                $labels
            ])
            TEXT;
        } else {
            $partial =  <<<TEXT
             @include('{$data_map["{{backLowerNamespace}}"]}.media._{$this->IMAGEMANAGER}', [
                $labels
                $models
            ])
            TEXT;
        }
        $search = "{{-- add {$this->IMAGEMANAGER} here --}}";

        return str_replace($search, $partial, $complied);
    }
}
