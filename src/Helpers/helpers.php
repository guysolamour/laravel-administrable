<?php

use Illuminate\Support\Facades\DB;
use Guysolamour\Administrable\Facades\Helper;

if (!function_exists('get_meta_tag')) {
    function get_meta_tag(string $page_name, string $code, ?string $key = null)
    {
        return Helper::getMetaTag($page_name, $code, $key);
    }
}

if (!function_exists('get_meta_type')) {
    function get_meta_type(int $id)
    {
        return Helper::getMetaType($id);
    }
}

if (!function_exists('get_guard_model_class')) {
    function get_guard_model_class()
    {
        return Helper::getGuardModelClass();
    }
}

if (!function_exists('get_guard')) {
    function get_guard(?string $attribute = null)
    {
        return Helper::getGuard($attribute);
    }
}

if (!function_exists('get_guard_notifiers')) {
    function get_guard_notifiers(bool $include_super_guard = false)
    {
        return Helper::getGuardNotifiers($include_super_guard);
    }
}

if (!function_exists('get_super_guard_notifiers')) {
    function get_super_guard_notifiers()
    {
        return Helper::getSuperGuardNotifiers();
    }
}

if (!function_exists('random_elements')) {
    function random_elements($collection, int $limit = 9)
    {
        return Helper::randomElements($collection, $limit);
    }
}

if (!function_exists('redirect_frontroute')) {
    function redirect_frontroute($route, $parameters = [], $status = 302, $headers = [])
    {
        return Helper::redirectFrontroute($route, $parameters, $status, $headers);
    }
}

if (!function_exists('redirect_backroute')) {
    function redirect_backroute($route, $parameters = [], $status = 302, $headers = [])
    {
        return Helper::redirectBackroute($route, $parameters, $status, $headers);
    }
}

if (!function_exists('redirect_backroute')) {
    function formatPrice($price, string $suffix = '')
    {
        return Helper::formatPrice($price, $suffix);
    }
}


if (!function_exists('parse_range_dates')) {
    /**
     *
     * @param string $dates
     * @return mixed
     */
    function parse_range_dates(string $dates)
    {
        return Helper::parseRangeDates($dates);
    }
}



if (!function_exists('back_view')) {
    function back_view($view = null, $data = [], $mergeData = [])
    {
        return Helper::backView($view, $data, $mergeData);
    }
}

if (!function_exists('back_view_path')) {
    function back_view_path($view = null, $data = [], $mergeData = [])
    {
        return Helper::backViewPath($view, $data, $mergeData);
    }
}

if (!function_exists('front_view')) {
    function front_view($view = null, $data = [], $mergeData = [])
    {
        return Helper::frontView($view, $data, $mergeData);
    }
}

if (!function_exists('front_view_path')) {
    function front_view_path($view = null, $data = [], $mergeData = [])
    {
        return Helper::frontViewPath($view, $data, $mergeData);
    }
}


if (!function_exists('back_route')) {
    function back_route($name, $parameters = [], $absolute = true)
    {
        return Helper::backRoute($name, $parameters, $absolute);
    }
}

if (!function_exists('back_route_path')) {
    function back_route_path($name, $parameters = [], $absolute = true)
    {
        return Helper::backRoutePath($name, $parameters, $absolute);
    }
}

if (!function_exists('front_route')) {
    function front_route($name, $parameters = [], $absolute = true)
    {
        return Helper::frontRoute($name, $parameters, $absolute);
    }
}

if (!function_exists('front_route_path')) {
    function front_route_path($name, $parameters = [], $absolute = true)
    {
        return Helper::frontRoutePath($name, $parameters, $absolute);
    }
}


if (!function_exists('set_active_link')) {
    function set_active_link(...$routes)
    {
        return Helper::setActiveLink($routes);
    }
}

if (!function_exists('get_meta_page')) {
    function get_meta_page(string $name)
    {
        return Helper::getMetaPage($name);
    }
}

if (!function_exists('configuration')) {
    function configuration(?string $attribute = null, string $default = null)
    {
        return Helper::configuration($attribute, $default);
    }
}

if (!function_exists('get_settings')) {
    function get_settings(string $class_name, ?string $attribute = null, $default = null)
    {
        return Helper::getSettings($class_name, $attribute, $default);
    }
}


if (!function_exists('translate_model_field')) {
    /**
     * @return string
     */
    function translate_model_field(string $field_name, ?string $field_trans, ?string $locale = null, ?array $replace = [])
    {
        return Helper::translateModelField($field_name, $field_trans, $locale, $replace);
    }
}

if (!function_exists('trans_fb')) {
    /**
     * Makes translation fall back to specified value if definition does not exist
     *
     * @return mixed
     */
    function trans_fb(string $key, ?string $fallback = null, ?string $locale = null, ?array $replace = [])
    {
        return Helper::transFb($key, $fallback, $locale, $replace);
    }
}

if (!function_exists('delete_all_between')) {
    /**
     * @param string $beginning
     * @param string $end
     * @param string $string
     * @return string
     */
    function delete_all_between(string $beginning, string $end, string $string)
    {
        return Helper::strDeleteAllBetween($beginning, $end, $string);
    }

}

