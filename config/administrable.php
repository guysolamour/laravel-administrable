<?php

return [

    'app_first_name' => config('app.first_name', 'Admin'),
    'app_last_name' => config('app.last_name', 'Admin'),
    'app_short_name' => config('app.short_namr', 'Lvl'),

    'auth_prefix_path' => 'administrable',

    'models' => [
        'folder' => 'App/Models',
        'category' => [
            'name' =>['name' => 'name', 'type' => 'string', 'rules' => 'required'],
            'description' =>['name' => 'description', 'type' => 'text', 'rules' => 'required'],
        ],
       'terrain' => [
           'title' => [
               'name' => 'title' , 'type' => 'string','rules' => 'required'
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
