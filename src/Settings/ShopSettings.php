<?php

namespace Guysolamour\Administrable\Settings;

use Guysolamour\Administrable\Settings\BaseSettings;
use Guysolamour\Administrable\Traits\CustomFieldsTrait;

class ShopSettings extends BaseSettings
{
    use CustomFieldsTrait;

    public ?bool $tva;
    public ?float $tva_percentage;
    public ?bool $coupon;
    public ?bool $review;
    public ?bool $stock_management;
    public ?bool $invoice_management;
    public ?bool $note;
    public ?bool $required_note;
    public ?string $command_prefix;
    public ?int $command_length;
    public ?string $command_suffix;
    public ?string $invoice_note;
    public ?int $default_deliver_id;
    public ?int $default_coveragearea_id;
    public ?array $custom_fields;


    public static function group(): string
    {
        return 'shop_settings';
    }
}
