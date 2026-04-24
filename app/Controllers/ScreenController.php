<?php

require_once BASE_PATH . '/app/Controllers/Controller.php';

class ScreenController extends Controller {
    
    protected function json($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    public function index() {
        header('Content-Type: application/json');
        try {
            $db = (new DatabaseConnector())->getConnection();
            $screenModel = new \App\Models\Screen($db);
            $screens = $screenModel->getAll();
            echo json_encode(['status' => 'ok', 'data' => $screens]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function show() {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) throw new \Exception("ID mancante");
            
            $db = (new DatabaseConnector())->getConnection();
            $screenModel = new \App\Models\Screen($db);
            $screen = $screenModel->find($id);
            
            if (!$screen) {
                throw new \Exception("Screen non trovato");
            }
            
            $screen['media'] = $screenModel->getMedia($id);
            echo json_encode(['status' => 'ok', 'data' => $screen]);
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
            $screenModel = new \App\Models\Screen($db);
            
            $id = $screenModel->create($data);
            echo json_encode(['status' => 'ok', 'id' => $id, 'message' => 'Screen creato']);
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
            $screenModel = new \App\Models\Screen($db);
            
            $screenModel->update($id, $data);
            echo json_encode(['status' => 'ok', 'message' => 'Screen aggiornato']);
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
            $screenModel = new \App\Models\Screen($db);
            $screenModel->delete($id);
            
            echo json_encode(['status' => 'ok', 'message' => 'Screen eliminato']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
