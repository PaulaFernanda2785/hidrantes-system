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

            foreach ($route['middlewares'] as $middlewareDefinition) {
                [$middlewareClass, $middlewareArgs] = $this->parseMiddleware($middlewareDefinition);

                if (!class_exists($middlewareClass)) {
                    throw new \RuntimeException("Middleware não encontrado: {$middlewareClass}");
                }

                $middleware = new $middlewareClass(...$middlewareArgs);
                $middleware->handle($request);
            }

            $this->validateCsrf($request);

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

        if ($this->requestExceededPostMaxSize()) {
            $maxFiles = (int) config('uploads.max_files', 3);
            $maxFileSize = $this->humanReadableBytes((int) config('uploads.max_file_size', 5 * 1024 * 1024));
            $postMaxSize = $this->humanReadableBytes($this->iniSizeToBytes((string) ini_get('post_max_size')));

            Session::flash(
                'error',
                "O envio excedeu o limite aceito pelo servidor. Envie ate {$maxFiles} foto(s) de {$maxFileSize} cada, com no maximo {$postMaxSize} por formulario."
            );
            redirect(previous_path('/'));
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

    private function requestExceededPostMaxSize(): bool
    {
        $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
        if ($contentLength <= 0) {
            return false;
        }

        $postMaxSize = $this->iniSizeToBytes((string) ini_get('post_max_size'));
        if ($postMaxSize <= 0 || $contentLength <= $postMaxSize) {
            return false;
        }

        return empty($_POST) && empty($_FILES);
    }

    private function iniSizeToBytes(string $value): int
    {
        $normalized = trim($value);
        if ($normalized === '') {
            return 0;
        }

        $unit = strtolower(substr($normalized, -1));
        $bytes = (float) $normalized;

        return match ($unit) {
            'g' => (int) round($bytes * 1024 * 1024 * 1024),
            'm' => (int) round($bytes * 1024 * 1024),
            'k' => (int) round($bytes * 1024),
            default => (int) round((float) $normalized),
        };
    }

    private function humanReadableBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = (float) $bytes;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        $precision = $size >= 10 || $unitIndex === 0 ? 0 : 1;

        return number_format($size, $precision, ',', '.') . ' ' . $units[$unitIndex];
    }
}
