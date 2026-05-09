<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Screen;

class ScreenController extends ApiController
{
    private Screen $screenModel;

    public function __construct()
    {
        $this->screenModel = new Screen(Connection::getInstance());
    }

    public function index(): void
    {
        header('Content-Type: application/json');
        try {
            echo json_encode(['status' => 'ok', 'data' => $this->screenModel->getAll()]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function show(): void
    {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new \RuntimeException('ID mancante');
            }
            $screen = $this->screenModel->find((int)$id);
            if (!$screen) {
                throw new \RuntimeException('Screen non trovato');
            }
            $screen['media'] = $this->screenModel->getMedia((int)$id);
            echo json_encode(['status' => 'ok', 'data' => $screen]);
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
            if (empty($data)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Payload JSON non valido']);
                return;
            }
            $id = $this->screenModel->create($data);
            echo json_encode(['status' => 'ok', 'id' => $id, 'message' => 'Screen creato']);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update(): void
    {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new \RuntimeException('ID mancante');
            }
            $data = $this->getJsonInput();
            $this->screenModel->update((int)$id, $data);
            echo json_encode(['status' => 'ok', 'message' => 'Screen aggiornato']);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete(): void
    {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new \RuntimeException('ID mancante');
            }
            $this->screenModel->delete((int)$id);
            echo json_encode(['status' => 'ok', 'message' => 'Screen eliminato']);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

if (!class_exists('ScreenController', false)) {
    class_alias(\App\Controllers\ScreenController::class, 'ScreenController');
}
