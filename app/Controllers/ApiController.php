<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Connection;
use App\Models\Talento;

class ApiController extends Controller
{
    protected function json($data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    protected function error(string $message, int $status = 400): void
    {
        $this->json(['status' => 'error', 'error' => $message, 'message' => $message], $status);
    }

    protected function getJsonInput(): array
    {
        $raw = file_get_contents('php://input');
        if ($raw === false || trim($raw) === '') {
            return [];
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    /**
     * @param array<string, mixed>  $data
     * @param array<string, string> $rules Currently supports the value "required".
     */
    protected function validate(array $data, array $rules): bool
    {
        foreach ($rules as $field => $rule) {
            if ($rule === 'required' && (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null)) {
                $this->error("Missing required field: $field");
            }
        }
        return true;
    }

    // ----- Talento helpers retained for backward compatibility with routes ----

    public function addTalento(): void
    {
        header('Content-Type: application/json');
        try {
            $data = $this->getJsonInput();
            if (trim((string)($data['nome'] ?? '')) === '') {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Nome talento obbligatorio']);
                return;
            }
            $talentoModel = new Talento(Connection::getInstance());
            $existing = $talentoModel->getScaletta();
            $data['ordine_scaletta'] = count($existing) + 1;
            $id = $talentoModel->create($data);
            echo json_encode(['status' => 'ok', 'id' => $id, 'message' => 'Talento aggiunto']);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteTalento(): void
    {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new \RuntimeException('ID mancante');
            }
            $talentoModel = new Talento(Connection::getInstance());
            $talentoModel->delete((int)$id);
            echo json_encode(['status' => 'ok', 'message' => 'Talento eliminato']);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function reorderTalento(): void
    {
        header('Content-Type: application/json');
        try {
            $data = $this->getJsonInput();
            $orderedIds = $data['ordered_ids'] ?? ($data['ids'] ?? []);
            $talentoModel = new Talento(Connection::getInstance());
            $talentoModel->reorder($orderedIds);
            echo json_encode(['status' => 'ok', 'message' => 'Scaletta aggiornata']);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}

if (!class_exists('ApiController', false)) {
    class_alias(\App\Controllers\ApiController::class, 'ApiController');
}
