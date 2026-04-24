<?php

namespace App\Models;

use PDO;

class MediaQueue {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Get all queue items
     */
    public function getAll() {
        $sql = "SELECT mq.*, mp.file_path, mp.tipo_media, t.nome as talento_nome
                FROM media_queue mq
                LEFT JOIN media_performance mp ON mq.media_id = mp.id
                LEFT JOIN talenti t ON mq.talento_id = t.id
                ORDER BY mq.ordine_coda ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get queue by talent
     */
    public function getByTalento($talento_id) {
        $sql = "SELECT mq.*, mp.file_path, mp.tipo_media
                FROM media_queue mq
                LEFT JOIN media_performance mp ON mq.media_id = mp.id
                WHERE mq.talento_id = ?
                ORDER BY mq.ordine_coda ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$talento_id]);
        return $stmt->fetchAll();
    }

    /**
     * Add item to queue
     */
    public function add($data) {
        // Get max order
        $maxOrder = $this->db->query("SELECT MAX(ordine_coda) as max_ord FROM media_queue")->fetch()['max_ord'] ?? 0;
        
        $sql = "INSERT INTO media_queue (talento_id, media_id, ordine_coda, stato) 
                VALUES (:talento_id, :media_id, :ordine_coda, :stato)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'talento_id' => $data['talento_id'],
            'media_id' => $data['media_id'],
            'ordine_coda' => $maxOrder + 1,
            'stato' => $data['stato'] ?? 'pending'
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Update queue item status
     */
    public function updateStatus($id, $status) {
        $sql = "UPDATE media_queue SET stato = :stato WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['stato' => $status, 'id' => $id]);
    }

    /**
     * Reorder queue
     */
    public function reorder($orderedIds) {
        $this->db->beginTransaction();
        try {
            foreach ($orderedIds as $index => $id) {
                $sql = "UPDATE media_queue SET ordine_coda = :ordine WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    'ordine' => $index + 1,
                    'id' => $id
                ]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Remove item from queue
     */
    public function remove($id) {
        $stmt = $this->db->prepare("DELETE FROM media_queue WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get currently playing item
     */
    public function getPlaying() {
        $stmt = $this->db->prepare("SELECT * FROM media_queue WHERE stato = 'playing' LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get next pending item
     */
    public function getNextPending() {
        $stmt = $this->db->prepare("SELECT * FROM media_queue WHERE stato = 'pending' ORDER BY ordine_coda ASC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }
}
