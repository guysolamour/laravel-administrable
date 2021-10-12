<?php

namespace Guysolamour\Administrable\Settings;

use Guysolamour\Administrable\Traits\CustomFieldsTrait;
use Illuminate\Support\Facades\Storage;

class ConfigurationSettings extends BaseSettings
{
    use CustomFieldsTrait;

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
    public ?array $custom_fields;


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
