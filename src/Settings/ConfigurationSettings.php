<?php

namespace Guysolamour\Administrable\Settings;

use Illuminate\Support\Facades\Storage;

class ConfigurationSettings extends BaseSettings
{
    public ?string $email;
    public ?string $postal;
    public ?string $area;
    public ?string $cell;
    public ?string $phone;
    public ?string $about;
    public ?string $youtube;
    public ?string $twitter;
    public ?string $facebook;
    public ?string $linkedin;
    public ?string $whatsapp;
    public ?string $logo;


    public static function group(): string
    {
        return 'configuration';
    }

    public function storeLogo(): void
    {
        if (!request()->file('logo')) {
            return;
        }

        $url = request()->file('logo')->storeAs('logo', 'logo.png', 'public');

        $this->update(['logo' => Storage::url($url)]);
    }
}