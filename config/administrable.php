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
    'theme' => 'theadmin',
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

    /**
     * Use PHP callable syntax...
     * use App\Http\Controllers\UserController;
     * Route::get('/users', [UserController::class, 'index']);

     * Use string syntax...
     * Route::get('/users', 'App\Http\Controllers\UserController@index');
     */
    'route_controller_callable_syntax' => true,

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
    /*
    |--------------------------------------------------------------------------
    | EXTENSIONS
    |--------------------------------------------------------------------------
    |
    | Pour utiliser une extension, vous devez passer l'option activate de cette extensions à `true`
    | et lancer la commande `php artisan administrable:add:extension extensionanme`
    |
    */
    'extensions' => [
        'livenews' => [
            'activate'    => false,
            'model'       => \Guysolamour\Administrable\Models\Extensions\Livenews\Livenews::class,
            'back'     => [
                'form'       => \Guysolamour\Administrable\Forms\Back\Extensions\Livenews\LivenewsForm::class,
                'controller' => \Guysolamour\Administrable\Http\Controllers\Back\Extensions\Livenews\LivenewsController::class,
            ],

        ],
        'blog' => [
            'activate' => false,
            'post' => [
                'model' => \Guysolamour\Administrable\Models\Extensions\Blog\Post::class,
                'back' => [
                    'form'       => \Guysolamour\Administrable\Forms\Back\Extensions\Blog\PostForm::class,
                    'controller' => \Guysolamour\Administrable\Http\Controllers\Back\Extensions\Blog\PostController::class,
                ],
                'front' => [
                    'controller' => \Guysolamour\Administrable\Http\Controllers\Front\Extensions\Blog\PostController::class,
                ],
            ],
            'category' => [
                'model' => \Guysolamour\Administrable\Models\Extensions\Blog\Category::class,
                'back'  => [
                    'controller' => \Guysolamour\Administrable\Http\Controllers\Back\Extensions\Blog\CategoryController::class,
                    'form'       => \Guysolamour\Administrable\Forms\Back\Extensions\Blog\CategoryForm::class,
                ],
            ],
            'tag' => [
                'model' => \Guysolamour\Administrable\Models\Extensions\Blog\Tag::class,
                'back'  => [
                    'controller' => \Guysolamour\Administrable\Http\Controllers\Back\Extensions\Blog\TagController::class,
                    'form'       => \Guysolamour\Administrable\Forms\Back\Extensions\Blog\TagForm::class,
                ],
            ],
        ],
        'testimonial' => [
            'activate' => false,
            'model'    => \Guysolamour\Administrable\Models\Extensions\Testimonial\Testimonial::class,
            'back'     => [
                'form'       => \Guysolamour\Administrable\Forms\Back\Extensions\Testimonial\TestimonialForm::class,
                'controller' => \Guysolamour\Administrable\Http\Controllers\Back\Extensions\Testimonial\TestimonialController::class,
            ],
            'front'     => [
                'controller' => \Guysolamour\Administrable\Http\Controllers\Front\Extensions\Testimonial\TestimonialController::class,
            ],
        ],
        'mailbox' => [
            'activate'    => false,
            'model'       => \Guysolamour\Administrable\Models\Extensions\Mailbox\Mailbox::class,
            'back'     => [
                'notification'    => \Guysolamour\Administrable\Notifications\Back\Extensions\Mailbox\ContactNotification::class,
                'mail'            => \Guysolamour\Administrable\Mail\Back\Extensions\Mailbox\ContactMail::class,
                'note_mail'       => \Guysolamour\Administrable\Mail\Front\NoteAnswerMail::class,
                'controller'      => \Guysolamour\Administrable\Http\Controllers\Back\Extensions\Mailbox\MailboxController::class,
            ],
            'front'     => [
                'note_mail'       => \Guysolamour\Administrable\Mail\Front\NoteAnswerMail::class,
                'form'            => \Guysolamour\Administrable\Forms\Front\Extensions\Mailbox\ContactForm::class,
                'controller'      => \Guysolamour\Administrable\Http\Controllers\Front\Extensions\Mailbox\ContactController::class,
                'mail'            => \Guysolamour\Administrable\Mail\Front\Extensions\Mailbox\SendMeContactMessageMail::class,
            ],
        ],
        'ad' => [
            'activate'    => true,
            'models'      => [
                'ad'      => Guysolamour\Administrable\Models\Extensions\Ad\Ad::class,
                'group'   => Guysolamour\Administrable\Models\Extensions\Ad\Group::class,
                'type'    => Guysolamour\Administrable\Models\Extensions\Ad\Type::class,
            ],
            'controllers' => [
                'back'  => [
                    'ad'    =>  Guysolamour\Administrable\Http\Controllers\Back\Extensions\Ad\AdController::class,
                    'group' =>  Guysolamour\Administrable\Http\Controllers\Back\Extensions\Ad\GroupController::class,
                ],
            ],
            'forms' => [
                'back' => [
                    'ad'    => Guysolamour\Administrable\Forms\Back\Extensions\Ad\AdForm::class,
                    'group' => Guysolamour\Administrable\Forms\Back\Extensions\Ad\GroupForm::class,
                ],
            ],
        ],
        'shop' => [
            'activate'          => false,
            'cart_dbname'       => 'shop_cart',
            'destroy_on_logout' => false,
            /**
             * Where to redirect when cart is empty
             */
            'redirect_empty_cart' => '/',
            /**
             * Each time a product is visited, it is saved as a cookie
             * to display them later in the recently viewed product section.
             */
            'recently_view_cookie_duration'      =>  525600, // one year in minutes
            /**
             * To display the imagemanager.
             * To activate a collection remove the false and add a label or to remove a model set the label to false
             */
            'media_collections' => ['front-image' => 'Image du produit', 'back-image' => false, 'images' => 'Gallerie'],
            'custom_fields'   => [
                // 'product' => [
                //     ['name' => 'display_in_slider',      'type' => 'boolean',   'label' => 'Mise en avant sous le menu'],
                //     ['name' => 'week_deal',              'type' => 'boolean',   'label' => 'Deal de la semaime'],
                //     ['name' => 'tendance',               'type' => 'boolean',   'label' => 'Tendance'],
                //     ['name' => 'display_in_home_slider', 'type' => 'boolean',   'label' => "Diaporama page d'accueil"],
                // ]
                'settings' => [
                    ['name' => 'send_order_confirmed_client_email', 'type' => 'boolean',   'label' => "Envoyer un mail au client lors de la confirmation de la commande"],
                ],
            ],
            'models'  => [
                'brand'          => Guysolamour\Administrable\Models\Extensions\Shop\Brand::class,
                'order'          => Guysolamour\Administrable\Models\Extensions\Shop\Order::class,
                'coupon'         => Guysolamour\Administrable\Models\Extensions\Shop\Coupon::class,
                'review'         => Guysolamour\Administrable\Models\Extensions\Shop\Review::class,
                'product'        => Guysolamour\Administrable\Models\Extensions\Shop\Product::class,
                'command'        => Guysolamour\Administrable\Models\Extensions\Shop\Command::class,
                'setting'        => Guysolamour\Administrable\Settings\ShopSettings::class,
                'deliver'        => Guysolamour\Administrable\Models\Extensions\Shop\Deliver::class,
                'category'       => Guysolamour\Administrable\Models\Extensions\Shop\Category::class,
                'attribute'      => Guysolamour\Administrable\Models\Extensions\Shop\Attribute::class,
                'deliverprice'   => Guysolamour\Administrable\Models\Extensions\Shop\DeliverPrice::class,
                'coveragearea'   => Guysolamour\Administrable\Models\Extensions\Shop\CoverageArea::class,
                'attributeterm'  => Guysolamour\Administrable\Models\Extensions\Shop\AttributeTerm::class,
            ],
            'controllers' => [
                'back' => [
                    'brand'         => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\BrandController::class,
                    'order'         => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\OrderController::class,
                    'client'        => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\ClientController::class,
                    'coupon'        => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\CouponController::class,
                    'review'        => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\ReviewController::class,
                    'product'       => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\ProductController::class,
                    'command'       => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\CommandController::class,
                    'setting'       => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\SettingController::class,
                    'deliver'       => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\DeliverController::class,
                    'category'      => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\CategoryController::class,
                    'attribute'     => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\AttributeController::class,
                    'deliverprice'  => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\DeliverPriceController::class,
                    'coveragearea'  => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\CoverageAreaController::class,
                    'attributeterm' => Guysolamour\Administrable\Http\Controllers\Back\Extensions\Shop\AttributeTermController::class,
                ]
            ],
            'forms' => [
                'back' => [
                    'attribute'     => Guysolamour\Administrable\Forms\Back\Extensions\Shop\AttributeForm::class,
                    'brand'         => Guysolamour\Administrable\Forms\Back\Extensions\Shop\BrandForm::class,
                    'category'      => Guysolamour\Administrable\Forms\Back\Extensions\Shop\CategoryForm::class,
                    'coupon'        => Guysolamour\Administrable\Forms\Back\Extensions\Shop\CouponForm::class,
                    'coveragearea'  => Guysolamour\Administrable\Forms\Back\Extensions\Shop\CoverageAreaForm::class,
                    'deliver'       => Guysolamour\Administrable\Forms\Back\Extensions\Shop\DeliverForm::class,
                    'product'       => Guysolamour\Administrable\Forms\Back\Extensions\Shop\ProductForm::class,
                    'review'        => Guysolamour\Administrable\Forms\Back\Extensions\Shop\ReviewForm::class,
                ]
            ],
            'notifications' => [
                'back' => [
                    'commandsent' => Guysolamour\Administrable\Notifications\Back\Extensions\Shop\CommandSentNotification::class,
                ],
            ],
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | EXTENSIONS -> Migrations
    |--------------------------------------------------------------------------
    |
    | Where to store extensions migrations.
    |
    */
    'migrations_path' => database_path('extensions'),

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
        'social_redirect' => [
            'controller' => Guysolamour\Administrable\Http\Controllers\Front\RedirectController::class,
            'networks'   => ['facebook', 'twitter', 'linkedin', 'youtube']
        ]
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
