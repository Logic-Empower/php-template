<?php

declare(strict_types=1);

namespace App\Http;

use Closure;

final class Router
{
    /** @var array<string, array<string, Closure>> */
    private array $routes = [];

    public function get(string $path, Closure $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, Closure $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH) ?: '/');
        $allowedMethods = [];

        foreach ($this->routes as $routeMethod => $routes) {
            if (isset($routes[$path])) {
                $allowedMethods[] = $routeMethod;
            }
        }

        if ($allowedMethods !== [] && ! isset($this->routes[$method][$path])) {
            http_response_code(405);
            header('Allow: ' . implode(', ', $allowedMethods));
            exit('Method not allowed.');
        }

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            (new Controllers\PageController())->show('404', 'Page Not Found');
            return;
        }

        $handler();
    }

    private function add(string $method, string $path, Closure $handler): void
    {
        $this->routes[$method][$this->normalizePath($path)] = $handler;
    }

    private function normalizePath(string $path): string
    {
        $basePath = base_path_url();

        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath)) ?: '/';
        }

        return rtrim($path, '/') ?: '/';
    }
}
