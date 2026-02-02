<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ID des rôles
    |--------------------------------------------------------------------------
    |
    | Ces identifiants sont utilisés dans la base de données pour référencer
    | les différents rôles des utilisateurs.
    |
    */
    'role_ids' => [
        'ADMIN' => 3,
        'ORGANIZER' => 2,
        'USER' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Noms des rôles
    |--------------------------------------------------------------------------
    |
    | Ces noms sont utilisés pour l'affichage dans l'interface utilisateur.
    |
    */
    'role_names' => [
        3 => 'Administrateur',
        2 => 'Organisateur',
        1 => 'Utilisateur',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions par rôle
    |--------------------------------------------------------------------------
    |
    | Définit les permissions accordées à chaque rôle.
    |
    */
    'permissions' => [
        'ADMIN' => [
            'manage_users',
            'manage_events',
            'manage_organizers',
            'manage_tickets',
            'manage_payments',
            'view_reports',
            'manage_settings',
            'manage_blog',
            'manage_organizer_requests',
        ],
        'ORGANIZER' => [
            'manage_own_events',
            'manage_own_tickets',
            'view_own_reports',
            'scan_tickets',
            'manage_own_blog',
        ],
        'USER' => [
            'purchase_tickets',
            'view_own_tickets',
            'view_own_orders',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rôle par défaut
    |--------------------------------------------------------------------------
    |
    | Rôle attribué par défaut aux nouveaux utilisateurs.
    |
    */
    'default_role' => 'USER',
];