if (!function_exists('get_base64encode_class')) {
    /**
     * @param object $model
     * @return string
     */
    function get_base64encode_class($model)
    {
        return Helper::getBase64encodeClass($model);
    }
}

if (!function_exists('get_clone_model_params')) {
    /**
     * @param object $model
     * @return array
     */
    function get_clone_model_params($model)
    {
        return Helper::getCloneModelParams($model);
    }
}

if (!function_exists('get_form_class_name')) {
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return string
     */
    function get_form_class_name($model)
    {
        return Helper::getFormClassName($model);
    }
}

if (!function_exists('get_form_name')) {
    /**
     * get_form_name
     *
     * @param  obje t $model
     * @return string
     */
    function get_form_name($model)
    {
        return Helper::getFormName($model);
    }
}

if (!function_exists('random_element')) {
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param integer $limit
     * @return mixed
     */
    function random_element($model, int $limit = 9)
    {
        return Helper::randomElement($model, $limit);
    }
}

if (!function_exists('merge_collections')) {
    /**
     * merge_collections
     *
     * @param  string[] $collections
     * @return void
     */
    function merge_collections(...$collections)
    {
        return Helper::mergeCollections($collections);
    }
}

if (!function_exists('filemanager_is_multiple_collection')) {
    /**
     *
     * @param  string $collection_name
     * @return bool
     */
    function filemanager_is_multiple_collection($collection_name)
    {
        return Helper::filemanagerIsMultipleCollection($collection_name);
    }
}

if (!function_exists('filemanager_collection_label')) {
    /**
     *
     * @param  mixed $collection_name
     * @return string
     */
    function filemanager_collection_label(string $collection_name)
    {
        return Helper::filemanagerCollectionLabel($collection_name);
    }
}

if (!function_exists('is_collection')) {
    /**
     *
     * @param  mixed $data
     * @return bool
     */
    function is_collection($data)
    {
        return Helper::isCollection($data);
    }
}

if (!function_exists('previous_route')) {
    /**
     * Generate a route name for the previous request.
     *
     * @return string|null
     */
    function previous_route()
    {
        return Helper::previousRoute();
    }
}

if (!function_exists('array_remove_by_value')) {
    /**
     *
     * @param array $array
     * @param mixed $value
     * @param boolean $return
     * @return null|array
     */
    function array_remove_by_value(array $array, $value, bool $return = false)
    {
        return Helper::arrayRemoveByValue($array, $value, $return);
    }
}

if (!function_exists('multiple_in_array')) {
    /**
     * @param array $needles
     * @param array $haystack
     * @return boolean
     */
    function multiple_in_array(array $needles = [], array $haystack = [])
    {
       return Helper::multipleInArray($needles, $haystack);
    }
}

if (!function_exists('get_app_namespace')) {

    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    function get_app_namespace()
    {
        return Helper::getAppNamespace();
    }
}

if (!function_exists('get_template_path')) {

    function get_template_path()
    {
        return Helper::getTemplatePath();
    }
}

if (!function_exists('option_create')) {
    /**
     *
     * @param string|array $name
     * @param string $value
     * @return stdClass
     */
    function option_create(string $name, string $value)
    {
        $values = (is_string($name) && $value) ? ['name' => $name, 'value' => $value] : $name;
        $id = DB::table('options')->insertGetId($values);

        return DB::table('options')->find($id);
    }
}

if (!function_exists('option_create_many')) {
    /**
     *
     * @param string|array $name
     * @param string $value
     * @return void
     */
    function option_create_many(array $data)
    {
        foreach ($data as $key => $value) {
            option_edit($key, $value);
        }
    }
}

if (!function_exists('option_set')) {
    /**
     *
     * @param string|array $name
     * @param string|null $value
     * @return stdClass
     */
    function option_set($name, $value = null)
    {
        return option_create($name, $value);
    }
}

if (!function_exists('option_get')) {
    /**
     *
     * @param string $name
     * @param string|null $value
     * @return string
     */
    function option_get(string $name, ?string $default = null)
    {
        $option = option_fetch($name);
        return is_null($option) ? $default : $option->value;
    }
}

if (!function_exists('option_fetch')) {
    /**
     * @param string $name
     * @return stdClass|null
     */
    function option_fetch(string $name)
    {
        return DB::table('options')->where('name', $name)->first();
    }
}

if (!function_exists('option_edit')) {
    /**
     * @param string $name
     * @param string $value
     * @return stdClass|null
     */
    function option_edit(string $name, string $value): string
    {
        if (is_array($name)) {
            $index = array_key_first($name);
            $value = $name[$index];
            $name  = $index;
        }

        DB::table('options')->updateOrInsert(['name' => $name], ['value' => $value]);

        return option_get($name);
    }
}

if (!function_exists('option_remove')) {
    /**
     * @param string $name
     * @return int
     */
    function option_remove(string $name)
    {
        return DB::table('options')->where('name', $name)->delete();
    }
}

if (!function_exists('option_delete')) {
    /**
     * @param string $name
     * @return int
     */
    function option_delete(string $name)
    {
        return option_remove($name);
    }
}




