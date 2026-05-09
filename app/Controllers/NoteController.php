<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Connection;
use App\Models\NoteTecniche;

class NoteController extends ApiController
{
    private NoteTecniche $noteModel;

    public function __construct()
    {
        $this->noteModel = new NoteTecniche(Connection::getInstance());
    }

    public function index(): void
    {
        header('Content-Type: application/json');
        try {
            echo json_encode(['status' => 'ok', 'data' => $this->noteModel->getAll()]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function show(): void
    {
        header('Content-Type: application/json');
        try {
            $talentoId = $_GET['talento_id'] ?? null;
            if (!$talentoId) {
                throw new \RuntimeException('Talento ID mancante');
            }
            echo json_encode(['status' => 'ok', 'data' => $this->noteModel->getByTalento((int)$talentoId)]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function grouped(): void
    {
        header('Content-Type: application/json');
        try {
            $talentoId = $_GET['talento_id'] ?? null;
            if (!$talentoId) {
                throw new \RuntimeException('Talento ID mancante');
            }
            echo json_encode(['status' => 'ok', 'data' => $this->noteModel->getGroupedByType((int)$talentoId)]);
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
            if (empty($data['tipo']) || empty($data['contenuto'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Tipo e contenuto sono obbligatori']);
                return;
            }
            $id = $this->noteModel->create($data);
            echo json_encode(['status' => 'ok', 'id' => $id, 'message' => 'Nota creata']);
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
            $this->noteModel->update((int)$id, $data);
            echo json_encode(['status' => 'ok', 'message' => 'Nota aggiornata']);
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
            $this->noteModel->delete((int)$id);
            echo json_encode(['status' => 'ok', 'message' => 'Nota eliminata']);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

if (!class_exists('NoteController', false)) {
    class_alias(\App\Controllers\NoteController::class, 'NoteController');
}
