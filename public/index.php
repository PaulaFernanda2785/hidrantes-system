<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

date_default_timezone_set('America/Fortaleza');

/*
|--------------------------------------------------------------------------
| Autoload simples sem Composer
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Arquivos auxiliares
|--------------------------------------------------------------------------
*/
require BASE_PATH . '/app/Helpers/functions.php';

use App\Core\Env;
use App\Core\Request;
use App\Core\Router;
use App\Core\Session;

Env::load(BASE_PATH . '/.env');
Session::start();

$router = new Router();

require BASE_PATH . '/routes/web.php';
require BASE_PATH . '/routes/api.php';

$router->dispatch(new Request());