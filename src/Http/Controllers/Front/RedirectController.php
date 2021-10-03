<?php

namespace Guysolamour\Administrable\Http\Controllers\Front;


use Illuminate\Support\Facades\Redirect;
use Guysolamour\Administrable\Http\Controllers\BaseController;
use Guysolamour\Administrable\Settings\ConfigurationSettings;

class RedirectController extends BaseController
{
    private $config;

    public function __construct(ConfigurationSettings $config)
    {
        $this->config = $config;
    }

    public function twitter() {
        return Redirect::to($this->config->twitter);
    }

	public function linkedin() {
        return Redirect::to($this->config->linkedin);
    }

	  public function facebook() {
        return Redirect::to($this->config->facebook);
    }

	public function youtube() {
        return Redirect::to($this->config->youtube);
    }

    public function rickroll()
    {
        return Redirect::to(config('administrable.rickroll.url', 'https://youtube.com'));
    }
}
