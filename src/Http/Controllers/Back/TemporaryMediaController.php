<?php

namespace Guysolamour\Administrable\Http\Controllers\Back;

use Illuminate\Http\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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


    public function getOption()
    {
        $key = config("administrable.modules.filemanager.temporary_model")::getMediaOptionsKey();

        return response()->json(json_decode(option_get($key), true));
    }

    public function store(MediaFormRequest $request)
    {
        $media = config("administrable.modules.filemanager.temporary_model")::store(
            $request->file('file')
        );

        return response()->json($media);
    }

    public function getTempFile(string $url): string
    {
        if (!$stream = @fopen($url, 'r')) {
            throw UnreachableUrl::create($url);
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'temporary-media');

        // return $temporaryFile;

        file_put_contents($temporaryFile, $stream);

        return $temporaryFile;
    }


    public function remote(Request $request)
    {
        $url = $request->input('url');

        abort_unless(
            Str::startsWith($url, $protocols =  ['http://', 'https://']),
            419,
            'The file url must start with ' . join(' or ', $protocols)
        );

        abort_unless(
            $stream = @fopen($url, 'r'),
            419,
            'The file can not be downloaded'
        );

        $temporaryFile = tempnam(sys_get_temp_dir(), 'temporary-media');

        file_put_contents($temporaryFile, $stream);

        $allowedMimeTypes = join(',', config("administrable.media.valid_mimetypes." . $request->input('type')));

        $validation = Validator::make(
            ['file' => new File($temporaryFile)],
            ['file' => 'mimetypes:' . $allowedMimeTypes]
        );

        if ($validation->fails()) {
            throw new \Exception(
                sprintf("File has a mime type of %s, while only %s are allowed", mime_content_type($temporaryFile), $allowedMimeTypes)
            );
        }

        $media = config("administrable.modules.filemanager.temporary_model")::store(
            new UploadedFile($temporaryFile, urldecode(basename(parse_url($url, PHP_URL_PATH))?: Str::random(40) ))
        );

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
