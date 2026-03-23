<?php

declare(strict_types=1);

$basePathCandidates = [
    dirname(__DIR__) . '/hidrantes_app',
    dirname(__DIR__) . '/hidrantes-system',
];

$resolvedBasePath = null;
foreach ($basePathCandidates as $candidate) {
    if (is_dir($candidate . '/app') && is_file($candidate . '/routes/web.php')) {
        $resolvedBasePath = $candidate;
        break;
    }
}

if ($resolvedBasePath === null) {
    http_response_code(500);
    echo 'Base path da aplicacao nao foi encontrado. Verifique se a pasta hidrantes_app foi enviada ao lado do public_html.';
    exit;
}

define('BASE_PATH', $resolvedBasePath);

date_default_timezone_set('America/Fortaleza');

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

require BASE_PATH . '/app/Helpers/functions.php';

use App\Core\Env;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;

Env::load(BASE_PATH . '/.env');
Session::start();
register_error_handlers();
send_security_headers();

$router = new Router();

require BASE_PATH . '/routes/web.php';
require BASE_PATH . '/routes/api.php';

$router->dispatch(new Request());
