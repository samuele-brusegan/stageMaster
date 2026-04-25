<?php

namespace Tests;

use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected PDO $db;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup test database connection
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3308';
        $db   = $_ENV['DB_NAME'] ?? 'olmos_talent_test';
        $user = $_ENV['DB_USERNAME'] ?? 'root';
        $pass = $_ENV['DB_PASSWORD'] ?? '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->db = new PDO($dsn, $user, $pass, $options);
        
        // Clean up database before each test
        $this->cleanDatabase();
        
        // Start transaction for each test
        $this->db->beginTransaction();
    }

    private function cleanDatabase(): void
    {
        // Drop all tables to ensure clean state
        $tables = ['transizioni', 'note_tecniche', 'media_queue', 'screens', 'player_state', 'media_performance', 'talenti', 'media'];
        
        foreach ($tables as $table) {
            try {
                $this->db->exec("DROP TABLE IF EXISTS $table");
            } catch (\PDOException $e) {
                // Table might not exist, continue
            }
        }
    }

    protected function tearDown(): void
    {
        // Rollback transaction to clean up test data
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        parent::tearDown();
    }

    protected function executeSql(string $sql): void
    {
        $this->db->exec($sql);
    }

    protected function insertTestData(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
        
        return (int) $this->db->lastInsertId();
    }
}
