<?php

declare(strict_types=1);

// PHPUnit test bootstrap. Relies entirely on Composer PSR-4 autoload;
// legacy `require_once` chains have been removed.
require_once __DIR__ . '/../vendor/autoload.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Database adapter shim and small helpers used by some legacy tests.
require_once BASE_PATH . '/public/functions.php';
require_once BASE_PATH . '/app/Models/databaseConnector.php';
