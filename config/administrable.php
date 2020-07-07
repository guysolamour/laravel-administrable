<?php

return [

    /**
     * Used in the back office
    */
    'app_first_name'   => config('app.first_name', 'Admin'),
    'app_last_name'    => config('app.last_name', 'Admin'),
    'app_short_name'   => config('app.short_name', 'Lvl'),

    /**
     * Available theme are adminlte,theadmin,cooladmin,tabler,themekit
     * The theme should not be changed once the installation has been done
     */
    'theme' => 'adminlte',

    /**
     * The guard used for the installation. By default the value is admin.
     * This value is also used to generate the crud.
     * After installation, this value should no longer be changed at the risk of farting the views of the crud
     */
    'guard' => 'admin',

    /**
     * The logo link to use for administration
     */
    'logo_url' => 'img/logo-administrable.png',

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
