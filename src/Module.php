<?php

namespace Guysolamour\Administrable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;




class Module
{
    private const FRONT = 'front';

    private const BACK  = 'back';

    public static function model(string $model) :?string
    {
        return config("administrable.modules.{$model}.model");
    }

    public static function getModel(string $model) :Model
    {
        $model = static::model($model);

        return new $model;
    }

    public static function getUserModel() :string
    {
        return sprintf("%s\%s\User", get_app_namespace(), config('administrable.models_folder'));
    }

    public static function getGuardModel() :string
    {
        return sprintf("%s\%s\%s", get_app_namespace(), config('administrable.models_folder'), Str::ucfirst(config('administrable.guard')));
    }

    public static function backController(string $name) :?string
    {
        return static::controller("{$name}." . self::BACK);
    }

    public static function frontController(string $name) :?string
    {
        return static::controller("{$name}." . self::FRONT);
    }

    public static function controller(string $name) :?string
    {
        return config("administrable.modules.{$name}.controller");
    }

    public static function backForm(string $name): ?string
    {
        return static::form("{$name}." . self::BACK);
    }

    public static function frontForm(string $name): ?string
    {
        return static::form("{$name}." . self::FRONT);
    }

    public static function form(string $name) :?string
    {
        return config("administrable.modules.{$name}.form");
    }

    public static function backMail(string $name): ?string
    {
        return static::mail("{$name}." . self::BACK);
    }

    public static function frontMail(string $name): ?string
    {
        return static::mail("{$name}." . self::FRONT);
    }

    public static function mail(string $name) :?string
    {
        return config("administrable.modules.{$name}.mail");
    }
}
