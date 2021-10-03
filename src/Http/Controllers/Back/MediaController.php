<?php

namespace Guysolamour\Administrable\Http\Controllers\Back;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Guysolamour\Administrable\Http\Requests\MediaFormRequest;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class MediaController extends BaseController
{
    public function index(string $model, int $id, string $collection)
    {
        $model = base64_decode($model)::find($id);
        $medias = $model->getMedia($collection);

        $ordered = $medias->sortBy(function ($media, $key) {
            return $media->getCustomProperty('order');
        });

        return $ordered->values()->all();
    }

    public function select(int $id)
    {
        /**
         * @var \Guysolamour\Administrable\Models\Media
         */
        $media = config("administrable.modules.filemanager.model")::findOrFail($id);

        /** @var \Guysolamour\Administrable\Traits\MediaableTrait */
        $model = $media->model;

        $collection_name = Str::before($media->collection_name, '-');

        $collection = $model->getMediaCollection($collection_name);


        if (!Arr::get($collection, 'multiple')) {
            $medias = $model->getMedia($media->collection_name);
            $medias->each->unSelect();
        }

        $media->select();

        return response()->json($media);
    }

    public function selectAll(string $model, int $id, string $collection)
    {
        /**
         * @var \Guysolamour\Administrable\Traits\MediaableTrait|string $model
         */
        $model = base64_decode($model)::find($id);

        $medias = $model->getMedia($collection);
        $medias->each->select();

        $medias;
    }

    public function unSelectAll(string $model, int $id, string $collection)
    {
        /**
         * @var \Guysolamour\Administrable\Traits\MediaableTrait|string $model
         */
        $model = base64_decode($model)::find($id);

        $medias = $model->getMedia($collection);
        $medias->each->unSelect();

        return $medias;
    }

    public function unselect(int $id)
    {
        /**
         * @var \Guysolamour\Administrable\Models\id
         */
        $media = config("administrable.modules.filemanager.model")::findOrFail($id);

        $media->unSelect();

        return response()->json($media);
    }

    public function rename(int $id, Request $request)
    {
        /**
         * @var \Guysolamour\Administrable\Models\Media
         */
        $media = config("administrable.modules.filemanager.model")::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string'
        ]);

        $media->name = $data['name'];
        $media->save();

        return response()->json($media);
    }

    public function store(string $model, int $id, string $collection, MediaFormRequest $request)
    {
        /**
         * @var \Guysolamour\Administrable\Traits\MediaableTrait|string $model
         */
        $model = base64_decode($model)::find($id);

        $media = $model
            ->addMediaFromRequest('file')
            ->sanitizingFileName(function ($fileName) {
                return str_replace(["'", " "], '', $fileName);
            })
            ->withCustomProperties([
                'order'  => (int) $request->get('order'),
                'select' => false,
            ])
            ->toMediaCollection($collection);

        return response()->json($media);
    }


    public function remote(string $model, int $id, string $collection, Request $request)
    {
        /**
         * @var \Guysolamour\Administrable\Traits\MediaableTrait|string $model
         */
        $model = base64_decode($model)::find($id);

        $media = $model
            ->addMediaFromUrl($request->input('url'), config("administrable.media.valid_mimetypes." . $request->input('type')))
            ->sanitizingFileName(function ($fileName) {
                return str_replace(["'", " "], '', $fileName);
            })
            ->withCustomProperties([
                'order'  => (int) $request->get('order'),
                'select' => false,
            ])
            ->toMediaCollection($collection);

        return response()->json($media);
    }

    public function modify(string $model, int $id, string $collection, int $mediaId, Request $request)
    {
        /**
         * @var \Guysolamour\Administrable\Models\Media
         */
        $media = config("administrable.modules.filemanager.model")::findOrFail($mediaId);

        /**
         * @var \Guysolamour\Administrable\Traits\MediaableTrait|string $model
         */
        $model = base64_decode($model)::find($id);

        $newMedia = $model
            ->addMediaFromBase64($request->input('file'))
            ->usingName($media->name)
            ->usingFileName($media->file_name)
            ->setOrder($media->order_column)
            ->withCustomProperties($media->custom_properties)
            ->toMediaCollection($collection)
            ;

        $media->delete();

        return $newMedia;
    }

    public function destroy(int $id)
    {
        /**
         * @var \Guysolamour\Administrable\Models\Media
         */
        $media = config("administrable.modules.filemanager.model")::findOrFail($id);

        $media->delete();

        return response()->json($media);
    }

    public function order(Request $request)
    {
        foreach ($request->get('ids') as $value) {
            config("administrable.modules.filemanager.model")::findOrFail($value['id'])
                ->setCustomProperty('order', $value['order'])
                ->save();
        }

        return response()->json(['success' => 'Media has been ordered']);
    }
}
