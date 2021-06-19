<?php

namespace Guysolamour\Administrable\Console\Crud\Generate;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Guysolamour\Administrable\Console\Crud\Field;



class GenerateViews extends BaseGenerate
{

    public function run()
    {
        if (!$this->crud->generateFieldIsAllowedFor($this)) {
            return [false, 'Skip creating views'];
        }


        $paths = PHP_EOL;

        if ($this->crud->hasAction('index')) {
            $paths .= $this->loadIndexView() . PHP_EOL;

            $this->registerLinkToLeftSidebar();
        }

        if ($this->crud->hasAction('show')) {
            $paths .= $this->loadShowView() . PHP_EOL;
        }

        if ($this->crud->hasAction('create')) {
            $paths .= $this->loadCreateView() . PHP_EOL;
        }


        if ($this->crud->hasAction('edit')) {
            $paths .= $this->loadEditView() . PHP_EOL;
        }

        if ($this->crud->hasActions('create', 'edit')) {
            $paths .= $this->loadFormPartial() . PHP_EOL;
        }

        return [true, $paths];
    }

    protected function loadFormPartial() :string
    {
        $stub = $this->getStub('partials/_form');
        $path = $this->getPath('_form');

        $complied =  $this->crud->filesystem->compliedFile($stub, true, $this->data_map);

        // tinymce a field
        if ($this->crud->hasTinymceField()) {
            $tinymce = <<<TEXT
            <x-administrable::tinymce
                selector="textarea[data-tinymce]"
                :model="\$form->getModel()"
            />
            TEXT;

            $complied =  str_replace('{{-- add tinymce here --}}', $tinymce, $complied);
        }

        if ($this->crud->hasAction('edit')) {
            $edit_button = <<<'TEXT'
            @if (isset($edit) && $edit)
            <div class="form-group">
                <button type="submit" class="btn btn-success"> <i class="fa fa-edit"></i> Modifier</button>
            </div>
            @endif
            TEXT;
            $complied =  str_replace('{{-- form edit button --}}', $edit_button, $complied);
        } else {
            $complied =  str_replace('{{-- form edit button --}}', '', $complied);
        }

        if ($this->crud->hasAction('create')) {
            $create_button = <<<'TEXT'
            @if (!isset($edit))
            <div class="form-group">
                <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i> Enregistrer</button>
            </div>
            @endif
            TEXT;
            $complied =  str_replace('{{-- form create button --}}', $create_button, $complied);
        } else {
            $complied =  str_replace('{{-- form create button --}}', '', $complied);
        }

        $complied = $this->addDatepickerAndDaterange($complied);

        $this->crud->filesystem->writeFile($path, $complied,  false);

        return $path;
    }

    public function addDatepickerAndDaterange(string $complied): string
    {
        $search = '{{-- add daterange here --}}';

        $back = $this->data_map['{{backLowerNamespace}}'];

        $fields = $this->crud->getFields();

        foreach ($fields as  $field) {
            /**
             * @var Field $field
             */
            if ($field->isDatepicker()) {
                $replace = <<<HTML
                <x-administrable::daterangepicker 
                    fieldname="{$field->getName()}"
                    drops="down"
                    opens="right"
                    :startdate="\$form->getModel()->{$field->getName()}"
                    :singledatepicker="true"
                    :timepicker24hour="true"
                />
                HTML;
                $complied =  str_replace($search, $search . PHP_EOL . PHP_EOL . $replace, $complied);
            } else if ($field->isDaterange()) {
                $replace = <<<HTML
                <x-administrable::daterangepicker 
                    fieldname="{$field->getName()}"
                    drops="down"
                    opens="right"
                    :startdate="\$form->getModel()->{$field->getDaterangeStartFieldName()}"
                    :enddate="\$form->getModel()->{$field->getDaterangeEndFieldName()}"
                    :singledatepicker="false"
                    :timepicker24hour="true"
                />
                HTML;
                $complied =  str_replace($search, $search . PHP_EOL . PHP_EOL . $replace, $complied);
            }
        }

        return $complied;
    }

