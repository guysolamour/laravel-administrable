<?php
namespace Guysolamour\Admin\Console;



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
        $this->fields = array_chunk($fields,3);
        $this->timestamps = $timestamps;
        $this->slug = $slug;
    }

    public static function generate(string $name, array $fields, ?string $slug = null, bool $timestamps = false)
    {
        return (new CreateCrudView($name,$fields,$slug,$timestamps))->loadViews();
    }

    public function loadViews()
    {
        $data_map = $this->parseName($this->name);

        $guard = $data_map['{{pluralSlug}}'];

        $views_path = resource_path('views/admin');

        $views = array(
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
        );



        foreach ($views as $view) {
            $stub = file_get_contents($view['stub']);
            $complied = strtr($stub, $data_map);

            $dir = dirname($view['path']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // for index view
            $values = '';
            $var_name = $data_map['{{singularSlug}}'];
            $fields = '';

            if (!is_null($this->slug)){
                $fields .= '                                    <th>'. ucfirst($this->slug) .'</th>' . "\n";
                $values .= '                                        <td>{{ $'. $var_name . '->' .$this->slug .' }}</td>' . "\n";

            }

            foreach ($this->fields as $field) {
                $fields .= '                                    <th>'. ucfirst($field[0]) .'</th>' . "\n";
            }





            foreach ($this->fields as $field) {
                if('string' === $field[1] || 'text' === $field[1])
                    $values .= '                                        <td>{{ Str::limit($'. $var_name . '->' .$field[0] .') }}</td>' . "\n";
                else
                    $values .= '                                        <td>{{ $'. $var_name . '->' .$field[0] .' }}</td>' . "\n";
            }

            if (!$this->timestamps) {
                $fields .= '                                    <th>Date ajout</th>' . "\n";
                $values .= '                                        <td>{{ $'. $var_name . '->created_at->format(\'d/m/Y h:i\') }}</td>' . "\n";
            }



            $slug_mw_bait = '{{fields}}';
            $form = str_replace($slug_mw_bait, $fields, $complied);

           // dd($form);

            $slug_mw_bait = '{{values}}';
            $form = str_replace($slug_mw_bait, $values, $form);

            // for show views
            $show_views = '';
            if (!is_null($this->slug)){
                $show_views .= '                <p><b>'. $this->slug .':</b>{{ $'. $var_name .'->'. $this->slug .' }}</p>'. "\n";

            }


            foreach ($this->fields as $field) {
                $show_views .= '                <p><b>'. $field[0] .':</b>{{ $'. $var_name .'->'. $field[0] .' }}</p>'. "\n";
            }



            if (!$this->timestamps) {
                $show_views .= '                <p><b>Date ajout:</b> {{ $'. $var_name .'->created_at->format(\'d/m/Y h:i\') }}</p>'. "\n";
            }

            //dd($form);

            $slug_mw_bait = '{{showView}}';
            $form = str_replace($slug_mw_bait, $show_views, $form);

            file_put_contents($view['path'], $form);


        }

        // register sidebar link
        $aside_stub = $this->TPL_PATH . '/views/_aside.blade.stub';
        $aside = resource_path('views/admin/adminlte/partials/_aside.blade.php');
        $stub = file_get_contents($aside_stub);
        $complied = strtr($stub, $data_map);

        $slug_mw_bait = '        </ul>' . "\n" . '    </section>';
        $form = str_replace($slug_mw_bait, $complied.$slug_mw_bait, file_get_contents($aside));
        file_put_contents($aside, $form);

        return $views_path;
    }
}
