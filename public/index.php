<?php
/*
 * Copyright (c) 2025. Brusegan Samuele
 * Questo file fa parte di StageMaster ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/bootstrap.php';

session_start();
checkSessionExpiration();

$router = new \App\Router();
require BASE_PATH . '/public/routes.php';

$requestUri    = $_SERVER['REQUEST_URI']    ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Bootstrap window globals for views (skip for API and stream calls).
if (strpos($requestUri, '/api/') === false && strpos($requestUri, '/sse/') === false) {
    echo "
    <script>
        sessionStorage.setItem('url', '" . URL_PATH . "');
        sessionStorage.setItem('theme', '" . THEME . "');
    </script>";
}

$router->dispatch($requestUri, $requestMethod);
