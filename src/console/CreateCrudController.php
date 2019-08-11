<?php
namespace Guysolamour\Admin\Console;


class CreateCrudController
{

    use MakeCrudTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * CreateCrudController constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function generate(string $name) :string
    {
        return (new CreateCrudController($name))
            ->loadController();
    }


    private function loadController() :string
    {
        try {

            $data_map = $this->parseName($this->name);

            $controller_name = $data_map['{{singularClass}}'];

            $controllers_path = app_path('/Http/Controllers/Admin');

            $controllers = array(
                [
                    'stub' => $this->TPL_PATH . '/controllers/Controller.stub',
                    'path' => $controllers_path . "/{$controller_name}Controller.php",
                ],
            );

            foreach ($controllers as $controller) {
                $stub = file_get_contents($controller['stub']);
                $complied = strtr($stub, $data_map);

                $dir = dirname($controller['path']);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                file_put_contents($controller['path'], $complied);
            }

            return $controllers_path;

        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }
    }
}
