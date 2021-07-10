<?php

use Guysolamour\Administrable\Extension;
use Illuminate\Support\Str;
use Guysolamour\Administrable\Module;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;

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
            | PAGE
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

            Route::post('comments/{comment}/reply', [Module::backController('comment'), 'reply'])->name('comment.reply');
            Route::get('comments/{comment}/approved', [Module::backController('comment'), 'approved'])->name('comment.approved');
        });


        /*
        |--------------------------------------------------------------------------
        | EXTENSIONS
        |--------------------------------------------------------------------------
        */
        Route::prefix('extensions/')->middleware([config('administrable.guard') . '.auth'])->group(function () {

            /*
            |--------------------------------------------------------------------------
            | EXTENSIONS -> Livenews
            |--------------------------------------------------------------------------
            */
            if (Extension::state('livenews')) {
                Route::name(Str::lower(config('administrable.back_namespace')) . '.extensions.livenews.livenews.')->group(function () {
                    Route::resource('livenews', Extension::backController('livenews'))->names([
                        'index'    => 'index',
                        'show'     => 'show',
                        'create'   => 'create',
                        'store'    => 'store',
                        'edit'     => 'edit',
                        'update'   => 'update',
                        'destroy'  => 'destroy',
                    ])->except('show');
                });
            }
            /*
            |--------------------------------------------------------------------------
            | EXTENSIONS -> Testimonial
            |--------------------------------------------------------------------------
            */
            if (Extension::state('testimonial')) {
                Route::name(Str::lower(config('administrable.back_namespace')) . '.extensions.testimonial.testimonial.')->group(function () {
                    Route::resource('testimonials', Extension::backController('testimonial'))->names([
                        'index'    => 'index',
                        'show'     => 'show',
                        'create'   => 'create',
                        'store'    => 'store',
                        'edit'     => 'edit',
                        'update'   => 'update',
                        'destroy'  => 'destroy',
                    ]);

                });
            }
            /*
            |--------------------------------------------------------------------------
            | EXTENSIONS -> Mailbox
            |--------------------------------------------------------------------------
            */
            if (Extension::state('mailbox')) {
                Route::name(Str::lower(config('administrable.back_namespace')) . '.extensions.mailbox.mailbox.')->group(function () {
                    Route::resource('mailboxes', Extension::backController('mailbox'))->names([
                        'index'    => 'index',
                        'show'     => 'show',
                        'create'   => 'create',
                        'store'    => 'store',
                        'edit'     => 'edit',
                        'update'   => 'update',
                        'destroy'  => 'destroy',
                    ])->except('create', 'edit', 'store', 'update');

                    Route::post('/mailboxes/{mailbox}/note', [Extension::backController('mailbox'), 'saveNote'])->name('note.store');
                    Route::put('/mailboxes/{mailbox}/note/{comment}', [Extension::backController('mailbox'), 'updateNote'])->name('note.update');
                    Route::delete('/mailboxes/{mailbox}/note/{comment}', [Extension::backController('mailbox'), 'deleteNote'])->name('note.destroy');
                });

            }
            /*
            |--------------------------------------------------------------------------
            | EXTENSIONS -> Blog
            |--------------------------------------------------------------------------
            */
            if (Extension::state('blog')){
                Route::name(Str::lower(config('administrable.back_namespace')) . '.extensions.blog.category.')->group(function () {
                    Route::prefix('blog')->group(function () {
                        Route::resource('categories', config('administrable.extensions.blog.category.back.controller'))->names([
                            'index'      => 'index',
                            'show'       => 'show',
                            'create'     => 'create',
                            'store'      => 'store',
                            'edit'       => 'edit',
                            'update'     => 'update',
                            'destroy'    => 'destroy',
                        ]);
                    });

                });

                Route::name(Str::lower(config('administrable.back_namespace')) . '.extensions.blog.tag.')->group(function () {
                    Route::prefix('blog')->group(function () {
                        Route::resource('tags', config('administrable.extensions.blog.tag.back.controller'))->names([
                            'index'      => 'index',
                            'show'       => 'show',
                            'create'     => 'create',
                            'store'      => 'store',
                            'edit'       => 'edit',
                            'update'     => 'update',
                            'destroy'    => 'destroy',
                        ]);
                    });
                });

                Route::name(Str::lower(config('administrable.back_namespace')) . '.extensions.blog.post.')->group(function () {
                    Route::prefix('blog')->group(function () {
                        Route::resource('posts', config('administrable.extensions.blog.post.back.controller'))->names([
                            'index'      => 'index',
                            'show'       => 'show',
                            'create'     => 'create',
                            'store'      => 'store',
                            'edit'       => 'edit',
                            'update'     => 'update',
                            'destroy'    => 'destroy',
                        ]);

                        // JS
                        Route::post('posts/category', [config('administrable.extensions.blog.post.back.controller'), 'addCategory']);
                        Route::post('posts/tag', [config('administrable.extensions.blog.post.back.controller'), 'addTag']);
                    });
                });
            }
        });
    }
);


