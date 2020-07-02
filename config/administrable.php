<?php

return [

    /**
             * Utilisés dans le back office
             */
    'app_first_name'   => config('app.first_name', 'Admin'),
    'app_last_name'    => config('app.last_name', 'Admin'),
    'app_short_name'   => config('app.short_name', 'Lvl'),

    /**
         * Thèmes disponiblent adminlte,theadmin,cooladmin,tabler,themekit
         * Le thème ne doit pas être changé une fois que l'installation a été faite
         */
    'theme' => 'theadmin',

    /**
         * Le guard utilise pour l'installation. Par défaut le valeur est admin
         * Cette valeur est aussi utilisée pour générer le crud. Après installation cette
     * valeur ne doit plus etre changé au risque de péter les vues du crud
     */
    'guard' => 'client',

    /**
     * Le lien du logo à utiliser pour l'administration
     */
    'logo_url' => 'img/logo-administrable.png',

    /**
     * Administration routes prefix.
     */
    'auth_prefix_path' => 'administrable',

    /**
     * Le nom du dossier ou sera stocké les controllers du front office dans le dossier controller
     */
    'front_namespace' => 'Front',
    /**
     * Le nom du dossier ou sera stocké les controllers du back office dans le dossier controller
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
