<?php

declare(strict_types=1);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
if (!defined('URL_PATH')) {
    define('URL_PATH', 'stageMaster/public');
}
if (!defined('URL')) {
    define('URL', 'http://localhost');
}
if (!defined('THEME')) {
    define('THEME', 'dark');
}

// Composer PSR-4 autoloader (App\\ => app/, Tests\\ => tests/).
$autoload = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

// Legacy entry point retained so external scripts and tests still work; it just
// re-includes the database adapter and any helpers that don't follow PSR-4 yet.
require_once BASE_PATH . '/public/functions.php';
require_once BASE_PATH . '/app/Models/databaseConnector.php';

if (!function_exists('checkSessionExpiration')) {
    function checkSessionExpiration(): void
    {
        // Placeholder for future session/auth middleware (see App\Auth).
    }
}
