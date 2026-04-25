<?php

require_once BASE_PATH . '/app/Controllers/Controller.php';

class ApiController extends Controller {
    
    protected function json($data, $status = 200) {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    public function getTalenti() {
        header('Content-Type: application/json');
        try {
            $db = (new DatabaseConnector())->getConnection();
            $talentoModel = new \App\Models\Talento($db);
            $talenti = $talentoModel->getScaletta();
            echo json_encode(['status' => 'ok', 'data' => $talenti]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function addTalento() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $db = (new DatabaseConnector())->getConnection();
            $talentoModel = new \App\Models\Talento($db);
            
            // Get last order to increment
            $talenti = $talentoModel->getScaletta();
            $data['ordine_scaletta'] = count($talenti) + 1;
            
            $id = $talentoModel->create($data);
            echo json_encode(['status' => 'ok', 'id' => $id, 'message' => 'Talento aggiunto']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteTalento() {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) throw new \Exception("ID mancante");
            
            $db = (new DatabaseConnector())->getConnection();
            $talentoModel = new \App\Models\Talento($db);
            $talentoModel->delete($id);
            echo json_encode(['status' => 'ok', 'message' => 'Talento eliminato']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function reorderTalento() {
        header('Content-Type: application/json');
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $orderedIds = $data['ordered_ids'] ?? [];
            
            $db = (new DatabaseConnector())->getConnection();
            $talentoModel = new \App\Models\Talento($db);
            $talentoModel->reorder($orderedIds);
            echo json_encode(['status' => 'ok', 'message' => 'Scaletta aggiornata']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    protected function error($message, $status = 400) {
        $this->json(['error' => $message], $status);
    }

    protected function getJsonInput() {
        return json_decode(file_get_contents('php://input'), true);
    }

    protected function validate($data, $rules) {
        // Logica di validazione semplice
        foreach ($rules as $field => $rule) {
            if ($rule === 'required' && (!isset($data[$field]) || empty($data[$field]))) {
                $this->error("Missing required field: $field");
            }
        }
        return true;
    }
}
