<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Connection;
use App\Models\PlayerState;

class PlayerStateController extends ApiController
{
    private PlayerState $stateModel;

    public function __construct()
    {
        $this->stateModel = new PlayerState(Connection::getInstance());
    }

    public function index(): void
    {
        $this->json($this->stateModel->getAllStates());
    }

    public function show(): void
    {
        $component = $_GET['component'] ?? null;
        if (!$component) {
            $this->error('Component is required');
        }
        $state = $this->stateModel->getState((string)$component);
        if (!$state) {
            $this->error('Component state not found', 404);
        }
        $this->json($state);
    }

    public function update(): void
    {
        $data = $this->getJsonInput();
        if (!isset($data['component'])) {
            $this->error('Component is required');
        }
        if ($this->stateModel->updateState((string)$data['component'], $data)) {
            $this->json(['status' => 'ok', 'message' => 'State updated successfully']);
        }
        $this->error('Failed to update state', 500);
    }
}

if (!class_exists('PlayerStateController', false)) {
    class_alias(\App\Controllers\PlayerStateController::class, 'PlayerStateController');
}
