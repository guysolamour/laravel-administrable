<?php

namespace Guysolamour\Administrable\Http\Controllers\Back;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Guysolamour\Administrable\Http\Requests\MediaFormRequest;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class TemporaryMediaController extends BaseController
{

    public function index(string $collection, Request $request)
    {
        /**
         * @var \Illuminate\Database\Eloquent\Collection
         */
        $medias = config("administrable.modules.filemanager.temporary_model")::where(
            'collection_name', $collection
        )->whereIn('id', $request->input('keys'))->get();

        return $medias->sortBy(fn ($media, $key) => $media->getCustomProperty('order'))->values()->all();
    }


    public function getOption(Request $request)
    {
        $option = json_decode(option_get('filemanager' . $request->input('collection')), true);

        return response()->json($option);
    }

    public function store(MediaFormRequest $request)
    {
        $uploaded_file = $request->file('file');

        $url = $uploaded_file->storeAs(
            config('administrable.media.temporary_files.folder'),
            config("administrable.modules.filemanager.temporary_model")::getUploadedFileName($uploaded_file, true),
            'public'
        );

        $media = new (config("administrable.modules.filemanager.temporary_model"));

        $media->name            = config("administrable.modules.filemanager.temporary_model")::getUploadedFileNameWithoutExtension($uploaded_file);
        $media->file_name       = config("administrable.modules.filemanager.temporary_model")::getUploadedFileName($uploaded_file);
        $media->collection_name = $request->input('collection');
        $media->url             = $url;
        $media->mime_type       = $uploaded_file->getMimeType();
        $media->size            = $uploaded_file->getSize();
        $media->model           = $request->input('model');
        $media->withCustomProperties([
            'order'  => (int) $request->input('order'),
            'select' => (bool) config('administrable.media.select_uploaded_file'),
        ]);

        $media->save();

        return response()->json($media);
    }

    public function rename(int $id, Request $request)
    {
        $temporaryMedia = config("administrable.modules.filemanager.temporary_model")::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string'
        ]);

        $temporaryMedia->name = $data['name'];
        $temporaryMedia->save();

        return response()->json($temporaryMedia);
    }

    public function select(int $id)
    {
        /**
         * @var \Guysolamour\Administrable\Models\TemporaryMedia
         */
        $temporaryMedia = config("administrable.modules.filemanager.temporary_model")::findOrFail($id);

        /** @var \Guysolamour\Administrable\Traits\MediaableTrait */
        $model = new ($temporaryMedia->model);

        $collection_name = Str::before($temporaryMedia->collection_name, '-');

        $collection = $model->getMediaCollection($collection_name);

        if (!Arr::get($collection, 'multiple')) {
            // voir cette partie
            $medias = $model->getMedia($temporaryMedia->collection_name);

            $medias->each->unSelect();
        }

        $temporaryMedia->select();

        return response()->json($temporaryMedia);
    }

    public function unSelectAll(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required'
        ]);

        $medias = config("administrable.modules.filemanager.temporary_model")::whereIn('id', $data['ids'])->get()->each->unSelect();

        return response()->json($medias);
    }

    public function selectAll(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required'
        ]);

        $medias = config("administrable.modules.filemanager.temporary_model")::whereIn('id', $data['ids'])->get()->each->select();

        return response()->json($medias);
    }

    public function unselect(int $id)
    {
        /**
         * @var \Guysolamour\Administrable\Models\TemporaryMedia
         */
        $temporaryMedia = config("administrable.modules.filemanager.temporary_model")::findOrFail($id);

        $temporaryMedia->unSelect();

        return response()->json($temporaryMedia);
    }

    public function modify(int $id, Request $request)
    {
        /**
         * @var \Guysolamour\Administrable\Models\TemporaryMedia
         */
        $temporaryMedia = config("administrable.modules.filemanager.temporary_model")::findOrFail($id);

        $base64data = $request->input('file');
        // strip out data uri scheme information (see RFC 2397)
        if (strpos($base64data, ';base64') !== false) {
            [$_, $base64data] = explode(';', $base64data);
            [$_, $base64data] = explode(',', $base64data);
        }

        // strict mode filters for non-base64 alphabet characters
        $binaryData = base64_decode($base64data, true);

        if (false === $binaryData){
            throw new \Exception("Invalid base 64");
        }

        Storage::disk('public')->put(
            $temporaryMedia->getRawOriginal('url') ,
            $binaryData
        );

        $temporaryMedia->update([
            'size' => Storage::disk('public')->size($temporaryMedia->getRawOriginal('url'))
        ]);

        return $temporaryMedia;
    }

    public function destroy(int $id)
    {
        /**
         * @var \Guysolamour\Administrable\Models\TemporaryMedia
         */
        $temporaryMedia = config("administrable.modules.filemanager.temporary_model")::findOrFail($id);

        $temporaryMedia->delete();

        return response()->json($temporaryMedia);
    }

    public function order(Request $request)
    {
        foreach ($request->get('ids') as $value) {
            config("administrable.modules.filemanager.temporary_model")::findOrFail($value['id'])
                ->setCustomProperty('order', $value['order'])
                ->save();
        }

        return response()->json(['success' => 'Media has been ordered']);
    }
}
