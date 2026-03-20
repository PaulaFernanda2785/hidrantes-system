<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, array $handler, array $middlewares = []): void
    {
        $this->routes[] = compact('method', 'path', 'handler', 'middlewares');
    }

    public function dispatch(Request $request): void
    {
        $requestMethod = $request->method();
        $requestUri = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route['path']);
            $pattern = '#^' . rtrim($pattern, '/') . '$#';
            if ($route['path'] === '/') {
                $pattern = '#^/$#';
            }

            if (!preg_match($pattern, $requestUri, $matches)) {
                continue;
            }

            array_shift($matches);

            foreach ($route['middlewares'] as $middlewareClass) {
                (new $middlewareClass())->handle($request);
            }

            [$controllerClass, $method] = $route['handler'];
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $matches);
            return;
        }

        http_response_code(404);
        echo '404 - Página não encontrada';
    }
}
