<?php

namespace Guysolamour\Administrable\Console\Crud;

use Guysolamour\Administrable\Console\BaseCommand;


class BaseCrudCommand extends BaseCommand
{

    /**
     * @var string[]
     */
    protected const GLOBAL_OPTIONS = ['slug', 'edit_slug', 'clone', 'seeder', 'entity', 'polymorphic', 'timestamps', 'breadcrumb', 'imagemanager', 'trans', 'icon'];

    /**
     * @var string[]
     */
    protected const RESERVED_WORDS = ['slug', 'icon', 'clone', 'edit_slug', 'breadcrumb', 'timestamps', 'seeder', 'trans'];


    /**
     * @var string
     */
    protected $model = '';
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $morphs = [];

    /**
     * @var bool
     */
    protected $timestamps = true;
    /**
     * @var bool
     */
    protected $seeder = true;
    /**
     * @var bool
     */
    protected $entity = false;
    /**
     * @var bool
     */
    protected $polymorphic;
    /**
     * @var string
     */
    protected $slug;
    /**
     * @var bool
     */
    protected $edit_slug = false;
    /**
     * @var bool
     */
    protected $clone = false;
    /**
     * @var string
     */
    protected $icon = 'fa-folder';
    /**
     * @var string
     */
    protected $trans = '';

    protected $imagemanager = '';


    /**
     * @var string
     */
    protected $breadcrumb;

}
