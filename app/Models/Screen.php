<?php

namespace App\Models;

use PDO;

class Screen {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Get all screens
     */
    public function getAll() {
        $sql = "SELECT s.*, 
                COALESCE(sr.nome, 'N/A') as screen_riferimento_nome
                FROM screens s
                LEFT JOIN screens sr ON s.screen_riferimento_id = sr.id
                ORDER BY s.id ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get screen by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM screens WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Create a new screen
     */
    public function create($data) {
        $data = $this->normalizeScreenData($data);
        $sql = "INSERT INTO screens (nome, tipo, screen_riferimento_id) 
                VALUES (:nome, :tipo, :screen_riferimento_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nome' => $data['nome'],
            'tipo' => $data['tipo'] ?? 'indipendente',
            'screen_riferimento_id' => $data['screen_riferimento_id'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    private function normalizeScreenData(array $data): array {
        $name = trim((string)($data['nome'] ?? ''));
        if ($name === '') {
            throw new \InvalidArgumentException('Il nome dello schermo è obbligatorio.');
        }

        if (mb_strlen($name) > 50) {
            throw new \InvalidArgumentException('Il nome dello schermo non può superare 50 caratteri.');
        }

        $type = $data['tipo'] ?? 'indipendente';
        if (!in_array($type, ['indipendente', 'mirror'], true)) {
            throw new \InvalidArgumentException('Tipo schermo non valido.');
        }

        $referenceId = $data['screen_riferimento_id'] ?? null;
        if ($referenceId === '' || $type === 'indipendente') {
            $referenceId = null;
        }

        if ($referenceId !== null) {
            $referenceId = (int)$referenceId;
            if ($referenceId <= 0 || !$this->find($referenceId)) {
                throw new \InvalidArgumentException('Schermo di riferimento non valido.');
            }
        }

        return [
            'nome' => $name,
            'tipo' => $type,
            'screen_riferimento_id' => $referenceId
        ];
    }

    /**
     * Update screen
     */
    public function update($id, $data) {
        $sql = "UPDATE screens SET 
                nome = :nome, 
                tipo = :tipo, 
                screen_riferimento_id = :screen_riferimento_id 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    /**
     * Delete screen
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM screens WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get media assigned to a screen
     */
    public function getMedia($screen_id) {
        $sql = "SELECT mp.*, t.nome as talento_nome 
                FROM media_performance mp
                LEFT JOIN talenti t ON mp.talento_id = t.id
                WHERE mp.screen_id = ? 
                ORDER BY mp.timestamp_inizio ASC, mp.ordine_esecuzione ASC, mp.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$screen_id]);
        return $stmt->fetchAll();
    }
}
