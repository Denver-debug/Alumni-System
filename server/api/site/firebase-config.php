<?php
/**
 * Public Firebase Config API
 * GET /api/site/firebase-config
 */

require_once __DIR__ . '/../../utils/helpers.php';

$config = [
    'apiKey' => getenv('FIREBASE_API_KEY') ?: '',
    'authDomain' => getenv('FIREBASE_AUTH_DOMAIN') ?: '',
    'projectId' => getenv('FIREBASE_PROJECT_ID') ?: '',
    'storageBucket' => getenv('FIREBASE_STORAGE_BUCKET') ?: '',
    'messagingSenderId' => getenv('FIREBASE_MESSAGING_SENDER_ID') ?: '',
    'appId' => getenv('FIREBASE_APP_ID') ?: '',
];

$requiredKeys = ['apiKey', 'authDomain', 'projectId', 'appId'];
$missing = [];

foreach ($requiredKeys as $key) {
    if (trim((string)($config[$key] ?? '')) === '') {
        $missing[] = $key;
    }
}

respondSuccess([
    'firebase' => $config,
    'isConfigured' => count($missing) === 0,
    'missing' => $missing,
]);
