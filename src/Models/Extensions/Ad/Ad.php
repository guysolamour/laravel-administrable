<?php

namespace Guysolamour\Administrable\Models\Extensions\Ad;


use Spatie\MediaLibrary\HasMedia;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\DaterangeTrait;
use Guysolamour\Administrable\Traits\DraftableTrait;
use Guysolamour\Administrable\Traits\MediaableTrait;
use Guysolamour\Administrable\Casts\DaterangepickerCast;


class Ad extends BaseModel implements HasMedia
{
    use MediaableTrait;
    use DraftableTrait;
    use DaterangeTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'extensions_ad_ads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'link', 'online', 'type_id', 'group_id', 'started_at',
        'ended_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'online'     => 'boolean',
        'started_at' => DaterangepickerCast::class,
        'ended_at'   => DaterangepickerCast::class,
    ];


    protected $medialibrary_collections = [
        'front'    => ['label' => 'front-image', 'description' => 'Image à la une',],
    ];

    protected $medialibrary_conversions = [
        'thumb'     => ['height' => 100],
        'thumb-sm'  => ['height' => 250],
    ];

    protected $datepickers = [
        'started_at',
        'ended_at'
    ];


    public function type()
    {
        return $this->belongsTo(config('administrable.extensions.ad.models.type'), 'type_id');
    }

    public function group()
    {
        return $this->belongsTo(config('administrable.extensions.ad.models.group'), 'group_id');
    }

    public function isImageType(): bool
    {
        return $this->type->name == 'image';
    }

    public function isTextType(): bool
    {
        return $this->type->name == 'text';
    }

    private function getHtmlTag(array $attrs): string
    {
        $html = "";

        if ($this->isImageType()) {
            $html = <<<HTML
            <img style="padding-top: 15px;" src="{$this->image}" {$this->getHtmlAttributes($attrs)} />
            HTML;
        }

        if ($this->isTextType()) {
            $html = <<<HTML
                <p style="padding-top: 15px;" {$this->getHtmlAttributes($attrs)}>{$this->description}</p>
            HTML;
        }

        if ($link = $this->link) {
            $html = <<<HTML
                <a href="{$link}" target="_blank">{$html}</a>
            HTML;
        }

        return $html;
    }

    private function getHtmlAttributes(array $attrs): string
    {
        $attr = '';

        foreach ($attrs as $key => $value) {
            $attr .= "{$key}='{$value}' ";
        }

        return $attr;
    }

    /**
     * @return string|null
     */
    public function render(array $attrs = [])
    {
        if (!$this->isOnline()) {
            return;
        }

        if ($this->started_at->isFuture()) {
            return;
        }

        if ($this->ended_at->isPast()) {
            return;
        }

        return $this->getHtmlTag($attrs);
    }
}
