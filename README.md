# Admin

[![Build Status](https://travis-ci.org/guysolamour/admin.svg?branch=master)](https://travis-ci.org/guysolamour/admin)
[![styleci](https://styleci.io/repos/CHANGEME/shield)](https://styleci.io/repos/CHANGEME)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/guysolamour/admin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/guysolamour/admin/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/CHANGEME/mini.png)](https://insight.sensiolabs.com/projects/CHANGEME)
[![Coverage Status](https://coveralls.io/repos/github/guysolamour/admin/badge.svg?branch=master)](https://coveralls.io/github/guysolamour/admin?branch=master)

[![Packagist](https://img.shields.io/packagist/v/guysolamour/admin.svg)](https://packagist.org/packages/guysolamour/admin)
[![Packagist](https://poser.pugx.org/guysolamour/admin/d/total.svg)](https://packagist.org/packages/guysolamour/admin)
[![Packagist](https://img.shields.io/packagist/l/guysolamour/admin.svg)](https://packagist.org/packages/guysolamour/admin)

Package description: CHANGE ME

## Installation

Install via composer
```bash
composer require guysolamour/laravel-administrable
```

### Register Service Provider

**Note! This and next step are optional if you use laravel>=5.5 with package
auto discovery feature.**

Add service provider to `config/app.php` in `providers` section
```php
Guysolamour\Admin\ServiceProvider::class,
```

### Register Facade

Register package facade in `config/app.php` in `aliases` section
```php
Guysolamour\Admin\Facades\Admin::class,
```

### Publish Configuration File

```bash
php artisan vendor:publish --provider="Guysolamour\Admin\ServiceProvider" --tag="config"
```

## Usage

### Install the package
    composer require guysolamour/laravel-admin

### Publish config and assets
    php artisan vendor:publish --tag=administrable-config
    php artisan vendor:publish --tag=administrable-public

### Publish lfm config and assets
    php artisan vendor:publish --tag=lfm_public

### Clear cache
    php artisan route:clear
    php artisan config:clear

### Create symbolic link for lfm
    php artisan storage:link

### Edit APP_URL in  .env file to add the port :8000


### use the make crud command
    php artisan admin:make:crud Post
    -t if the model should not be timestamps
    --slug= the field to slugify
    NB: the slug field in automatically generate don't enter it when the command prompt you
    Don't forget to dump-autoload before seeding
    composer dump-autolad (because some class where created)
    php artisan db:seed --class PostsTableSeeder



### Edited files
    app/Http/Kernel.php
	app/Providers/RouteServiceProvider.php
	config/auth.php
	database/seeds/DatabaseSeeder.php


### Add files
    app/Admin.php
    app/Forms/
    app/Handlers/
    app/Http/Controllers/Admin/
    app/Http/Middleware/EnsureAdminEmailIsVerified.php
    app/Http/Middleware/RedirectIfAdmin.php
    app/Http/Middleware/RedirectIfNotAdmin.php
    app/Http/Middleware/RedirectIfNotSuperAdmin.php
    app/Notifications/
    app/Traits/
    config/lfm.php
    database/factories/AdminFactory.php
    database/migrations/2019_08_14_074523_create_admin_password_resets_table.php
    database/migrations/2019_08_14_074523_create_admins_table.php
    database/seeds/AdminsTableSeeder.php
    resources/views/admin/
    routes/admin.php
    routes/breadcrumb.php



## Security

If you discover any security related issues, please email
instead of using the issue tracker.

## Credits

- [](https://github.com/guysolamour/admin)
- [All contributors](https://github.com/guysolamour/admin/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).


