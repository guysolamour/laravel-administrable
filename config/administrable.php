<?php

return [

    'app_first_name'   => config('app.first_name', 'Admin'),
    'app_last_name'    => config('app.last_name', 'Admin'),
    'app_short_name'   => config('app.short_name', 'Lvl'),

     /**
      * Thèmes disponiblent adminlte,theadmin,cooladmin,tabler,themekit
      * Le thème ne doit pas être changé une fois que l'installation a été faite
      */
    'theme' => 'themekit',

    /**
     * Le guard utilise pour l'installation. Par défaut le valeur est admin
     * Cette valeur est aussi utilisée pour générer le crud. Après installation cette
     * valeur ne doit plus etre changé au risque de péter les vues du crud
     */
    'guard' => 'client',

    'logo_url' => 'img/logo-administrable.png',

    'auth_prefix_path' => 'administrable',

    'front_namespace' => 'Front',
    'back_namespace' => 'Back',

    'comments' => [
        /**
         * By default comments posted are marked as approved. If you want
         * to change this, set this option to true. Then, all comments
         * will need to be approved by setting the `approved` column to
         * `true` for each comment.
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

    'models' => [
        'folder' => 'App/Models',
        'image' => [
            'url' => ['name' => 'url', 'type' => 'string', 'rules' => 'required'],
            'entity' => true,
            'polymorphic' => true,
        ],
        'category' => [
            'name' => ['name' => 'name', 'type' => 'string', 'rules' => 'nullable', 'nullable' => true,],
            'title' => ['name' => 'title','' => 'salut','rules' => 'nullable'],
            'image' => ['name' => 'image','type' => 'image'],
            'description' =>['name' => 'description', 'type' => 'text', 'rules' => 'required'],
            'slug' => 'title',
            //'breadcrumb' => 'title'
        ],
//        'category' => [
//            'name' =>['name' => 'name', 'type' => 'string', 'rules' => 'required'],
//            'description' =>['name' => 'description', 'type' => 'text', 'rules' => 'required'],
//        ],
        'prestation' => [
            'name' =>['name' => 'name', 'type' => 'string', 'rules' => 'required'],
            'image' =>['name' => 'image', 'type' => 'image', 'rules' => 'required'],
            'description' =>['name' => 'description', 'type' => 'text', 'rules' => 'required'],
            'seed' => true,
            'slug' => 'name'
        ],
        'comment' => [
            'name' =>['name' => 'name', 'type' => 'string', 'rules' => ''],
            'image' =>['name' => 'image', 'type' => 'image', 'rules' => 'required'],
            'description' =>['name' => 'description', 'type' => 'text', 'rules' => 'required'],
            'morphs' => [
                'name' => 'morphs', 'type' => [
                    'relation' => [
                        'name' => 'One To Many (Polymorphic)',
                        'model' => 'App\Models\Image',
                        'property' => 'name'
                    ]
                ]
            ],
            'trans' => 'imagezoo',
            'slug' => 'name'
        ],
        'review' => [
            'name' => ['name' => 'name', 'type' => 'string'],
            'email' => ['name' => 'email', 'type' => 'string', 'rules' => 'required|email'],
            'phone_number' => ['name' => 'phone_number', 'type' => 'string','trans' => 'Guysolamour'],
            'content' => ['name' => 'content', 'type' => 'text', 'rules' => 'required|min:20'],
            'user_id' => [
                'name' => 'user_id', 'type' => [
                    'relation' => [
                        'name' => 'Many to One',
                        'model' => 'App\Models\User',
                        'property' => 'name'
                    ]
                ],
                'trans' => 'client',
                'guest' => ['name','email','phone_number']
            ],
            // 'product_id' => [
            //     'name' => 'product_id', 'type' => [
            //         'relation' => [
            //             'name' => 'Many to One',
            //             'model' => 'App\Models\Product',
            //             'property' => 'name'
            //         ]
            //     ]
            // ],
        ],
       'terrain' => [
           'title' => [
               'name' => 'title' , 'type' => 'string','rules' => 'required','default' => 'guy'
           ],
           'morphs' => [
               'name' => 'morphs', 'type' => [
                   'relation' => [
                       'name' => 'One To Many (Polymorphic)',
                       'model' => 'App\Models\Image',
                       'property' => 'name'
                   ]
               ],
           ],
           'category_id' => [
               'name' => 'category_id', 'type' => [
                   'relation' => [
                       'name' => 'Many to One',
                       'model' => 'App\Models\Category',
                       'property' => 'name'
                   ]
                ],
              // 'rules' => ''
           ]
       ],
//        'category' => [
//            [
//                'name' => 'title' ,
//                'type' => 'string',
//                'rules' => 'required'
//            ],
//            ['name' => 'description' , 'type' => 'text','rules' => 'required'],
//            ['name' => 'publish' , 'type' => 'boolean','rules' => 'required'],
//        ]
    ]
];
