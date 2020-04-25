<?php
namespace Guysolamour\Administrable\Console\Crud;

use Illuminate\Support\Str;


class CreateCrudBreadcumb
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
    /**
     * @var null|string
     */
    private $slug;
    /**
     * @var null|string
     */
    private $breadcrumb;


    /**
     * CreateCrudBreadcumb constructor.
     * @param string $name
     * @param array $fields
     * @param null|string $slug
     * @param null|string $breadcrumb
     */
    public function __construct(string $name, array $fields, ?string $slug = null, ?string $breadcrumb = null)
    {
        $this->name = $name;
        $this->fields = $fields;
        $this->slug = $slug;
        $this->breadcrumb = $breadcrumb;
    }

    public static function generate(string $name, array $fields, ?string $slug = null, ?string $breadcrumb = null) :string
    {
        return (new CreateCrudBreadcumb($name,$fields, $slug, $breadcrumb))
            ->loadBreadcumb();
    }

    private function loadBreadcumb() :string
    {
        $data_map = $this->parseName($this->name);

        $breadcrumb_path = base_path('/routes/breadcrumbs.php');
        $breadcrumb_stub = $this->TPL_PATH . '/routes/breadcrumbs.stub';

        $breadcrumb = file_get_contents($breadcrumb_path);
        $stub = file_get_contents($breadcrumb_stub);
        $complied = strtr($stub, $data_map);

        $slug_mw_bait = '*/';
        $complied = str_replace($slug_mw_bait, $slug_mw_bait .  $complied, $breadcrumb);

        file_put_contents($breadcrumb_path, $complied);

        return $breadcrumb_path;
    }

    private function parseName(string $name)
    {
        return array(
            '{{namespace}}' => $this->getNamespace(),
            '{{pluralCamel}}' => Str::plural(Str::camel($name)),
            '{{pluralSlug}}' => Str::plural(Str::slug($name)),
            '{{pluralSnake}}' => Str::plural(Str::snake($name)),
            '{{pluralClass}}' => Str::plural(Str::studly($name)),
            '{{singularCamel}}' => Str::singular(Str::camel($name)),
            '{{singularSlug}}' => Str::singular(Str::slug($name)),
            '{{singularSnake}}' => Str::singular(Str::snake($name)),
            '{{singularClass}}' => Str::singular(Str::studly($name)),
            '{{breadcrumbField}}' => $this->getBreadcrumbFieldToShow(),
        );
    }

    private function getBreadcrumbFieldToShow() :string
    {

        if (!empty($this->breadcrumb)) return $this->breadcrumb;

        // guest a default breadcrumb
        foreach ($this->fields as $field) {
            if (
                $this->getType($field['name']) === 'text' ||
                $this->getType($field['type'] === 'textarea')
            ) {
                return $field['name'];
            }
        }
    }

}