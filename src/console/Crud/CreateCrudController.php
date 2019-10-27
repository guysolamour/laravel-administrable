<?php
namespace Guysolamour\Administrable\Console\Crud;


class CreateCrudController
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
     * CreateCrudController constructor.
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
    public static function generate(string $model, array $fields) :string
    {
        return (new CreateCrudController($model, $fields))
            ->loadController();
    }


    /**
     * @return string
     */
    private function loadController() :string
    {
        $data_map = $this->parseName($this->model);

        $controller_name = $data_map['{{singularClass}}'];

        $controllers_path = app_path('/Http/Controllers/Admin');

        [$stub, $path] = $this->loadStubAndPath($controllers_path, $controller_name);

        $complied = $this->loadAndRegisterControllerStub($stub, $data_map);
        $this->createDirIfNotExists($path);

        $this->writeFile($path, $complied);

        return $controllers_path;

    }

    /**
     * @param $stub
     * @param $data_map
     * @return string
     */
    private function loadAndRegisterControllerStub($stub, $data_map): string
    {
        if (is_file($stub)){
            $stub = file_get_contents($stub);
        }
        $complied = strtr($stub, $data_map);
        return $complied;
    }

    /**
     * @param $controllers_path
     * @param $controller_name
     * @return array
     */
    private function loadStubAndPath($controllers_path, $controller_name): array
    {
        foreach ($this->fields as $field){
            if ($this->isMorphsFIeld($field)){

                if ($this->isImagesMorphRelation($field)){
                    $stub = $this->TPL_PATH . '/controllers/morphs/images/controller.stub';
                    $path = $controllers_path . "/{$controller_name}Controller.php";

                    $map = $this->parseMorphsName($field);


                    $stub = file_get_contents($stub);
                    $stub = strtr($stub, $map);

                    return [$stub, $path];
                }
            }
        }

        $stub = $this->TPL_PATH . '/controllers/Controller.stub';
        $path = $controllers_path . "/{$controller_name}Controller.php";

        return [$stub, $path];
    }
}
