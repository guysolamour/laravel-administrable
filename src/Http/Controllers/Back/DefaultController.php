<?php

namespace Guysolamour\Administrable\Http\Controllers\Back;


use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Guysolamour\Administrable\Traits\FormBuilderTrait;
use Guysolamour\Administrable\Http\Controllers\BaseController;


class DefaultController extends BaseController
{
    use FormBuilderTrait;


    public function destroyModels(Request $request)
    {
        $request->get('model')::destroy($request->get('ids'));

        flashy('Les élements sélectionnés ont été supprimés');

        return response()->json();
    }

    public function clone(string $model, $key)
    {
        $model = base64_decode($model);
        /**
         * @var \Illuminate\Database\Eloquent\Model $model
         */
        $model = (new $model())->resolveRouteBinding($key);

        $form_name = $model->getRelatedForm();

        // View
        $views_folder   = $model->getViewsFolder();
        $view_segments = Str::afterLast(Str::beforeLast($form_name, '\\'), 'Forms\\');
        $view_segments = Str::lower(str_replace('\\', '.', $view_segements));

        $view = "administrable::{$view_segments}.{$views_folder}.create";

        if (!class_exists($form_name)) {
            return view($view);
        }

        $form = $this->getForm($model->replicate(), $form_name);

        return view($view, compact('form'));
    }
}
