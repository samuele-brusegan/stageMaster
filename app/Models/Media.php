<?php

namespace App\Models;

use PDO;

class Media {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Create a new media entry
     */
    public function create($data) {
        $sql = "INSERT INTO media_performance (talento_id, tipo_output, file_path, timestamp_inizio, timestamp_fine, fade_in_sec, fade_out_sec, ordine_esecuzione) 
                VALUES (:talento_id, :tipo_output, :file_path, :timestamp_inizio, :timestamp_fine, :fade_in_sec, :fade_out_sec, :ordine_esecuzione)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'talento_id'        => $data['talento_id'],
            'tipo_output'       => $data['tipo_output'],
            'file_path'         => $data['file_path'],
            'timestamp_inizio'  => $data['timestamp_inizio'] ?? '00:00:00',
            'timestamp_fine'    => $data['timestamp_fine'] ?? null,
            'fade_in_sec'       => $data['fade_in_sec'] ?? 0,
            'fade_out_sec'      => $data['fade_out_sec'] ?? 0,
            'ordine_esecuzione' => $data['ordine_esecuzione'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Get media by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM media_performance WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Update media information
     */
    public function update($id, $data) {
        $sql = "UPDATE media_performance SET 
                talento_id = :talento_id, 
                tipo_output = :tipo_output, 
                file_path = :file_path, 
                timestamp_inizio = :timestamp_inizio, 
                timestamp_fine = :timestamp_fine, 
                fade_in_sec = :fade_in_sec, 
                fade_out_sec = :fade_out_sec, 
                ordine_esecuzione = :ordine_esecuzione 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    /**
     * Delete media
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM media_performance WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get all media for a specific talent
     */
    public function getByTalento($talento_id) {
        $stmt = $this->db->prepare("SELECT * FROM media_performance WHERE talento_id = ? ORDER BY ordine_esecuzione ASC");
        $stmt->execute([$talento_id]);
        return $stmt->fetchAll();
    }
}
