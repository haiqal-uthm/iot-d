<?php

return [
'credentials' => [
        'json' => [
            'type'                        => env('FIREBASE_TYPE'),
            'project_id'                  => env('FIREBASE_PROJECT_ID'),
            'private_key_id'              => env('FIREBASE_PRIVATE_KEY_ID'),
            // Turn the “\n” sequences back into real newlines:
            'private_key'                 => str_replace('\\n', "\n", env('FIREBASE_PRIVATE_KEY')),
            'client_email'                => env('FIREBASE_CLIENT_EMAIL'),
            'client_id'                   => env('FIREBASE_CLIENT_ID'),
            'auth_uri'                    => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri'                   => 'https://oauth2.googleapis.com/token',
            'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
            'client_x509_cert_url'        => 'https://www.googleapis.com/robot/v1/metadata/x509/' . rawurlencode(env('FIREBASE_CLIENT_EMAIL')),
        ],
    ],


    'database' => [
    'url' => env('FIREBASE_DATABASE_URL'),
    'firestoreurl' => env('FIREBASE_FIRESTORE_DATABASE_URL'),
],

    'auth' => [
        'tenant_id' => null,
    ],

    'storage' => [
        'default_bucket' => env('FIREBASE_STORAGE_BUCKET'),  // Your Firebase storage bucket
    ],

    'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),

    'logging' => [
        'http_log_channel' => env('FIREBASE_HTTP_LOG_CHANNEL', null),
        'http_debug_log_channel' => env('FIREBASE_HTTP_DEBUG_LOG_CHANNEL', null),
    ],
];
