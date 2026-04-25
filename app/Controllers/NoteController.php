<?php

require_once BASE_PATH . '/app/Controllers/ApiController.php';

class NoteController extends ApiController {

    public function index() {
        header('Content-Type: application/json');
        try {
            $db = (new DatabaseConnector())->getConnection();
            $noteModel = new \App\Models\NoteTecniche($db);
            $notes = $noteModel->getAll();
            echo json_encode(['status' => 'ok', 'data' => $notes]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function show() {
        header('Content-Type: application/json');
        try {
            $talento_id = $_GET['talento_id'] ?? null;
            if (!$talento_id) throw new \Exception("Talento ID mancante");
            
            $db = (new DatabaseConnector())->getConnection();
            $noteModel = new \App\Models\NoteTecniche($db);
            $notes = $noteModel->getByTalento($talento_id);
            
            echo json_encode(['status' => 'ok', 'data' => $notes]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function grouped() {
        header('Content-Type: application/json');
        try {
            $talento_id = $_GET['talento_id'] ?? null;
            if (!$talento_id) throw new \Exception("Talento ID mancante");
            
            $db = (new DatabaseConnector())->getConnection();
            $noteModel = new \App\Models\NoteTecniche($db);
            $notes = $noteModel->getGroupedByType($talento_id);
            
            echo json_encode(['status' => 'ok', 'data' => $notes]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function create() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $db = (new DatabaseConnector())->getConnection();
            $noteModel = new \App\Models\NoteTecniche($db);
            
            $id = $noteModel->create($data);
            echo json_encode(['status' => 'ok', 'id' => $id, 'message' => 'Nota creata']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update() {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) throw new \Exception("ID mancante");
            
            $data = json_decode(file_get_contents('php://input'), true);
            $db = (new DatabaseConnector())->getConnection();
            $noteModel = new \App\Models\NoteTecniche($db);
            
            $noteModel->update($id, $data);
            echo json_encode(['status' => 'ok', 'message' => 'Nota aggiornata']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete() {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) throw new \Exception("ID mancante");
            
            $db = (new DatabaseConnector())->getConnection();
            $noteModel = new \App\Models\NoteTecniche($db);
            $noteModel->delete($id);
            
            echo json_encode(['status' => 'ok', 'message' => 'Nota eliminata']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
