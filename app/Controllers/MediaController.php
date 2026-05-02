<?php

use App\Models\Media;

class MediaController extends ApiController {
    private $db;
    private $mediaModel;

    public function __construct() {
        $database = new DatabaseConnector();
        $this->db = $database->getConnection();
        $this->mediaModel = new Media($this->db);
    }

    /**
     * API: Get media list or media details
     */
    public function index() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $media = $this->mediaModel->find($id);
            if (!$media) $this->error("Media not found", 404);
            $this->json($media);
        }

        $this->json(['status' => 'ok', 'data' => $this->mediaModel->getAll()]);
    }

    /**
     * API: Create media performance entry
     */
    public function create() {
        $data = $this->getJsonInput();
        $this->validate($data, [
            'talento_id' => 'required',
            'file_path' => 'required'
        ]);

        $id = $this->mediaModel->create($data);
        $this->json(['status' => 'ok', 'id' => $id, 'message' => 'Media creato']);
    }

    /**
     * API: Guided slot media creation.
     */
    public function addToSlot() {
        $data = $this->getJsonInput();
        $this->validate($data, [
            'talento_id' => 'required',
            'media_library_id' => 'required',
            'screen_id' => 'required'
        ]);

        $mediaLibrary = new \App\Models\MediaLibrary($this->db);
        $queueModel = new \App\Models\MediaQueue($this->db);
        $transitionModel = new \App\Models\Transizione($this->db);
        $screenModel = new \App\Models\Screen($this->db);
        $talentoModel = new \App\Models\Talento($this->db);

        $talento = $talentoModel->find((int)$data['talento_id']);
        if (!$talento) $this->error('Slot non trovato', 404);

        $libraryMedia = $mediaLibrary->find((int)$data['media_library_id']);
        if (!$libraryMedia) $this->error('Media library non trovato', 404);

        $screen = $screenModel->find((int)$data['screen_id']);
        if (!$screen) $this->error('Schermo non trovato', 404);

        $allowDuplicate = !empty($data['allow_duplicate']);
        if (!$allowDuplicate && $this->mediaModel->existsForTalento($data['talento_id'], $libraryMedia['file_path'])) {
            $this->json([
                'status' => 'duplicate',
                'message' => 'Questo media è già associato allo slot.',
                'duplicate' => true
            ], 409);
        }

        $this->db->beginTransaction();
        try {
            $mediaId = $this->mediaModel->create([
                'talento_id' => (int)$data['talento_id'],
                'file_path' => $libraryMedia['file_path'],
                'friendly_name' => $data['friendly_name'] ?? pathinfo($libraryMedia['file_name'], PATHINFO_FILENAME),
                'screen_id' => (int)$data['screen_id'],
                'tipo_media' => $data['tipo_media'] ?? $libraryMedia['file_type'],
                'ordine_esecuzione' => $data['ordine_esecuzione'] ?? null
            ]);

            $transitionId = $transitionModel->create([
                'media_id' => $mediaId,
                'tipo_dissolvenza' => 'cut',
                'durata_sec' => 0
            ]);

            $queueId = $queueModel->add([
                'talento_id' => (int)$data['talento_id'],
                'media_id' => $mediaId,
                'stato' => 'pending'
            ]);

            $this->db->commit();
            $this->json([
                'status' => 'ok',
                'media_id' => $mediaId,
                'transition_id' => $transitionId,
                'queue_id' => $queueId,
                'message' => 'Media aggiunto allo slot e alla coda'
            ]);
        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Delete media performance entry
     */
    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) $this->error("ID is required");

        $this->mediaModel->delete($id);
        $this->json(['status' => 'ok', 'message' => 'Media eliminato']);
    }

    public function updateTimeline() {
        $id = $_GET['id'] ?? null;
        if (!$id) $this->error("ID is required");

        $data = $this->getJsonInput();
        if ($this->mediaModel->updateTimelineMedia((int)$id, $data)) {
            $this->json(['status' => 'ok', 'message' => 'Timeline media aggiornato']);
        }

        $this->error('Nessun campo aggiornabile', 400);
    }

    public function reorderTimeline() {
        $data = $this->getJsonInput();
        $items = $data['items'] ?? [];
        if (!is_array($items)) {
            $this->error('items deve essere un array', 400);
        }

        foreach ($items as $item) {
            if (empty($item['id'])) continue;
            $this->mediaModel->updateTimelineMedia((int)$item['id'], [
                'screen_id' => $item['screen_id'] ?? null,
                'timestamp_inizio' => $item['timestamp_inizio'] ?? '00:00:00',
                'timestamp_fine' => $item['timestamp_fine'] ?? null,
                'durata_totale_sec' => $item['durata_totale_sec'] ?? null,
                'ordine_esecuzione' => $item['ordine_esecuzione'] ?? null
            ]);
        }

        $this->json(['status' => 'ok', 'message' => 'Timeline riordinata']);
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
