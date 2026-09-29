<?php

/**
 * Vercel serverless entry point for VJFlix Uganda (Laravel 12).
 *
 * The deployed filesystem is read-only apart from /tmp, and each function
 * instance boots cold, so this bootstrap:
 *   1. Seeds the bootstrap cache in /tmp from the built copy, because Laravel
 *      needs a writable path for services.php and packages.php and those env
 *      vars only take effect if the files exist.
 *   2. Creates the writable directories Laravel expects under /tmp.
 *   3. Points storage_path() at /tmp/storage.
 *   4. Hands off to public/index.php.
 *
 * The matching env overrides live in vercel.json.
 */

$cacheDir = '/tmp/bootstrap/cache';

// 1. Make sure a writable bootstrap cache exists. Copy the built manifest in
//    when it is present so a warm cache is reused, but never depend on it.
if (! is_dir($cacheDir)) {
    @mkdir($cacheDir, 0755, true);
}

$builtCacheDir = __DIR__.'/../bootstrap/cache';

if (is_dir($builtCacheDir)) {
    foreach (['services.php', 'packages.php'] as $manifest) {
        $target = $cacheDir.'/'.$manifest;

        if (! file_exists($target)) {
            $source = $builtCacheDir.'/'.$manifest;

            if (is_file($source) && is_readable($source)) {
                @copy($source, $target);
            }
        }
    }
}

// Keep the application pointed at the writable copy.
$_ENV['APP_SERVICES_CACHE'] = $cacheDir.'/services.php';
$_SERVER['APP_SERVICES_CACHE'] = $cacheDir.'/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $cacheDir.'/packages.php';
$_SERVER['APP_PACKAGES_CACHE'] = $cacheDir.'/packages.php';

// 2. Writable directories Laravel expects.
$tmpDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    $cacheDir,
];

foreach ($tmpDirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// 3. Point storage_path() at /tmp.
$_ENV['LARAVEL_STORAGE_PATH'] = '/tmp/storage';
$_SERVER['LARAVEL_STORAGE_PATH'] = '/tmp/storage';

// 4. Hand off to Laravel.
require __DIR__.'/../public/index.php';