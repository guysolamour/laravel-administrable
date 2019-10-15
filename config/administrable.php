<?php

return [
    'app_first_name' => env('APP_FIRST_NAME', 'Admin'),
    'app_last_name' => env('APP_LAST_NAME', 'Admin'),
    'app_short_name' => env('APP_SHORT_NAME', 'Lvl'),

    'auth_prefix_path' => 'administrable',

    /*'models' => [
        'folder' => 'App/Models',
        'post' => [
            ['name' => 'title' , 'type' => 'string','rules' => 'required'],
            ['name' => 'description' , 'type' => 'text','rules' => 'required'],
            ['name' => 'publish' , 'type' => 'boolean','rules' => 'required'],
        ],
        'category' => [
            [
                'name' => 'title' ,
                'type' => 'string',
                'rules' => 'required'
            ],
            ['name' => 'description' , 'type' => 'text','rules' => 'required'],
            ['name' => 'publish' , 'type' => 'boolean','rules' => 'required'],
        ]
    ]*/
];
