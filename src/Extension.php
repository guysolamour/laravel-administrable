<?php

namespace Guysolamour\Administrable;

use Illuminate\Database\Eloquent\Model;



class Extension
{
    private const FRONT = 'front';

    private const BACK  = 'back';

    public static function state(string $name) :?string
    {
        // dd($name);
        return (config("administrable.extensions.{$name}.activate"));
    }

    public static function model(string $name) :?string
    {
        return config("administrable.extensions.{$name}.model");
    }

    public static function getModel(string $name) :Model
    {
        $model = static::model($name);

        return new $model;
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
        return config("administrable.extensions.{$name}.controller");
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
        return config("administrable.extensions.{$name}.form");
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
        return config("administrable.extensions.{$name}.mail");
    }
}
