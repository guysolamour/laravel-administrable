<?php

use Illuminate\Support\Str;
use Guysolamour\Administrable\Module;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;
use Guysolamour\Administrable\ServiceProvider;


Route::name(Str::lower(config('administrable.front_namespace') . '.'))
->middleware('web')
->group(
    function () {
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
        foreach ($networks as $network) {
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

if (ServiceProvider::checkIfUserDashboardWasGenerated()) {

    Route::middleware('web')->group(function (){
        /*
        |--------------------------------------------------------------------------
        | User dashboard
        |--------------------------------------------------------------------------
        */
        Route::middleware([ProtectAgainstSpam::class])->group(function () {
            Route::get('login', [config('administrable.modules.user_dashboard.controllers.front.login'), 'showLoginForm'])->name('login');
            Route::post('login', [config('administrable.modules.user_dashboard.controllers.front.login'), 'login']);
            Route::post('logout', [config('administrable.modules.user_dashboard.controllers.front.login'), 'logout'])->name('logout');

            Route::get('register', [config('administrable.modules.user_dashboard.controllers.front.register'), 'showRegistrationForm'])->name('register');
            Route::post('register', [config('administrable.modules.user_dashboard.controllers.front.register'), 'register']);

            Route::get('password/reset', [config('administrable.modules.user_dashboard.controllers.front.forgot_password'), 'showLinkRequestForm'])->name('password.request');
            Route::post('password/email', [config('administrable.modules.user_dashboard.controllers.front.forgot_password'), 'sendResetLinkEmail'])->name('password.email');
            Route::get('password/reset/{token}', [config('administrable.modules.user_dashboard.controllers.front.reset_password'), 'showResetForm'])->name('password.reset');
            Route::post('password/reset', [config('administrable.modules.user_dashboard.controllers.front.reset_password'), 'reset'])->name('password.update');

            Route::get('password/confirm', [config('administrable.modules.user_dashboard.controllers.front.confirm_password'), 'showConfirmForm'])->name('password.confirm');
            Route::post('password/confirm', [config('administrable.modules.user_dashboard.controllers.front.confirm_password'), 'confirm']);

            Route::get('email/verify', [config('administrable.modules.user_dashboard.controllers.front.verification'), 'show'])->name('verification.notice');
            Route::get('email/verify/{id}/{hash}', [config('administrable.modules.user_dashboard.controllers.front.verification'), 'verify'])->name('verification.verify');
            Route::post('email/resend', [config('administrable.modules.user_dashboard.controllers.front.verification'), 'resend'])->name('verification.resend');
        });
        /*
        |--------------------------------------------------------------------------
        | Dashbord
        |--------------------------------------------------------------------------
        */
        Route::name(Str::lower(config('administrable.front_namespace') . '.dashboard.'))
            ->prefix('dashboard')->middleware(['auth'])->group(function () {

                Route::get('edit', [config('administrable.modules.user_dashboard.controllers.front.dashboard'), 'showEditProfileForm'])->name('profile.edit');
                Route::put('edit', [config('administrable.modules.user_dashboard.controllers.front.dashboard'), 'updateProfile']);

                Route::get('edit/password', [config('administrable.modules.user_dashboard.controllers.front.dashboard'), 'showEditPasswordForm'])->name('password.change')->middleware('password.confirm');
                Route::put('edit/password', [config('administrable.modules.user_dashboard.controllers.front.dashboard'), 'changePassword'])->middleware('password.confirm');
        });
    });
}


