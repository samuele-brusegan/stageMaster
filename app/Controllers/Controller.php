<?php

declare(strict_types=1);

namespace App\Controllers;

class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $viewPath = BASE_PATH . "/app/Views/$view.php";
        if (file_exists($viewPath)) {
            include $viewPath;
            return;
        }
        die("View $view not found at $viewPath");
    }

    public function index(): void
    {
        $this->dashboard();
    }

    public function dashboard(): void
    {
        $this->render('dashboard');
    }

    public function projector(): void
    {
        $this->render('projector');
    }

    public function admin(): void
    {
        $this->render('admin');
    }

    public function timeline(): void
    {
        $this->render('timeline');
    }

    public function editor(): void
    {
        $this->render('editor');
    }
}

// Backward-compatible alias for legacy code that still references global names.
if (!class_exists('Controller', false)) {
    class_alias(\App\Controllers\Controller::class, 'Controller');
}
