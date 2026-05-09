<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Slot folder tree. Folders are an organizational layer above `talenti`
 * (slots). Each folder may have a parent folder; the resulting tree is used
 * by the admin UI to group slots into acts/scenes.
 */
class SlotFolder
{
    public function __construct(private PDO $db)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        $sql = 'SELECT id, parent_id, nome, ordine, created_at, updated_at
                FROM slot_folders
                ORDER BY COALESCE(parent_id, 0), ordine, id';
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Returns the folder tree as a nested structure, plus a `slots` array per
     * folder (only ids/names – the admin UI uses /api/talenti for full data).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTreeWithSlots(): array
    {
        $folders = $this->getAll();
        $slotsByFolder = $this->fetchSlotsGroupedByFolder();

        $byParent = [];
        foreach ($folders as $folder) {
            $folder['children'] = [];
            $folder['slots']    = $slotsByFolder[(int)$folder['id']] ?? [];
            $byParent[$folder['parent_id'] ?? 0][] = $folder;
        }

        $build = static function (?int $parentId) use (&$build, &$byParent): array {
            $key = $parentId ?? 0;
            $children = $byParent[$key] ?? [];
            foreach ($children as &$child) {
                $child['children'] = $build((int)$child['id']);
            }
            return $children;
        };

        return [
            'folders'   => $build(null),
            'unfiled'   => $slotsByFolder[0] ?? [],
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM slot_folders WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * @param array{nome:string, parent_id?:?int, ordine?:?int} $data
     */
    public function create(array $data): int
    {
        if (trim((string)($data['nome'] ?? '')) === '') {
            throw new RuntimeException('Folder name is required');
        }
        $parentId = $this->normalizeParentId($data['parent_id'] ?? null);
        $ordine   = $data['ordine'] ?? $this->nextOrdineFor($parentId);

        $stmt = $this->db->prepare(
            'INSERT INTO slot_folders (parent_id, nome, ordine) VALUES (:parent_id, :nome, :ordine)'
        );
        $stmt->execute([
            'parent_id' => $parentId,
            'nome'      => trim((string)$data['nome']),
            'ordine'    => $ordine,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): bool
    {
        $folder = $this->find($id);
        if ($folder === null) {
            return false;
        }

        $fields = [];
        $params = ['id' => $id];

        if (array_key_exists('nome', $data)) {
            $name = trim((string)$data['nome']);
            if ($name === '') {
                throw new RuntimeException('Folder name cannot be empty');
            }
            $fields[]       = 'nome = :nome';
            $params['nome'] = $name;
        }

        if (array_key_exists('parent_id', $data)) {
            $newParent = $this->normalizeParentId($data['parent_id']);
            if ($newParent !== null && $this->wouldCreateCycle($id, $newParent)) {
                throw new RuntimeException('Cannot move folder into one of its descendants');
            }
            $fields[]            = 'parent_id = :parent_id';
            $params['parent_id'] = $newParent;
        }

        if (array_key_exists('ordine', $data)) {
            $fields[]          = 'ordine = :ordine';
            $params['ordine']  = (int)$data['ordine'];
        }

        if ($fields === []) {
            return true;
        }

        $sql  = 'UPDATE slot_folders SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM slot_folders WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * Reorder folders within their parent. The list is the new ordering for
     * a single parent context (or root when $parentId is null).
     *
     * @param array<int, int> $orderedIds
     */
    public function reorder(?int $parentId, array $orderedIds): bool
    {
        if ($orderedIds === []) {
            return true;
        }
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'UPDATE slot_folders SET ordine = :ordine, parent_id = :parent_id WHERE id = :id'
            );
            foreach ($orderedIds as $idx => $folderId) {
                $stmt->execute([
                    'ordine'    => $idx + 1,
                    'parent_id' => $parentId,
                    'id'        => (int)$folderId,
                ]);
            }
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function fetchSlotsGroupedByFolder(): array
    {
        $sql = 'SELECT id, nome, categoria, ordine_scaletta, folder_id
                FROM talenti
                ORDER BY ordine_scaletta IS NULL, ordine_scaletta, id';
        $rows = $this->db->query($sql)->fetchAll();
        $grouped = [];
        foreach ($rows as $row) {
            $key = $row['folder_id'] === null ? 0 : (int)$row['folder_id'];
            $grouped[$key][] = $row;
        }
        return $grouped;
    }

    private function normalizeParentId(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === 0 || $value === '0') {
            return null;
        }
        return (int)$value;
    }

    private function nextOrdineFor(?int $parentId): int
    {
        $sql  = 'SELECT COALESCE(MAX(ordine), 0) + 1 FROM slot_folders WHERE '
              . ($parentId === null ? 'parent_id IS NULL' : 'parent_id = :p');
        $stmt = $this->db->prepare($sql);
        if ($parentId !== null) {
            $stmt->execute(['p' => $parentId]);
        } else {
            $stmt->execute();
        }
        return (int)$stmt->fetchColumn();
    }

    private function wouldCreateCycle(int $id, int $newParent): bool
    {
        if ($id === $newParent) {
            return true;
        }
        $current = $newParent;
        $guard   = 0;
        while ($current !== 0 && $guard < 100) {
            $stmt = $this->db->prepare('SELECT parent_id FROM slot_folders WHERE id = ?');
            $stmt->execute([$current]);
            $parent = $stmt->fetchColumn();
            if ($parent === false || $parent === null) {
                return false;
            }
            if ((int)$parent === $id) {
                return true;
            }
            $current = (int)$parent;
            $guard++;
        }
        return false;
    }
}
