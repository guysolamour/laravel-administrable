<?php

namespace Guysolamour\Administrable\Http\Controllers\Back;


use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;


class DefaultController extends BaseController
{
    use FormBuilderTrait;


    public function destroyModels(Request $request)
    {
        $request->get('model')::destroy($request->get('ids'));

        flashy(Lang::get('administrable::messages.default.destroymodels'));

        return response()->json();
    }

    public function clone(string $model, $key)
    {
        $model = base64_decode($model);

        /**
         * @var \Illuminate\Database\Eloquent\Model|\Guysolamour\Administrable\Traits\ModelTrait $model
         */
        $model = (new $model())->resolveRouteBinding($key);

        $form_name = $model->getRelatedForm();

        $view = Str::lower(str_replace('\\', '/', Str::afterLast(get_class($model), 'Models\\')));

        if (Str::contains($view, '/')){
            $view = Str::beforeLast($view, '/') . '/' . Str::plural(Str::afterLast($view, '/'));
        } else {
            $view = Str::plural($view);
        }

        $view = $view . '/create';

        return class_exists($form_name)
                    ? back_view($view, ['form' => $this->getForm($model->replicate(), $form_name)])
                    : back_view($view);
    }

}
