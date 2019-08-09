<?php

return [
    'models' => [
        'Certification' => [
            'attributes' => [
                'title' => 'required|min:5',
                'description' => 'required'
            ]
        ],
        'Post' => [
            'fields' => [
                'url' => [
                    'type' => 'text',
                    'label' => 'messagerie',
                     'attributes' => [
                         'data-min' => 5
                     ],
                    'rules' => 'required|min:5',
                    'relations' => 'categories'
                ],
                'morphs' => true,
            ],
            'routes' => ['index','edit','update']
        ]
    ]
];
