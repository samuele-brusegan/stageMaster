<?php

declare(strict_types=1);

/**
 * Legacy adapter kept for backward compatibility. New code should use
 * \App\Database\Connection::getInstance() directly.
 */

require_once dirname(__DIR__) . '/Database/Connection.php';

if (!class_exists('DatabaseConnector', false)) {
    class DatabaseConnector
    {
        private \PDO $connection;

        public function __construct()
        {
            $this->connection = \App\Database\Connection::getInstance();
        }

        public function getConnection(): \PDO
        {
            return $this->connection;
        }
    }
}
