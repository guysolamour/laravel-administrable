<?php

return [
    'livenews' => [
        'label'      => "Ruban d'actualité",
        'controller' => [
            'create' => 'Le ruban d\'actualité a bien été ajouté',
            'update' => "Le ruban d'actualité a bien été mise à jour",
            'delete' => "Le ruban d'actualité a bien été supprimé",
        ],
        'view' => [
            'text_color' => "Couleur de texte",
            'back_color' => 'Couleur de fond',
            'size'       => 'Taille du texte',
            'started_at' => 'Date de début',
            'ended_at'   => 'Date de fin',
            'content'    => 'Message à afficher',
            'uppercase'  => 'Mettre le texte en majuscule',
            'online'     => 'Afficher pour les utilisateurs',
            'actions'    => 'Actions',
            'text'       => 'Texte',
            'destroy'    => "Etes vous sûr de bien vouloir procéder à la suppression ?",
        ]
    ],
    'testimonial' => [
        'label'      => "Témoignages",
        'controller' => [
            'create' => "L'élément a bien été ajouté",
            'update' => "L'élément a bien été mis à jour",
            'delete' => "L'élément a bien été supprimé",
        ],
        'view' => [
            'name' => "Nom",
            'email' => 'Email',
            'job'       => 'Job',
            'online' => 'En ligne',
            'offline' => 'Hors ligne',
            'content'    => 'Contenu',
            'actions'    => 'Actions',
            'status'    => 'Status',
            'createdat' => 'Date ajout',
            'destroy'    => "Etes vous sûr de bien vouloir procéder à la suppression ?",
        ]
    ],
    'blog' => [
        'post' => [

        ],
        'category' => [

        ],
        'tag' => [

        ],
    ],
    'mailbox' => [
        'label'      => "Messagerie",
        'controller' => [
            'create' => "Nous avons réussi votre message et nous vous répondrons dans les plus brefs délais !",
            'update' => "L'élément a bien été mis à jour",
            'delete' => "L'élément a bien été supprimé",
            'note'   => [
                'create' => "La note a bien été ajoutée pour ce message",
                'update' => "La note a bien été modifiée",
                'delete' => "La note a bien été supprimée",
            ]
        ],
        'mail' => [
            'front' => [
                'subject' => 'Message de contact ',
            ],
            'back' => [
                'subject' => 'Message de contact ',
            ],
        ],
        'validation' => [
            'comment_required'    => "La note ne peut pas être vide",
            'email_required_if'   => "L'email est obligatoire et doit être valide pour envoyer un email",
            'subject_required_if' => "Le sujet est obligatoire et doit être valide pour envoyer un email",
        ],
        'view' => [
            'name'           => "Nom",
            'email'          => 'Email',
            'read'           => 'Lu',
            'from'           => 'De',
            'notes'          => 'Notes',
            'note_color'     => 'Couleur',
            'note_content'   => 'Contenu',
            'send_email'     => 'Envoyer un email à',
            'edit_note'      => 'Edition de note',
            'reading'        => 'Lecture de message',
            'unread'         => "messages non lu(s))",
            'phone_number'   => 'Numéro de téléphone',
            'send_copy'      => 'Envoyez-moi une copie du message',
            // 'online' => 'En ligne',
            // 'offline' => 'Hors ligne',
            'content'    => 'Message',
            'actions'    => 'Actions',
            // 'status'    => 'Status',
            'createdat'  => 'Date ajout',
            'destroy'    => "Etes vous sûr de bien vouloir procéder à la suppression ?",
        ]
    ],
]
;
