<?php


namespace Guysolamour\Administrable\Services;

use Illuminate\Session\Store;


class Flashy
{
    public const NOTIFICATION_KEY = 'flashy_notification';


    public function __construct(private Store $session){}


    public function info(string $message, string $link = '#')
    {
        $this->message($message, $link, 'info');

        return $this;
    }


    public function success(string $message, string $link = '#')
    {
        $this->message($message, $link, 'success');

        return $this;
    }


    public function error(string $message, string $link = '#')
    {
        $this->message($message, $link, 'error');

        return $this;
    }

    public function warning(string $message, string $link = '#')
    {
        $this->message($message, $link, 'warning');

        return $this;
    }

    public function primary(string $message, string $link = '#')
    {
        $this->message($message, $link, 'primary');

        return $this;
    }

    public function primaryDark(string $message, string $link = '#')
    {
        $this->message($message, $link, 'primary-dark');

        return $this;
    }

    public function muted(string $message, string $link = '#')
    {
        $this->message($message, $link, 'muted');

        return $this;
    }


    public function mutedDark(string $message,string $link = '#')
    {
        $this->message($message, $link, 'muted-dark');

        return $this;
    }

    public function message(string $message,string $link = '#', string $type = 'success')
    {
        $this->session->flash(self::NOTIFICATION_KEY . '.message', $message);
        $this->session->flash(self::NOTIFICATION_KEY . '.link', $link);
        $this->session->flash(self::NOTIFICATION_KEY .'.type', $type);

        return $this;
    }
}
