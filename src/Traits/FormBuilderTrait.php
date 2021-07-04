<?php

namespace Guysolamour\Administrable\Traits;

use Illuminate\Support\Str;
use Kris\LaravelFormBuilder\Form;
use Kris\LaravelFormBuilder\FormBuilderTrait as FormBuilder;


trait FormBuilderTrait
{
    use FormBuilder;

    /**
     *
     * @param \Illuminate\Database\Eloquent\Model|\Spatie\LaravelSettings\Settings|string $model
     * @param string|null $form
     * @param boolean $withModel
     * @param boolean $withNamespace
     * @return Form
     */
    protected function getForm($model = null, ?string $form = null, bool $withModel = true, bool $withNamespace = true): Form
    {
        $form ??= $this->getFormName($withNamespace);

        $options = [];

        if ($withModel) {
            if (is_string($model)){
                $model = new $model;
            }
            else if (!$model) {
                $modelName = $this->getModelClassName();
                $model     = new $modelName();
            }

            $options['model'] = $model;
        }

        return $this->form($form, $options);
    }

    private function getModelClassName(): string
    {
        $model = $this->getModelName(true);

        return sprintf("%s\%s\%s", get_app_namespace(), config('administrable.models_folder'),$model);
    }

    /**
     * Get controller without App\Http\Controllers
     *
     * @return string
     */
    private function getControllerWithoutPrefixNamespace(): string
    {
        return  Str::afterLast(get_called_class(), 'Controllers\\');
    }

    private function getModelWithFullNamespace(string $class_name): string
    {
        return Str::after(Str::beforeLast($class_name, 'Controller'), '\\');
    }

    private function getModelWithoutNamespace(string $model): string
    {
        return Str::afterLast($model, '\\');
    }

    private function getModelName(bool $withNamespace = true): string
    {
        $controller = $this->getControllerWithoutPrefixNamespace();
        $modelWithNamespace = $this->getModelWithFullNamespace($controller);

        if ($withNamespace) {
            return $modelWithNamespace;
        }

        return $this->getModelWithoutNamespace($modelWithNamespace);
    }

    private function getFormName(bool $withNamespace = true): string
    {
        return sprintf(
            "%s\Forms\%s\%sForm",
            get_app_namespace(), config('administrable.back_namespace'), $this->getModelName($withNamespace)
        );
    }
}