    protected function loadEditView() :string
    {
        $stub = $this->getStub('edit');
        $path = $this->getPath('edit');

        $complied =  $this->crud->filesystem->compliedFile($stub, true, $this->data_map);

        $complied =  $this->loadBreadcrumbFor('edit', $complied);

        $complied =  $this->loadLinkButtonFor('delete', $complied);


        $complied = $this->loadImagemanagerViewsAndAssets( $complied, 'edit');


        $this->crud->filesystem->writeFile($path, $complied,  false);

        return $path;
    }

    protected function loadCreateView() :string
    {
        $stub = $this->getStub('create');
        $path = $this->getPath('create');

        $complied =  $this->crud->filesystem->compliedFile($stub, true, $this->data_map);

        $complied =  $this->loadBreadcrumbFor('create', $complied);

        $complied = $this->loadImagemanagerViewsAndAssets( $complied, 'create');

        $this->crud->filesystem->writeFile($path, $complied,  false);

        return $path;
    }

    protected function loadBreadcrumbFor(string $key, string $complied): string
    {
        if (!$this->crud->getBreadcrumb()) {
            return $complied;
        }

        $breadcrumb_stub = $this->getStub("/breadcrumbs/{$key}");
        // $breadcrumb_stub = $this->crud->getCrudTemplatePath("views/" . $this->crud->getTheme() . "/breadcrumbs/{$key}.blade.stub");
        $replace = $this->crud->filesystem->compliedFile($breadcrumb_stub, true, $this->data_map);

        $complied =   str_replace('{{-- breadcrumb --}}', $replace, $complied);


        return $complied;
    }

    protected function loadShowView() :string
    {
        $stub = $this->getStub('show');
        $path =  $this->getPath('show');

        $complied =  $this->crud->filesystem->compliedFile($stub, true, $this->data_map);

        $complied =  $this->loadBreadcrumbFor('show', $complied);
        $complied =  $this->loadLinkButtonFor('edit', $complied);
        $complied =  $this->loadLinkButtonFor('delete', $complied);

        $complied = $this->loadImagemanagerViewsAndAssets( $complied, 'show');

        $show_views = $this->getShowViewFields();
        $view = $this->insertFieldToViewSHow($show_views, $complied);

        $this->crud->filesystem->writeFile($path, $view,  false);

        return $path;
    }


    protected function insertFieldToViewSHow(string $show_views, string $view) :string
    {
        $search = '{{showView}}';
        $view = str_replace($search, $show_views, $view);

        return $view;
    }


