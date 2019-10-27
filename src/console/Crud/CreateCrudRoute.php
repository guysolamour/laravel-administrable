<?php
namespace Guysolamour\Administrable\Console\Crud;


class CreateCrudRoute
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
     * CreateCrudRoute constructor.
     * @param string $model
     * @param array $fields
     */
    public function __construct(string $model, array $fields)
    {
        $this->model = $model;
        $this->fields = $fields;
    }

    /**
     * @param string $model
     * @param array $fields
     * @return string
     */
    public static function generate(string $model, array $fields)
    {
        return (new CreateCrudRoute($model, $fields))
            ->loadRoutes();
    }

    /**
     * @return string
     */
    protected function loadRoutes() :string
    {
        $data_map = $this->parseName($this->model);

        $routes_path = base_path('/routes/admin.php');
        $routes_stub = $this->TPL_PATH . '/routes/routes.stub';

        $complied = $this->loadAndRegisterRoutes($routes_path, $routes_stub, $data_map);

        file_put_contents($routes_path, $complied);

        return $routes_path;
    }

    /**
     * @param $routes_path
     * @param $routes_stub
     * @param $data_map
     * @return mixed|string
     */
    protected function loadAndRegisterRoutes($routes_path, $routes_stub, $data_map)
    {
        $routes = file_get_contents($routes_path);
        $stub = file_get_contents($routes_stub);
        $complied = strtr($stub, $data_map);

        // add others routes if morphsImageField
        foreach ($this->fields as $field){
            if ($this->isMorphsFIeld($field)){
                if ($this->isImagesMorphRelation($field)){

                    $map = $this->parseMorphsName($field);

                    $partial_stub  = $this->TPL_PATH . '/routes/morphs/images/routes.stub';
                    $partial_stub = file_get_contents($partial_stub);
                    $partial = strtr($partial_stub, $data_map);
                    $partial = strtr($partial, $map);

                    $search = "// {$data_map['{{pluralClass}}']}";
                    $complied = str_replace($search, $search . $partial, $complied);


                }
            }
        }

        $search = '    });';
        $complied = str_replace($search, $complied . $search, $routes);
        return $complied;
    }
}
