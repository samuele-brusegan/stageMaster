<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Transizione;

class TransizioneController extends ApiController
{
    private Transizione $transizioneModel;

    public function __construct()
    {
        $this->transizioneModel = new Transizione(Connection::getInstance());
    }

    public function show(): void
    {
        header('Content-Type: application/json');
        try {
            $mediaId = $_GET['media_id'] ?? null;
            if (!$mediaId) {
                throw new \RuntimeException('Media ID mancante');
            }
            echo json_encode(['status' => 'ok', 'data' => $this->transizioneModel->getByMedia((int)$mediaId)]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function create(): void
    {
        header('Content-Type: application/json');
        try {
            $data = $this->getJsonInput();
            $id = $this->transizioneModel->create($data);
            echo json_encode(['status' => 'ok', 'id' => $id, 'message' => 'Transizione creata']);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update(): void
    {
        header('Content-Type: application/json');
        try {
            $mediaId = $_GET['media_id'] ?? null;
            if (!$mediaId) {
                throw new \RuntimeException('Media ID mancante');
            }
            $data = $this->getJsonInput();
            $this->transizioneModel->update((int)$mediaId, $data);
            echo json_encode(['status' => 'ok', 'message' => 'Transizione aggiornata']);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete(): void
    {
        header('Content-Type: application/json');
        try {
            $mediaId = $_GET['media_id'] ?? null;
            if (!$mediaId) {
                throw new \RuntimeException('Media ID mancante');
            }
            $this->transizioneModel->delete((int)$mediaId);
            echo json_encode(['status' => 'ok', 'message' => 'Transizione eliminata']);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getOrCreate(): void
    {
        header('Content-Type: application/json');
        try {
            $mediaId = $_GET['media_id'] ?? null;
            if (!$mediaId) {
                throw new \RuntimeException('Media ID mancante');
            }
            echo json_encode(['status' => 'ok', 'data' => $this->transizioneModel->getOrCreate((int)$mediaId)]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

if (!class_exists('TransizioneController', false)) {
    class_alias(\App\Controllers\TransizioneController::class, 'TransizioneController');
}