    protected function getShowViewFields(): string
    {
        $field_name = $this->data_map['{{singularSlug}}'];

        $show_views = '';

        $guard = $this->data_map['{{backLowerNamespace}}'];

        foreach ($this->crud->getFields() as $key =>  $field) {
            /**
             * @var Field $field
             */
            if ($field->isRelation()) {

                if ($field->isSimpleRelation()) {
                    if ($field->isSimpleOneToOneRelation() || $field->isSimpleOneToManyRelation()) {
                        $model = ucfirst(translate_model_field($field->getName(), $field->getTrans()));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2">
                                            <b>$model: </b>
                                            <a href="{{ route('$guard.{$field->getRelationModelWithoutId()}.show', \${$field_name}->{$field->getRelationModelWithoutId()}) }}">
                                                {{ \${$field_name}->{$field->getRelationModelWithoutId()}->{$field->getRelationRelatedModelProperty()} }}
                                            </a>
                                        </p>
                        TEXT;
                    } else {
                        $model = ucfirst(translate_model_field($field->getName($field), $field->getTrans()));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$field->getRelationModelWithoutId()}[0]->{$field->getRelationRelatedModelProperty()} }}</p>
                        TEXT;
                    }
                } else if ($field->isPolymorphicRelation()) {
                    if ($field->isPolymorphicOneToOneRelation()) {
                        $model = Str::ucfirst(translate_model_field($field->getName(), $field->getTrans()));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$field->getRelationModelWithoutId()}->{$field->getRelationRelatedModelProperty()} }}</p>
                        TEXT;
                    } else {
                        $model = Str::ucfirst(translate_model_field($field->getName(), $field->getTrans()));
                        $show_views = <<<TEXT
                            $show_views
                                        <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$field->getRelationModelWithoutId()}[0]->{$field->getRelationRelatedModelProperty()} }}</p>
                        TEXT;
                    }
                }
            } else if ($field->isDaterange()) {
                $start_model = Str::ucfirst(translate_model_field($field->getName(), $field->getDateRangeStartTrans()));
                $end_model   = Str::ucfirst(translate_model_field($field->getName(), $field->getDateRangeEndTrans()));

                $show_views = <<<TEXT
                            $show_views
                                    <p class="pb-2"><b>$start_model: </b>{{ format_date(\${$field_name}->{$field->getDaterangeStartFieldName()}) }}</p>
                                    <p class="pb-2"><b>$end_model: </b>{{ format_date(\${$field_name}->{$field->getDaterangeEndFieldName($field)}) }}</p>
                        TEXT;
            } else if ($field->isDatetime()) {
                $model = Str::ucfirst(translate_model_field($field->getName(), $field->getTrans()));

                $show_views = <<<TEXT
                            $show_views
                                    <p class="pb-2"><b>$model: </b>{{ format_date(\${$field_name}->{$field->getName()}) }}</p>
                        TEXT;
            } else if ($field->isBoolean()) {
                $model = Str::ucfirst(translate_model_field($field->getName(), $field->getTrans()));

                $show_views = <<<TEXT
                            $show_views
                                    <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$field->getName()} ? __('Yes') : __('No') }}</p>
                        TEXT;
            } else {
                $model = ucfirst(translate_model_field($field->getName($field), $field->getTrans()));
                $show_views = <<<TEXT
                    $show_views
                            <p class="pb-2"><b>$model: </b>{{ \${$field_name}->{$field->getName($field)} }}</p>
                TEXT;
            }
        }

        if ($this->crud->getTimestamps()) {
            $show_views = <<<TEXT
                $show_views
                        <p class="pb-2"><b>Date ajout: </b>{{ format_date(\${$field_name}->created_at) }}</p>
            TEXT;
            // $show_views .= '                <p class="pb-2"><b>Date ajout:</b> {{ $' . $field_name . '->created_at->format(\'d/m/Y h:i\') }}</p>' . "\n";
        }

        return $show_views;
    }

    protected function loadImagemanagerViewsAndAssets(string $complied, string $action) :string
    {

        if (!$this->crud->getImagemanager()) {
            $search = "{{-- add imagemanager here --}}";
            return str_replace($search, '', $complied);
        }


        $field = $this->crud->getImagemanager();


        $media_collections = config('media-library.collections');

        if (true === $field) {
            $labels = <<<LABEL
            'front_image_label' =>  '{$media_collections['front']['description']}',
            'back_image_label'  =>  '{$media_collections['back']['description']}',
            'images_label'      =>  '{$media_collections['images']['description']}',
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

            foreach ($media_collections as $key => $collection) {
                if (Arr::exists($field, $key)) {
                    if ('images' === $key) {
                        $labels .= "'{$key}_label' =>  '{$field[$key]}'," . PHP_EOL;
                        $collections .= "'{$key}' =>  true," . PHP_EOL;
                    } else {
                        $labels .= "'{$key}_image_label' =>  '{$field[$key]}'," . PHP_EOL;
                        $collections .= "'{$key}_image' =>  true," . PHP_EOL;
                    }
                }
            }

            if ('show' !== $action) {
                $labels .= $collections;
            }
        } else if (is_array($field)) {
            $labels = '';
            $collections = '';

            foreach ($media_collections as $key => $collection) {
                if (in_array($key, $field)) {
                    if ('images' === $key) {
                        $labels .= <<<LABEL
                            '{$key}_label' =>  '{$media_collections[$key]['description']}',\n
                        LABEL;
                        $collections .= "'{$key}' =>  true," . PHP_EOL;
                    } else {
                        $labels .= <<<LABEL
                            '{$key}_image_label' =>  '{$media_collections[$key]['description']}',\n
                        LABEL;
                        $collections .= "'{$key}_image' =>  true," . PHP_EOL;
                    }
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
                'model'             =>   new {$this->data_map["{{namespace}}"]}\\{$this->data_map["{{modelsNamespace}}"]}\\{$this->data_map["{{singularClass}}"]},
               // 'form_name'         =>  \$img_model->form_name,
            TEXT;
        } else if ($action === 'edit') {
            $model = $this->data_map["{{singularSlug}}"];
            $models = <<<TEXT
               'model'      =>  \${$model},
               // 'form_name'  =>  \${$model}->form_name,
            TEXT;
        } else if ('show' === $action) {
            $model = $this->data_map["{{singularSlug}}"];
            $models = <<<TEXT
                'model'   =>  \${$model},
            TEXT;
        }

        if ('show' === $action) {
            $model = $this->data_map["{{singularSlug}}"];
            $partial =  <<<TEXT
             @include('{$this->data_map["{{backLowerNamespace}}"]}.media._show', [
                $models
                $labels
            ])
            TEXT;
        } else {
            $partial =  <<<TEXT
             @include('{$this->data_map["{{backLowerNamespace}}"]}.media._imagemanager', [
                $labels
                $models
            ])
            TEXT;
        }
        $search = "{{-- add imagemanager here --}}";

        return str_replace($search, $partial, $complied);
    }


    protected function getStub(string $name) :string
    {
        return $this->crud->getCrudTemplatePath('views/' . $this->crud->getTheme() . "/{$name}.blade.stub");
    }

    protected function getLinkButtonStub(string $action) :string
    {
        return $this->getStub("partials/indexlinks/_{$action}link");
    }

    protected function insertHeaderMegaMenuForThemeKitTheme() :void
    {
        if (!$this->crud->isThemeKitTheme()){
            return;
        }

        $replace = <<<HTML
                        <!-- {$this->data_map['{{sidebarViewModelWithSubfolder}}']} link -->
                        <div class="app-item">
                            <a href="{{ route('{$this->data_map['{{indexRoute}}']}') }}">
                                <i class="fa {$this->data_map['{{icon}}']}"></i><span>{$this->data_map['{{pluralClass}}']}</span>
                            </a>
                        </div>
                        <!-- end {$this->data_map['{{sidebarViewModelWithSubfolder}}']} link -->
                    HTML;

        $header_path =  $this->getRootPath('partials/_header');

        $search = "{{-- add megamenu Link here --}}";
        $this->crud->filesystem->replaceAndWriteFile(
            $this->crud->filesystem->get($header_path),
            $search,
            $search . PHP_EOL . $replace,
            $header_path
        );

    }

    protected function loadIndexView() :string
    {
        $complied =  $this->crud->filesystem->compliedFile($this->getStub('index'), true, $this->data_map);

        $complied =  $this->loadBreadcrumbFor('index', $complied);


        $complied =  $this->loadLinkButtonFor('create', $complied);
        $complied =  $this->loadShowEditDeleteLinksButtons($complied);
        $complied =  $this->loadCloneLink($complied);

        $complied = $this->insertFieldsAndValuesToIndexViewStub( $complied);

        $this->insertHeaderMegaMenuForThemeKitTheme();

        $path = $this->getPath('index');

        $this->crud->filesystem->writeFile($path, $complied, false);


        return $path;
    }

    protected function insertFieldsAndValuesToIndexViewStub(string $complied): string
    {
        [$fields, $values] = $this->getIndexViewFieldsAndValues();

        $search = '{{fields}}';
        $form = str_replace($search, $fields, $complied);


        $search = '{{values}}';
        $form = str_replace($search, $values, $form);


        $search = '{{quickViewValues}}';
        if ($this->crud->isTheadminTheme()) {
            $values = $this->getTheadminThemeQuickViewValues(); // the admin theme views
            // $values = $this->getIndexFields(); // the admin theme views
            $form = str_replace($search, $values, $form);
        } else {
            $form = str_replace($search, '', $form);
        }

        return $form;
    }

    protected function getIndexViewFieldsAndValues(): array
    {
        $values = $fields = '';


        $complied =  $this->crud->filesystem->compliedFile($this->getStub('partials/_checkbox'));

        // added multiple deletion checkbox
        $values = <<<TEXT
        $complied
        TEXT;

        $values = <<<TEXT
            $values
                        <td>{{ \$loop->iteration }}</td>
        TEXT;

        [$fields, $values] = $this->getFormatedIndexViewFieldsAndValues( $fields, $values);

        if ($this->crud->getTimestamps()) {
            $fields .= PHP_EOL . '                <th>Date création</th>' . PHP_EOL;
            $values = <<<TEXT
                $values
                            <td>{{ format_date(\${$this->data_map['{{singularSlug}}']}->created_at) }}</td>
            TEXT;
        }

        return [$fields, $values];
    }

    protected function addIndexViewRelationField(Field $field, string $fields) :string
    {
        $value = Str::ucfirst(translate_model_field($field->getRelationModelWithoutId(), $field->getTrans()));
        $fields = <<<TEXT
                    $fields
                                    <th>$value</th>
                    TEXT;

        return $fields;
    }

    protected function addIndexViewDaterangeField(Field $field, string $fields) :string
    {
        $start_value = Str::ucfirst(translate_model_field($field->getName(), $field->getDateRangeStartTrans()));
        $end_value = Str::ucfirst(translate_model_field($field->getName(), $field->getDateRangeEndTrans()));

        $fields = <<<TEXT
                    $fields
                                    <th>$start_value</th>
                                    <th>$end_value</th>
                    TEXT;

        return $fields;
    }

    protected function addIndexViewField(Field $field, string $fields) :string
    {
        $value = Str::ucfirst(translate_model_field($field->getName(), $field->getTrans()));
        $fields = <<<TEXT
                    $fields
                                    <th>$value</th>
                    TEXT;

        return $fields;
    }

    protected function getRelationRelatedModelShowRoute(Field $field) :string
    {
        return $field->getRelationRelatedModelRoute('show');
    }

    protected function addIndexViewSimpleRelationValues(Field $field, string $values, bool $many = false) :string
    {
        $route = $this->getRelationRelatedModelShowRoute($field);
        $model = "{$this->data_map['{{singularSlug}}']}->{$field->getRelationModelWithoutId()}";
        if ($many){
            $model . '[0]';
        }
        $text  = "{$model}->{$field->getRelationRelatedModelProperty()}";

        return <<<TEXT
                        $values
                                        <td>
                                            <a href="{{ route('{$route}',\${$model} ) }}" classs="badge badge-secondary p-2">
                                                {{ \${$text} }}
                                            </a>
                                        </td>
                        TEXT;

    }

    protected function addIndexViewPolymorphicRelationValues(Field $field, string $values) :string
    {
        $model = "{$this->data_map['{{singularSlug}}']}->{$field->getRelationModelWithoutId()}";

        if ($field->isPolymorphicOneToOneRelation()){
            $model . '[0]';
        }

        return <<<TEXT
                    $values
                                    <td>
                                        <a href="Javascript:void(0)" classs="badge badge-secondary p-2">
                                            {{ \${model}->{$field->getRelationRelatedModelProperty()} }}
                                        </a>
                                    </td>
                    TEXT;
    }

    protected function addIndexViewBooleanValues(Field $field, string $values) :string
    {
        $values = <<<TEXT
                        $values
                                    <td>{{ \${$this->data_map['{{singularSlug}}']}->{$field->getName()} ? __('Yes')  : __('No') }}</td>
                    TEXT;


        return $values;
    }

    protected function addIndexViewDaterangeValues(Field $field, string $values) :string
    {
        $values = <<<TEXT
                        $values
                                    <td>{{ format_date(\${$this->data_map['{{singularSlug}}']}->{$field->getDateRangeStartFieldName()}) }}</td>
                                    <td>{{ format_date(\${$this->data_map['{{singularSlug}}']}->{$field->getdateRangeEndFieldName()}) }}</td>
                    TEXT;

        return $values;
    }

    protected function addIndexViewDatetimeValues(Field $field, string $values) :string
    {
        $values = <<<TEXT
                        $values
                                    <td>{{ format_date(\${$this->data_map['{{singularSlug}}']}->{$field->getName()}) }}</td>
                    TEXT;

        return $values;
    }

    protected function addIndexViewValues(Field $field, string $values) :string
    {
        $values = <<<TEXT
                        $values
                                    <td>{{ \${$this->data_map['{{singularSlug}}']}->{$field->getName()} }}</td>
                    TEXT;

        return $values;
    }

    protected function addIndexViewTextFieldValues(Field $field, string $values) :string
    {
        if ($this->crud->isTheadminTheme() && ($field->getName() === $this->guestBreadcrumbFieldNane())) {
            $values = <<<TEXT
                        $values
                                    <td>
                                        <a class="text-dark" data-provide="tooltip" title="Apercu rapide"
                                            href="#qv-{$this->data_map['{{pluralSlug}}']}-details-{{ \${$this->data_map['{{singularSlug}}']}->id }}" data-toggle="quickview"
                                        >
                                            {{ Str::limit(\${$this->data_map['{{singularSlug}}']}->{$field->getName($field)}, 50) }}
                                        </a>
                                    </td>
                    TEXT;
        } else {
            $values = <<<TEXT
                        $values
                                    <td>{{ Str::limit(\${$this->data_map['{{singularSlug}}']}->{$field->getName()}, 50) }}</td>
                    TEXT;
        }

        return $values;
    }

    protected function getFormatedIndexViewFieldsAndValues(string $fields = '', string $values = ''): array
    {
        foreach ($this->crud->getFields() as $key => $field) {

            // if the field is of type image we skip it because we do not want to display it on the index page
            if ($field->isImageType()) {
                continue;
            };

            // Fields
            if ($field->isRelation()) {
                $fields = $this->addIndexViewRelationField($field, $fields);
            } else if ($field->isDaterange()) {
                $fields = $this->addIndexViewDaterangeField($field, $fields);
            } else {
                $fields = $this->addIndexViewField($field, $fields);
            }

            // Values
            if (!$field->isRelation() && $field->isText()) {
                $values = $this->addIndexViewTextFieldValues($field, $values);

            } else if ($field->isRelation()) {

                if ($field->isSimpleRelation()) {
                    if ($field->isSimpleOneToOneRelation() || $field->isSimpleOneToManyRelation()) {
                        $values = $this->addIndexViewSimpleRelationValues($field, $values);

                    } else {
                        $values = $this->addIndexViewSimpleRelationValues($field, $values, true);
                    }
                } else if ($field->isPolymorphicRelation()) {
                    $values = $this->addIndexViewPolymorphicRelationValues($field, $values);

                }
            } else if ($field->isBoolean()) {
                $values = $this->addIndexViewBooleanValues($field, $values);

            } else if ($field->isDaterange()) {
                $values = $this->addIndexViewDaterangeValues($field, $values);

            } else if ($field->isDatetime()) {
                $values = $this->addIndexViewDatetimeValues($field, $values);
            } else {
                $values = $this->addIndexViewValues($field, $values);
            }
        }

        return [$fields, $values];
    }

    protected function guestBreadcrumbFieldNane(): string
    {
        if ($this->crud->getBreadcrumb()) {
            return $this->crud->getBreadcrumb();
        }

        foreach ($this->crud->getFields() as $field) {
            if ($field->isText()) {
                return $field->getName();
            }
        }

        return '';
    }

    protected function registerLinkToLeftSidebar(): void
    {
        $stub = $this->getStub('partials/_sidebar');

        $path = resource_path("views/{$this->data_map['{{backLowerNamespace}}']}") . '/partials/_sidebar.blade.php';

        $search = '{{-- insert sidebar links here --}}';
        $replace = $this->crud->filesystem->compliedFile($stub, true, $this->data_map);

        $this->crud->filesystem->replaceAndWriteFile(
            $this->crud->filesystem->get($path),
            $search,
            $search . PHP_EOL . $replace,
            $path
        );
    }

    protected function getTheadminThemeQuickViewSimpleRelation(Field $field, string $values) :string
    {
        $route = $this->getRelationRelatedModelShowRoute($field);

        $model = "{$this->data_map['{{singularSlug}}']}->{$field->getRelationModelWithoutId()}";
        if ($field->isSimpleManyToManyRelation()){
            $model . '[0]';
        }

        $text = "{$model}->{$field->getRelationRelatedModelProperty()}";

        return <<<TEXT
                        $values
                                        <div>
                                            <a href="{{ route( '{$route}', \${$model} ) }}" classs="badge badge-secondary p-2">
                                                {$text}
                                            </a>
                                        </div>
                        TEXT;
    }

    protected function getTheadminThemeQuickViewPolymorphicRelation(Field $field, string $values)
    {
        $route = "Javscript:void(0)";

        $model = "{$this->data_map['{{singularSlug}}']}->{$field->getRelationModelWithoutId()}";
        if (!$field->isPolymorphicOneToOneRelation()()){
            $model . '[0]';
        }

        $text = "{$model}->{$field->getRelationRelatedModelProperty()}";

        return <<<TEXT
                        $values
                                        <div>
                                            <a href="{{ route( '{$route}', \${$model} ) }}" classs="badge badge-secondary p-2">
                                                {$text}
                                            </a>
                                        </div>
                        TEXT;
    }

    protected function getTheadminThemeQuickViewTextValue(Field $field, string $values) :string
    {
        $values = <<<TEXT
                    $values
                                <div>{{ Str::limit(\${$this->data_map['{{singularSlug}}']}->{$field->getName()}, 50) }}</div>
                TEXT;

        return $values;
    }

    protected function getTheadminThemeQuickViewValues(): string
    {
        $values = '';

        foreach ($this->crud->getFields() as  $field) {

            if ($field->isImageType()) {
                continue;
            }

            if (!$field->isRelation() && $field->isText()) {
                $values = $this->getTheadminThemeQuickViewTextValue($field, $values);
            } else if ($field->isRelation()) {

                if ($field->isSimpleRelation()) {
                    $values = $this->getTheadminThemeQuickViewSimpleRelation($field, $values);

                } else if ($field->isPolymorphicRelation()) {
                    $values = $this->getTheadminThemeQuickViewPolymorphicRelation($field, $values);
                }
            }
        }

        if ($this->crud->getTimestamps()) {
            $values = <<<TEXT
                $values
                            <{div}>{{ \${$this->data_map['{{singularSlug}}']}->formated_date }}</{div}>
            TEXT;
        }

        return $values;
    }

    protected function loadLinkButtonFor(string $key, string $complied): string
    {
        if (!$this->crud->hasAction($key)){
            return $complied;
        }

        $stub  =  $this->getStub("/partials/links/_{$key}link");
        $replace = $this->crud->filesystem->compliedFile($stub, true, $this->data_map);

        $complied = str_replace("{{-- {$key} link --}}", $replace, $complied);

        if ($this->crud->isTheAdminTheme() && ('edit' === $key || 'delete' === $key)) {
            $stub  =  $this->getStub("/partials/links/_show{$key}link");
            // $stub  =  $this->crud->getCrudTemplatePath('views/' . $this->crud->getTheme() . "/partials/links/_show{$key}link.blade.stub");
            $replace = $this->crud->filesystem->compliedFile($stub, true, $this->data_map);

            $complied = str_replace("{{-- show{$key} link --}}", $replace, $complied);
        }

        return $complied;
    }

    protected function loadCloneLink(string $complied) :string
    {
        if (!$this->crud->getClone()) {
            return $complied;
        }

        return $this->replaceLinkButton('clone', $complied);
    }

    protected function replaceLinkButton(string $action, string $complied) :string
    {
        $replace = $this->crud->filesystem->complied($this->getLinkButtonStub($action), $this->data_map);

        return str_replace("{{-- index {$action} link --}}", $replace, $complied);
    }

    protected function loadShowEditDeleteLinksButtons(string $complied): string
    {
        $actions = ['show', 'edit', 'delete'];

        foreach ($actions as $action) {
            if ($this->crud->hasAction($action)) {
                $complied = $this->replaceLinkButton($action, $complied);
            }
        }

        return $complied;
    }

    public function getParsedName(?string $name = null): array
    {
        return array_merge(
            $this->crud->getParsedName($name),
            $this->getRoutesParsedName(),
            [
                '{{translateModelLower}}'   =>  Str::lower($this->getModelTranslation($name)),
                '{{translateModelUcfirst}}' =>  $this->getModelTranslation(),
                '{{icon}}'                  =>  $this->crud->getIcon(),
                '{{sidebarViewModelWithSubfolder}}' => $this->sidebarViewModelWithSubfolder(),
                '{{breadcrumb}}'            =>  $this->crud->guestBreadcrumbFieldName(),
                '{{formPath}}'              =>  $this->getFormPath(),
            ]
        );
    }

    protected function getFormPath() :string
    {
        $data_map = $this->crud->getParsedName();
        $path = $data_map['{{backLowerNamespace}}'] . '.';

        if ($subfolder = $this->crud->getSubFolder()){
            $path .= $subfolder . '.';
        }

        return $path . $data_map['{{pluralSlug}}'] . '._form';
    }

    protected function sidebarViewModelWithSubfolder() :string
    {
        $model = '';

        if ($subfolder = $this->crud->getSubFolder()){
            $model .= $subfolder . '.';
        }

        return $model . $this->crud->getParsedName()['{{singularSlug}}'];
    }

    protected function getModelTranslation(): string
    {
        if (empty($this->crud->getTrans())) {
            return Str::plural(Str::studly($this->crud->getModel()));
        }

        return $this->crud->getTrans();
    }

    protected function getRootPath(?string $name = null) :string
    {
        $path = resource_path("views/{$this->data_map['{{backLowerNamespace}}']}") . '/';

        if ($name) {
            $path .= $name . '.blade.php';
        }

        return $path;
    }

    protected function getPath(?string $name = null): string
    {
        $path = resource_path("views/{$this->data_map['{{backLowerNamespace}}']}") . '/';

        if ($subfolder = $this->crud->getSubFolder()) {
            $path .= Str::lower($subfolder) . '/';
        }

        $path .= $this->data_map['{{pluralSlug}}'] . '/';

        if ($name){
            $path .= $name . '.blade.php';
        }

        return $path;
    }
}
