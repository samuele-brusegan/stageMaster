<?php

namespace App\Models;

use PDO;

class PlayerState {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Get current state for a component
     */
    public function getState($component) {
        $stmt = $this->db->prepare("SELECT * FROM player_state WHERE component = ?");
        $stmt->execute([$component]);
        return $stmt->fetch();
    }

    /**
     * Get all components state
     */
    public function getAllStates() {
        $sql = "SELECT * FROM player_state";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Update or insert state for a component
     */
    public function updateState($component, $data) {
        $sql = "INSERT INTO player_state (component, current_talento_id, current_media_id, status)
                VALUES (:component, :talento_id, :media_id, :status)
                ON DUPLICATE KEY UPDATE 
                current_talento_id = VALUES(current_talento_id),
                current_media_id = VALUES(current_media_id),
                status = VALUES(status),
                last_update = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'component'  => $component,
            'talento_id' => $data['current_talento_id'] ?? null,
            'media_id'   => $data['current_media_id'] ?? null,
            'status'     => $data['status'] ?? 'stopped'
        ]);
    }
}
