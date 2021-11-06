<?php

use Illuminate\Support\Str;
use Guysolamour\Administrable\Module;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;


Route::name(Str::lower(config('administrable.front_namespace') . '.'))
    ->middleware('web')
    ->group(function () {
        Route::view('notpaid', 'administrable::front.home.notpaid')->name('notpaid');
        /*
        |--------------------------------------------------------------------------
        | COMMENT
        |--------------------------------------------------------------------------
        */
        Route::middleware([ProtectAgainstSpam::class])->group(function () {
            Route::post('comments', [Module::frontController('comment'), 'store'])->name('comments.store');
            Route::delete('comments/{comment}', [Module::frontController('comment'), 'destroy'])->name('comments.destroy');
            Route::put('comments/{comment}', [Module::frontController('comment'), 'update'])->name('comments.update');
            Route::post('comments/{comment}', [Module::frontController('comment'), 'reply'])->name('comments.reply');
        });
        /*
        |--------------------------------------------------------------------------
        | SOCIAL REDIRECT
        |--------------------------------------------------------------------------
        */

        $networks = config('administrable.modules.social_redirect.networks');
        foreach ($networks as $network ) {
            Route::get($network, [Module::controller('social_redirect'), $network])->name($network);
        }
        /*
        |--------------------------------------------------------------------------
        | RICKROLL
        |--------------------------------------------------------------------------
        */

        $routes = config('administrable.rickroll.routes', []);

        foreach ($routes as $route) {
            if ($route !== config('administrable.auth_prefix_path', 'administrable')) {
                Route::get($route, [Module::controller('social_redirect'), 'rickroll']);
            }
        }
    }
);
