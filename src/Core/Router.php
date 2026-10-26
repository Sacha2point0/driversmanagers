<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $base = rtrim((string) Config::get('app.base_url', ''), '/');
        $basePath = $base !== '' ? (parse_url($base, PHP_URL_PATH) ?: '') : '';
        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }
        $path = '/' . trim($path, '/');

        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            // {param} => segment numérique ou texte
            $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $path, $m)) {
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                [$class, $action] = $handler;
                (new $class())->$action(...array_values($params));
                return;
            }
        }
        http_response_code(404);
        View::render('layout/error', ['title' => 'Page introuvable', 'message' => 'Cette page n’existe pas.']);
    }
}
