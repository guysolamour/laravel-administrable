<?php
namespace Guysolamour\Administrable\Console\Crud;




use Illuminate\Support\Str;

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

    private $timestamps;
    /**
     * @var null|string
     */
    private $slug;


    /**
     * CreateCrudView constructor.
     * @param string $model
     * @param array $fields
     * @param null|string $slug
     * @param bool $timestamps
     */
    public function __construct(string $model, array $fields, ?string $slug = null, bool $timestamps = false)
    {
        $this->model = $model;
        $this->fields = $fields;
        $this->timestamps = $timestamps;
        $this->slug = $slug;
    }

    /**
     * @param string $name
     * @param array $fields
     * @param null|string $slug
     * @param bool $timestamps
     * @return string
     */
    public static function generate(string $name, array $fields, ?string $slug = null, bool $timestamps = false)
    {
        return (new CreateCrudView($name,$fields,$slug,$timestamps))->loadViews();
    }

    /**
     * @return string
     */
    public function loadViews() :string
    {
        $data_map = $this->parseName($this->model);

        $guard = $data_map['{{pluralSlug}}'];

        $views_path = resource_path('views/admin');

        $this->loadIndexView($guard, $views_path, $data_map);
        $this->loadCreateView($guard, $views_path, $data_map);
        $this->loadEditView($guard, $views_path, $data_map);
        $this->loadShowView($guard, $views_path, $data_map);
        $this->loadFormPartial($guard, $views_path, $data_map);

        // register sidebar link
        $this->registerLinkToLeftSidebar($data_map);

        return $views_path;
    }

    private function loadIndexView($guard, $views_path, $data_map)
    {
        $stub  = $this->TPL_PATH . '/views/index.blade.stub';
        $path =  $views_path . '/' . $guard . '/index.blade.php';

        $stub = file_get_contents($stub);
        $complied = strtr($stub, $data_map);

        $this->createDirIfNotExists($path);

        $var_name = $data_map['{{singularSlug}}'];
        [$values, $fields] = $this->showIndexFields($var_name);
        $view = $this->insertFieldToViewIndex($fields, $complied, $values);

        $this->writeFile($path, $view);


    }
    private function loadShowView($guard, $views_path, $data_map)
    {
        $stub  = $this->TPL_PATH . '/views/show.blade.stub';
        $path =  $views_path . '/' . $guard . '/show.blade.php';

        $stub = file_get_contents($stub);
        $complied = strtr($stub, $data_map);

        $this->createDirIfNotExists($path);

        $var_name = $data_map['{{singularSlug}}'];
        $show_views = $this->showViewFields($var_name);
        $view = $this->insertFieldToViewSHow($show_views, $complied);

        $this->writeFile($path, $view);


    }
    private function loadFormPartial($guard, $views_path, $data_map)
    {
        $stub  = $this->TPL_PATH . '/views/_form.blade.stub';
        $path =  $views_path . '/' . $guard . '/_form.blade.php';

        $stub = file_get_contents($stub);
        $complied = strtr($stub, $data_map);


        $this->createDirIfNotExists($path);


        $this->writeFile($path, $complied);


    }
    private function loadCreateView($guard, $views_path, $data_map)
    {
        $stub  = $this->TPL_PATH . '/views/create.blade.stub';
        $path =  $views_path . '/' . $guard . '/create.blade.php';

        $stub = file_get_contents($stub);
        $complied = strtr($stub, $data_map);

        $this->createDirIfNotExists($path);

        foreach ($this->fields as $field){
            if ($this->isMorphsFIeld($field)){
                if ($this->isImagesMorphRelation($field)){
                    $complied = $this->loadMorphsViewAndAssets($data_map, $field, $complied, 'create');
                }
            }

            if ($this->isImageFIeld($field)){
                $complied = $this->loadImageViewsAndAssets($data_map, $complied, 'create');
            }
        }

        $this->writeFile($path, $complied);
    }

    private function loadEditView($guard, $views_path, $data_map)
    {
        $stub  = $this->TPL_PATH . '/views/edit.blade.stub';
        $path =  $views_path . '/' . $guard . '/edit.blade.php';

        $stub = file_get_contents($stub);
        $complied = strtr($stub, $data_map);

        $this->createDirIfNotExists($path);

        foreach ($this->fields as $field){
            if ($this->isMorphsFIeld($field)){
                if ($this->isImagesMorphRelation($field)){
                    $complied = $this->loadMorphsViewAndAssets($data_map, $field, $complied, 'edit');
                }
            }
            if ($this->isImageFIeld($field)){
                $complied = $this->loadImageViewsAndAssets($data_map, $complied, 'edit');
            }
        }

        $this->writeFile($path, $complied);
    }




    /**
     * @param $field_name
     * @return string
     */
    protected function showViewFields($field_name) :string
    {
        $show_views = '';
        // ajout du champ slug
        if (!is_null($this->slug)) {
            $show_views .= '                <p><b>' . ucfirst($this->slug) . ':</b>{{ $' . $field_name . '->' . $this->slug . ' }}</p>' . "\n";
        }

        foreach ($this->fields as $field) {
//            if ($this->isRelationField($field)){
//
//            }else {
//
//            }
            $show_views .= '                <p><b>' . ucfirst($field['name']) . ':</b>{{ $' . $field_name . '->' . $field['name'] . ' }}</p>' . "\n";
        }

        if (!$this->timestamps) {
            $show_views .= '                <p><b>Date ajout:</b> {{ $' . $field_name . '->created_at->format(\'d/m/Y h:i\') }}</p>' . "\n";
        }

        return $show_views;
    }

    /**
     * @param $show_views
     * @param $view
     * @return array
     */
    protected function insertFieldToViewSHow($show_views, $view)
    {
        $slug_mw_bait = '{{showView}}';
        $view = str_replace($slug_mw_bait, $show_views, $view);
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
    private function showIndexFields(string $var_name): array
    {
        $values = '';

        $fields = '';

        // if (!is_null($this->slug)) {
        //     $fields .= '                                    <th>' . ucfirst($this->slug) . '</th>' . "\n";
        //     $values .= '                                        <td>{{ $' . $var_name . '->' . $this->slug . ' }}</td>' . "\n";

        // }

        // ajout du checkbox de suppression multiple
        $values .= '<td>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" data-check class="custom-control-input" data-id="{{ $' . $var_name . '->id }}" id="check-{{ $' . $var_name . '->id }}">
                        <label class="custom-control-label" for="check-{{ $' . $var_name . '->id }}"></label>
                    </div>
                </td>';

        $values .= '<td>{{ $loop->iteration }}</td>';

        // dd($this->fields);
        foreach ($this->fields as $field) {

            // si le champ est de type image on le saute parcequ'on ne souhaite pas l'afficher sur la page d´index
            if($field['type'] == 'image'){
                continue;
            };


            if ($this->isRelationField($field['type'])){

                if (!$this->isMorphsFIeld($field)){
                    $fields .= '                                    <th>' . ucfirst(translate_model_field($this->getRelationModelWithoutId($field['name']),$field['trans'] ?? null)) . '</th>' . "\n";
                }
            }else {
                $fields .= '                                    <th>' . ucfirst(translate_model_field($field['name'],$field['trans'] ?? null)) . '</th>' . "\n";
            }




            if ($this->checkTextFieldType($field['type'])){
                $values .= '                                        <td>{{ Str::limit($' . $var_name . '->' . $field['name'] . ') }}</td>' . "\n";
            } else {
                if ($this->isRelationField($field['type'])){
                    if (!$this->isMorphsFIeld($field)){
                        $values .= '                                        <td><a href="{{ route(\'admin.'. $this->getRelationModelWithoutId($field['name']) .'.show\',$'. $var_name .'->'. $this->getRelationModelWithoutId($field['name']) .') }}" classs="badge badge-secondary p-2">{{ $' . $var_name . '->' . $this->getRelationModelWithoutId($field['name']) . '->'. $this->getRelatedModelProperty($field) .' }}</a></td>' . "\n";
                    }

                }else {

                    $values .= '                                        <td>{{ $' . $var_name . '->' . $field['name'] . ' }}</td>' . "\n";
                }
            }

        }

        if (!$this->timestamps) {
            $fields .= '                                    <th>Date création</th>' . "\n";
            $values .= '                                        <td>{{ $' . $var_name . '->created_at->format(\'d/m/Y h:i\') }}</td>' . "\n";
        }


        return [$values, $fields];
    }




    /**
     * @param $fields
     * @param $complied
     * @param $values
     * @return string
     */
    private function insertFieldToViewIndex($fields, $complied, $values): string
    {
        $slug_mw_bait = '{{fields}}';
        $form = str_replace($slug_mw_bait, $fields, $complied);


        $slug_mw_bait = '{{values}}';
        $form = str_replace($slug_mw_bait, $values, $form);
        return $form;
    }

    /**
     * @param $data_map
     */
    private function registerLinkToLeftSidebar($data_map): void
    {
        $aside_stub = $this->TPL_PATH . '/views/_aside.blade.stub';
        $aside = resource_path('views/admin/partials/_aside.blade.php');
        $stub = file_get_contents($aside_stub);

        $complied = strtr($stub, $data_map);

        $search = '{{-- insert sidebar links here --}}';
        $form = str_replace($search, $complied . $search, file_get_contents($aside));
        file_put_contents($aside, $form);
    }

    /**
     * @param string|string[] $field
     * @return bool
     */
    private function checkTextFieldType($field) :bool
    {
        if ($this->isRelationField($field)) return false;

        return
            $field === 'text' || $field === 'mediumText' ||
            $field === 'longText';
    }

    /**
     * @param $data_map
     * @param $field
     * @param $complied
     * @param string $action
     * @return mixed
     */
    private function loadMorphsViewAndAssets($data_map, $field, $complied, string $action)
    {
        $map = $this->parseMorphsName($field);

        $partial_stub = $this->TPL_PATH . "/views/morphs/images/{$action}/{$action}.blade.stub";
        $partial_stub = file_get_contents($partial_stub);
        $partial = strtr($partial_stub, $data_map);
        $partial = strtr($partial, $map);
        $search = '{{-- add morphs image  here --}}';
        $complied = str_replace($search, $partial, $complied);

        $partial_stub = $this->TPL_PATH . "/views/morphs/images/{$action}/css.blade.stub";
        $partial_stub = file_get_contents($partial_stub);
        $partial = strtr($partial_stub, $data_map);
        $partial = strtr($partial, $map);
        $search = '{{-- add morphs image css here --}}';
        $complied = str_replace($search, $partial, $complied);

        $partial_stub = $this->TPL_PATH . "/views/morphs/images/{$action}/js.blade.stub";
        $partial_stub = file_get_contents($partial_stub);
        $partial = strtr($partial_stub, $data_map);
        $partial = strtr($partial, $map);
        $search = '{{-- add morphs image js here --}}';
        $complied = str_replace($search, $partial, $complied);


        return $complied;
    }

    /**
     * @param $data_map
     * @param $complied
     * @param string $action
     * @return mixed
     */
    private function loadImageViewsAndAssets($data_map, $complied, string $action)
    {
        $partial_stub = $this->TPL_PATH . "/views/images/{$action}/{$action}.blade.stub";
        $partial_stub = file_get_contents($partial_stub);
        $partial = strtr($partial_stub, $data_map);
        $search = '{{-- add standalone image  here --}}';
        $complied = str_replace($search, $partial, $complied);

        $partial_stub = $this->TPL_PATH . "/views/images/{$action}/css.blade.stub";
        $partial_stub = file_get_contents($partial_stub);
        $partial = strtr($partial_stub, $data_map);
        $partial = strtr($partial, $data_map);
        $search = '{{-- add standalone image css here --}}';
        $complied = str_replace($search, $partial, $complied);

        $partial_stub = $this->TPL_PATH . "/views/images/{$action}/js.blade.stub";
        $partial_stub = file_get_contents($partial_stub);
        $partial = strtr($partial_stub, $data_map);
        $partial = strtr($partial, $data_map);
        $search = '{{-- add standalone image js here --}}';
        $complied = str_replace($search, $partial, $complied);
        return $complied;
    }

}
