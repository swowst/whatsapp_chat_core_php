<?php
return [
    'databases' => [
        'default' => [
            'host'     => '127.0.0.1',
            'port'     => 27017,
            'username' => null,
            'password' => null,
            'dbname'   => 'notes',
        ],
    ],
    'default_constants' => [
        'timezone' => 100,
        'user_timezone' => 101,
    ],
    'skipped_filtering_collections' => [ // Will skip filtering collection in DBManager
        "companies", "logs_access"
    ],
    's2s'   => [
        'app_id'       => 256,
        'server_token' => 'n0T3sChXBn2HbmUHdk1AaQDRJasdkAaKs09j2KNl2wka0023l2msa',
        'timezone'     => 101,
    ],
];
