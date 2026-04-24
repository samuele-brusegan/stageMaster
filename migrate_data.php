<?php
/*
 * Migration Script for Existing Data
 * Converts existing data to new schema
 */

require_once __DIR__ . '/app/bootstrap.php';

try {
    $db = (new DatabaseConnector())->getConnection();
    $db->beginTransaction();
    
    echo "Starting data migration...\n";
    
    // 1. Migrate existing note_luci and materiale_palco to note_tecniche
    echo "Migrating notes from talenti table...\n";
    $stmt = $db->query("SELECT id, note_luci, materiale_palco FROM talenti");
    $talenti = $stmt->fetchAll();
    
    $noteStmt = $db->prepare("INSERT INTO note_tecniche (talento_id, tipo, contenuto) VALUES (:talento_id, :tipo, :contenuto)");
    
    foreach ($talenti as $talent) {
        if (!empty($talent['note_luci'])) {
            $noteStmt->execute([
                'talento_id' => $talent['id'],
                'tipo' => 'luci',
                'contenuto' => $talent['note_luci']
            ]);
        }
        
        if (!empty($talent['materiale_palco'])) {
            $noteStmt->execute([
                'talento_id' => $talent['id'],
                'tipo' => 'materiale_palco',
                'contenuto' => $talent['materiale_palco']
            ]);
        }
    }
    echo "Notes migrated successfully.\n";
    
    // 2. Assign existing media to Screen 1
    echo "Assigning existing media to Screen 1...\n";
    $db->query("UPDATE media_performance SET screen_id = 1 WHERE screen_id IS NULL");
    echo "Media assigned to Screen 1.\n";
    
    // 3. Create default transitions for existing media
    echo "Creating default transitions for existing media...\n";
    $transStmt = $db->prepare("INSERT INTO transizioni (media_id, tipo_dissolvenza, durata_sec) VALUES (:media_id, :tipo, :durata)");
    
    $mediaStmt = $db->query("SELECT id, fade_in_sec, fade_out_sec FROM media_performance");
    $mediaItems = $mediaStmt->fetchAll();
    
    foreach ($mediaItems as $media) {
        $totalFade = ($media['fade_in_sec'] ?? 0) + ($media['fade_out_sec'] ?? 0);
        $transStmt->execute([
            'media_id' => $media['id'],
            'tipo' => 'fade_to_black',
            'durata' => $totalFade > 0 ? $totalFade : 0
        ]);
    }
    echo "Transitions created successfully.\n";
    
    // 4. Set default tipo_media for existing media
    echo "Setting default media types...\n";
    $db->query("UPDATE media_performance SET tipo_media = 'VIDEO' WHERE tipo_media IS NULL");
    echo "Media types set.\n";
    
    $db->commit();
    echo "\nMigration completed successfully!\n";
    
} catch (Exception $e) {
    $db->rollBack();
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
