<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Talento;

class TalentoController extends ApiController
{
    private Talento $talentoModel;

    public function __construct()
    {
        $this->talentoModel = new Talento(Connection::getInstance());
    }

    /** API: Get all talents in order */
    public function list(): void
    {
        $talenti = $this->talentoModel->getScaletta();
        $this->json(['status' => 'ok', 'data' => $talenti]);
    }

    /** API: Get talent details with media */
    public function show(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->error('ID is required');
        }
        $talento = $this->talentoModel->getWithMedia((int)$id);
        if (!$talento) {
            $this->error('Talent not found', 404);
        }
        $this->json($talento);
    }

    /** API: Update talent information */
    public function update(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->error('ID is required');
        }

        $data = $this->getJsonInput();
        $this->validate($data, ['nome' => 'required']);

        if ($this->talentoModel->update((int)$id, $data)) {
            $this->json(['status' => 'ok', 'message' => 'Slot aggiornato con successo']);
        }
        $this->error('Failed to update slot', 500);
    }

    /** API: Reorder setlist (Drag & Drop) */
    public function reorder(): void
    {
        $data = $this->getJsonInput();
        if (!isset($data['ordered_ids']) || !is_array($data['ordered_ids'])) {
            $this->error('ordered_ids array is required');
        }

        if ($this->talentoModel->reorder($data['ordered_ids'])) {
            $this->json(['status' => 'ok', 'message' => 'Setlist updated successfully']);
        }
        $this->error('Failed to update setlist', 500);
    }
}

if (!class_exists('TalentoController', false)) {
    class_alias(\App\Controllers\TalentoController::class, 'TalentoController');
}
