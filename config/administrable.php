<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Names
    |--------------------------------------------------------------------------
    |
    | Used in the back office
    |
    */
    'app_first_name'   => config('app.first_name', 'Admin'),
    'app_last_name'    => config('app.last_name', 'Admin'),
    'app_short_name'   => config('app.short_name', 'Lvl'),
    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | Available theme are adminlte,theadmin,cooladmin,tabler,themekit
    | The theme should not be changed once the installation has been done
    */
    'theme' => 'adminlte',
    /*
    |--------------------------------------------------------------------------
    | Folder
    |--------------------------------------------------------------------------
    |
    | Models folder name inside app directory
    */
    'models_folder' => 'Models',
     /*
    |--------------------------------------------------------------------------
    | Guard
    |--------------------------------------------------------------------------
    |
    | The guard used for the installation. By default the value is admin.
    | After installation, this value should no longer be changed at the risk of farting the views of the crud
    | This value is also used to generate the crud.
    */
    'guard' => 'admin',

    /**
     * The color useed by for the emails header background.
     */
    'notification_email_header_color' => '#33cabb',

    /**
     * The logo link to use for administration
     */
    'logo_url' => '/img/logo-administrable.png',

    /**
     * Administration routes prefix.
     */
    'auth_prefix_path' => 'administrable',

    /**
     * The name of the folder where the front office controllers will be stored in App/Http/Controller folder
     */
    'front_namespace' => 'Front',

    /**
     * The name of the folder where the back office controllers will be stored in App/Http/Controller folder
     */
    'back_namespace' => 'Back',

    /**
     * Use PHP callable syntax...
     * use App\Http\Controllers\UserController;
     * Route::get('/users', [UserController::class, 'index']);

     * Use string syntax...
     * Route::get('/users', 'App\Http\Controllers\UserController@index');
     */
    'route_controller_callable_syntax' => true,

    /*
     * Where to store extensions migrations.
     */
    'migrations_path' => database_path('extensions'),

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    |
    | Regsiter media collections and conversions
    */
    'media' => [
        /**
         * Default disc name
         */
        'collections_disc' => 'media',
        'collections' => [
            'front'          => ['label' => 'front-image', 'description' => 'Image à la une',         'conversion'  => true, 'multiple' => false],
            'back'           => ['label' => 'back-image',  'description' => 'Seconde image à la une', 'conversion'  => true, 'multiple' => false],
            'images'         => ['label' => 'images',      'description' => 'Gallerie',               'conversion'  => true, 'multiple' => true],
            'attachments'    => ['label' => 'attachments', 'conversion' => true],
            'seo'            => ['label' => 'seo',         'conversion' => false],
        ],
        'conversions' => [
            'avatar'    => ['label' => 'avatar',    'height' => 100, 'width'   =>  100],
            'avatar-sm' => ['label' => 'avatar-sm', 'height' => 50,  'width'   =>  50],
            'avatar-xs' => ['label' => 'avatar-xs', 'height' => 30,  'width'   =>  30],
            'thumb'     => ['label' => 'thumb',     'height' => 100, 'default' => true],
            'thumb-sm'  => ['label' => 'thumb-sm',  'height' => 250, 'default' => true],
        ],
    ],

    /**
     * Redirect when attempting to access some routes or files
     */
    'rickroll' => [
        /**
         * Url to redirect
         */
        'url' => 'http:://youtube.com',
        /**
         * Be sure that auth_prefix_path is not in the list
         */
        'routes' => [
            'wp-admin',
            'admin',
            'composer.json',
            'wp-login',
            '.htaccess',
        ],
    ],
    /**
     * Lorsqu'on utiliser un daterage deux champs seront crér avec le nom du champ
     * et chacun aura un suffix pour le début et fin. Ces suffixes peuvent etre changés ici
     */
    'daterange' => [
        'start' => 'started_at',
        'end'   => 'ended_at',
    ],
    /**
     * Le format utilisé pour afficher les dates dans les vues
     */
    'format_date' => 'd/m/Y H:i',

    'storage_dump' => [
        /**
         * Dump filename
         */
        'filename' => config('app.name','storage_dump'),
        /**
         * Dumps to keep
         */
        'limit' => 5,
        /*
         * The directory where the temporary files will be stored.
         * Can not be storage path
         */
        'temporary_directory' => public_path(),
        /**
         * Where to store dumps on each disk
         */
        'dump_folder'  => 'storagedump',
        /*
        * The disk names on which the backups will be stored.
        */
        'disks' => ['ftp'],
        /*
        * You can get notified when specific events occur.
        */
        'notifications' => [
            'mail' => [
                'from' => [
                    'address' => 'backup@administrable.com',
                    'name' => config('app.name'),
                ],
                /**
                 * Notification to use
                 */
                //'class' => '\App\Notifications\Back\SuccessfulStorageFolderBackupNotification'
            ],
        ],
    ],
    'comments' => [
        /**
         * By default comments posted are marked as approved. If you want
         * to change this, set this option to true. Then, all comments
         * will need to be approved by setting the `approved` column to
         * `true` for each comment.
         * or use public $approved = true attribute on the model
         *
         *
         * To see only approved comments use this code in your view:
         *
         *     @comments([
         *         'model' => $book,
         *         'approved' => true
         *     ])
         *
         */
        'approval_required' => true,

        /**
         * Set this option to `true` to enable guest commenting.
         *
         * Visitors will be asked to provide their name and email
         * address in order to post a comment.
         */
        'guest_commenting' => true,
    ],

    /**
     * These mails is used by the conceptor to maintain the application
     */
    'emails' => array_filter(explode('|', env('CONCEPTOR_EMAILS'))),
];
