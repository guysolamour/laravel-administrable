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
    | Backend Theme
    |--------------------------------------------------------------------------
    |
    | Available theme are adminlte,theadmin,cooladmin,tabler,themekit
    | The theme should not be changed once the installation has been done
    */
    'theme' => 'themekit',

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
    /*
    |--------------------------------------------------------------------------
    | Back namespace
    |--------------------------------------------------------------------------
    | Tous les fichiers de l'administration sont sauvegardés dans un sous dossier pour mieux organiser
    | Exemple: les controllers sera sauvegardés dans App/Http/Controller/Back
    | Exemple: les forms sera sauvegardés dans App/Forms/Back
    |
    | Vous devez publier les vues avec la commande `php artisan vendor:publish --tag="administrable-views" pour éviter des erreurs
    | et renommer le dossier back dans ressources/vendor/administrable en minuscule
    */
    'back_namespace' => 'Back',

    'schedule' => [
        'command' => [
            /**
             * Faire un backup de la base de donnes et l'envoyer par ftp
             */
            'backup'    => true,
             /**
             * Faire un backup du dossier public storage et l'envoyer par ftp
             */
            'storage'   => true,
             /**
             * Run telescope:prune command
             */
            'telescope' => true,

        ],
    ],

    'modules' => [
        'auth' => [
            'back' => [
               'controller' => [
                    'login'        => \Guysolamour\Administrable\Http\Controllers\Back\Auth\LoginController::class,
                    'forgot'       => \Guysolamour\Administrable\Http\Controllers\Back\Auth\ForgotPasswordController::class,
                    'reset'        => \Guysolamour\Administrable\Http\Controllers\Back\Auth\ResetPasswordController::class,
                    'confirm'      => \Guysolamour\Administrable\Http\Controllers\Back\Auth\ConfirmPasswordController::class,
                    'verification' => \Guysolamour\Administrable\Http\Controllers\Back\Auth\VerificationController::class,
               ],
               'policy' => \Guysolamour\Administrable\Policies\GuardPolicy::class,
            ],
        ],
        'page' => [
            'model'       => \Guysolamour\Administrable\Models\Page::class,
            'back' => [
                'form'        => \Guysolamour\Administrable\Forms\Back\PageForm::class,
                'controller'  => \Guysolamour\Administrable\Http\Controllers\Back\PageController::class,
            ],
        ],
        'seo' => [
            'model' => \Guysolamour\Administrable\Models\Seo::class,
        ],
        'default' => [
            'back' => [
                'controller'  => Guysolamour\Administrable\Http\Controllers\Back\DefaultController::class,
            ],
        ],
        'pagemeta' => [
            'model'       => Guysolamour\Administrable\Models\PageMeta::class,
        ],
        'configuration' => [
            'model'       => Guysolamour\Administrable\Settings\ConfigurationSettings::class,
            'back' => [
                'form'        => Guysolamour\Administrable\Forms\Back\ConfigurationForm::class,
                'controller'  => Guysolamour\Administrable\Http\Controllers\Back\ConfigurationController::class,
            ],
            'custom_fields'   => [
                // ['name' => 'display_in_slider',      'type' => 'text',   'label' => 'Mise en avant sous le menu'],
                // ['name' => 'week_deal',              'type' => 'text',   'label' => 'Deal de la semaime'],
                // ['name' => 'tendance',               'type' => 'text',   'label' => 'Tendance'],
            ],

        ],
        'user' => [
            'back' => [
                'form'        => Guysolamour\Administrable\Forms\Back\UserForm::class,
                'controller'  => Guysolamour\Administrable\Http\Controllers\Back\UserController::class,
            ],
        ],
        'guard' => [
            'model'       => Guysolamour\Administrable\Models\Guard::class,
            'back' => [
                'controller'  => Guysolamour\Administrable\Http\Controllers\Back\GuardController::class,
                'form'        => Guysolamour\Administrable\Forms\Back\GuardForm::class,
                'createform'  => Guysolamour\Administrable\Forms\Back\CreateGuardForm::class,
                'resetpasswordform'  => Guysolamour\Administrable\Forms\Back\ResetGuardPasswordForm::class,
            ],
        ],
        'comment' => [
            'model' => Guysolamour\Administrable\Models\Comment::class,
            'front' => [
                'policy' => Guysolamour\Administrable\Policies\CommentPolicy::class,
                'controller'   => Guysolamour\Administrable\Http\Controllers\Front\CommentController::class,
                'replymail'    => Guysolamour\Administrable\Mail\Front\ReplyCommentMail::class,
            ],
            'back' => [
                'controller'   => Guysolamour\Administrable\Http\Controllers\Back\CommentController::class,
                'form'         => Guysolamour\Administrable\Forms\Back\CommentForm::class,
                'mail'         => Guysolamour\Administrable\Mail\Back\CommentMail::class,
                'notification' => Guysolamour\Administrable\Notifications\Back\CommentNotification::class,
            ],
        ],
        'note' => [
            'back' => [
                'controller'  => \Guysolamour\Administrable\Http\Controllers\Back\NoteController::class,
            ],
        ],
        'filemanager'     => [
            'model'          => \Guysolamour\Administrable\Models\Media::class,
            'temporary_model' => \Guysolamour\Administrable\Models\TemporaryMedia::class,
            'back'           => [
                'controller'            => \Guysolamour\Administrable\Http\Controllers\Back\MediaController::class,
                'temporary_controller'  => \Guysolamour\Administrable\Http\Controllers\Back\TemporaryMediaController::class,
            ],
        ],
        'dropzone' => [
            'front' => [
                'controller' => \Guysolamour\Administrable\Http\Controllers\Front\DropzoneController::class,
            ]
        ],
        'social_redirect' => [
            'controller' => Guysolamour\Administrable\Http\Controllers\Front\RedirectController::class,
            'networks'   => ['facebook', 'twitter', 'linkedin', 'youtube']
        ],
        'user_dashboard' => [
            /*
            |--------------------------------------------------------------------------
            | Frontend Theme
            |--------------------------------------------------------------------------
            |
            | Available theme are sleek
            | The theme should not be changed once the installation has been done
            */
            'theme' => 'sleek',

            'model' => \App\Models\User::class,

            'controllers' => [
                'front' => [
                    'login'            => \Guysolamour\Administrable\Http\Controllers\Front\Auth\LoginController::class,
                    'register'         => \Guysolamour\Administrable\Http\Controllers\Front\Auth\RegisterController::class,
                    'dashboard'        => \Guysolamour\Administrable\Http\Controllers\Front\Dashboard\DashboardController::class,
                    'verification'     => \Guysolamour\Administrable\Http\Controllers\Front\Auth\VerificationController::class,
                    'reset_password'   => \Guysolamour\Administrable\Http\Controllers\Front\Auth\ResetPasswordController::class,
                    'forgot_password'  => \Guysolamour\Administrable\Http\Controllers\Front\Auth\ForgotPasswordController::class,
                    'confirm_password' => \Guysolamour\Administrable\Http\Controllers\Front\Auth\ConfirmPasswordController::class,
                ]
            ],
            // 'custom_fields' => [

            // ],
        ]
    ],
    'flashy' => [
        'show_icon' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    |
    | Register media collections and conversions
    */
    'media' => [
        /**
         * Default disc name
         */
        'collections_disc' => 'media',
        'collections' => [
            'front'          => ['label' => 'front-image', 'description' => 'Image à la une',         'conversion'  => true, 'multiple' => false],
            'back'           => ['label' => 'back-image',  'description' => 'Seconde image à la une', 'conversion'  => true, 'multiple' => false],
            'images'         => ['label' => 'images',      'description' => 'Gallerie',               'conversion'  => true, 'multiple' => true
            ],
            'attachments'    => ['label' => 'attachments', 'conversion' => true],
            'seo'            => ['label' => 'seo',         'conversion' => false],
        ],
        'conversions' => [
            'avatar'    => ['label' => 'avatar',    'height' => 100, 'width'   =>  100],
            'avatar-sm' => ['label' => 'avatar-sm', 'height' => 50,  'width'   =>  50],
            'avatar-xs' => ['label' => 'avatar-xs', 'height' => 30,  'width'   =>  30
            ],
            'thumb'     => ['label' => 'thumb',     'height' => 100, 'default' => true],
            'thumb-sm'  => ['label' => 'thumb-sm',  'height' => 250, 'default' => true
            ],
        ],
        'temporary_files' => [
            'folder' => 'administrable/temp',
            'suffix' => true, // append date('Ymds') to file name
        ],
        // If true, the uploaded file will be renamed to uniqid() + file extension in kb.
        'rename_file' => false,
        // If true, the uploading file's size will be verified for over than max_image_size/max_file_size.
        'should_validate_size' => true,
        'select_uploaded_file' => false,

        'max_image_size' => 10000,
        'max_file_size'  => 10000,

        'valid_mimetypes' => [
            'image' => [
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/gif',
                'image/svg+xml',
            ],
            'file' => [
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/gif',
                'image/svg+xml',
                'application/pdf',
                'application/zip',
                'application/msword',
                'application/epub+zip',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-powerpoint',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv',
                'text/plain',
            ],
        ],
        'file_icon_array' => [
            'pdf'  => 'fa-file-pdf',
            'doc'  => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls'  => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'zip'  => 'fa-file-archive',
            'gif'  => 'fa-file-image',
            'jpg'  => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png'  => 'fa-file-image',
            'ppt'  => 'fa-file-powerpoint',
            'pptx' => 'fa-file-powerpoint',
        ],


        /*
        |--------------------------------------------------------------------------
        | File Extension Information
        |--------------------------------------------------------------------------
        */
        'file_icon_array' => [
            'pdf'  => 'fa-file-pdf',
            'doc'  => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls'  => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'zip'  => 'fa-file-archive',
            'gif'  => 'fa-file-image',
            'jpg'  => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png'  => 'fa-file-image',
            'ppt'  => 'fa-file-powerpoint',
            'pptx' => 'fa-file-powerpoint',
        ],
    ],
    /**
     * Redirect when attempting to access some routes or files
     */
    'rickroll' => [
        /**
         * Url to redirect
         */
        'url' => 'https://youtube.com', // must start with https
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

];
