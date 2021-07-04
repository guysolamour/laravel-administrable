<?php

return [
    'title'        => 'Référencement',
    'nav'          => [
        'home'         => 'Accueil',
        'facebook'     => 'Facebook',
        'twitter'      => 'Twitter',
        'author'       => 'Auteur',
    ],
    'page' => [
        'title' => [
            'label'    => 'Titre',
            'help'     => 'Le titre est très important pour les moteurs de recherche car c\'est lui qui incite les internautes à cliquer'
        ],
        'index'    => "Permettre aux robots d'indéxer la page",
        'follow'   => 'Permettre aux robots de suivre les liens',
        'canonicaluri' => [
            'label'    => 'Url canonique',
            'help'     => "Laisser vide à moins de savoir ce que vous faites"
        ],
        'description'  => [
            'label'    => 'Meta description',
            'help'     => "La meta description est le texte qui accompagne le titre lors de l'affichage sur facebook :num caractère(s) restant(s))"
        ],
        'author'       => [
            'label'    => 'Auteur de cet article / page web',
            'help'     => ''
        ],
        'keywords'     => [
            'label'    => 'Les mots clés',
            'help'     => 'Les mots clés doivent être séparés par une virgule'
        ],
    ],
    'og' => [
        'title'       => [
            'label'   => 'Titre de la page',
            'help'    => "Le titre est très important pour les moteurs de recherche car c'est lui qui incite les internautes à cliquer"
        ],
        'type'        => [
            'label'   => 'Type de page',
            'help'    => "Les valeurs acceptées sont article pour les articles de blog, produit et webpage pour une page internet classique",
            'select'  => [
                'webpage' => 'page',
                'article' => 'article',
            ],
        ],
        'description' => [
            'label'   => 'Description',
            'help'    => "La meta description est le texte qui accompagne le titre lors de l'affichage sur les moteurs de recherche"
        ],
        'uri'         => [
            'label'   => 'Url',
            'help'    => "Laisser vide à moins de savoir ce que vous faites"
        ],
        'image'       => [
            'label'   => 'Image',
            'help'    => 'Cette image sera utilisée lors du partage sur Facebook'
        ],
        'locale'      => [
            'label'   => 'Locale (langue)',
            'help'    => "Si vide la locale (langue) par défaut sera utilisée"
        ],
    ],
    'twitter' => [
        'title'       => [
            'label'   => 'Titre',
            'help'    => "Le titre est très important pour les moteurs de recherche car c'est lui qui incite les internautes à cliquer"
        ],
        'type'        => [
            'label'   => 'Carte Twitter',
            'help'    =>  "Les valeurs acceptées sont summary pour les articles de blog, produit et summary_large_image pour une page internet classique",
            'select'  => [
                'summary' => 'résumé',
                'summary_large_image' => 'résumé large avec image',
            ],
        ],
        'description' => [
            'label'   => 'Description',
            'help'    => "La meta description est le texte qui accompagne le titre lors de l'affichage sur les moteurs de recherche"
        ],
        'image'       => [
            'label'   => "Image Twitter",
            'help'    => "Cette image sera utilisée lors du partage sur Facebook"
        ],
    ],
];
