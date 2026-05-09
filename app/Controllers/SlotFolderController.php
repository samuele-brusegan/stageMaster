<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Connection;
use App\Models\SlotFolder;
use RuntimeException;

class SlotFolderController extends ApiController
{
    private SlotFolder $folderModel;

    public function __construct()
    {
        $this->folderModel = new SlotFolder(Connection::getInstance());
    }

    /** GET /api/folders – flat list of folders. */
    public function index(): void
    {
        $this->json(['status' => 'ok', 'data' => $this->folderModel->getAll()]);
    }

    /** GET /api/folders/tree – nested folders + slots grouped per folder. */
    public function tree(): void
    {
        $this->json(['status' => 'ok', 'data' => $this->folderModel->getTreeWithSlots()]);
    }

    /** GET /api/folders/show?id=N */
    public function show(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->error('ID is required');
        }
        $folder = $this->folderModel->find((int)$id);
        if ($folder === null) {
            $this->error('Folder not found', 404);
        }
        $this->json(['status' => 'ok', 'data' => $folder]);
    }

    /** POST /api/folders/create  body: { nome, parent_id?, ordine? } */
    public function create(): void
    {
        $data = $this->getJsonInput();
        if (trim((string)($data['nome'] ?? '')) === '') {
            $this->error('Nome cartella obbligatorio');
        }
        try {
            $id = $this->folderModel->create($data);
            $this->json(['status' => 'ok', 'id' => $id, 'message' => 'Cartella creata']);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
        }
    }

    /** POST /api/folders/update?id=N  body: { nome?, parent_id?, ordine? } */
    public function update(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->error('ID is required');
        }
        $data = $this->getJsonInput();
        try {
            $ok = $this->folderModel->update((int)$id, $data);
            if (!$ok) {
                $this->error('Folder not found', 404);
            }
            $this->json(['status' => 'ok', 'message' => 'Cartella aggiornata']);
        } catch (RuntimeException $e) {
            $this->error($e->getMessage());
        }
    }

    /** DELETE /api/folders/delete?id=N */
    public function delete(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->error('ID is required');
        }
        $this->folderModel->delete((int)$id);
        $this->json(['status' => 'ok', 'message' => 'Cartella eliminata']);
    }

    /**
     * POST /api/folders/reorder body: { parent_id?: int|null, ordered_ids: [int,...] }
     * Reorders folders within their parent.
     */
    public function reorder(): void
    {
        $data = $this->getJsonInput();
        $orderedIds = $data['ordered_ids'] ?? [];
        if (!is_array($orderedIds) || $orderedIds === []) {
            $this->error('ordered_ids array is required');
        }
        $parent = $data['parent_id'] ?? null;
        $parentId = ($parent === null || $parent === '' || $parent === 'null') ? null : (int)$parent;

        if ($this->folderModel->reorder($parentId, $orderedIds)) {
            $this->json(['status' => 'ok', 'message' => 'Ordine cartelle aggiornato']);
        }
        $this->error('Riordino fallito', 500);
    }
}

if (!class_exists('SlotFolderController', false)) {
    class_alias(\App\Controllers\SlotFolderController::class, 'SlotFolderController');
}
