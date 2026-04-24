<?php

namespace App\Models;

use PDO;

class NoteTecniche {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Get all notes
     */
    public function getAll() {
        $sql = "SELECT nt.*, t.nome as talento_nome
                FROM note_tecniche nt
                LEFT JOIN talenti t ON nt.talento_id = t.id
                ORDER BY nt.tipo, nt.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get notes by talent
     */
    public function getByTalento($talento_id) {
        $sql = "SELECT * FROM note_tecniche WHERE talento_id = ? ORDER BY tipo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$talento_id]);
        return $stmt->fetchAll();
    }

    /**
     * Get notes by type
     */
    public function getByType($tipo) {
        $sql = "SELECT nt.*, t.nome as talento_nome
                FROM note_tecniche nt
                LEFT JOIN talenti t ON nt.talento_id = t.id
                WHERE nt.tipo = ?
                ORDER BY nt.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tipo]);
        return $stmt->fetchAll();
    }

    /**
     * Create a new note
     */
    public function create($data) {
        $sql = "INSERT INTO note_tecniche (talento_id, tipo, contenuto) 
                VALUES (:talento_id, :tipo, :contenuto)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'talento_id' => $data['talento_id'] ?? null,
            'tipo' => $data['tipo'],
            'contenuto' => $data['contenuto']
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Update note
     */
    public function update($id, $data) {
        $sql = "UPDATE note_tecniche SET 
                talento_id = :talento_id, 
                tipo = :tipo, 
                contenuto = :contenuto 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    /**
     * Delete note
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM note_tecniche WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get notes grouped by type for a talent
     */
    public function getGroupedByType($talento_id) {
        $notes = $this->getByTalento($talento_id);
        $grouped = [
            'materiale_palco' => [],
            'luci' => [],
            'generiche' => [],
            'pause' => []
        ];
        
        foreach ($notes as $note) {
            $grouped[$note['tipo']][] = $note;
        }
        
        return $grouped;
    }
}
