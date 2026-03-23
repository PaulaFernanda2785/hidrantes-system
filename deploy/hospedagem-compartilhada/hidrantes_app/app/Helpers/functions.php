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
        $target = trim($path);

        if ($target === '') {
            $target = '/';
        }

        $parts = parse_url($target);
        if ($parts === false) {
            $target = '/';
        } elseif (isset($parts['scheme']) || isset($parts['host'])) {
            $target = '/';
        } elseif (!str_starts_with($target, '/')) {
            $target = '/' . ltrim($target, '/');
        }

        header('Location: ' . $target);
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

if (!function_exists('client_ip')) {
    function client_ip(): string
    {
        $ip = trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));

        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }
}

if (!function_exists('app_is_secure_request')) {
    function app_is_secure_request(): bool
    {
        $https = strtolower((string) ($_SERVER['HTTPS'] ?? ''));
        $forwardedProto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
        $serverPort = (string) ($_SERVER['SERVER_PORT'] ?? '');

        return $https === 'on'
            || $https === '1'
            || $forwardedProto === 'https'
            || $serverPort === '443';
    }
}

if (!function_exists('client_user_agent')) {
    function client_user_agent(): string
    {
        $userAgent = trim((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));

        if ($userAgent === '') {
            return 'unknown';
        }

        $userAgent = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $userAgent);

        return trim((string) $userAgent) !== '' ? trim((string) $userAgent) : 'unknown';
    }
}

if (!function_exists('auth_session_fingerprint')) {
    function auth_session_fingerprint(): string
    {
        return hash('sha256', client_user_agent());
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

if (!function_exists('app_url')) {
    function app_url(string $path = ''): string
    {
        $baseUrl = rtrim((string) config('app.url', ''), '/');
        $normalizedPath = '/' . ltrim($path, '/');

        if ($baseUrl === '') {
            return $normalizedPath;
        }

        return $baseUrl . ($normalizedPath === '/' ? '' : $normalizedPath);
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

if (!function_exists('send_security_headers')) {
    function send_security_headers(): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(self), camera=(self), microphone=(), payment=(), usb=()');
        header('Cross-Origin-Opener-Policy: same-origin');
        header('Cross-Origin-Resource-Policy: same-origin');
        header('X-Robots-Tag: noindex, nofollow');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        if (app_is_secure_request()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}

if (!function_exists('register_error_handlers')) {
    function register_error_handlers(): void
    {
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');

        set_exception_handler(static function (\Throwable $exception): void {
            report_exception($exception);

            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: text/html; charset=utf-8');
            }

            echo 'Erro interno do sistema.';
            exit;
        });

        register_shutdown_function(static function (): void {
            $error = error_get_last();

            if (!is_array($error)) {
                return;
            }

            $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
            if (!in_array((int) ($error['type'] ?? 0), $fatalTypes, true)) {
                return;
            }

            report_exception(new \ErrorException(
                (string) ($error['message'] ?? 'Erro fatal'),
                0,
                (int) ($error['type'] ?? E_ERROR),
                (string) ($error['file'] ?? 'unknown'),
                (int) ($error['line'] ?? 0)
            ));

            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: text/html; charset=utf-8');
            }

            echo 'Erro interno do sistema.';
        });
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
