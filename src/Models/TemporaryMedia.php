<?php

namespace Guysolamour\Administrable\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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


    // public function getMedia(string $collection)
    // {

    // }


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
        //  return storage_path('app/public/administrable/temp/gallery-62021090806.jpg');
         return storage_path('app/public/' .   $this->getRawOriginal('url'));
    }

    private function getMediaInOptions() :array
    {
        $option = json_decode(option_get(self::getMediaOptionsKey()), true);

        if (
            empty($option) ||
            $option['path'] !== request('path') ||
            $option['model_name'] !== request('model')
        ) {
            return [];
        }

        return $option;
    }

    public static function getMediaOptionsKey() :string
    {
        return 'filemanager' . request('collection') . Str::lower(str_replace('\\', '', request('model')));
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


        $option = $this->getMediaInOptions();

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
             * @var \App\Models\TemporaryMedia $model
             */
            $model->registerMediaInOptions();
        });

        static::deleted(function ($model) {
            /**
             * @var \App\Models\TemporaryMedia $model
             */
            Storage::disk('public')->delete($model->getRawOriginal('url'));
        });
    }
}
