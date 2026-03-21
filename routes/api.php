<?php

use App\Controllers\HidranteController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

$router->get('/api/hidrantes/mapa', [HidranteController::class, 'mapData'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor,operador',
]);

$router->get('/api/bairros/municipio/{id}', [HidranteController::class, 'bairrosByMunicipio'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor,operador',
]);
