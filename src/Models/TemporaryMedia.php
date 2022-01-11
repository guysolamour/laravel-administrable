<?php

namespace Guysolamour\Administrable\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Guysolamour\Administrable\Traits\HasMediaTrait;

class TemporaryMedia extends Model
{
    use HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'file_name', 'url', 'collection_name', 'custom_properties', 'mime_type', 'size'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['select', 'order', 'date_for_humans'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'size'               => 'int',
        'custom_properties'  => 'array',
    ];


    public function getUrlAttribute($value)
    {
        return  url(Storage::url($value));
    }


    /**
     * Get the value of custom property with the given name.
     *
     * @param string $propertyName
     * @param mixed $default
     *
     * @return mixed
     */
    public function getCustomProperty(string $propertyName, $default = null)
    {
        return Arr::get($this->custom_properties, $propertyName, $default);
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    public function setCustomProperty(string $name, $value): self
    {
        $customProperties = $this->custom_properties;

        Arr::set($customProperties, $name, $value);

        $this->custom_properties = $customProperties;

        return $this;
    }

    public function forgetCustomProperty(string $name): self
    {
        $customProperties = $this->custom_properties;

        Arr::forget($customProperties, $name);

        $this->custom_properties = $customProperties;

        return $this;
    }

    public function withCustomProperties(array $customProperties): self
    {
        $this->custom_properties = $customProperties;

        return $this;
    }

    /**
     * @param \Illuminate\Http\UploadedFile $uploaded_file
     * @return string
     */
    public static function storeUploadFileOnDisk(UploadedFile $uploaded_file)
    {
        return $uploaded_file->storeAs(
            config('administrable.media.temporary_files.folder'),
            static::getUploadedFileName($uploaded_file, true),
            'public'
        );
    }

    public static function storeMedia(UploadedFile $uploaded_file, string $url, ?string $collection = null, ?string $model = null, bool $select = false) :self
    {
        /**
         * @var self
         */
        $media = new static();

        $media->name            = static::getUploadedFileNameWithoutExtension($uploaded_file);
        $media->file_name       = static::getUploadedFileName($uploaded_file);
        $media->collection_name = $collection ?? request('collection');
        $media->url             = $url;
        $media->mime_type       = $uploaded_file->getMimeType();
        $media->size            = $uploaded_file->getSize();
        $media->model           = $model ?? request('model');
        $media->withCustomProperties([
            'order'  => (int) request('order') ?? 1,
            'select' => (bool) $select ?: config('administrable.media.select_uploaded_file'),
        ]);

        $media->save();

        return $media;
    }

    public static function store(UploadedFile $uploaded_file) :self
    {
        return static::storeMedia(
            $uploaded_file,
            static::storeUploadFileOnDisk($uploaded_file)
        );
    }


    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @param bool $suffix
     * @param bool $extension
     * @return string
     */
    public static function getUploadedFileName($file, bool $suffix = false, bool $extension = true): string
    {
        $name = (string) config('administrable.media.rename_file')
                ?  Str::random(40)
                :  Str::of($file->getClientOriginalName())->beforeLast('.')->slug();

        if ($suffix && config('administrable.media.temporary_files.suffix')) {
            $name .= date('Ymds');
        }

        if ($extension){
            $name .= '.' . $file->guessExtension();
        }

        return $name;
    }

    /**
     * @param \Illuminate\Http\UploadedFile $file
     * @return string
     */
    public static function getUploadedFileNameWithoutExtension($file): string
    {
        return static::getUploadedFileName($file, false, false);
    }

    public function getStorageUrl() :string
    {
        return storage_path('app/public/' .   $this->getRawOriginal('url'));
    }

    public static function fetchMediaInOptions(string $collection, string $model, string $path)
    {
        $model = base64_decode($model);

        $options = static::getMediaInOptions($collection, $model, $path);

        if(empty($options)){
            return collect();
        }

        return config("administrable.modules.filemanager.temporary_model")::where(
            'collection_name', $collection
        )->whereIn('id', $options['keys'])->get();
    }

    private static function getMediaInOptions(?string $collection = null, ?string $model = null, ?string $path = null) :array
    {
        $path   = $path ?? request('path');
        $model  = $model ?? request('model');

        $option = json_decode(option_get(self::getMediaOptionsKey($collection, $model)), true);

        if (
            empty($option) ||
            $option['path'] !== $path ?? request('path') ||
            $option['model_name'] !== $model ?? request('model')
        ) {
            return [];
        }

        return $option;
    }

    public static function getMediaOptionsKey(?string $collection = null, ?string $model = null) :string
    {
        return sprintf("filemanager%s%s", $collection ?? request('collection'), Str::lower(str_replace('\\', '', $model ?? request('model'))));
    }


    public function registerMediaInOptions()
    {
        $collection = request('collection');

        $key  = self::getMediaOptionsKey();

        $data = [
            'path'       => request('path'),
            'collection' => $collection,
            'model_name' => request('model'),
        ];

        $option = self::getMediaInOptions();

        if (empty($option)){
            return option_create($key, json_encode(array_merge($data, [
                'keys'       => [$this->getKey()],
            ])));
        }

        return option_edit($key, json_encode(array_merge($data, [
            'keys'       => [...$option['keys'], $this->getKey()],
        ])));
    }


    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();

        static::created(function ($model) {
            /**
             * @var \Guysolamour\Administrable\Models\TemporaryMedia $model
             */
            $model->registerMediaInOptions();
        });

        static::deleted(function ($model) {
            /**
             * @var \Guysolamour\Administrable\Models\TemporaryMedia $model
             */
            Storage::disk('public')->delete($model->getRawOriginal('url'));
        });
    }
}
