<?php
/**
 * Vercel Serverless Entry Point for VJFlix Uganda (Laravel 12)
 *
 * Vercel's filesystem is read-only except for /tmp. This bootstrap:
 *   1. Creates the Laravel-required writable directories under /tmp.
 *   2. Sets LARAVEL_STORAGE_PATH so Laravel resolves storage_path() to /tmp/storage.
 *   3. Forwards the request to public/index.php.
 *
 * Corresponding vercel.json env overrides handle:
 *   - APP_CONFIG_CACHE, APP_ROUTES_CACHE, etc. → /tmp/*.php
 *   - VIEW_COMPILED_PATH                        → /tmp/storage/framework/views
 *   - SESSION_DRIVER=cookie, CACHE_STORE=array, LOG_CHANNEL=stderr
 */

// ── 1. Bootstrap writable /tmp directories ────────────────────────────────────
$tmpDirs = [
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirs as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ── 2. Point Laravel's storage path to /tmp ───────────────────────────────────
// Laravel 11+ checks $_ENV['LARAVEL_STORAGE_PATH'] in Application::storagePath()
$_ENV['LARAVEL_STORAGE_PATH']    = '/tmp/storage';
$_SERVER['LARAVEL_STORAGE_PATH'] = '/tmp/storage';

// ── 3. Hand off to Laravel ────────────────────────────────────────────────────
require __DIR__ . '/../public/index.php';
