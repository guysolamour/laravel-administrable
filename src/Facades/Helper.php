<?php

namespace Guysolamour\Administrable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Helper
 *
 * @method static \Guysolamour\Administrable\Helper translateModelField(string $field_name, ?string $field_trans, ?string $locale = null, ?array $replace = [])
 * @method static \Guysolamour\Administrable\Helper transFb(string $key, ?string $fallback = null, ?string $locale = null, ?array $replace = [])
 * @method static \Guysolamour\Administrable\Helper strDeleteAllBetween(string $beginning, string $end, string $string)
 * @method static \Guysolamour\Administrable\Helper getBase64encodeClass($model)
 * @method static \Guysolamour\Administrable\Helper getCloneModelParams($model)
 * @method static \Guysolamour\Administrable\Helper getFormClassName($model)
 * @method static \Guysolamour\Administrable\Helper getFormName($model)
 * @method static \Guysolamour\Administrable\Helper randomElements(\Illuminate\Support\Collection $collection, int $limit = 9)
 * @method static \Guysolamour\Administrable\Helper mergeCollections(...$collections)
 * @method static \Guysolamour\Administrable\Helper isCollection($data)
 * @method static \Guysolamour\Administrable\Helper previousRoute()
 * @method static \Guysolamour\Administrable\Helper arrayRemoveByValue(array $array, $value, bool $return = false)
 * @method static \Guysolamour\Administrable\Helper multipleInArray(array $needles = [], array $haystack = [])
 * @method static \Guysolamour\Administrable\Helper getAppNamespace()
 * @method static \Guysolamour\Administrable\Helper getTemplatePath()
 * @method static \Guysolamour\Administrable\Helper configuration(?string $attribute = null, string $default = null)
 * @method static \Guysolamour\Administrable\Helper getMetaPage(string $name)
 * @method static \Guysolamour\Administrable\Helper getMetaTag(string $page_name, string $code, ?string $key = null)
 * @method static \Guysolamour\Administrable\Helper getMetaType(int $id)
 * @method static \Guysolamour\Administrable\Helper setActiveLink(...$routes)
 * @method static \Guysolamour\Administrable\Helper backRoutePath(string $route)
 * @method static \Guysolamour\Administrable\Helper frontRoutePath(string $route)
 * @method static \Guysolamour\Administrable\Helper backRoute($name, $parameters = [], $absolute = true)
 * @method static \Guysolamour\Administrable\Helper frontRoute($name, $parameters = [], $absolute = true)
 * @method static \Guysolamour\Administrable\Helper getGuardModelClass()
 * @method static \Guysolamour\Administrable\Helper getGuard()
 * @method static \Guysolamour\Administrable\Helper getGuardNotifiers(bool $include_super_guard)
 * @method static \Guysolamour\Administrable\Helper getSuperGuardNotifiers()
 * @method static \Guysolamour\Administrable\Helper backViewPath(string $view)
 * @method static \Guysolamour\Administrable\Helper frontViewPath(string $view)
 * @method static \Guysolamour\Administrable\Helper backView($view = null, $data = [], $mergeData = [])
 * @method static \Guysolamour\Administrable\Helper frontView($view = null, $data = [], $mergeData = [])
 * @method static \Guysolamour\Administrable\Helper filemanagerIsMultipleCollection(string $collection_name)
 * @method static \Guysolamour\Administrable\Helper filemanagerCollectionLabel(string $collection_name)
 *
 */
class Helper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'administrable-helper';
    }
}
