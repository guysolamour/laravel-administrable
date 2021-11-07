<?php

use Illuminate\Support\Str;
use Guysolamour\Administrable\Module;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;
use Guysolamour\Administrable\Extension;

Route::prefix(config('administrable.auth_prefix_path'))
    ->middleware(['web'])

    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | AUTH
        |--------------------------------------------------------------------------
        */
        Route::middleware([ProtectAgainstSpam::class])->group(function () {

            $controllers = config('administrable.modules.auth.back.controller');

            // Login
            Route::get('login', [$controllers['login'], 'showLoginForm'])->name('admin.login');
            Route::post('login', [$controllers['login'], 'login']);
            Route::post('logout', [$controllers['login'], 'logout'])->name('admin.logout');

            // Register
            // Route::get('register', [$controllers['register'], 'showRegistrationForm'])->name('admin.register');
            // Route::post('register', [$controllers['register'], 'register']);

            // Passwords
            Route::post('password/email', [$controllers['forgot'], 'sendResetLinkEmail'])->name('admin.password.email');
            Route::post('password/reset', [$controllers['reset'], 'reset'])->name('admin.password.update');
            Route::get('password/reset', [$controllers['forgot'], 'showLinkRequestForm'])->name('admin.password.request');
            Route::get('password/reset/{token}', [$controllers['reset'], 'showResetForm'])->name('admin.password.reset');

            Route::get('password/confirm', [$controllers['confirm'], 'showConfirmForm'])->name('admin.password.confirm');
            Route::post('password/confirm', [$controllers['confirm'], 'confirm']);

            // Verify
            // Route::get('email/resend', [$controllers['verification'], 'resend'])->name('admin.verification.resend');
            Route::get('email/verify', [$controllers['verification'], 'show'])->name('admin.verification.notice');
            Route::get('email/verify/{id}', [$controllers['verification'], 'verify'])->name('admin.verification.verify');
        });

        /*
        |--------------------------------------------------------------------------
        | ADMINISTRATION
        |--------------------------------------------------------------------------
        */
        Route::name(Str::lower(config('administrable.back_namespace') . '.'))
            ->middleware([config('administrable.guard') . '.auth'])
            ->group(function(){

            /*
            |--------------------------------------------------------------------------
            | PAGE
            |--------------------------------------------------------------------------
            */
            Route::resource('pages', Module::backController('page'))->names([
                'index'      => 'page.index',
                'create'     => 'page.create',
                'show'       => 'page.show',
                'store'      => 'page.store',
                'edit'       => 'page.edit',
                'update'     => 'page.update',
                'destroy'    => 'page.destroy',
            ]);
            Route::post('pages/{page}/pagemeta', [Module::backController('page'), 'storeMetaTag'])->name('pagemeta.store');
            Route::post('pages/{page}/pagemeta/{pagemeta}', [Module::backController('page'), 'updateMetaTag'])->name('pagemeta.update');
            Route::delete('pages/{page}/pagemeta/{pagemeta}', [Module::backController('page'), 'deleteMetaTag'])->name('pagemeta.destroy');
            /*
            |--------------------------------------------------------------------------
            | CONFIGURATION
            |--------------------------------------------------------------------------
            */
            Route::get('configuration', [Module::backController('configuration'), 'edit'])->name('configuration.edit');
            Route::post('configuration', [Module::backController('configuration'), 'store'])->name('configuration.store');
            /*
            |--------------------------------------------------------------------------
            | USER
            |--------------------------------------------------------------------------
            */
            Route::resource('users', Module::backController('user'))->names([
                'index'      => 'user.index',
                'show'       => 'user.show',
                'create'     => 'user.create',
                'store'      => 'user.store',
                'edit'       => 'user.edit',
                'update'     => 'user.update',
                'destroy'    => 'user.destroy',
            ]);
            Route::put('users/change-password/{user}', [Module::backController('user'), 'changePassword'])->name('user.changepassword');
            /*
            |--------------------------------------------------------------------------
            | GUARD
            |--------------------------------------------------------------------------
            */
            $guard = config('administrable.guard');

            Route::get(Str::plural($guard), [Module::backController('guard'), 'index'])->name("{$guard}.index");
            Route::get( 'profile/{' .$guard. '}', [Module::backController('guard'), 'profile'])->name("{$guard}.profile");
            Route::put('profile/{' . $guard . '}', [Module::backController('guard'), 'update'])->name("{$guard}.update");
            Route::post('/update-' . $guard .'-avatar', [Module::backController('guard'), 'updateAvatar'])->name("{$guard}.update-avatar");
            Route::get(Str::plural($guard) .'/create', [Module::backController('guard'), 'create'])->name("{$guard}.create");
            Route::post(Str::plural($guard) . '/store', [Module::backController('guard'), 'store'])->name("{$guard}.store");
            Route::delete(Str::plural($guard) .'/{' . $guard . '}', [Module::backController('guard'), 'delete'])->name("{$guard}.delete");
            Route::put('change-password/{' . $guard . '}', [Module::backController('guard'), 'changePassword'])->name("{$guard}.change.password");
            /*
            |--------------------------------------------------------------------------
            | DELETE ALL
            |--------------------------------------------------------------------------
            */
            Route::post('model/destroy/all', [Module::backController('default'), 'destroyModels'])->name('model.destroy.all');
            /*
            |--------------------------------------------------------------------------
            | CLONE
            |--------------------------------------------------------------------------
            */
            Route::get('model/{model}/{key}/clone', [Module::backController('default'), 'clone'])->name('model.clone');
            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION
            |--------------------------------------------------------------------------
            */
            Route::get('notifications/markread', [Module::backController('guard'), 'markNotificationsAsRead'])->name('notification.markasread');
            /*
            |--------------------------------------------------------------------------
            | COMMENT
            |--------------------------------------------------------------------------
            */
            Route::resource('comments', Module::backController('comment'))->names([
                'index'      => 'comment.index',
                'show'       => 'comment.show',
                'create'     => 'comment.create',
                'store'      => 'comment.store',
                'edit'       => 'comment.edit',
                'update'     => 'comment.update',
                'destroy'    => 'comment.destroy',
                ])->except(['create', 'store']);
            });

            Route::post('comments/{comment}/reply', [Module::backController('comment'), 'reply'])->name('comment.reply');
            Route::get('comments/{comment}/approved', [Module::backController('comment'), 'approved'])->name('comment.approved');
            /*--------------------------------------------------------------------------
            | NOTE
            |--------------------------------------------------------------------------
            */
            Route::post('comments/notes', [Module::backController('note'), 'store'])->name('comment.note.store');
            Route::put('comments/notes/{note}', [Module::backController('note'), 'update'])->name('comment.note.update');
            Route::delete('comments/notes/{note}', [Module::backController('note'), 'destroy'])->name('comment.note.destroy');

            /*
            |--------------------------------------------------------------------------
            | FILEMANAGER
            |--------------------------------------------------------------------------
            */
            Route::post('temporarymedia/remote', [config("administrable.modules.filemanager.back.temporary_controller"), 'remote']);
            Route::post('temporarymedia/option', [config("administrable.modules.filemanager.back.temporary_controller"), 'getOption']);
            Route::post('temporarymedia/order', [config("administrable.modules.filemanager.back.temporary_controller"), 'order']);
            Route::post('temporarymedia', [config("administrable.modules.filemanager.back.temporary_controller"), 'store']);
            Route::post('temporarymedia/selectall', [config("administrable.modules.filemanager.back.temporary_controller"), 'selectAll']);
            Route::post('temporarymedia/unselectall', [config("administrable.modules.filemanager.back.temporary_controller"), 'unSelectAll']);
            Route::post('temporarymedia/{collection}', [config("administrable.modules.filemanager.back.temporary_controller"), 'index']);
            Route::post('temporarymedia/{temporaryMedia}/select', [config("administrable.modules.filemanager.back.temporary_controller"), 'select']);
            Route::post('temporarymedia/{temporaryMedia}/unselect', [config("administrable.modules.filemanager.back.temporary_controller"), 'unselect']);
            Route::post('temporarymedia/{temporaryMedia}/rename', [config("administrable.modules.filemanager.back.temporary_controller"), 'rename']);
            Route::post('temporarymedia/{temporaryMedia}/modify', [config("administrable.modules.filemanager.back.temporary_controller"), 'modify']);
            Route::delete('temporarymedia/{temporaryMedia}', [config("administrable.modules.filemanager.back.temporary_controller"), 'destroy']);

            Route::post('media/{model}/{id}/{collection}/remote', [config("administrable.modules.filemanager.back.controller"), 'remote']);
            Route::post('media/{model}/{id}/{collection}/unselectall', [config("administrable.modules.filemanager.back.controller"), 'unSelectAll']);
            Route::get('media/{model}/{id}/{collection}', [config("administrable.modules.filemanager.back.controller"), 'index']);
            Route::post('media/{model}/{id}/{collection}', [config("administrable.modules.filemanager.back.controller"), 'store']);
            Route::post('media/{model}/{id}/{collection}/selectall', [config("administrable.modules.filemanager.back.controller"), 'selectAll']);
            Route::post('media/{media}/select', [config("administrable.modules.filemanager.back.controller"), 'select']);
            Route::post('media/{media}/unselect', [config("administrable.modules.filemanager.back.controller"), 'unselect']);
            Route::post('media/{media}/rename', [config("administrable.modules.filemanager.back.controller"), 'rename']);
            Route::post('media/{model}/{id}/{collection}/modify/{media}', [config("administrable.modules.filemanager.back.controller"), 'modify']);
            Route::delete('media/{media}', [config("administrable.modules.filemanager.back.controller"), 'destroy']);
            Route::post('media/order', [config("administrable.modules.filemanager.back.controller"), 'order']);

            // // Js
            Route::get('media/order', [config("administrable.modules.filemanager.back.controller"), 'order'])->name('back.media.order.index');
            Route::get('media/{model}/{id}/tinymce', [config("administrable.modules.filemanager.back.controller"), 'tinymce'])->name('back.media.tinymce');
            Route::delete('media/seo/{model}/{id}', [config("administrable.modules.filemanager.back.controller"), 'destroySeo'])->name('back.media.seodestroy');
            Route::delete('media/{model}/{id}/{collection}/all', [config("administrable.modules.filemanager.back.controller"), 'destroyAll'])->name('back.media.destroy.all');
    }
);


