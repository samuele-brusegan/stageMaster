<?php

namespace App\Models;

use PDO;

class Talento {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Create a new talent
     */
    public function create($data) {
        $sql = "INSERT INTO talenti (nome, categoria, materiale_palco, note_luci, ordine_scaletta) 
                VALUES (:nome, :categoria, :materiale_palco, :note_luci, :ordine_scaletta)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nome'            => $data['nome'],
            'categoria'       => $data['categoria'] ?? null,
            'materiale_palco' => $data['materiale_palco'] ?? null,
            'note_luci'       => $data['note_luci'] ?? null,
            'ordine_scaletta' => $data['ordine_scaletta'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    /**
     * Get talent by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM talenti WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Update talent information
     */
    public function update($id, $data) {
        $sql = "UPDATE talenti SET 
                nome = :nome, 
                categoria = :categoria, 
                materiale_palco = :materiale_palco, 
                note_luci = :note_luci, 
                ordine_scaletta = :ordine_scaletta 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        // Ensure all required parameters exist
        $params = [
            'nome' => $data['nome'] ?? '',
            'categoria' => $data['categoria'] ?? null,
            'materiale_palco' => $data['materiale_palco'] ?? null,
            'note_luci' => $data['note_luci'] ?? null,
            'ordine_scaletta' => $data['ordine_scaletta'] ?? null,
            'id' => $id
        ];
        
        return $stmt->execute($params);
    }

    /**
     * Delete talent
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM talenti WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get all talents ordered by scaletta
     */
    public function getScaletta() {
        $sql = "SELECT * FROM talenti ORDER BY ordine_scaletta ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get talent with its media
     */
    public function getWithMedia($id) {
        $talent = $this->find($id);
        if (!$talent) return null;

        $stmt = $this->db->prepare("SELECT * FROM media_performance WHERE talento_id = ? ORDER BY ordine_esecuzione ASC");
        $stmt->execute([$id]);
        $talent['media'] = $stmt->fetchAll();
        
        return $talent;
    }
    /**
     * Reorder talents by updating their ordine_scaletta
     * @param array $orderedIds List of talent IDs in the new order
     */
    public function reorder($orderedIds) {
        $this->db->beginTransaction();
        try {
            foreach ($orderedIds as $index => $id) {
                $sql = "UPDATE talenti SET ordine_scaletta = :ordine WHERE id = :id";
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
}
