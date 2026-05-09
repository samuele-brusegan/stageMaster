<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Talento
{
    public function __construct(private PDO $db)
    {
    }

    /**
     * @param array{nome:string, categoria?:?string, materiale_palco?:?string,
     *              note_luci?:?string, ordine_scaletta?:?int, folder_id?:?int} $data
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO talenti (nome, categoria, materiale_palco, note_luci, ordine_scaletta, folder_id)
                VALUES (:nome, :categoria, :materiale_palco, :note_luci, :ordine_scaletta, :folder_id)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'nome'            => $data['nome'],
            'categoria'       => $data['categoria'] ?? null,
            'materiale_palco' => $data['materiale_palco'] ?? null,
            'note_luci'       => $data['note_luci'] ?? null,
            'ordine_scaletta' => $data['ordine_scaletta'] ?? null,
            'folder_id'       => $this->normalizeFolderId($data['folder_id'] ?? null),
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** @return array<string, mixed>|false */
    public function find(int $id)
    {
        $stmt = $this->db->prepare('SELECT * FROM talenti WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $existing = $this->find($id);
        if ($existing === false) {
            return false;
        }

        $payload = array_merge($existing, $data);

        $sql = 'UPDATE talenti SET
                nome = :nome,
                categoria = :categoria,
                materiale_palco = :materiale_palco,
                note_luci = :note_luci,
                ordine_scaletta = :ordine_scaletta,
                folder_id = :folder_id
                WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'nome'            => $payload['nome'] ?? '',
            'categoria'       => $payload['categoria'] ?? null,
            'materiale_palco' => $payload['materiale_palco'] ?? null,
            'note_luci'       => $payload['note_luci'] ?? null,
            'ordine_scaletta' => $payload['ordine_scaletta'] ?? null,
            'folder_id'       => $this->normalizeFolderId($payload['folder_id'] ?? null),
            'id'              => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM talenti WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Get all talents ordered by scaletta. When `$folderId` is provided, only
     * the slots inside that folder are returned. Pass `0` for the root level
     * (slots without a folder).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getScaletta(?int $folderId = null): array
    {
        if ($folderId === null) {
            $sql = 'SELECT * FROM talenti ORDER BY ordine_scaletta IS NULL, ordine_scaletta, id';
            return $this->db->query($sql)->fetchAll();
        }

        if ($folderId === 0) {
            $sql = 'SELECT * FROM talenti WHERE folder_id IS NULL ORDER BY ordine_scaletta IS NULL, ordine_scaletta, id';
            return $this->db->query($sql)->fetchAll();
        }

        $stmt = $this->db->prepare('SELECT * FROM talenti WHERE folder_id = ? ORDER BY ordine_scaletta IS NULL, ordine_scaletta, id');
        $stmt->execute([$folderId]);
        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function getWithMedia(int $id): ?array
    {
        $talent = $this->find($id);
        if ($talent === false) {
            return null;
        }
        $stmt = $this->db->prepare('SELECT * FROM media_performance WHERE talento_id = ? ORDER BY ordine_esecuzione ASC');
        $stmt->execute([$id]);
        $talent['media'] = $stmt->fetchAll();
        return $talent;
    }

    /**
     * Reorder talents by updating their `ordine_scaletta`.
     *
     * @param array<int, int> $orderedIds List of talent IDs in the new order.
     */
    public function reorder(array $orderedIds): bool
    {
        if ($orderedIds === []) {
            return true;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('UPDATE talenti SET ordine_scaletta = :ordine WHERE id = :id');
            foreach ($orderedIds as $idx => $talentId) {
                $stmt->execute([
                    'ordine' => $idx + 1,
                    'id'     => (int)$talentId,
                ]);
            }
            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    /**
     * Move a slot into a different folder (or to the root if $folderId is null).
     */
    public function moveToFolder(int $talentId, ?int $folderId): bool
    {
        $stmt = $this->db->prepare('UPDATE talenti SET folder_id = :folder WHERE id = :id');
        return $stmt->execute([
            'folder' => $this->normalizeFolderId($folderId),
            'id'     => $talentId,
        ]);
    }

    private function normalizeFolderId(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === 0 || $value === '0') {
            return null;
        }
        return (int)$value;
    }
}
