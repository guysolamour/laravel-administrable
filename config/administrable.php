<?php

return [

    'app_first_name' => config('app.first_name', 'Admin'),
    'app_last_name' => config('app.last_name', 'Admin'),
    'app_short_name' => config('app.short_namr', 'Lvl'),

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
