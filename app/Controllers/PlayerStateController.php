<?php

use App\Models\PlayerState;

class PlayerStateController extends ApiController {
    private $stateModel;

    public function __construct() {
        $database = new DatabaseConnector();
        $this->stateModel = new PlayerState($database->getConnection());
    }

    /**
     * API: Get state of all components
     */
    public function index() {
        $states = $this->stateModel->getAllStates();
        $this->json($states);
    }

    /**
     * API: Get state of a specific component
     */
    public function show() {
        $component = $_GET['component'] ?? null;
        if (!$component) $this->error("Component is required");

        $state = $this->stateModel->getState($component);
        if (!$state) $this->error("Component state not found", 404);

        $this->json($state);
    }

    /**
     * API: Update state (called by Proiettore/Gobbo or Dashboard for control)
     */
    public function update() {
        $data = $this->getJsonInput();
        if (!isset($data['component'])) {
            $this->error("Component is required");
        }

        if ($this->stateModel->updateState($data['component'], $data)) {
            $this->json(['message' => 'State updated successfully']);
        } else {
            $this->error("Failed to update state", 500);
        }
    }
}
