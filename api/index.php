<?php

/**
 * Entry point for Vercel Serverless Functions.
 * 
 * Vercel serverless functions run on a read-only filesystem,
 * except for the /tmp directory. We prepare the necessary
 * directory structure inside /tmp for Laravel's cache, views,
 * sessions, and logs.
 */

$directories = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap',
    '/tmp/bootstrap/cache',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Forward the incoming request to Laravel's public entry point
require __DIR__ . '/../public/index.php';
