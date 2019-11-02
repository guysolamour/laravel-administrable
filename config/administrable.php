<?php

return [

    'app_first_name' => config('app.first_name', 'Admin'),
    'app_last_name' => config('app.last_name', 'Admin'),
    'app_short_name' => config('app.short_namr', 'Lvl'),

    'logo_url' => '/img/logo.png',

    'auth_prefix_path' => 'administrable',

    'models' => [
        'folder' => 'App/Models',
        'image' => [
            'url' => ['name' => 'url', 'type' => 'string', 'rules' => 'required'],
            'entity' => true,
            'polymorphic' => true,
        ],
        'category' => [
            'name' =>['name' => 'name',],
            'description' =>['name' => 'description', 'type' => 'text', 'rules' => 'required'],
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
                ],
            ],
            'slug' => 'name'
        ],
       'terrain' => [
           'title' => [
               'name' => 'title' , 'type' => 'string','rules' => 'required'
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
