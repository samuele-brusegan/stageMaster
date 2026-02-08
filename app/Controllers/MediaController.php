<?php

use App\Models\Media;

class MediaController extends ApiController {
    private $mediaModel;

    public function __construct() {
        $database = new DatabaseConnector();
        $this->mediaModel = new Media($database->getConnection());
    }

    /**
     * API: Get all media for a talent
     */
    public function getByTalento() {
        $talento_id = $_GET['talento_id'] ?? null;
        if (!$talento_id) $this->error("talento_id is required");

        $media = $this->mediaModel->getByTalento($talento_id);
        $this->json($media);
    }

    /**
     * API: Get media details
     */
    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) $this->error("ID is required");

        $media = $this->mediaModel->find($id);
        if (!$media) $this->error("Media not found", 404);

        $this->json($media);
    }
}
