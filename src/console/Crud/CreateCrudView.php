<?php
namespace Guysolamour\Administrable\Console\Crud;




class CreateCrudView
{

    use MakeCrudTrait;
    /**
     * @var string
     */
    private $name;
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
     * @param string $name
     * @param array $fields
     * @param null|string $slug
     * @param bool $timestamps
     */
    public function __construct(string $name, array $fields, ?string $slug = null, bool $timestamps = false)
    {
        $this->name = $name;
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
        $data_map = $this->parseName($this->name);

        $guard = $data_map['{{pluralSlug}}'];

        $views_path = resource_path('views/admin');

        $views = [
            [
                'stub' => $this->TPL_PATH . '/views/index.blade.stub',
                'path' => $views_path . '/' . $guard . '/index.blade.php',
            ],
            [
                'stub' => $this->TPL_PATH . '/views/create.blade.stub',
                'path' => $views_path . '/' . $guard . '/create.blade.php',
            ],
            [
                'stub' => $this->TPL_PATH. '/views/edit.blade.stub',
                'path' => $views_path . '/' . $guard . '/edit.blade.php',
            ],

            [
                'stub' => $this->TPL_PATH . '/views/show.blade.stub',
                'path' => $views_path . '/' . $guard . '/show.blade.php',
            ],
        ];

        foreach ($views as $view) {
            $stub = file_get_contents($view['stub']);
            $complied = strtr($stub, $data_map);

            $dir = dirname($view['path']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // for index view
            $var_name = $data_map['{{singularSlug}}'];
            [$values, $fields] = $this->showIndexFields($var_name);
            $form = $this->insertFieldToViewIndex($fields, $complied, $values);

            // for show views
            $show_views = $this->showViewFields($var_name);
            $form = $this->insertFieldToViewSHow($show_views, $form);

            // write file
            file_put_contents($view['path'], $form);
        }

        // register sidebar link
        $this->registerLinkToLeftSidebar($data_map);

        return $views_path;
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
            $show_views .= '                <p><b>' . $this->slug . ':</b>{{ $' . $field_name . '->' . $this->slug . ' }}</p>' . "\n";
        }

        foreach ($this->fields as $field) {
//            if ($this->isRelationField($field)){
//
//            }else {
//
//            }
            $show_views .= '                <p><b>' . $field['name'] . ':</b>{{ $' . $field_name . '->' . $field['name'] . ' }}</p>' . "\n";
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

    /**
     * @param string $var_name
     * @return array
     * @internal param $data_map
     */
    private function showIndexFields(string $var_name): array
    {
        $values = '';

        $fields = '';

        if (!is_null($this->slug)) {
            $fields .= '                                    <th>' . ucfirst($this->slug) . '</th>' . "\n";
            $values .= '                                        <td>{{ $' . $var_name . '->' . $this->slug . ' }}</td>' . "\n";

        }

        foreach ($this->fields as $field) {

            if ($this->isRelationField($field['type'])){

                if (!$this->isMorphsFIeld($field)){
                    $fields .= '                                    <th>' . ucfirst($this->getRelationModelWithoutId($field['name'])) . '</th>' . "\n";
                }
            }else {
                $fields .= '                                    <th>' . ucfirst($field['name']) . '</th>' . "\n";
            }

            if ($this->checkTextFieldType($field['type'])){
                $values .= '                                        <td>{{ Str::limit($' . $var_name . '->' . $field['name'] . ') }}</td>' . "\n";
            } else {
                if ($this->isRelationField($field['type'])){
                    if (!$this->isMorphsFIeld($field)){
                        $values .= '                                        <td><a href="{{ route(\'admin.'. $this->getRelationModelWithoutId($field['name']) .'.show\',$'. $var_name .'->'. $this->getRelationModelWithoutId($field['name']) .') }}">{{ $' . $var_name . '->' . $this->getRelationModelWithoutId($field['name']) . '->'. $this->getRelatedModelProperty($field) .' }}</a></td>' . "\n";
                    }

                }else {

                    $values .= '                                        <td>{{ $' . $var_name . '->' . $field['name'] . ' }}</td>' . "\n";
                }
            }

        }

        if (!$this->timestamps) {
            $fields .= '                                    <th>Date ajout</th>' . "\n";
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
        $aside = resource_path('views/admin/adminlte/partials/_aside.blade.php');
        $stub = file_get_contents($aside_stub);
        $complied = strtr($stub, $data_map);

        $slug_mw_bait = '        </ul>' . "\n" . '    </section>';
        $form = str_replace($slug_mw_bait, $complied . $slug_mw_bait, file_get_contents($aside));
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
            $field === 'string' || $field === 'decimal' ||
            $field === 'double' || $field === 'float' ||
            $field === 'text' || $field === 'mediumText' ||
            $field === 'longText';
    }

}
