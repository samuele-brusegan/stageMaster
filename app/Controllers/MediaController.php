<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Media;
use App\Models\MediaLibrary;
use App\Models\Screen;
use App\Models\Talento;
use App\Models\Transizione;
use App\Support\MediaProbe;
use PDO;

class MediaController extends ApiController
{
    private PDO $db;
    private Media $mediaModel;

    public function __construct()
    {
        $this->db = Connection::getInstance();
        $this->mediaModel = new Media($this->db);
    }

    /** API: Get media list or media details */
    public function index(): void
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $media = $this->mediaModel->find((int)$id);
            if (!$media) {
                $this->error('Media not found', 404);
            }
            $this->json($media);
            return;
        }

        $this->json(['status' => 'ok', 'data' => $this->mediaModel->getAll()]);
    }

    /** API: Create media performance entry */
    public function create(): void
    {
        $data = $this->getJsonInput();
        $this->validate($data, [
            'talento_id' => 'required',
            'file_path'  => 'required',
        ]);

        $id = $this->mediaModel->create($data);
        $this->json(['status' => 'ok', 'id' => $id, 'message' => 'Media creato']);
    }

    /** API: Guided slot media creation. */
    public function addToSlot(): void
    {
        $data = $this->getJsonInput();
        $this->validate($data, [
            'talento_id'       => 'required',
            'media_library_id' => 'required',
            'screen_id'        => 'required',
        ]);

        $mediaLibrary    = new MediaLibrary($this->db);
        $transitionModel = new Transizione($this->db);
        $screenModel     = new Screen($this->db);
        $talentoModel    = new Talento($this->db);

        $talento = $talentoModel->find((int)$data['talento_id']);
        if (!$talento) {
            $this->error('Slot non trovato', 404);
        }

        $libraryMedia = $mediaLibrary->find((int)$data['media_library_id']);
        if (!$libraryMedia) {
            $this->error('Media library non trovato', 404);
        }

        $screen = $screenModel->find((int)$data['screen_id']);
        if (!$screen) {
            $this->error('Schermo non trovato', 404);
        }

        $allowDuplicate = !empty($data['allow_duplicate']);
        if (!$allowDuplicate && $this->mediaModel->existsForTalento((int)$data['talento_id'], $libraryMedia['file_path'])) {
            $this->json([
                'status'    => 'duplicate',
                'message'   => 'Questo media è già associato allo slot.',
                'duplicate' => true,
            ], 409);
        }

        $this->db->beginTransaction();
        try {
            $mediaType = strtoupper((string)($data['tipo_media'] ?? $libraryMedia['file_type'] ?? 'VIDEO'));
            $duration  = $this->resolveDefaultDuration($mediaLibrary, $libraryMedia, $mediaType);

            $mediaId = $this->mediaModel->create([
                'talento_id'        => (int)$data['talento_id'],
                'file_path'         => $libraryMedia['file_path'],
                'friendly_name'     => $data['friendly_name'] ?? pathinfo($libraryMedia['file_name'], PATHINFO_FILENAME),
                'screen_id'         => (int)$data['screen_id'],
                'tipo_media'        => $mediaType,
                'timestamp_fine'    => $duration ? gmdate('H:i:s', $duration) : null,
                'durata_totale_sec' => $duration,
                'ordine_esecuzione' => $data['ordine_esecuzione'] ?? null,
            ]);

            $transitionId = $transitionModel->create([
                'media_id'         => $mediaId,
                'tipo_dissolvenza' => 'cut',
                'durata_sec'       => 0,
            ]);

            $this->db->commit();
            $this->json([
                'status'        => 'ok',
                'media_id'      => $mediaId,
                'transition_id' => $transitionId,
                'message'       => 'Media aggiunto allo slot',
            ]);
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /** API: Delete media performance entry */
    public function delete(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->error('ID is required');
        }
        $this->mediaModel->delete((int)$id);
        $this->json(['status' => 'ok', 'message' => 'Media eliminato']);
    }

    public function updateTimeline(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->error('ID is required');
        }

        $data = $this->getJsonInput();
        if ($this->mediaModel->updateTimelineMedia((int)$id, $data)) {
            $this->json(['status' => 'ok', 'message' => 'Timeline media aggiornato']);
        }
        $this->error('Nessun campo aggiornabile', 400);
    }

    public function reorderTimeline(): void
    {
        $data = $this->getJsonInput();
        $items = $data['items'] ?? [];
        if (!is_array($items)) {
            $this->error('items deve essere un array', 400);
        }

        foreach ($items as $item) {
            if (empty($item['id'])) {
                continue;
            }
            $this->mediaModel->updateTimelineMedia((int)$item['id'], [
                'screen_id'         => $item['screen_id'] ?? null,
                'timestamp_inizio'  => $item['timestamp_inizio'] ?? '00:00:00',
                'timestamp_fine'    => $item['timestamp_fine'] ?? null,
                'durata_totale_sec' => $item['durata_totale_sec'] ?? null,
                'ordine_esecuzione' => $item['ordine_esecuzione'] ?? null,
            ]);
        }

        $this->json(['status' => 'ok', 'message' => 'Timeline riordinata']);
    }

    /**
     * Resolve a sensible default duration (in seconds) for a slot media entry.
     *
     * Order of resolution:
     *   1. Cached `media.duration_sec` from the library row.
     *   2. Fresh ffprobe call against the media file (writethrough on success).
     * For non audio/video types this returns null.
     */
    protected function resolveDefaultDuration(MediaLibrary $library, array $libraryMedia, string $mediaType): ?int
    {
        if (!in_array($mediaType, ['AUDIO', 'VIDEO'], true)) {
            return null;
        }
        if (!empty($libraryMedia['duration_sec'])) {
            return max(1, (int) round((float) $libraryMedia['duration_sec']));
        }

        $publicDir = realpath(__DIR__ . '/../../public') ?: __DIR__ . '/../../public';
        $probed = MediaProbe::durationFromWebPath((string) $libraryMedia['file_path'], $publicDir);
        if ($probed !== null) {
            $library->updateDuration((int) $libraryMedia['id'], $probed);
            return max(1, $probed);
        }
        return null;
    }

    /** API: Get all media for a talent */
    public function getByTalento(): void
    {
        $talentoId = $_GET['talento_id'] ?? null;
        if (!$talentoId) {
            $this->error('talento_id is required');
        }
        $this->json($this->mediaModel->getByTalento((int)$talentoId));
    }

    /** API: Get media details */
    public function show(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->error('ID is required');
        }
        $media = $this->mediaModel->find((int)$id);
        if (!$media) {
            $this->error('Media not found', 404);
        }
        $this->json($media);
    }
}

if (!class_exists('MediaController', false)) {
    class_alias(\App\Controllers\MediaController::class, 'MediaController');
}
