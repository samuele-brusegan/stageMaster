<?php

use App\Models\Talento;

class TalentoController extends ApiController {
    private $talentoModel;

    public function __construct() {
        $database = new DatabaseConnector();
        $this->talentoModel = new Talento($database->getConnection());
    }

    /**
     * API: Get all talents in order
     */
    public function list() {
        $talenti = $this->talentoModel->getScaletta();
        $this->json(['status' => 'ok', 'data' => $talenti]);
    }

    /**
     * API: Get talent details with media
     */
    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) $this->error("ID is required");

        $talento = $this->talentoModel->getWithMedia($id);
        if (!$talento) $this->error("Talent not found", 404);

        $this->json($talento);
    }

    /**
     * API: Update talent information
     */
    public function update() {
        $id = $_GET['id'] ?? null;
        if (!$id) $this->error("ID is required");

        $data = $this->getJsonInput();
        $this->validate($data, [
            'nome' => 'required'
        ]);

        if ($this->talentoModel->update($id, $data)) {
            $this->json(['status' => 'ok', 'message' => 'Slot aggiornato con successo']);
        } else {
            $this->error("Failed to update slot", 500);
        }
    }

    /**
     * API: Reorder setlist (Drag & Drop)
     */
    public function reorder() {
        $data = $this->getJsonInput();
        if (!isset($data['ordered_ids']) || !is_array($data['ordered_ids'])) {
            $this->error("ordered_ids array is required");
        }

        if ($this->talentoModel->reorder($data['ordered_ids'])) {
            $this->json(['message' => 'Setlist updated successfully']);
        } else {
            $this->error("Failed to update setlist", 500);
        }
    }
}
