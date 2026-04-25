<?php

require_once BASE_PATH . '/app/Controllers/ApiController.php';

class QueueController extends ApiController {

    public function index() {
        header('Content-Type: application/json');
        try {
            $db = (new DatabaseConnector())->getConnection();
            $queueModel = new \App\Models\MediaQueue($db);
            $queue = $queueModel->getAll();
            echo json_encode(['status' => 'ok', 'data' => $queue]);
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
            $queueModel = new \App\Models\MediaQueue($db);
            $queue = $queueModel->getByTalento($talento_id);
            
            echo json_encode(['status' => 'ok', 'data' => $queue]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function add() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $db = (new DatabaseConnector())->getConnection();
            $queueModel = new \App\Models\MediaQueue($db);
            
            $id = $queueModel->add($data);
            echo json_encode(['status' => 'ok', 'id' => $id, 'message' => 'Elemento aggiunto alla coda']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateStatus() {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            $status = $_GET['status'] ?? null;
            if (!$id || !$status) throw new \Exception("ID o status mancante");
            
            $db = (new DatabaseConnector())->getConnection();
            $queueModel = new \App\Models\MediaQueue($db);
            $queueModel->updateStatus($id, $status);
            
            echo json_encode(['status' => 'ok', 'message' => 'Stato aggiornato']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function reorder() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $orderedIds = $data['ids'] ?? [];
            
            $db = (new DatabaseConnector())->getConnection();
            $queueModel = new \App\Models\MediaQueue($db);
            $queueModel->reorder($orderedIds);
            
            echo json_encode(['status' => 'ok', 'message' => 'Coda riordinata']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function remove() {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) throw new \Exception("ID mancante");
            
            $db = (new DatabaseConnector())->getConnection();
            $queueModel = new \App\Models\MediaQueue($db);
            $queueModel->remove($id);
            
            echo json_encode(['status' => 'ok', 'message' => 'Elemento rimosso dalla coda']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function playing() {
        header('Content-Type: application/json');
        try {
            $db = (new DatabaseConnector())->getConnection();
            $queueModel = new \App\Models\MediaQueue($db);
            $playing = $queueModel->getPlaying();
            
            echo json_encode(['status' => 'ok', 'data' => $playing]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function next() {
        header('Content-Type: application/json');
        try {
            $db = (new DatabaseConnector())->getConnection();
            $queueModel = new \App\Models\MediaQueue($db);
            $next = $queueModel->getNextPending();
            
            echo json_encode(['status' => 'ok', 'data' => $next]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
