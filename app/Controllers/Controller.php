<?php

class Controller {
    protected function render($view, $data = []) {
        extract($data);
        $viewPath = BASE_PATH . "/app/Views/$view.php";
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            die("View $view not found at $viewPath");
        }
    }
    function index() {
        $this->dashboard();
    }
    function dashboard() {
        $this->render('dashboard');
    }
    function projector() {
        $this->render('projector');
    }
    function admin() {
        $this->render('admin');
    }
    function timeline() {
        $this->render('timeline');
    }
}
