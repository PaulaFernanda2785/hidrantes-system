<?php

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = dirname(__DIR__, 2);
        return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, '/\\') : $base;
    }
}

if (!function_exists('config_path')) {
    function config_path(string $path = ''): string
    {
        return base_path('config' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

if (!function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        return base_path('public' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return $value === false || $value === null ? $default : $value;
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $configs = [];
        [$file, $item] = array_pad(explode('.', $key, 2), 2, null);
        if (!isset($configs[$file])) {
            $path = config_path($file . '.php');
            $configs[$file] = file_exists($path) ? require $path : [];
        }
        if ($item === null) {
            return $configs[$file] ?? $default;
        }
        return $configs[$file][$item] ?? $default;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('report_exception')) {
    function report_exception(\Throwable $exception): void
    {
        error_log(sprintf(
            '[%s] %s in %s:%d',
            $exception::class,
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        ));
    }
}

if (!function_exists('previous_path')) {
    function previous_path(string $default = '/'): string
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referer === '') {
            return $default;
        }

        $parts = parse_url($referer);
        if ($parts === false) {
            return $default;
        }

        $currentHost = $_SERVER['HTTP_HOST'] ?? '';
        $refererHost = $parts['host'] ?? '';

        if ($refererHost !== '' && $currentHost !== '' && !hash_equals($currentHost, $refererHost)) {
            return $default;
        }

        $path = $parts['path'] ?? $default;
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';

        return $path . $query;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('csrf_token_is_valid')) {
    function csrf_token_is_valid(?string $token): bool
    {
        $sessionToken = $_SESSION['_csrf_token'] ?? null;

        return is_string($token)
            && is_string($sessionToken)
            && $token !== ''
            && hash_equals($sessionToken, $token);
    }
}

if (!function_exists('css_asset')) {
    function css_asset(string $path): string
    {
        return '/assets/css/' . ltrim(str_replace('\\', '/', $path), '/');
    }
}

if (!function_exists('js_asset')) {
    function js_asset(string $path): string
    {
        return '/assets/js/' . ltrim(str_replace('\\', '/', $path), '/');
    }
}
