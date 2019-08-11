<?php
namespace Guysolamour\Admin\Console;


class CreateCrudRoute
{

    use MakeCrudTrait;
    /**
     * @var string
     */
    private $name;

    /**
     * CreateCrudRoute constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function generate(string $name)
    {
        return (new CreateCrudRoute($name))
            ->loadRoutes();
    }

    protected function loadRoutes()
    {
        $data_map = $this->parseName($this->name);

        $routes_path = base_path('/routes/admin.php');
        $routes_stub = $this->TPL_PATH . '/routes/routes.stub';

        $routes = file_get_contents($routes_path);
        $stub = file_get_contents($routes_stub);
        $complied = strtr($stub, $data_map);

        $slug_mw_bait = '    });';
        $complied = str_replace($slug_mw_bait, $complied.$slug_mw_bait, $routes);

        file_put_contents($routes_path, $complied);

        return $routes_path;
    }
}
