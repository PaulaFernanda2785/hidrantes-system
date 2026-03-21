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

            $this->validateCsrf($request);

            foreach ($route['middlewares'] as $middlewareDefinition) {
                [$middlewareClass, $middlewareArgs] = $this->parseMiddleware($middlewareDefinition);

                if (!class_exists($middlewareClass)) {
                    throw new \RuntimeException("Middleware não encontrado: {$middlewareClass}");
                }

                $middleware = new $middlewareClass(...$middlewareArgs);
                $middleware->handle($request);
            }

            [$controllerClass, $method] = $route['handler'];
            $controller = new $controllerClass();

            call_user_func_array([$controller, $method], $matches);
            return;
        }

        http_response_code(404);
        echo '404 - Página não encontrada';
    }

    private function validateCsrf(Request $request): void
    {
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }

        if (csrf_token_is_valid((string) $request->input('_token'))) {
            return;
        }

        Session::flash('error', 'Sua sessao expirou. Reenvie o formulario.');
        redirect(previous_path('/'));
    }

    private function parseMiddleware(string $middlewareDefinition): array
    {
        if (!str_contains($middlewareDefinition, ':')) {
            return [$middlewareDefinition, []];
        }

        [$middlewareClass, $rawArgs] = explode(':', $middlewareDefinition, 2);

        $args = array_values(array_filter(array_map(
            static fn(string $item): string => trim($item),
            explode(',', $rawArgs)
        ), static fn(string $item): bool => $item !== ''));

        return [$middlewareClass, [$args]];
    }
}
