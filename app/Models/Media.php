<?php

namespace App\Models;

use PDO;

class Media {
    private $db;
    private ?bool $hasTipoOutput = null;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Get all media entries
     */
    public function getAll() {
        $sql = "SELECT mp.*, t.nome as talento_nome, s.nome as screen_nome
                FROM media_performance mp
                LEFT JOIN talenti t ON mp.talento_id = t.id
                LEFT JOIN screens s ON mp.screen_id = s.id
                ORDER BY mp.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Create a new media entry
     */
    public function create($data) {
        $fields = ['talento_id', 'file_path', 'friendly_name', 'screen_id', 'tipo_media', 'timestamp_inizio', 'timestamp_fine', 'durata_totale_sec', 'fade_in_sec', 'fade_out_sec', 'ordine_esecuzione'];
        $params = [
            'talento_id'        => $data['talento_id'],
            'file_path'         => $data['file_path'],
            'friendly_name'     => $data['friendly_name'] ?? null,
            'screen_id'         => $data['screen_id'] ?? null,
            'tipo_media'        => $data['tipo_media'] ?? 'VIDEO',
            'timestamp_inizio'  => $data['timestamp_inizio'] ?? '00:00:00',
            'timestamp_fine'    => $data['timestamp_fine'] ?? null,
            'durata_totale_sec' => $data['durata_totale_sec'] ?? null,
            'fade_in_sec'       => $data['fade_in_sec'] ?? 0,
            'fade_out_sec'      => $data['fade_out_sec'] ?? 0,
            'ordine_esecuzione' => $data['ordine_esecuzione'] ?? null
        ];

        if ($this->hasTipoOutputColumn()) {
            array_splice($fields, 1, 0, 'tipo_output');
            $params['tipo_output'] = $data['tipo_output'] ?? 'proiettore';
        }

        $placeholders = array_map(fn($field) => ":$field", $fields);
        $sql = "INSERT INTO media_performance (" . implode(', ', $fields) . ")
                VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
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
                file_path = :file_path, 
                friendly_name = :friendly_name,
                screen_id = :screen_id,
                tipo_media = :tipo_media,
                timestamp_inizio = :timestamp_inizio, 
                timestamp_fine = :timestamp_fine, 
                durata_totale_sec = :durata_totale_sec,
                fade_in_sec = :fade_in_sec, 
                fade_out_sec = :fade_out_sec, 
                ordine_esecuzione = :ordine_esecuzione 
                WHERE id = :id";
        if ($this->hasTipoOutputColumn()) {
            $sql = str_replace('talento_id = :talento_id,', 'talento_id = :talento_id, tipo_output = :tipo_output,', $sql);
            $data['tipo_output'] = $data['tipo_output'] ?? 'proiettore';
        } else {
            unset($data['tipo_output']);
        }
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
     * Check whether the same file is already assigned to a talent.
     */
    public function existsForTalento($talento_id, $file_path) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM media_performance WHERE talento_id = ? AND file_path = ?");
        $stmt->execute([$talento_id, $file_path]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Get all media for a specific talent
     */
    public function getByTalento($talento_id) {
        $sql = "SELECT mp.*, s.nome as screen_nome
                FROM media_performance mp
                LEFT JOIN screens s ON mp.screen_id = s.id
                WHERE mp.talento_id = ?
                ORDER BY COALESCE(mp.screen_id, 0), mp.ordine_esecuzione ASC, mp.timestamp_inizio ASC, mp.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$talento_id]);
        return $stmt->fetchAll();
    }

    public function updateTimelineMedia($id, array $data) {
        $allowed = [
            'friendly_name',
            'screen_id',
            'tipo_media',
            'timestamp_inizio',
            'timestamp_fine',
            'durata_totale_sec',
            'fade_in_sec',
            'fade_out_sec',
            'ordine_esecuzione'
        ];
        $updates = [];
        $params = ['id' => $id];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $updates[] = "$field = :$field";
                $params[$field] = $data[$field] === '' ? null : $data[$field];
            }
        }
        if (!$updates) return false;
        $stmt = $this->db->prepare("UPDATE media_performance SET " . implode(', ', $updates) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    private function hasTipoOutputColumn(): bool {
        if ($this->hasTipoOutput !== null) return $this->hasTipoOutput;
        $stmt = $this->db->query("SHOW COLUMNS FROM media_performance LIKE 'tipo_output'");
        $this->hasTipoOutput = (bool) $stmt->fetch();
        return $this->hasTipoOutput;
    }
}
