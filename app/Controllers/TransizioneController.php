<?php

require_once BASE_PATH . '/app/Controllers/ApiController.php';

class TransizioneController extends ApiController {

    public function show() {
        header('Content-Type: application/json');
        try {
            $media_id = $_GET['media_id'] ?? null;
            if (!$media_id) throw new \Exception("Media ID mancante");
            
            $db = (new DatabaseConnector())->getConnection();
            $transizioneModel = new \App\Models\Transizione($db);
            $transizione = $transizioneModel->getByMedia($media_id);
            
            echo json_encode(['status' => 'ok', 'data' => $transizione]);
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
            $transizioneModel = new \App\Models\Transizione($db);
            
            $id = $transizioneModel->create($data);
            echo json_encode(['status' => 'ok', 'id' => $id, 'message' => 'Transizione creata']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update() {
        header('Content-Type: application/json');
        try {
            $media_id = $_GET['media_id'] ?? null;
            if (!$media_id) throw new \Exception("Media ID mancante");
            
            $data = json_decode(file_get_contents('php://input'), true);
            $db = (new DatabaseConnector())->getConnection();
            $transizioneModel = new \App\Models\Transizione($db);
            
            $transizioneModel->update($media_id, $data);
            echo json_encode(['status' => 'ok', 'message' => 'Transizione aggiornata']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete() {
        header('Content-Type: application/json');
        try {
            $media_id = $_GET['media_id'] ?? null;
            if (!$media_id) throw new \Exception("Media ID mancante");
            
            $db = (new DatabaseConnector())->getConnection();
            $transizioneModel = new \App\Models\Transizione($db);
            $transizioneModel->delete($media_id);
            
            echo json_encode(['status' => 'ok', 'message' => 'Transizione eliminata']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getOrCreate() {
        header('Content-Type: application/json');
        try {
            $media_id = $_GET['media_id'] ?? null;
            if (!$media_id) throw new \Exception("Media ID mancante");
            
            $db = (new DatabaseConnector())->getConnection();
            $transizioneModel = new \App\Models\Transizione($db);
            $transizione = $transizioneModel->getOrCreate($media_id);
            
            echo json_encode(['status' => 'ok', 'data' => $transizione]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
