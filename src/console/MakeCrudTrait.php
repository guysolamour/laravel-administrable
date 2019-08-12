<?php
namespace Guysolamour\Admin\Console;


use Illuminate\Container\Container;

trait MakeCrudTrait
{

    protected   $TPL_PATH = __DIR__. '/../templates/crud';

    protected function parseName(string $name)
    {
        return $parsed = array(
            '{{namespace}}' => $this->getNamespace(),
            '{{pluralCamel}}' => str_plural(camel_case($name)),
            '{{pluralSlug}}' => str_plural(str_slug($name)),
            '{{pluralSnake}}' => str_plural(snake_case($name)),
            '{{pluralClass}}' => str_plural(studly_case($name)),
            '{{singularCamel}}' => str_singular(camel_case($name)),
            '{{singularSlug}}' => str_singular(str_slug($name)),
            '{{singularSnake}}' => str_singular(snake_case($name)),
            '{{singularClass}}' => str_singular(studly_case($name)),
        );
    }

    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    protected function getNamespace()
    {
        $namespace = Container::getInstance()->getNamespace();
        return rtrim($namespace, '\\');
    }

    /**
     * @param  string $path
     */
    private function createDirIfNotExists(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
