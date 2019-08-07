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
composer require guysolamour/admin
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

CHANGE ME

## Security

If you discover any security related issues, please email 
instead of using the issue tracker.

## Credits

- [](https://github.com/guysolamour/admin)
- [All contributors](https://github.com/guysolamour/admin/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
