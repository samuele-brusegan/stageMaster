<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Singleton wrapper around the PDO connection used across the app.
 * Loads configuration from `.env` (or environment) via config/database.php.
 */
class Connection
{
    private static ?PDO $pdo = null;

    public static function getInstance(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        require_once dirname(__DIR__, 2) . '/config/database.php';

        try {
            self::$pdo = getDatabaseConnection();
        } catch (PDOException $e) {
            throw new RuntimeException('Database connection failed: ' . $e->getMessage(), 0, $e);
        }

        return self::$pdo;
    }

    public static function reset(): void
    {
        self::$pdo = null;
    }

    public static function set(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }
}
