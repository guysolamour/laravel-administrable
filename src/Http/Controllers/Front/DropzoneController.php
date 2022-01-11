<?php

namespace Guysolamour\Administrable\Http\Controllers\Front;

use Illuminate\Http\Request;
use Guysolamour\Administrable\Http\Controllers\BaseController;

class DropzoneController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->abortIfUserOrAdminIsNotAuthenticated();
    }

    public function store(string $model, int $id, string $collection) {
        /**
         * @var \Spatie\MediaLibrary\InteractsWithMedia|string $model
         */
        $model = base64_decode($model)::find($id);

        $media = $model
            ->addMediaFromRequest('file')
            ->sanitizingFileName(function ($fileName) {
                return str_replace(["'", " "], '', $fileName);
            })
            ->withCustomProperties([
                'select' => true,
            ])
            ->toMediaCollection($collection);

        return response()->json($media);
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

    public function storeTemporaryMedia(string $model, string $collection, string $path, Request $request)
    {
        $path  = base64_decode($path);
        $model = base64_decode($model);

        $request->merge([
            'collection' => $collection,
            'model'      => $model,
            'path'       => $path,
        ]);

        $file = $request->file('file')[0];

        $media = config("administrable.modules.filemanager.temporary_model")::storeMedia(
            $file,
            config("administrable.modules.filemanager.temporary_model")::storeUploadFileOnDisk($file),
            $collection,
            $model,
            true
        );

        return $media;
    }

    public function destoryTemporaryMedia(int $id)
    {
        /**
         * @var \Guysolamour\Administrable\Models\TemporaryMedia
         */
        $temporaryMedia = config("administrable.modules.filemanager.temporary_model")::findOrFail($id);

        $temporaryMedia->delete();

        return response()->json($temporaryMedia);
    }

    private function abortIfUserOrAdminIsNotAuthenticated()
    {
        abort_unless(!(get_admin() && get_user()),403, 'Unauthenticated');
    }
}
