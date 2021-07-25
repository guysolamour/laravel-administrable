<?php

use Illuminate\Support\Str;
use Guysolamour\Administrable\Module;
use Illuminate\Support\Facades\Route;
use Spatie\Honeypot\ProtectAgainstSpam;
use Guysolamour\Administrable\Extension;


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
        $redirect_url = config('administrable.rickroll.url', 'http://youtube.com');
        $routes = config('administrable.rickroll.routes', []);

        foreach ($routes as $route) {
            if ($route !== config('administrable.auth_prefix_path', 'administrable')) {
                Route::redirect($route, $redirect_url);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | EXTENSIONS -> Testimonial
        |--------------------------------------------------------------------------
        */
        if (Extension::state('testimonial')){
            Route::get('testimonials', [Extension::frontController('testimonial'), 'index'])->name('extensions.testimonial.testimonial.index');
        }
        /*
        |--------------------------------------------------------------------------
        | EXTENSIONS -> Mailbox
        |--------------------------------------------------------------------------
        */
        if (Extension::state('mailbox')){
            Route::get('/contact', [Extension::frontController('mailbox'), 'create'])->name('extensions.mailbox.mailbox.create');
            Route::post('/contact', [Extension::frontController('mailbox'), 'store'])->name('extensions.mailbox.mailbox.store')->middleware(ProtectAgainstSpam::class);
        }
        /*
        |--------------------------------------------------------------------------
        | EXTENSIONS -> Blog
        |--------------------------------------------------------------------------
        */
        if (Extension::state('blog')) {
            Route::get('blog/posts', [config('administrable.extensions.blog.post.front.controller'), 'index'])->name('extensions.blog.blog.index');

            Route::get('blog/posts/search', [config('administrable.extensions.blog.post.front.controller'), 'search'])->name('extensions.blog.search');
            Route::get('blog/posts/{post}', [config('administrable.extensions.blog.post.front.controller'), 'show'])->name('extensions.blog.show');

            Route::get('blog/posts/categories/{category}', [config('administrable.extensions.blog.post.front.controller'), 'category'])->name('extensions.blog.category');
            Route::get('blog/posts/tags/{tag}', [config('administrable.extensions.blog.post.front.controller'), 'tag'])->name('extensions.blog.tag');
        }



    }
);
