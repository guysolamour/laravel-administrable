<?php


if (!function_exists('translate_model_field')) {
    /**
     * @param string $field_name
     * @param string|null $field_trans
     * @param string|null $locale
     * @param array|null $replace
     * @return string
     */
    function translate_model_field(string $field_name, ?string $field_trans, ?string $locale = null, ?array $replace = []): string
    {
        if ($field_trans) {
            return $field_trans;
        }

        return trans_fb("validation.attributes.{$field_name}", $field_name, $locale, $replace);
    }
}

if (!function_exists('trans_fb')) {
    /**
     * Makes translation fall back to specified value if definition does not exist
     *
     * @param string $key
     * @param null|string $fallback
     * @param null|string $locale
     * @param array|null $replace
     *
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    function trans_fb(string $key, ?string $fallback = null, ?string $locale = null, ?array $replace = [])
    {
        if (\Illuminate\Support\Facades\Lang::has($key, $locale)) {
            return trans($key, $replace, $locale);
        }

        return $fallback;
    }
}


if (!function_exists('delete_all_between')) {
    /**
     * @param string $beginning
     * @param string $end
     * @param string $string
     * @return string
     */
    function delete_all_between(string $beginning, string $end, string $string): string
    {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }

        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

        return delete_all_between($beginning, $end, str_replace($textToDelete, '', $string)); // recursion to ensure all occurrences are replaced
    }

}

if (!function_exists('create_zip_archive_from_folder')) {
    /**
     *
     * @param string $filePath
     * @param string $folderPath
     * @return string
     */
    function create_zip_archive_from_folder(string $filePath, string $folderPath): string
    {
        $zip = new \ZipArchive();


        if ($zip->open($filePath, \ZipArchive::CREATE) === TRUE) {
            $files = Illuminate\Support\Facades\File::allFiles($folderPath);

            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }

            $zip->close();
        }

        return $filePath;
    }
}


if (!function_exists('get_base64encode_class')) {
    /**
     * @param object $model
     * @return string
     */
    function get_base64encode_class($model): string
    {
        return base64_encode(get_class($model));
    }
}


if (!function_exists('get_clone_model_params')) {
    /**
     * @param object $model
     * @return array
     */
    function get_clone_model_params($model): array
    {
        return [
            get_base64encode_class($model),
            $model,
        ];
    }
}

if (!function_exists('get_form_class_name')) {
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return string
     */
    function get_form_class_name($model): string
    {
        $name = class_basename(get_class($model));

        return sprintf('\%s\Forms\%s\%sForm', config('administrable.back_namespace', 'Back') ,get_app_namespace() ,\Illuminate\Support\Str::singular(\Illuminate\Support\Str::studly($name)));
    }
}

if (!function_exists('get_form_name')) {
    /**
     * get_form_name
     *
     * @param  obje t $model
     * @return string
     */
    function get_form_name($model): string
    {
        return 'entity-' .  strtolower(class_basename(($model)));
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
        $count = $model->count();

        // if count is 0 return because there are no elements to random
        if (!$count) {
            return collect();
        }

        if ($count < $limit) {
            return $model->random($count);
        }
        return $model->random($limit);
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
        $collect = collect();

        foreach ($collections as $collection) {
            if (is_collection($collection)) {
                foreach ($collection as $model) {
                    $collect->push($model);
                }
            } else {
                $collect->push($collection);
            }
        }
        return $collect;
    }
}

if (!function_exists('is_collection')) {
    /**
     * is_collection
     *
     * @param  mixed $data
     * @return bool
     */
    function is_collection($data): bool
    {
        return $data instanceof Illuminate\Database\Eloquent\Collection || $data instanceof Illuminate\Support\Collection;
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
        $previousRequest = app('request')->create(app('url')->previous());

        try {
            $routeName = app('router')->getRoutes()->match($previousRequest)->getName();
        } catch (Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {
            return null;
        }

        return $routeName;
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
        if (($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }

        if ($return) {
            return $array;
        }
    }
}

if (!function_exists('multiple_in_array')) {
    /**
     * @param array $needles
     * @param array $haystack
     * @return boolean
     */
    function multiple_in_array(array $needles = [], array $haystack = []) :bool
    {
       foreach ($needles as $needle ) {
           if (!in_array($needle, $haystack)){
               return false;
           }
       }

       return true;
    }
}


if (!function_exists('guest_views_folder_name')) {
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return string
     */
    function guest_views_folder_name($model): string
    {
        return \Illuminate\Support\Str::plural(\Illuminate\Support\Str::slug(class_basename($model)));
    }
}


if (!function_exists('get_app_namespace')) {

    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    function get_app_namespace() :string
    {
        $namespace = \Illuminate\Container\Container::getInstance()->getNamespace();
        return rtrim($namespace, '\\');
    }
}

if (!function_exists('get_template_path')) {

    
    function get_template_path() :string
    {
        return dirname(__FILE__);
    }
}

