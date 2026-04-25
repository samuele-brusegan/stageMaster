<?php
define('BASE_PATH', dirname(__DIR__));
define('URL_PATH', 'stageMaster/public'); // Update this if the app is in a subdirectory
define('URL', 'http://localhost'); // Base URL
define('THEME', 'dark');

require_once BASE_PATH . '/public/imports.php';

function checkSessionExpiration() {
    // Basic implementation for now
}
