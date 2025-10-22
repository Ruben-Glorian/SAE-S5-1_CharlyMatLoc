<?php
return [
    'settings' => [
        'db' => [
            'dsn' => 'pgsql:host=charlyMatLoc.db;dbname=charlyMatLoc',
            'user' => 'admin',
            'password' => 'admin',
        ],
        'db_catalogue' => [
            'dsn' => 'pgsql:host=charlyMatLoc.db;dbname=charlyMatLoc',
            'user' => 'admin',
            'password' => 'admin',
        ],
        'jwt' => [
            'key' => 'clef'
        ],
        'displayErrorDetails' => true,
    ],
];