<?php

namespace App\Models;

use PDO;

class Transizione {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Get transition by media ID
     */
    public function getByMedia($media_id) {
        $stmt = $this->db->prepare("SELECT * FROM transizioni WHERE media_id = ?");
        $stmt->execute([$media_id]);
        return $stmt->fetch();
    }

    /**
     * Create a new transition
     */
    public function create($data) {
        $sql = "INSERT INTO transizioni (media_id, tipo_dissolvenza, durata_sec, offset_prima_sec, offset_dopo_sec) 
                VALUES (:media_id, :tipo_dissolvenza, :durata_sec, :offset_prima_sec, :offset_dopo_sec)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'media_id' => $data['media_id'],
            'tipo_dissolvenza' => $data['tipo_dissolvenza'] ?? 'fade_to_black',
            'durata_sec' => $data['durata_sec'] ?? 0,
            'offset_prima_sec' => $data['offset_prima_sec'] ?? 0,
            'offset_dopo_sec' => $data['offset_dopo_sec'] ?? 0
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Update transition
     */
    public function update($media_id, $data) {
        $sql = "UPDATE transizioni SET 
                tipo_dissolvenza = :tipo_dissolvenza, 
                durata_sec = :durata_sec, 
                offset_prima_sec = :offset_prima_sec, 
                offset_dopo_sec = :offset_dopo_sec 
                WHERE media_id = :media_id";
        $stmt = $this->db->prepare($sql);
        $data['media_id'] = $media_id;
        return $stmt->execute($data);
    }

    /**
     * Delete transition
     */
    public function delete($media_id) {
        $stmt = $this->db->prepare("DELETE FROM transizioni WHERE media_id = ?");
        return $stmt->execute([$media_id]);
    }

    /**
     * Get or create transition for media
     */
    public function getOrCreate($media_id) {
        $transition = $this->getByMedia($media_id);
        if (!$transition) {
            $this->create(['media_id' => $media_id]);
            $transition = $this->getByMedia($media_id);
        }
        return $transition;
    }
}
