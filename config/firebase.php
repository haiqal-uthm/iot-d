<?php

return [
'credentials' => [
    'json' => function () {
        $base64 = env('FIREBASE_BASE64_CREDENTIALS');

        if (!$base64) {
            throw new \Exception('FIREBASE_BASE64_CREDENTIALS is not set');
        }

        // Optional decryption (if encrypted)
        // $base64 = Crypt::decryptString($base64);

        $jsonString = base64_decode($base64);

        if (!$jsonString) {
            throw new \Exception('Invalid base64 FIREBASE_BASE64_CREDENTIALS');
        }

         try {
            $jsonString = Crypt::decryptString($jsonString);
        } catch (\Exception $e) {
            throw new \Exception('Decryption failed: ' . $e->getMessage());
        }

        $json = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON in Firebase credentials: ' . json_last_error_msg());
        }

        if (isset($json['private_key'])) {
            $json['private_key'] = str_replace('\\n', "\n", $json['private_key']);
        }

        return $json;
    },
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
