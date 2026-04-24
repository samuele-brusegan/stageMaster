<?php
/*
 * Run Schema Migration via PHP
 */

require_once __DIR__ . '/app/bootstrap.php';

try {
    $db = (new DatabaseConnector())->getConnection();
    
    echo "Starting schema migration...\n";
    
    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/schema_migration.sql');
    
    // Split by semicolon and execute each statement
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !str_starts_with($statement, '--') && !str_starts_with($statement, 'USE')) {
            try {
                $db->exec($statement);
                echo "Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (Exception $e) {
                echo "Error executing statement: " . $e->getMessage() . "\n";
                echo "Statement: " . $statement . "\n";
            }
        }
    }
    
    echo "\nSchema migration completed successfully!\n";
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
