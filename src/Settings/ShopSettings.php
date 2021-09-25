<?php

namespace Guysolamour\Administrable\Settings;

use Guysolamour\Administrable\Settings\BaseSettings;


class ShopSettings extends BaseSettings
{

    public ?bool $tva; // Activer le calcul de la TVA
    public ?float $tva_percentage; // Activer le calcul de la TVA
    public ?bool $coupon; // Activer l'utilisation des codes promo
    public ?bool $review; // Activer les avis sur les produits
    public ?bool $stock_management; // Activer les avis sur les produits
    public ?bool $invoice_management; // Activer les avis sur les produits
    public ?bool $note; // Activer les notes sur les produits
    public ?bool $required_note; // La note est elle obligatoire pour laisser un avis
    public ?string $command_prefix;
    public ?int $command_length;
    public ?string $command_suffix;
    public ?string $invoice_note;



    public static function group(): string
    {
        return 'shop_settings';
    }
}
