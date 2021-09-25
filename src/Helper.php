<?php

namespace Guysolamour\Administrable;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Guysolamour\Administrable\Models\Page;


class Helper {

    /**
     * @param string|null $attribute
     * @return mixed
     */
    public function getGuard(?string $attribute = null)
    {
        $guard = auth()->guard(config('administrable.guard'))->user();

        return is_null($attribute) ? $guard : $guard->$attribute;
    }

    /**
     *
     * @param boolean $include_super_guard
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGuardNotifiers(bool $include_super_guard = false)
    {
        /**
         * @var \Guysolamour\Administrable\Models\BaseModel
         */
        $model = get_guard_model_class();

        /**
         * @var \Illuminate\Database\Eloquent\Collection
         */
        $guards = $model::get();

        if (!$include_super_guard) {
            $guard = config('administrable.guard');
            $guards = $guards->filter(fn ($item) => !$item->hasRole('super-' . $guard, $guard));
        }

        return $guards;
    }

    public function redirectFrontroute($route, $parameters = [], $status = 302, $headers = [])
    {
        return redirect()->route(
            Str::start($route, Str::lower(config('administrable.front_namespace') . '.')),
            $parameters,
            $status,
            $headers
        );
    }

    public function redirectBackroute($route, $parameters = [], $status = 302, $headers = [])
    {
        return redirect()->route(
            Str::start($route, Str::lower(config('administrable.back_namespace') . '.')),
            $parameters,
            $status,
            $headers
        );
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSuperGuardNotifiers()
    {
        /**
         * @var \Guysolamour\Administrable\Models\BaseModel
         */
        $model = get_guard_model_class();

        /**
         * @var \Illuminate\Database\Eloquent\Collection
         */
        $guards = $model::get();

        $guard = config('administrable.guard');
        $guards = $guards->filter(fn ($item) => $item->hasRole('super-' . $guard, $guard));

        return $guards;
    }


    /**
     * @param string $dates
     * @return array|string
     */
    public function parseRangeDates(string $dates)
    {
        $formated_dates =  array_map(function ($date) {
            return \Carbon\Carbon::parse(str_replace('/', '-', str_replace(['AM', 'am', 'PM', 'pm'], '', $date)))->toDateTimeString();
        }, explode(' - ', $dates));

        return count($formated_dates) === 1 ? \Illuminate\Support\Arr::first($formated_dates) : $formated_dates;
    }


    private function getViewPath(string $view, string $prefix) :string
    {
        $prefix = Str::lower($prefix);

        if (!Str::startsWith($view . '.', $prefix)) {
            $view = $prefix .  Str::start($view, '.');
        }

        return $view;
    }


    public function backViewPath(string $view) :string
    {
        $view =  $this->getViewPath($view, config('administrable.back_namespace'));

        if (View::exists($view)) {
            return $view;
        }

        if (View::exists("administrable::{$view}")) {
            return "administrable::{$view}";
        }

        return $view;
    }

    public function frontViewPath(string $view) :string
    {
        $view =  $this->getViewPath($view, config('administrable.front_namespace'));

        if (View::exists($view)) {
            return $view;
        }

        if (View::exists("administrable::{$view}")) {
            return "administrable::{$view}";
        }

        return $view;
    }

    public function backView($view = null, $data = [], $mergeData = [])
    {
        $path = $this->backViewPath($view);

        return view($path, $data, $mergeData);
    }

    public function frontView($view = null, $data = [], $mergeData = [])
    {
        $path = $this->frontViewPath($view);

        return view($path, $data, $mergeData);
    }

    private function getRoutePath(string $route, string $prefix) :string
    {
        $prefix = Str::lower($prefix);

        if (!Str::startsWith($route . '.', $prefix)) {
            $route = $prefix .  Str::start($route, '.');
        }

        return $route;
    }

    public function backRoutePath(string $route): string
    {
        return $this->getRoutePath($route, config('administrable.back_namespace'));
    }

    public function frontRoutePath(string $route): string
    {
        return $this->getRoutePath($route, config('administrable.front_namespace'));
    }

    public function backRoute($route, $parameters = [], $absolute = true)
    {
        return route($this->backRoutePath($route), $parameters, $absolute);
    }

    public function frontRoute($route, $parameters = [], $absolute = true)
    {
        return route($this->frontRoutePath($route), $parameters, $absolute);
    }

    public function setActiveLink(...$routes) :string
    {
        foreach ($routes as $route) {
            if (Route::is($route)) {
                return 'active';
            }
        }
        return '';
    }

    public function getMetaTag(string $page_name, string $code, ?string $key = null)
    {
        $page = $this->getMetaPage($page_name);

        if (!$page) {
            return;
        }

        return $page->getTag($code, $key);
    }

    public function getMetaType(int $id): string
    {
        $types = Module::model('pagemeta')::TYPES;

        foreach ($types as $key => $type) {
            if ($type['value'] === $id) {
                return $key;
            }
        }
        return '';
    }

    public function formatPrice($price, string $suffix = '')
    {
        return number_format($price, 0, ',', ' ') . " {$suffix}";
    }

    public function getMetaPage(string $name) :?Page
    {
        return Module::model('page')::firstWhere('code', $name);
    }

    public function configuration(?string $attribute = null, string $default = null)
    {
        return $this->getSettings(config('administrable.modules.configuration.model'), $attribute, $default);
    }

    public function getSettings(string $class_name, ?string $attribute = null, $default = null)
    {
        $configuration = app($class_name);

        if (is_null($attribute)) {
            return $configuration;
        }

        if (property_exists($configuration, $attribute)) {
            return $configuration->$attribute;
        }

        return $default;
    }

    public function translateModelField(string $field_name, ?string $field_trans, ?string $locale = null, ?array $replace = []): string
    {
        if ($field_trans) {
            return $field_trans;
        }

        return $this->transFb("validation.attributes.{$field_name}", $field_name, $locale, $replace);
    }

    /**
     * Makes translation fall back to specified value if definition does not exist
     *
     * @return mixed
     */
    public function transFb(string $key, ?string $fallback = null, ?string $locale = null, ?array $replace = [])
    {
        if (Lang::has($key, $locale)) {
            return trans($key, $replace, $locale);
        }

        return $fallback;
    }

    public function strDeleteAllBetween(string $beginning, string $end, string $string): string
    {
        $beginningPos = strpos($string, $beginning);
        $endPos = strpos($string, $end);
        if ($beginningPos === false || $endPos === false) {
            return $string;
        }

        $textToDelete = substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos);

        return $this->strDeleteAllBetween($beginning, $end, str_replace($textToDelete, '', $string)); // recursion to ensure all occurrences are replaced
    }

    /**
     * @param object $model
     * @return string
     */
    public function getBase64encodeClass($model): string
    {
        return base64_encode(get_class($model));
    }

    public function getGuardModelClass() :string
    {
        return "\\" . $this->getAppNamespace() . "\\" . Str::ucfirst(config('administrable.models_folder')) . "\\" . Str::ucfirst(config('administrable.guard'));
    }

    /**
     * @param object $model
     * @return array
     */
    public function getCloneModelParams($model): array
    {
        return [
            $this->getBase64encodeClass($model),
            $model,
        ];
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return string
     */
    public function getFormClassName($model): string
    {
        $name = class_basename(get_class($model));

        return sprintf('\%s\Forms\%s\%sForm', config('administrable.back_namespace', 'Back'), get_app_namespace(), \Illuminate\Support\Str::singular(\Illuminate\Support\Str::studly($name)));
    }

    /**
     * get_form_name
     *
     * @param  object $model
     * @return string
     */
    public function getFormName($model): string
    {
        return 'entity-' .  strtolower(class_basename(($model)));
    }

    /**
     * @param \Illuminate\Support\Collection $collection
     * @param integer $limit
     * @return mixed
     */
    public function randomElements($collection, int $limit = 9)
    {
        $count = $collection->count();

        // if count is 0 return because there are no elements to random
        if (!$count) {
            return collect();
        }

        if ($count < $limit) {
            return $collection->random($count);
        }
        return $collection->random($limit);
    }

    /**
     *
     * @param string[] $collections
     * @return void
     */
    public function mergeCollections(...$collections)
    {
        $collect = collect();

        foreach ($collections as $collection) {
            if ($this->isCollection($collection)) {
                foreach ($collection as $model) {
                    $collect->push($model);
                }
            } else {
                $collect->push($collection);
            }
        }
        return $collect;
    }

    /**
     *
     * @param  object $data
     * @return bool
     */
    public function isCollection($data): bool
    {
        return $data instanceof \Illuminate\Database\Eloquent\Collection || $data instanceof \Illuminate\Support\Collection;
    }

    /**
     * Generate a route name for the previous request.
     *
     * @return string|null
     */
    public function previousRoute()
    {
        $previousRequest = app('request')->create(app('url')->previous());

        try {
            $routeName = app('router')->getRoutes()->match($previousRequest)->getName();
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {
            return null;
        }

        return $routeName;
    }

    /**
     *
     * @param array $array
     * @param mixed $value
     * @param boolean $return
     * @return null|array
     */
    public function arrayRemoveByValue(array $array, $value, bool $return = false)
    {
        if (($key = array_search($value, $array)) !== false) {
            unset($array[$key]);
        }

        if ($return) {
            return $array;
        }
    }

    /**
     * @param array $needles
     * @param array $haystack
     * @return boolean
     */
    public function multipleInArray(array $needles = [], array $haystack = []): bool
    {
        foreach ($needles as $needle) {
            if (!in_array($needle, $haystack)) {
                return false;
            }
        }

        return true;
    }

    public function filemanagerIsMultipleCollection(string $collection_name) :bool
    {
        $collection_name = Str::before($collection_name, '-');

        return config("administrable.media.collections.{$collection_name}.multiple", false);
    }

    public function filemanagerCollectionLabel(string $collection_name) :string
    {
        $collection_name = Str::before($collection_name, '-');

        return config("administrable.media.collections.{$collection_name}.description", '');
    }

    /**
     * Get project namespace
     * Default: App
     * @return string
     */
    public function getAppNamespace(): string
    {
        $namespace = \Illuminate\Container\Container::getInstance()->getNamespace();
        return rtrim($namespace, '\\');
    }

    public function getTemplatePath(): string
    {
        return dirname(__FILE__);
    }
}



