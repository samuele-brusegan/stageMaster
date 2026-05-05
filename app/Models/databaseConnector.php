<?php
class DatabaseConnector {
    private $connection;

    public function __construct() {
        require_once dirname(__DIR__, 2) . '/config/database.php';
        try {
            $this->connection = getDatabaseConnection();
        } catch (PDOException $e) {
            throw new RuntimeException("Connection failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function getConnection() {
        return $this->connection;
    }
}
