<?php

declare(strict_types=1);

namespace App;

/**
 * HTTP-method aware router with optional middleware chain.
 *
 * Supported methods: GET, POST, PUT, DELETE, PATCH, ANY (for fallback).
 * Each route maps `[method]:[path]` to controller class + action.
 */
class Router
{
    /** @var array<string, array{controller: string, action: string, middlewares: array<int, callable>}> */
    private array $routes = [];

    /** @var array<int, string> Prefixes recognized as the public mount point */
    private array $basePrefixes = [];

    public function setBasePrefixes(array $prefixes): void
    {
        $this->basePrefixes = array_filter(array_map(static fn ($p) => '/' . trim((string) $p, '/'), $prefixes));
    }

    /**
     * Register a route. $controller is a fully-qualified class name or a short
     * legacy name; the dispatcher will try both.
     *
     * @param string|array<int, string> $methods One or more HTTP methods (or 'ANY').
     * @param array<int, callable>      $middlewares
     */
    public function add($methods, string $path, string $controller, string $action, array $middlewares = []): void
    {
        $methodList = is_array($methods) ? $methods : [$methods];
        foreach ($methodList as $method) {
            $key = strtoupper($method) . ':' . $this->normalizePath($path);
            $this->routes[$key] = [
                'controller'  => $controller,
                'action'      => $action,
                'middlewares' => $middlewares,
            ];
        }
    }

    public function dispatch(string $url, ?string $method = null): void
    {
        $method = strtoupper($method ?? ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $path   = $this->normalizePath((string) parse_url($url, PHP_URL_PATH));

        $route = $this->routes[$method . ':' . $path]
            ?? $this->routes['ANY:' . $path]
            ?? null;

        if ($route === null) {
            $this->send404('Route ' . $path . ' not found');
            return;
        }

        $controllerName = $this->resolveController($route['controller']);
        if ($controllerName === null) {
            $this->send404('Controller ' . $route['controller'] . ' not found');
            return;
        }

        $controller = new $controllerName();
        if (!method_exists($controller, $route['action'])) {
            $this->send404('Method ' . $route['action'] . ' not found in controller ' . $controllerName);
            return;
        }

        // Run middlewares in order; each must return true to continue.
        foreach ($route['middlewares'] as $mw) {
            $continue = $mw($controller);
            if ($continue === false) {
                return;
            }
        }

        $controller->{$route['action']}();
    }

    private function resolveController(string $name): ?string
    {
        if (class_exists($name)) {
            return $name;
        }
        $candidate = 'App\\Controllers\\' . ltrim($name, '\\');
        if (class_exists($candidate)) {
            return $candidate;
        }
        return null;
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        if ($path === '/') {
            return '/';
        }
        foreach ($this->basePrefixes as $prefix) {
            if ($prefix !== '/' && str_starts_with($path, $prefix)) {
                $stripped = substr($path, strlen($prefix));
                return $stripped === '' ? '/' : '/' . ltrim($stripped, '/');
            }
        }
        return $path;
    }

    private function send404(string $message = ''): void
    {
        header('HTTP/1.0 404 Not Found');
        echo '404 Not Found: ' . $message;
    }
}

// Backward-compatible alias used by older tests / legacy bootstrap files.
if (!class_exists('Router', false)) {
    class_alias(\App\Router::class, 'Router');
}
