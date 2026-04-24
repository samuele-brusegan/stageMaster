<?php

namespace App\Models;

use PDO;

class MediaLibrary {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Create a new media entry
     */
    public function create($data) {
        $sql = "INSERT INTO media (file_name, file_path, file_type, file_size, duration_sec) 
                VALUES (:file_name, :file_path, :file_type, :file_size, :duration_sec)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'file_name' => $data['file_name'],
            'file_path' => $data['file_path'],
            'file_type' => $data['file_type'],
            'file_size' => $data['file_size'] ?? null,
            'duration_sec' => $data['duration_sec'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Get all media
     */
    public function getAll() {
        $sql = "SELECT * FROM media ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get media by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM media WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get media by file path
     */
    public function findByPath($file_path) {
        $stmt = $this->db->prepare("SELECT * FROM media WHERE file_path = ?");
        $stmt->execute([$file_path]);
        return $stmt->fetch();
    }

    /**
     * Delete media
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM media WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get media by type
     */
    public function getByType($type) {
        $stmt = $this->db->prepare("SELECT * FROM media WHERE file_type = ? ORDER BY created_at DESC");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }
}
