<?php

return [
'credentials' => [
    'file' => storage_path('app/firebase/' . env('FIREBASE_CREDENTIALS')),
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
