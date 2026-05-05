<?php

/**
 * Database Configuration
 * Returns a PDO connection instance.
 */

function getDatabaseConnection() {
    // Load .env variables if not already loaded (simple implementation)
    $envPath = __DIR__ . '/../.env';
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            if (getenv($name) === false && !isset($_ENV[$name])) {
                $_ENV[$name] = trim($value);
            }
        }
    }

    $env = static function (string $name, $default = null) {
        $value = getenv($name);
        return $value !== false ? $value : ($_ENV[$name] ?? $default);
    };

    $host = $env('DB_HOST', 'db');
    $port = $env('DB_PORT');
    $db   = $env('DB_NAME', 'olmos_talent');
    $user = $env('DB_USERNAME', 'olmos_user');
    $pass = $env('DB_PASSWORD', 'user_password');
    $charset = 'utf8mb4';

    $portPart = $port ? ";port=$port" : '';
    $dsn = "mysql:host=$host{$portPart};dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}
