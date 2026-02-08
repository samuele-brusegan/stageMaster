<?php

class Router {
    private $routes = [];

    public function add($url, $controller, $method) {
        $this->routes[$url] = [
            'controller' => $controller,
            'method' => $method
        ];
    }

    public function dispatch($url) {
        // Rimuovi query string
        $url = parse_url($url, PHP_URL_PATH);
        
        // Rimuovi prefisso se necessario (es. se l'app è in una sottocartella)
        // Per ora assumiamo che giri in root o gestito da .htaccess
        
        if (array_key_exists($url, $this->routes)) {
            $controllerName = $this->routes[$url]['controller'];
            $method = $this->routes[$url]['method'];

            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $method)) {
                    $controller->$method();
                } else {
                    $this->send404("Method $method not found in controller $controllerName");
                }
            } else {
                $this->send404("Controller $controllerName not found");
            }
        } else {
            $this->send404("Route $url not found");
        }
    }

    private function send404($message = "") {
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found: " . $message;
        exit;
    }
}
